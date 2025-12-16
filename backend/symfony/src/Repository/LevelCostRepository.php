<?php

namespace App\Repository;

use App\Entity\LevelCost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LevelCostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LevelCost::class);
    }

    public function findByLevelAndCycle(int $levelId, int $cycleId): ?LevelCost
    {
        return $this->createQueryBuilder('lc')
            ->andWhere('lc.academicLevel = :level')
            ->andWhere('lc.schoolCycle = :cycle')
            ->setParameter('level', $levelId)
            ->setParameter('cycle', $cycleId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
