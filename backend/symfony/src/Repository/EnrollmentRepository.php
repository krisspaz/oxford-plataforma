<?php

namespace App\Repository;

use App\Entity\Enrollment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Enrollment>
 */
class EnrollmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enrollment::class);
    }

    public function findByStudentAndCycle(int $studentId, int $cycleId): ?Enrollment
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.student = :student')
            ->andWhere('e.schoolCycle = :cycle')
            ->setParameter('student', $studentId)
            ->setParameter('cycle', $cycleId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findActiveByStudent(int $studentId): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.student = :student')
            ->andWhere('e.status IN (:statuses)')
            ->setParameter('student', $studentId)
            ->setParameter('statuses', ['INSCRITO', 'MATRICULADO', 'ACTIVO'])
            ->orderBy('e.enrollmentDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
