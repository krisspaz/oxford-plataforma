<?php

namespace App\Repository;

use App\Entity\Quota;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuotaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quota::class);
    }

    public function findPendingByStudent(int $studentId): array
    {
        return $this->createQueryBuilder('q')
            ->join('q.paymentPlan', 'pp')
            ->andWhere('pp.student = :student')
            ->andWhere('q.status != :paid')
            ->setParameter('student', $studentId)
            ->setParameter('paid', Quota::STATUS_PAID)
            ->orderBy('q.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOverdue(): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.status != :paid')
            ->andWhere('q.dueDate < :today')
            ->setParameter('paid', Quota::STATUS_PAID)
            ->setParameter('today', new \DateTime())
            ->orderBy('q.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
