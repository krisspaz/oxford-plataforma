<?php

namespace App\Repository;

use App\Entity\Package;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Package>
 */
class PackageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Package::class);
    }

    public function findActiveByCycle(int $cycleId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.schoolCycle = :cycle')
            ->andWhere('p.isActive = true')
            ->setParameter('cycle', $cycleId)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByGrade(int $gradeId): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.applicableGrades', 'g')
            ->andWhere('g.id = :grade')
            ->andWhere('p.isActive = true')
            ->setParameter('grade', $gradeId)
            ->getQuery()
            ->getResult();
    }
}
