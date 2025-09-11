<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Form\AssetType;
use App\Repository\AssetRepository;
use App\Service\SubscriptionAccessService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/my-assets')]
#[IsGranted('ROLE_USER')]
class AssetController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AssetRepository $assetRepository,
        private SubscriptionAccessService $subscriptionAccess
    ) {}

    #[Route('/', name: 'app_asset_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        $assets = $this->assetRepository->findByUser($user);
        // Grouper les actifs par type
        $assetsByType = [];
        foreach ($assets as $asset) {
            $type = $asset->getType();
            if (!isset($assetsByType[$type])) {
                $assetsByType[$type] = [];
            }
            $assetsByType[$type][] = $asset;
        }

        return $this->render('asset/index.html.twig', [
            'assets' => $assets,
            'assetsByType' => $assetsByType,
            'totalAssets' => count($assets)
        ]);
    }

    #[Route('/new', name: 'app_asset_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        
        if (!$this->subscriptionAccess->canAddAsset($user)) {
            $this->addFlash('error', $this->subscriptionAccess->getUpgradeMessage('asset'));
            return $this->redirectToRoute('app_billing_index');
        }

        $asset = new Asset();
        $asset->setUser($user);
        $asset->setIsActive(true);
        
        $form = $this->createForm(AssetType::class, $asset);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Convertir le symbole en majuscules
            $asset->setSymbol(strtoupper($asset->getSymbol()));
            
            $this->entityManager->persist($asset);
            $this->entityManager->flush();

            $this->addFlash('success', 'Actif ajouté avec succès !');
            return $this->redirectToRoute('app_asset_index');
        }

        return $this->render('asset/new.html.twig', [
            'asset' => $asset,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_asset_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Asset $asset): Response
    {
        // Vérifier que l'actif appartient à l'utilisateur
        if ($asset->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('asset/show.html.twig', [
            'asset' => $asset,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_asset_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Asset $asset): Response
    {
        // Vérifier que l'actif appartient à l'utilisateur
        if ($asset->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(AssetType::class, $asset);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Convertir le symbole en majuscules
            $asset->setSymbol(strtoupper($asset->getSymbol()));
            
            $this->entityManager->flush();

            $this->addFlash('success', 'Actif modifié avec succès !');
            return $this->redirectToRoute('app_asset_show', ['id' => $asset->getId()]);
        }

        return $this->render('asset/edit.html.twig', [
            'asset' => $asset,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_asset_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Asset $asset): Response
    {
        // Vérifier que l'actif appartient à l'utilisateur
        if ($asset->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$asset->getId(), $request->request->get('_token'))) {
            // Vérifier s'il y a des transactions associées
            if ($asset->getTransactions()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer cet actif car il contient des transactions. Désactivez-le à la place.');
                return $this->redirectToRoute('app_asset_show', ['id' => $asset->getId()]);
            }

            $this->entityManager->remove($asset);
            $this->entityManager->flush();

            $this->addFlash('success', 'Actif supprimé avec succès !');
        }

        return $this->redirectToRoute('app_asset_index');
    }

    #[Route('/{id}/toggle-status', name: 'app_asset_toggle_status', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function toggleStatus(Request $request, Asset $asset): Response
    {
        // Vérifier que l'actif appartient à l'utilisateur
        if ($asset->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('toggle'.$asset->getId(), $request->request->get('_token'))) {
            $asset->setIsActive(!$asset->isActive());
            $this->entityManager->flush();

            $status = $asset->isActive() ? 'activé' : 'désactivé';
            $this->addFlash('success', "Actif {$status} avec succès !");
        }

        return $this->redirectToRoute('app_asset_show', ['id' => $asset->getId()]);
    }

    #[Route('/type/{type}', name: 'app_asset_by_type', methods: ['GET'])]
    public function byType(string $type): Response
    {
        $user = $this->getUser();
        $assets = $this->assetRepository->findByType($user, $type);

        return $this->render('asset/by_type.html.twig', [
            'assets' => $assets,
            'type' => $type,
            'typeLabel' => $this->getTypeLabel($type)
        ]);
    }

    private function getTypeLabel(string $type): string
    {
        return match($type) {
            Asset::TYPE_CRYPTO => 'Cryptomonnaies',
            Asset::TYPE_STOCK => 'Actions',
            Asset::TYPE_ETF => 'ETF',
            Asset::TYPE_BOND => 'Obligations',
            Asset::TYPE_SAVINGS => 'Épargne',
            Asset::TYPE_REAL_ESTATE => 'Immobilier',
            Asset::TYPE_COMMODITY => 'Matières premières',
            Asset::TYPE_CURRENCY => 'Devises',
            default => 'Actifs'
        };
    }
}
