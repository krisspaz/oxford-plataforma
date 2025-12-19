<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findForStudent($grade, $section)
    {
        return $this->createQueryBuilder('t')
            ->join('t.taskGrades', 'tg')
            ->andWhere('tg.grade = :grade')
            ->andWhere('tg.section IS NULL OR tg.section = :section')
            ->andWhere('t.status = :status')
            ->setParameter('grade', $grade)
            ->setParameter('section', $section)
            ->setParameter('status', Task::STATUS_ACTIVE)
            ->orderBy('t.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
