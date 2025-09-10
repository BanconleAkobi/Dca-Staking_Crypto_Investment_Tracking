<?php

namespace App\Controller;

use App\Entity\SavingsAccount;
use App\Form\SavingsAccountType;
use App\Repository\SavingsAccountRepository;
use App\Service\SubscriptionAccessService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/savings-accounts')]
#[IsGranted('ROLE_USER')]
class SavingsAccountController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SavingsAccountRepository $savingsAccountRepository,
        private SubscriptionAccessService $subscriptionAccess
    ) {}

    #[Route('/', name: 'app_savings_account_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        $savingsAccounts = $this->savingsAccountRepository->findByUser($user);

        // Séparer les comptes réglementés et à terme
        $regulatedAccounts = $this->savingsAccountRepository->findRegulatedAccounts($user);
        $termAccounts = $this->savingsAccountRepository->findTermAccounts($user);

        // Calculer les totaux
        $totalBalance = array_sum(array_map(fn($account) => $account->getCurrentBalance() ?? 0, $savingsAccounts));
        $totalAnnualInterest = $this->savingsAccountRepository->getTotalAnnualInterest($user);

        return $this->render('savings_account/index.html.twig', [
            'savingsAccounts' => $savingsAccounts,
            'regulatedAccounts' => $regulatedAccounts,
            'termAccounts' => $termAccounts,
            'totalBalance' => $totalBalance,
            'totalAnnualInterest' => $totalAnnualInterest,
            'totalAccounts' => count($savingsAccounts)
        ]);
    }

    #[Route('/new', name: 'app_savings_account_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        
        if (!$this->subscriptionAccess->canAddSavingsAccount($user)) {
            $this->addFlash('error', $this->subscriptionAccess->getUpgradeMessage('savings_account'));
            return $this->redirectToRoute('app_billing_index');
        }

        $savingsAccount = new SavingsAccount();
        $savingsAccount->setUser($user);
        $savingsAccount->setIsActive(true);
        
        $form = $this->createForm(SavingsAccountType::class, $savingsAccount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($savingsAccount);
            $this->entityManager->flush();

            $this->addFlash('success', 'Compte d\'épargne ajouté avec succès !');
            return $this->redirectToRoute('app_savings_account_index');
        }

        return $this->render('savings_account/new.html.twig', [
            'savingsAccount' => $savingsAccount,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_savings_account_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(SavingsAccount $savingsAccount): Response
    {
        // Vérifier que le compte appartient à l'utilisateur
        if ($savingsAccount->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('savings_account/show.html.twig', [
            'savingsAccount' => $savingsAccount,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_savings_account_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, SavingsAccount $savingsAccount): Response
    {
        // Vérifier que le compte appartient à l'utilisateur
        if ($savingsAccount->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(SavingsAccountType::class, $savingsAccount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Compte d\'épargne modifié avec succès !');
            return $this->redirectToRoute('app_savings_account_show', ['id' => $savingsAccount->getId()]);
        }

        return $this->render('savings_account/edit.html.twig', [
            'savingsAccount' => $savingsAccount,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_savings_account_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, SavingsAccount $savingsAccount): Response
    {
        // Vérifier que le compte appartient à l'utilisateur
        if ($savingsAccount->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$savingsAccount->getId(), $request->request->get('_token'))) {
            // Vérifier s'il y a des transactions associées
            if ($savingsAccount->getTransactions()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer ce compte car il contient des transactions. Désactivez-le à la place.');
                return $this->redirectToRoute('app_savings_account_show', ['id' => $savingsAccount->getId()]);
            }

            $this->entityManager->remove($savingsAccount);
            $this->entityManager->flush();

            $this->addFlash('success', 'Compte d\'épargne supprimé avec succès !');
        }

        return $this->redirectToRoute('app_savings_account_index');
    }

    #[Route('/{id}/toggle-status', name: 'app_savings_account_toggle_status', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function toggleStatus(Request $request, SavingsAccount $savingsAccount): Response
    {
        // Vérifier que le compte appartient à l'utilisateur
        if ($savingsAccount->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('toggle'.$savingsAccount->getId(), $request->request->get('_token'))) {
            $savingsAccount->setIsActive(!$savingsAccount->isActive());
            $this->entityManager->flush();

            $status = $savingsAccount->isActive() ? 'activé' : 'désactivé';
            $this->addFlash('success', "Compte d'épargne {$status} avec succès !");
        }

        return $this->redirectToRoute('app_savings_account_show', ['id' => $savingsAccount->getId()]);
    }

    #[Route('/regulated', name: 'app_savings_account_regulated', methods: ['GET'])]
    public function regulated(): Response
    {
        $user = $this->getUser();
        $regulatedAccounts = $this->savingsAccountRepository->findRegulatedAccounts($user);

        return $this->render('savings_account/regulated.html.twig', [
            'regulatedAccounts' => $regulatedAccounts,
        ]);
    }

    #[Route('/term', name: 'app_savings_account_term', methods: ['GET'])]
    public function term(): Response
    {
        $user = $this->getUser();
        $termAccounts = $this->savingsAccountRepository->findTermAccounts($user);

        return $this->render('savings_account/term.html.twig', [
            'termAccounts' => $termAccounts,
        ]);
    }

    #[Route('/maturity-soon', name: 'app_savings_account_maturity_soon', methods: ['GET'])]
    public function maturitySoon(): Response
    {
        $user = $this->getUser();
        $accountsMaturitySoon = $this->savingsAccountRepository->findAccountsMaturitySoon($user, 30);

        return $this->render('savings_account/maturity_soon.html.twig', [
            'accountsMaturitySoon' => $accountsMaturitySoon,
        ]);
    }
}
