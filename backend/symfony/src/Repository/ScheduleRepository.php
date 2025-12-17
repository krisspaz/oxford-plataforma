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

    public function findByTeacher(int $teacherId, ?int $cycleId = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.teacher = :teacherId')
            ->setParameter('teacherId', $teacherId)
            ->andWhere('s.isActive = true')
            ->leftJoin('s.subject', 'sub')
            ->leftJoin('s.grade', 'g')
            ->leftJoin('s.section', 'sec')
            ->addSelect('sub', 'g', 'sec')
            ->orderBy('s.dayOfWeek', 'ASC')
            ->addOrderBy('s.period', 'ASC');

        if ($cycleId) {
            $qb->andWhere('s.schoolCycle = :cycleId')
               ->setParameter('cycleId', $cycleId);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByTeacherAndDay(int $teacherId, int $dayOfWeek, ?int $cycleId = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.teacher = :teacherId')
            ->andWhere('s.dayOfWeek = :day')
            ->setParameter('teacherId', $teacherId)
            ->setParameter('day', $dayOfWeek)
            ->andWhere('s.isActive = true')
            ->leftJoin('s.subject', 'sub')
            ->leftJoin('s.grade', 'g')
            ->leftJoin('s.section', 'sec')
            ->addSelect('sub', 'g', 'sec')
            ->orderBy('s.period', 'ASC');

        if ($cycleId) {
            $qb->andWhere('s.schoolCycle = :cycleId')
               ->setParameter('cycleId', $cycleId);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByGradeSection(int $gradeId, ?int $sectionId = null, ?int $cycleId = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.grade = :gradeId')
            ->setParameter('gradeId', $gradeId)
            ->andWhere('s.isActive = true')
            ->leftJoin('s.teacher', 't')
            ->leftJoin('s.subject', 'sub')
            ->addSelect('t', 'sub')
            ->orderBy('s.dayOfWeek', 'ASC')
            ->addOrderBy('s.period', 'ASC');

        if ($sectionId) {
            $qb->andWhere('s.section = :sectionId')
               ->setParameter('sectionId', $sectionId);
        }

        if ($cycleId) {
            $qb->andWhere('s.schoolCycle = :cycleId')
               ->setParameter('cycleId', $cycleId);
        }

        return $qb->getQuery()->getResult();
    }

    public function getCurrentClass(int $teacherId): ?Schedule
    {
        $now = new \DateTime();
        $currentDay = (int) $now->format('N'); // 1 = Monday, 7 = Sunday
        
        if ($currentDay > 5) {
            return null; // Weekend
        }

        return $this->createQueryBuilder('s')
            ->andWhere('s.teacher = :teacherId')
            ->andWhere('s.dayOfWeek = :day')
            ->andWhere('s.startTime <= :time')
            ->andWhere('s.endTime > :time')
            ->setParameter('teacherId', $teacherId)
            ->setParameter('day', $currentDay)
            ->setParameter('time', $now->format('H:i:s'))
            ->andWhere('s.isActive = true')
            ->leftJoin('s.subject', 'sub')
            ->leftJoin('s.grade', 'g')
            ->leftJoin('s.section', 'sec')
            ->addSelect('sub', 'g', 'sec')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findWeeklySchedule(int $teacherId, int $cycleId): array
    {
        $schedules = $this->findByTeacher($teacherId, $cycleId);
        
        $weekly = [];
        foreach (Schedule::DAYS as $day => $dayName) {
            $weekly[$day] = [];
        }

        foreach ($schedules as $schedule) {
            $weekly[$schedule->getDayOfWeek()][] = $schedule;
        }

        return $weekly;
    }
}
