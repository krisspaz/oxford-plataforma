<?php

namespace App\Service;

use App\Entity\Schedule;
use App\Entity\SubjectAssignment;
use App\Entity\SchoolCycle;
use App\Repository\SubjectAssignmentRepository;
use App\Repository\ScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;

class ScheduleGeneratorService
{
    private EntityManagerInterface $entityManager;
    private SubjectAssignmentRepository $assignmentRepository;
    private ScheduleRepository $scheduleRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        SubjectAssignmentRepository $assignmentRepository,
        ScheduleRepository $scheduleRepository
    ) {
        $this->entityManager = $entityManager;
        $this->assignmentRepository = $assignmentRepository;
        $this->scheduleRepository = $scheduleRepository;
    }

    public function generateSchedules(SchoolCycle $schoolCycle): array
    {
        // 1. Clear existing schedules
        $this->clearSchedules($schoolCycle);

        // 2. Fetch active assignments
        $assignments = $this->assignmentRepository->findBy([
            'schoolCycle' => $schoolCycle,
            'isActive' => true
        ]);

        // SORT: Prioritize assignments with MORE hours (harder to fit)
        usort($assignments, function ($a, $b) {
            return $b->getHoursPerWeek() <=> $a->getHoursPerWeek();
        });

        $generatedCount = 0;
        $conflictCount = 0;
        $errors = [];

        // Track occupied slots
        // $occupied[type][id][day][period] = true
        $teacherOccupied = [];
        $groupOccupied = [];

        foreach ($assignments as $assignment) {
            $hoursNeeded = $assignment->getHoursPerWeek();
            $teacher = $assignment->getTeacher();
            $subject = $assignment->getSubject();
            $grade = $assignment->getGrade();
            $section = $assignment->getSection();
            
            if (!$teacher || !$grade) {
                $errors[] = "Assignment ID {$assignment->getId()} missing data.";
                continue;
            }

            $tId = $teacher->getId();
            $gId = $grade->getId();
            $sId = $section ? $section->getId() : '0';

            // Find Course (Group) once
            $course = $this->findCourseForGrade($grade, $section);
            if (!$course) {
                 $errors[] = "No Course found for Grade ID {$gId}";
                 continue; // Cannot schedule without course link
            }

            $hoursAssigned = 0;
            $maxPeriodsPerDay = 2; // Heuristic: Limit daily load per subject

            // Try to schedule all needed hours
            for ($h = 0; $h < $hoursNeeded; $h++) {
                $bestSlot = null;
                $bestScore = -1000;

                // Evaluate ALL valid slots (Days 1-5, Periods 1-7)
                for ($day = 1; $day <= 5; $day++) {
                    // Check max hours per day for this specific subject
                    if ($this->countHoursForSubjectToday($teacherOccupied, $tId, $day, $subject->getId()) >= $maxPeriodsPerDay) {
                         continue;
                    }

                    for ($period = 1; $period <= 7; $period++) {
                        if ($this->isSlotFree($teacherOccupied, $groupOccupied, $tId, $gId, $sId, $day, $period)) {
                            
                            $score = $this->calculateScore($teacherOccupied, $groupOccupied, $tId, $gId, $sId, $day, $period);
                            
                            // Optimization: Add a tiny random factor to break ties and vary schedules slightly
                            // $score += mt_rand(0, 2); 

                            if ($score > $bestScore) {
                                $bestScore = $score;
                                $bestSlot = ['day' => $day, 'period' => $period];
                            }
                        }
                    }
                }

                if ($bestSlot) {
                    // Assign the best slot found
                    $day = $bestSlot['day'];
                    $period = $bestSlot['period'];

                    $schedule = new Schedule();
                    $schedule->setSchoolCycle($schoolCycle);
                    $schedule->setTeacher($teacher);
                    $schedule->setSubject($subject);
                    $schedule->setCourse($course);
                    $schedule->setSection($section);
                    $schedule->setDayOfWeek($day);
                    $schedule->setPeriod($period);
                    
                    $times = $this->getPeriodTimes($period);
                    $schedule->setStartTime($times['start']);
                    $schedule->setEndTime($times['end']);

                    $this->entityManager->persist($schedule);

                    // Mark as occupied
                    $teacherOccupied[$tId][$day][$period] = $subject->getId(); // Store subject ID for specific checks
                    $groupOccupied[$gId][$sId][$day][$period] = true;

                    $hoursAssigned++;
                    $generatedCount++;
                } else {
                    // No valid slot found for this hour
                    break; 
                }
            }

            if ($hoursAssigned < $hoursNeeded) {
                $conflictCount += ($hoursNeeded - $hoursAssigned);
                $errors[] = "Conflict: {$subject->getName()} (Teacher: {$teacher->getName()}) - Missing " . ($hoursNeeded - $hoursAssigned) . " hours.";
            }
        }

        $this->entityManager->flush();

        return [
            'generated' => $generatedCount,
            'conflicts' => $conflictCount,
            'errors' => $errors
        ];
    }

    private function calculateScore($tOcc, $gOcc, $tId, $gId, $sId, $day, $period): int
    {
        $score = 100; // Base score

        // 1. Teacher Continuity (Prefer adjacent to existing classes)
        if (isset($tOcc[$tId][$day][$period - 1])) $score += 20;
        if (isset($tOcc[$tId][$day][$period + 1])) $score += 15; // Future lookahead (less reliable inside loop but helpful)

        // 2. Group Continuity (Students should have blocks)
        if (isset($gOcc[$gId][$sId][$day][$period - 1])) $score += 20;
        if (isset($gOcc[$gId][$sId][$day][$period + 1])) $score += 15;

        // 3. Gap Penalties (Avoid holes like: Class - Empty - Class)
        // Check if Period-2 is occupied but Period-1 is empty (we are at Period-1 candidate, so this actually FILLS a gap)
        // Wait, if we are evaluating Period P.
        // If P-1 is empty and P-2 is occupied, placing at P creates a 1-hour gap? No, that relies on P-1.
        
        // Let's penalize if P-1 is Empty AND P-2 is Occupied -> This placement P is far from P-2.
        // Actually simplest gap check: 
        // If P-1 is empty, but P-2 is occupied -> We are placing at P. We are isolated from P-2. Bad? 
        // Real gap: existing structure X _ X. We want to fill the _. 
        
        // Simple Heuristic: Minimize "Isolated" blocks.
        $hasNeighbor = isset($tOcc[$tId][$day][$period - 1]) || isset($tOcc[$tId][$day][$period + 1]);
        if (!$hasNeighbor) {
            // Check if there are ANY classes today. If yes, and we have no neighbor, we might be creating a gap or stand-alone.
            if (!empty($tOcc[$tId][$day])) {
                $score -= 10; // Penalty for scattering
            }
        }

        // 4. Preference for earlier periods? (Optional)
        // $score -= $period; // Slight penalty for late classes

        return $score;
    }

    private function countHoursForSubjectToday($tOcc, $tId, $day, $subjectId): int
    {
        if (!isset($tOcc[$tId][$day])) return 0;
        $count = 0;
        foreach ($tOcc[$tId][$day] as $p => $sid) {
            if ($sid === $subjectId) $count++;
        }
        return $count;
    }

    private function clearSchedules(SchoolCycle $schoolCycle): void
    {
        $query = $this->entityManager->createQuery(
            'DELETE FROM App\Entity\Schedule s WHERE s.schoolCycle = :cycle'
        );
        $query->setParameter('cycle', $schoolCycle);
        $query->execute();
    }

    private function isSlotFree($teacherOcc, $groupOcc, $tId, $gId, $sId, $day, $period): bool
    {
        if (isset($teacherOcc[$tId][$day][$period])) return false;
        if (isset($groupOcc[$gId][$sId][$day][$period])) return false;
        return true;
    }

    private function getPeriodTimes(int $period): array
    {
        $startHour = 7;
        $startMin = 30;
        $minutesPerPeriod = 45;
        
        $offset = ($period - 1) * $minutesPerPeriod;
        if ($period > 3) $offset += 20; // Recess

        $startTime = (new \DateTime())->setTime($startHour, $startMin)->modify("+$offset minutes");
        $endTime = (clone $startTime)->modify("+$minutesPerPeriod minutes");

        return ['start' => $startTime, 'end' => $endTime];
    }

    private function findCourseForGrade($grade, $section)
    {
        $courses = $this->entityManager->getRepository('App\Entity\Course')->findAll();
        foreach ($courses as $c) {
             if ($section && $c->getSection() === $section->getName()) {
                 return $c;
             }
        }
        return $courses[0] ?? null;
    }
}
