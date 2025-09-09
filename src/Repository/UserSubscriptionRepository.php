<?php

namespace App\Repository;

use App\Entity\UserSubscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserSubscription>
 */
class UserSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSubscription::class);
    }

    public function save(UserSubscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserSubscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByStripeSubscriptionId(string $stripeSubscriptionId): ?UserSubscription
    {
        return $this->createQueryBuilder('us')
            ->andWhere('us.stripeSubscriptionId = :stripeSubscriptionId')
            ->setParameter('stripeSubscriptionId', $stripeSubscriptionId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByStripeCustomerId(string $stripeCustomerId): ?UserSubscription
    {
        return $this->createQueryBuilder('us')
            ->andWhere('us.stripeCustomerId = :stripeCustomerId')
            ->setParameter('stripeCustomerId', $stripeCustomerId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findActiveSubscriptions(): array
    {
        return $this->createQueryBuilder('us')
            ->andWhere('us.status IN (:statuses)')
            ->setParameter('statuses', ['active', 'trialing'])
            ->getQuery()
            ->getResult();
    }

    public function findExpiredSubscriptions(): array
    {
        return $this->createQueryBuilder('us')
            ->andWhere('us.currentPeriodEnd < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }
}
