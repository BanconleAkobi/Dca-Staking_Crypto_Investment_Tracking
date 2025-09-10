<?php

namespace App\Repository;

use App\Entity\SavingsAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SavingsAccount>
 */
class SavingsAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SavingsAccount::class);
    }

    /**
     * Trouve tous les comptes d'épargne d'un utilisateur
     */
    public function findByUser($user): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.isActive = :active')
            ->setParameter('user', $user)
            ->setParameter('active', true)
            ->orderBy('s.type', 'ASC')
            ->addOrderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les comptes d'épargne par type
     */
    public function findByType($user, string $type): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.type = :type')
            ->andWhere('s.isActive = :active')
            ->setParameter('user', $user)
            ->setParameter('type', $type)
            ->setParameter('active', true)
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les comptes d'épargne réglementés
     */
    public function findRegulatedAccounts($user): array
    {
        $regulatedTypes = [
            SavingsAccount::TYPE_LIVRET_A,
            SavingsAccount::TYPE_LDDS,
            SavingsAccount::TYPE_LEP,
            SavingsAccount::TYPE_PEL,
            SavingsAccount::TYPE_CEL,
            SavingsAccount::TYPE_LAJ,
            SavingsAccount::TYPE_LDD,
            SavingsAccount::TYPE_LEP_PLUS
        ];

        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.type IN (:types)')
            ->andWhere('s.isActive = :active')
            ->setParameter('user', $user)
            ->setParameter('types', $regulatedTypes)
            ->setParameter('active', true)
            ->orderBy('s.type', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les comptes d'épargne à terme
     */
    public function findTermAccounts($user): array
    {
        $termTypes = [
            SavingsAccount::TYPE_TERM_DEPOSIT,
            SavingsAccount::TYPE_SAVINGS_BOND,
            SavingsAccount::TYPE_ASSURANCE_VIE,
            SavingsAccount::TYPE_PEA,
            SavingsAccount::TYPE_PEA_PME
        ];

        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.type IN (:types)')
            ->andWhere('s.isActive = :active')
            ->setParameter('user', $user)
            ->setParameter('types', $termTypes)
            ->setParameter('active', true)
            ->orderBy('s.maturityDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les comptes arrivant à échéance
     */
    public function findAccountsMaturitySoon($user, int $daysAhead = 30): array
    {
        $dateThreshold = new \DateTimeImmutable("+{$daysAhead} days");
        
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.maturityDate IS NOT NULL')
            ->andWhere('s.maturityDate <= :threshold')
            ->andWhere('s.isActive = :active')
            ->setParameter('user', $user)
            ->setParameter('threshold', $dateThreshold)
            ->setParameter('active', true)
            ->orderBy('s.maturityDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule le solde total des comptes d'épargne d'un utilisateur
     */
    public function getTotalBalanceByUser($user): float
    {
        $result = $this->createQueryBuilder('s')
            ->select('SUM(s.currentBalance)')
            ->andWhere('s.user = :user')
            ->andWhere('s.isActive = :active')
            ->setParameter('user', $user)
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /**
     * Calcule le total des soldes par type
     */
    public function getTotalBalanceByType($user): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.type, SUM(s.currentBalance) as totalBalance')
            ->andWhere('s.user = :user')
            ->andWhere('s.isActive = :active')
            ->andWhere('s.currentBalance IS NOT NULL')
            ->setParameter('user', $user)
            ->setParameter('active', true)
            ->groupBy('s.type')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule le total des intérêts annuels
     */
    public function getTotalAnnualInterest($user): float
    {
        $result = $this->createQueryBuilder('s')
            ->select('SUM(s.currentBalance * s.annualRate / 100) as totalInterest')
            ->andWhere('s.user = :user')
            ->andWhere('s.isActive = :active')
            ->andWhere('s.currentBalance IS NOT NULL')
            ->andWhere('s.annualRate IS NOT NULL')
            ->setParameter('user', $user)
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }
}
