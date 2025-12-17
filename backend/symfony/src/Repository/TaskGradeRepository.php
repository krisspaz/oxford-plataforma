<?php

namespace App\Repository;

use App\Entity\TaskGrade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskGrade>
 */
class TaskGradeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskGrade::class);
    }

    public function findByTask(int $taskId): array
    {
        return $this->createQueryBuilder('tg')
            ->andWhere('tg.task = :taskId')
            ->setParameter('taskId', $taskId)
            ->leftJoin('tg.grade', 'g')
            ->leftJoin('tg.section', 's')
            ->addSelect('g', 's')
            ->getQuery()
            ->getResult();
    }

    public function findByGrade(int $gradeId, ?int $sectionId = null): array
    {
        $qb = $this->createQueryBuilder('tg')
            ->andWhere('tg.grade = :gradeId')
            ->setParameter('gradeId', $gradeId)
            ->leftJoin('tg.task', 't')
            ->addSelect('t');

        if ($sectionId) {
            $qb->andWhere('tg.section = :sectionId')
               ->setParameter('sectionId', $sectionId);
        }

        return $qb->getQuery()->getResult();
    }
}
