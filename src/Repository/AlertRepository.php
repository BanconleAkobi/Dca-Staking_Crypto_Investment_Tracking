<?php

namespace App\Repository;

use App\Entity\Alert;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alert>
 */
class AlertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alert::class);
    }

    public function findActiveByUser(User $user): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->andWhere('a.isActive = :active')
            ->setParameter('user', $user)
            ->setParameter('active', true)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByUser(User $user, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
            ->orderBy('a.createdAt', 'DESC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByUserAndType(User $user, string $type): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->andWhere('a.type = :type')
            ->andWhere('a.isActive = :active')
            ->setParameter('user', $user)
            ->setParameter('type', $type)
            ->setParameter('active', true)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findTriggeredByUser(User $user): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->andWhere('a.triggeredAt IS NOT NULL')
            ->setParameter('user', $user)
            ->orderBy('a.triggeredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findDueAlerts(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isActive = :active')
            ->andWhere('a.dueDate <= :now')
            ->andWhere('a.triggeredAt IS NULL')
            ->setParameter('active', true)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }
}
