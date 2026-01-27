<?php

namespace App\Repository;

use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Schedule>
 */
class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    public function findByGradeAndSection(int $gradeId, ?int $sectionId = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.grade = :gradeId')
            ->setParameter('gradeId', $gradeId);
        
        if ($sectionId) {
            $qb->andWhere('s.section = :sectionId')
               ->setParameter('sectionId', $sectionId);
        }

        return $qb->orderBy('s.dayOfWeek', 'ASC')
                  ->addOrderBy('s.startTime', 'ASC')
                  ->getQuery()
                  ->getResult();
    }

    public function findByTeacher(int $teacherId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.teacher = :teacherId')
            ->setParameter('teacherId', $teacherId)
            ->orderBy('s.dayOfWeek', 'ASC')
            ->addOrderBy('s.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
