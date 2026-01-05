<?php

namespace App\Repository;

use App\Entity\RequestEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RequestEntity>
 *
 * @method RequestEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestEntity[]    findAll()
 * @method RequestEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestEntity::class);
    }

    public function findPending(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.status = :val')
            ->setParameter('val', 'PENDIENTE')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
