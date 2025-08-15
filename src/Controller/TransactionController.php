<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\CryptoRepository;
use App\Service\TimeSeriesBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TransactionController extends AbstractController
{
    #[Route('/transaction/new/{symbol}', name: 'transaction_new', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(string $symbol, Request $req, CryptoRepository $cryptos, EntityManagerInterface $em): Response
    {
        $crypto = $cryptos->findOneBy(['symbol' => strtoupper($symbol)]);
        if (!$crypto) throw $this->createNotFoundException('Unknown crypto');

        $t = new Transaction();
        $form = $this->createForm(TransactionType::class, $t);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $t->setUser($this->getUser());
            $t->setCrypto($crypto);
            $em->persist($t); $em->flush();
            return $this->redirectToRoute('app_dashboard', ['crypto' => $crypto->getSymbol()]);
        }
        return $this->forward(DashboardController::class.'::index', [ 'req' => $req ]);
    }

    #[Route('/api/asset/{symbol}/series', name: 'api_series', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function series(string $symbol, CryptoRepository $cryptos, TimeSeriesBuilder $builder): JsonResponse
    {
        $crypto = $cryptos->findOneBy(['symbol' => strtoupper($symbol)]);
        if (!$crypto) return $this->json(['error' => 'Unknown crypto'], 404);
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        return $this->json($builder->build($user, $crypto));
    }
}
