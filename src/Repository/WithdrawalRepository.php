<?php

namespace App\Repository;

use App\Entity\Withdrawal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Withdrawal>
 */
class WithdrawalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Withdrawal::class);
    }

    /**
     * Trouve tous les retraits d'un utilisateur
     */
    public function findByUser($user): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :user')
            ->setParameter('user', $user)
            ->orderBy('w.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les retraits par type
     */
    public function findByType($user, string $type): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :user')
            ->andWhere('w.type = :type')
            ->setParameter('user', $user)
            ->setParameter('type', $type)
            ->orderBy('w.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les retraits par statut
     */
    public function findByStatus($user, string $status): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :user')
            ->andWhere('w.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', $status)
            ->orderBy('w.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les retraits en attente
     */
    public function findPending($user): array
    {
        return $this->findByStatus($user, Withdrawal::STATUS_PENDING);
    }

    /**
     * Trouve les retraits terminés
     */
    public function findCompleted($user): array
    {
        return $this->findByStatus($user, Withdrawal::STATUS_COMPLETED);
    }

    /**
     * Trouve les retraits dans une période
     */
    public function findByDateRange($user, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :user')
            ->andWhere('w.date >= :startDate')
            ->andWhere('w.date <= :endDate')
            ->setParameter('user', $user)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('w.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les retraits par actif
     */
    public function findByAsset($user, $asset): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :user')
            ->andWhere('w.asset = :asset')
            ->setParameter('user', $user)
            ->setParameter('asset', $asset)
            ->orderBy('w.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les retraits par compte d'épargne
     */
    public function findBySavingsAccount($user, $savingsAccount): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :user')
            ->andWhere('w.savingsAccount = :savingsAccount')
            ->setParameter('user', $user)
            ->setParameter('savingsAccount', $savingsAccount)
            ->orderBy('w.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule le montant total des retraits d'un utilisateur
     */
    public function getTotalAmountByUser($user): float
    {
        $result = $this->createQueryBuilder('w')
            ->select('SUM(w.amount)')
            ->andWhere('w.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /**
     * Calcule le total des retraits par type
     */
    public function getTotalByType($user): array
    {
        return $this->createQueryBuilder('w')
            ->select('w.type, SUM(w.amount) as totalAmount, COUNT(w.id) as count')
            ->andWhere('w.user = :user')
            ->andWhere('w.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', Withdrawal::STATUS_COMPLETED)
            ->groupBy('w.type')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule le total des retraits dans une période
     */
    public function getTotalInPeriod($user, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate): float
    {
        $result = $this->createQueryBuilder('w')
            ->select('SUM(w.amount) as total')
            ->andWhere('w.user = :user')
            ->andWhere('w.date >= :startDate')
            ->andWhere('w.date <= :endDate')
            ->andWhere('w.status = :status')
            ->setParameter('user', $user)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('status', Withdrawal::STATUS_COMPLETED)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /**
     * Calcule le total des impôts payés
     */
    public function getTotalTaxes($user): float
    {
        $result = $this->createQueryBuilder('w')
            ->select('SUM(w.taxAmount) as totalTaxes')
            ->andWhere('w.user = :user')
            ->andWhere('w.status = :status')
            ->andWhere('w.taxAmount IS NOT NULL')
            ->setParameter('user', $user)
            ->setParameter('status', Withdrawal::STATUS_COMPLETED)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /**
     * Trouve les retraits récents (derniers 30 jours)
     */
    public function findRecent($user, int $days = 30): array
    {
        $startDate = new \DateTimeImmutable("-{$days} days");
        return $this->findByDateRange($user, $startDate, new \DateTimeImmutable());
    }
}
