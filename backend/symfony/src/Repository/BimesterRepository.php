<?php

namespace App\Repository;

use App\Entity\Bimester;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bimester>
 */
class BimesterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bimester::class);
    }

    public function findCurrent(): ?Bimester
    {
        $today = new \DateTime();
        
        return $this->createQueryBuilder('b')
            ->andWhere('b.isActive = true')
            ->andWhere('b.startDate <= :today')
            ->andWhere('b.endDate >= :today')
            ->setParameter('today', $today)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByCycle(int $cycleId): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.schoolCycle = :cycle')
            ->setParameter('cycle', $cycleId)
            ->orderBy('b.number', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOpen(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.isClosed = false')
            ->andWhere('b.isActive = true')
            ->orderBy('b.number', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
