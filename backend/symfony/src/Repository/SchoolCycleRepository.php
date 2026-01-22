<?php

namespace App\Repository;

use App\Entity\SchoolCycle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchoolCycle>
 */
class SchoolCycleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolCycle::class);
    }

    public function findActiveCycle(): ?SchoolCycle
    {
        return $this->findOneBy(['isActive' => true]);
    }
}
