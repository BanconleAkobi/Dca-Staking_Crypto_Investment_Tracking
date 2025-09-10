<?php

namespace App\Controller;

use App\Entity\Withdrawal;
use App\Form\WithdrawalType;
use App\Repository\WithdrawalRepository;
use App\Repository\AssetRepository;
use App\Repository\SavingsAccountRepository;
use App\Service\SubscriptionAccessService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/withdrawals')]
#[IsGranted('ROLE_USER')]
class WithdrawalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WithdrawalRepository $withdrawalRepository,
        private AssetRepository $assetRepository,
        private SavingsAccountRepository $savingsAccountRepository,
        private SubscriptionAccessService $subscriptionAccess
    ) {}

    #[Route('/', name: 'app_withdrawal_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        $withdrawals = $this->withdrawalRepository->findByUser($user);

        // Grouper par statut
        $withdrawalsByStatus = [
            'pending' => $this->withdrawalRepository->findPending($user),
            'completed' => $this->withdrawalRepository->findCompleted($user),
        ];

        // Calculer les totaux
        $totalWithdrawals = array_sum(array_map(fn($w) => $w->getAmount(), $withdrawals));
        $totalTaxes = $this->withdrawalRepository->getTotalTaxes($user);

        return $this->render('withdrawal/index.html.twig', [
            'withdrawals' => $withdrawals,
            'withdrawalsByStatus' => $withdrawalsByStatus,
            'totalWithdrawals' => $totalWithdrawals,
            'totalTaxes' => $totalTaxes,
            'totalCount' => count($withdrawals)
        ]);
    }

    #[Route('/new', name: 'app_withdrawal_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        
        if (!$this->subscriptionAccess->canAddWithdrawal($user)) {
            $this->addFlash('error', $this->subscriptionAccess->getUpgradeMessage('withdrawal'));
            return $this->redirectToRoute('app_billing_index');
        }

        $withdrawal = new Withdrawal();
        $withdrawal->setUser($user);
        $withdrawal->setDate(new \DateTimeImmutable());
        $withdrawal->setStatus(Withdrawal::STATUS_PENDING);
        
        $form = $this->createForm(WithdrawalType::class, $withdrawal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($withdrawal);
            $this->entityManager->flush();

            $this->addFlash('success', 'Retrait enregistré avec succès !');
            return $this->redirectToRoute('app_withdrawal_index');
        }

        return $this->render('withdrawal/new.html.twig', [
            'withdrawal' => $withdrawal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_withdrawal_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Withdrawal $withdrawal): Response
    {
        // Vérifier que le retrait appartient à l'utilisateur
        if ($withdrawal->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('withdrawal/show.html.twig', [
            'withdrawal' => $withdrawal,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_withdrawal_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Withdrawal $withdrawal): Response
    {
        // Vérifier que le retrait appartient à l'utilisateur
        if ($withdrawal->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(WithdrawalType::class, $withdrawal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $withdrawal->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $this->addFlash('success', 'Retrait modifié avec succès !');
            return $this->redirectToRoute('app_withdrawal_show', ['id' => $withdrawal->getId()]);
        }

        return $this->render('withdrawal/edit.html.twig', [
            'withdrawal' => $withdrawal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_withdrawal_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Withdrawal $withdrawal): Response
    {
        // Vérifier que le retrait appartient à l'utilisateur
        if ($withdrawal->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$withdrawal->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($withdrawal);
            $this->entityManager->flush();

            $this->addFlash('success', 'Retrait supprimé avec succès !');
        }

        return $this->redirectToRoute('app_withdrawal_index');
    }

    #[Route('/{id}/complete', name: 'app_withdrawal_complete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function complete(Request $request, Withdrawal $withdrawal): Response
    {
        // Vérifier que le retrait appartient à l'utilisateur
        if ($withdrawal->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('complete'.$withdrawal->getId(), $request->request->get('_token'))) {
            $withdrawal->setStatus(Withdrawal::STATUS_COMPLETED);
            $withdrawal->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $this->addFlash('success', 'Retrait marqué comme terminé !');
        }

        return $this->redirectToRoute('app_withdrawal_show', ['id' => $withdrawal->getId()]);
    }

    #[Route('/{id}/cancel', name: 'app_withdrawal_cancel', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function cancel(Request $request, Withdrawal $withdrawal): Response
    {
        // Vérifier que le retrait appartient à l'utilisateur
        if ($withdrawal->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('cancel'.$withdrawal->getId(), $request->request->get('_token'))) {
            $withdrawal->setStatus(Withdrawal::STATUS_CANCELLED);
            $withdrawal->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $this->addFlash('success', 'Retrait annulé !');
        }

        return $this->redirectToRoute('app_withdrawal_show', ['id' => $withdrawal->getId()]);
    }

    #[Route('/type/{type}', name: 'app_withdrawal_by_type', methods: ['GET'])]
    public function byType(string $type): Response
    {
        $user = $this->getUser();
        $withdrawals = $this->withdrawalRepository->findByType($user, $type);

        return $this->render('withdrawal/by_type.html.twig', [
            'withdrawals' => $withdrawals,
            'type' => $type,
            'typeLabel' => $this->getTypeLabel($type)
        ]);
    }

    #[Route('/status/{status}', name: 'app_withdrawal_by_status', methods: ['GET'])]
    public function byStatus(string $status): Response
    {
        $user = $this->getUser();
        $withdrawals = $this->withdrawalRepository->findByStatus($user, $status);

        return $this->render('withdrawal/by_status.html.twig', [
            'withdrawals' => $withdrawals,
            'status' => $status,
            'statusLabel' => $this->getStatusLabel($status)
        ]);
    }

    #[Route('/recent', name: 'app_withdrawal_recent', methods: ['GET'])]
    public function recent(): Response
    {
        $user = $this->getUser();
        $recentWithdrawals = $this->withdrawalRepository->findRecent($user, 30);

        return $this->render('withdrawal/recent.html.twig', [
            'recentWithdrawals' => $recentWithdrawals,
        ]);
    }

    private function getTypeLabel(string $type): string
    {
        return match($type) {
            Withdrawal::TYPE_RETIREMENT => 'Retraite',
            Withdrawal::TYPE_EMERGENCY => 'Urgence',
            Withdrawal::TYPE_REBALANCING => 'Rééquilibrage',
            Withdrawal::TYPE_TAX_PAYMENT => 'Paiement d\'impôts',
            Withdrawal::TYPE_PURCHASE => 'Achat',
            Withdrawal::TYPE_INVESTMENT => 'Réinvestissement',
            Withdrawal::TYPE_OTHER => 'Autre',
            default => 'Retrait'
        };
    }

    private function getStatusLabel(string $status): string
    {
        return match($status) {
            Withdrawal::STATUS_PENDING => 'En attente',
            Withdrawal::STATUS_COMPLETED => 'Terminé',
            Withdrawal::STATUS_CANCELLED => 'Annulé',
            default => 'Inconnu'
        };
    }
}
