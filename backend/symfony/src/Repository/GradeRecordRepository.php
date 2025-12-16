<?php

namespace App\Repository;

use App\Entity\GradeRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GradeRecord>
 */
class GradeRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradeRecord::class);
    }

    public function findByAssignmentAndBimester(int $assignmentId, int $bimesterId): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.subjectAssignment = :assignment')
            ->andWhere('g.bimester = :bimester')
            ->setParameter('assignment', $assignmentId)
            ->setParameter('bimester', $bimesterId)
            ->getQuery()
            ->getResult();
    }

    public function findByStudent(int $studentId): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.student = :student')
            ->setParameter('student', $studentId)
            ->orderBy('g.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getAverageByStudentAndBimester(int $studentId, int $bimesterId): ?float
    {
        $result = $this->createQueryBuilder('g')
            ->select('AVG(g.score) as avg')
            ->andWhere('g.student = :student')
            ->andWhere('g.bimester = :bimester')
            ->andWhere('g.score IS NOT NULL')
            ->setParameter('student', $studentId)
            ->setParameter('bimester', $bimesterId)
            ->getQuery()
            ->getSingleScalarResult();
        
        return $result ? (float) $result : null;
    }
}
