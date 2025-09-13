<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Document>
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function findByUser(User $user, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('d')
            ->andWhere('d.user = :user')
            ->setParameter('user', $user)
            ->orderBy('d.uploadedAt', 'DESC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByUserAndType(User $user, string $type): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.user = :user')
            ->andWhere('d.type = :type')
            ->setParameter('user', $user)
            ->setParameter('type', $type)
            ->orderBy('d.uploadedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByRelatedEntity(User $user, string $entityType, int $entityId): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.user = :user')
            ->andWhere('d.relatedEntity = :entityType')
            ->andWhere('d.relatedEntityId = :entityId')
            ->setParameter('user', $user)
            ->setParameter('entityType', $entityType)
            ->setParameter('entityId', $entityId)
            ->orderBy('d.uploadedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPublic(): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.isPublic = :public')
            ->setParameter('public', true)
            ->orderBy('d.uploadedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByDateRange(User $user, \DateTime $startDate, \DateTime $endDate): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.user = :user')
            ->andWhere('d.uploadedAt BETWEEN :startDate AND :endDate')
            ->setParameter('user', $user)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('d.uploadedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function searchByUser(User $user, string $searchTerm): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.user = :user')
            ->andWhere('d.name LIKE :searchTerm OR d.description LIKE :searchTerm')
            ->setParameter('user', $user)
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->orderBy('d.uploadedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
