<?php

namespace App\Repository;

use App\Entity\Assembly;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Assembly>
 */
class AssemblyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assembly::class);
    }

    public function findUpcoming(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.date > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('a.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPast(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.date < :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('a.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.status = :status')
            ->setParameter('status', $status)
            ->orderBy('a.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.type = :type')
            ->setParameter('type', $type)
            ->orderBy('a.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findWithVotingOpen(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.votingOpen = :votingOpen')
            ->setParameter('votingOpen', true)
            ->orderBy('a.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCompany(string $company): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.company = :company')
            ->setParameter('company', $company)
            ->orderBy('a.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
