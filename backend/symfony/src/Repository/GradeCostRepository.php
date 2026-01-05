<?php

namespace App\Repository;

use App\Entity\GradeCost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GradeCost>
 *
 * @method GradeCost|null find($id, $lockMode = null, $lockVersion = null)
 * @method GradeCost|null findOneBy(array $criteria, array $orderBy = null)
 * @method GradeCost[]    findAll()
 * @method GradeCost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeCostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradeCost::class);
    }
}
