<?php

namespace App\Repository;

use App\Entity\PaymentPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PaymentPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentPlan::class);
    }

    public function findByStudent(int $studentId): array
    {
        return $this->createQueryBuilder('pp')
            ->andWhere('pp.student = :student')
            ->setParameter('student', $studentId)
            ->orderBy('pp.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findActiveByStudent(int $studentId): ?PaymentPlan
    {
        return $this->createQueryBuilder('pp')
            ->andWhere('pp.student = :student')
            ->andWhere('pp.status = :status')
            ->setParameter('student', $studentId)
            ->setParameter('status', PaymentPlan::STATUS_ACTIVE)
            ->orderBy('pp.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
