<?php

namespace App\Repository;

use App\Entity\AssemblyVote;
use App\Entity\Assembly;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AssemblyVote>
 */
class AssemblyVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssemblyVote::class);
    }

    public function findByAssembly(Assembly $assembly): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.assembly = :assembly')
            ->setParameter('assembly', $assembly)
            ->orderBy('v.votedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.user = :user')
            ->setParameter('user', $user)
            ->orderBy('v.votedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByUserAndAssembly(User $user, Assembly $assembly): ?AssemblyVote
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.user = :user')
            ->andWhere('v.assembly = :assembly')
            ->setParameter('user', $user)
            ->setParameter('assembly', $assembly)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getVoteResults(Assembly $assembly): array
    {
        $results = $this->createQueryBuilder('v')
            ->select('v.vote, COUNT(v.id) as count')
            ->andWhere('v.assembly = :assembly')
            ->setParameter('assembly', $assembly)
            ->groupBy('v.vote')
            ->getQuery()
            ->getResult();

        $formattedResults = [
            'yes' => 0,
            'no' => 0,
            'abstain' => 0,
            'total' => 0
        ];

        foreach ($results as $result) {
            $formattedResults[$result['vote']] = (int) $result['count'];
            $formattedResults['total'] += (int) $result['count'];
        }

        return $formattedResults;
    }
}
