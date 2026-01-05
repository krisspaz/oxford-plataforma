<?php

namespace App\Repository;

use App\Entity\TaskSubmission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskSubmission>
 */
class TaskSubmissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskSubmission::class);
    }

    public function findByTask(int $taskId): array
    {
        return $this->createQueryBuilder('ts')
            ->andWhere('ts.task = :taskId')
            ->setParameter('taskId', $taskId)
            ->leftJoin('ts.student', 's')
            ->addSelect('s')
            ->orderBy('ts.submittedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByStudent(int $studentId): array
    {
        return $this->createQueryBuilder('ts')
            ->andWhere('ts.student = :studentId')
            ->setParameter('studentId', $studentId)
            ->leftJoin('ts.task', 't')
            ->addSelect('t')
            ->orderBy('ts.submittedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByTaskAndStudent(int $taskId, int $studentId): ?TaskSubmission
    {
        return $this->createQueryBuilder('ts')
            ->andWhere('ts.task = :taskId')
            ->andWhere('ts.student = :studentId')
            ->setParameter('taskId', $taskId)
            ->setParameter('studentId', $studentId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countByTask(int $taskId): int
    {
        return $this->createQueryBuilder('ts')
            ->select('COUNT(ts.id)')
            ->andWhere('ts.task = :taskId')
            ->setParameter('taskId', $taskId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findPendingGrading(int $teacherId): array
    {
        return $this->createQueryBuilder('ts')
            ->leftJoin('ts.task', 't')
            ->andWhere('t.teacher = :teacherId')
            ->andWhere('ts.status = :status')
            ->setParameter('teacherId', $teacherId)
            ->setParameter('status', TaskSubmission::STATUS_SUBMITTED)
            ->orderBy('ts.submittedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
