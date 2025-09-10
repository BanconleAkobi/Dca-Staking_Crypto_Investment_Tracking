<?php

namespace App\Repository;

use App\Entity\Asset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Asset>
 */
class AssetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Asset::class);
    }

    /**
     * Trouve tous les actifs d'un utilisateur
     */
    public function findByUser($user): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->andWhere('a.isActive = :active')
            ->setParameter('user', $user)
            ->setParameter('active', true)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les actifs par type
     */
    public function findByType($user, string $type): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->andWhere('a.type = :type')
            ->andWhere('a.isActive = :active')
            ->setParameter('user', $user)
            ->setParameter('type', $type)
            ->setParameter('active', true)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les actifs par catégorie
     */
    public function findByCategory($user, string $category): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->andWhere('a.category = :category')
            ->andWhere('a.isActive = :active')
            ->setParameter('user', $user)
            ->setParameter('category', $category)
            ->setParameter('active', true)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve un actif par symbole et utilisateur
     */
    public function findBySymbolAndUser($user, string $symbol): ?Asset
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->andWhere('a.symbol = :symbol')
            ->andWhere('a.isActive = :active')
            ->setParameter('user', $user)
            ->setParameter('symbol', $symbol)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Compte le nombre d'actifs par type pour un utilisateur
     */
    public function countByType($user): array
    {
        return $this->createQueryBuilder('a')
            ->select('a.type, COUNT(a.id) as count')
            ->andWhere('a.user = :user')
            ->andWhere('a.isActive = :active')
            ->setParameter('user', $user)
            ->setParameter('active', true)
            ->groupBy('a.type')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les actifs nécessitant une mise à jour de prix
     */
    public function findAssetsNeedingPriceUpdate($user, int $hoursOld = 24): array
    {
        $dateThreshold = new \DateTimeImmutable("-{$hoursOld} hours");
        
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->andWhere('a.isActive = :active')
            ->andWhere('(a.lastPriceUpdate IS NULL OR a.lastPriceUpdate < :threshold)')
            ->setParameter('user', $user)
            ->setParameter('active', true)
            ->setParameter('threshold', $dateThreshold)
            ->orderBy('a.lastPriceUpdate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
