<?php

namespace App\Repository;

use App\Entity\SchoolActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchoolActivity>
 *
 * @method SchoolActivity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolActivity|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolActivity[]    findAll()
 * @method SchoolActivity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolActivity::class);
    }

    /**
     * @return SchoolActivity[] Returns an array of upcoming SchoolActivity objects
     */
    public function findUpcoming(int $limit = 5): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.isActive = :active')
            ->andWhere('s.date >= :today')
            ->setParameter('active', true)
            ->setParameter('today', new \DateTime('today'))
            ->orderBy('s.date', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
