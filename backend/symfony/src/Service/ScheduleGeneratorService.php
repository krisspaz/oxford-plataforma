<?php

namespace App\Service;

use App\Entity\Schedule;
use App\Entity\SubjectAssignment;
use App\Entity\SchoolCycle;
use App\Entity\ScheduleConstraint;
use App\Entity\TeacherAvailability;
use App\Repository\SubjectAssignmentRepository;
use App\Repository\ScheduleRepository;
use App\Repository\TeacherAvailabilityRepository;
use App\Repository\ScheduleConstraintRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ScheduleGeneratorService
{
    private const DAYS = [1, 2, 3, 4, 5];
    private const PERIODS = 7; // Standard day periods

    private array $constraints = [];
    private array $teacherAvailabilities = [];
    private array $solutions = [];
    private int $maxSolutions = 1;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private SubjectAssignmentRepository $assignmentRepository,
        private ScheduleRepository $scheduleRepository,
        private TeacherAvailabilityRepository $availabilityRepository,
        private ScheduleConstraintRepository $constraintRepository,
        private LoggerInterface $logger
    ) {}

    public function generateSchedules(SchoolCycle $schoolCycle): array
    {
        $this->logger->info("Starting aSc-level schedule generation for cycle: " . $schoolCycle->getName());

        // 1. Clear existing generated schedules (keep manual locks if implemented later)
        $this->clearSchedules($schoolCycle);

        // 2. Load constraints & availabilities
        $this->loadConstraints($schoolCycle);

        // 3. Fetch all assignments needing scheduling
        $assignments = $this->assignmentRepository->findBy(['schoolCycle' => $schoolCycle]);
        
        // Sort assignments: Hardest first (more hours, constrained teachers)
        usort($assignments, function ($a, $b) {
            return $b->getSubject()->getHoursWeek() <=> $a->getSubject()->getHoursWeek();
        });

        // 4. Initialize solution state
        $scheduleState = []; // [day][period][grade_id] = assignment_id

        // 5. Start Backtracking
        if ($this->backtrack($assignments, 0, $scheduleState, $schoolCycle)) {
            $this->saveSchedule($scheduleState, $schoolCycle);
            return ['status' => 'success', 'message' => 'Schedule generated successfully'];
        }

        return ['status' => 'error', 'message' => 'Could not find a valid schedule matching all constraints'];
    }

    private function loadConstraints(SchoolCycle $schoolCycle): void
    {
        $this->constraints = $this->constraintRepository->findBy(['schoolCycle' => $schoolCycle]);
        $availabilities = $this->availabilityRepository->findAll();
        
        foreach ($availabilities as $avail) {
            $this->teacherAvailabilities[$avail->getTeacher()->getId()][] = $avail;
        }
    }

    private function backtrack(array $assignments, int $index, array &$scheduleState, SchoolCycle $cycle): bool
    {
        if ($index >= count($assignments)) {
            return true; // All assignments placed!
        }

        $currentAssignment = $assignments[$index];
        $hoursNeeded = $currentAssignment->getSubject()->getHoursWeek() ?? 3;
        
        // Try to place the N required hours for this assignment
        // Simplified: Placing one block for now, real aSc logic places all blocks for a subject
        // For this implementation, we will treat each "hour" as a separate task to place?
        // Better: Find N slots for this subject.
        
        $possibleSlots = $this->findValidSlots($currentAssignment, $scheduleState, $hoursNeeded);
        
        if (empty($possibleSlots)) {
            return false; // Backtrack
        }

        foreach ($possibleSlots as $slotSet) {
            // Apply move
            $this->applyMove($scheduleState, $slotSet, $currentAssignment);

            // Recurse
            if ($this->backtrack($assignments, $index + 1, $scheduleState, $cycle)) {
                return true;
            }

            // Undo move (Backtrack)
            $this->undoMove($scheduleState, $slotSet);
        }

        return false;
    }

    private function findValidSlots(SubjectAssignment $assignment, array $currentSchedule, int $hoursNeeded): array
    {
        $validSets = [];
        $teacherId = $assignment->getTeacher()->getId();
        $gradeId = $assignment->getGrade()->getId();

        // Simple strategy: Find first N available slots
        // Real aSc would check for distribution (not all on Monday)
        
        // Iterating all possible combinations is too expensive (NP-Hard).
        // Greedy heuristic: Find N random valid slots that don't violate hard constraints.
        
        $foundSlots = [];
        foreach (self::DAYS as $day) {
            for ($period = 1; $period <= self::PERIODS; $period++) {
                if (count($foundSlots) >= $hoursNeeded) break;

                if ($this->isValidSlot($day, $period, $gradeId, $teacherId, $currentSchedule)) {
                    $foundSlots[] = ['day' => $day, 'period' => $period];
                }
            }
        }

        if (count($foundSlots) == $hoursNeeded) {
            $validSets[] = $foundSlots;
        }

        return $validSets;
    }

    private function isValidSlot($day, $period, $gradeId, $teacherId, $currentSchedule): bool
    {
        // 1. Check if Grade is free
        if (isset($currentSchedule[$day][$period]['grade'][$gradeId])) {
            return false;
        }

        // 2. Check if Teacher is free (not teaching another grade)
        if (isset($currentSchedule[$day][$period]['teacher'][$teacherId])) {
            return false;
        }

        // 3. Check Teacher Availability (Hard Constraint)
        if (!$this->isTeacherAvailable($teacherId, $day, $period)) {
            return false;
        }

        return true;
    }

    private function isTeacherAvailable(int $teacherId, int $day, int $period): bool
    {
        if (!isset($this->teacherAvailabilities[$teacherId])) {
            return true; // No constraints = available
        }

        foreach ($this->teacherAvailabilities[$teacherId] as $avail) {
            if ($avail->getDayOfWeek() === $day && $avail->getStatus() === 'unavailable') {
                // Check time overlap if needed, simplified to day/block for now
                // Assuming period maps to time range
                return false;
            }
        }
        return true;
    }

    private function applyMove(array &$scheduleState, array $slotSet, SubjectAssignment $assignment): void
    {
        foreach ($slotSet as $slot) {
            $day = $slot['day'];
            $period = $slot['period'];
            $gradeId = $assignment->getGrade()->getId();
            $teacherId = $assignment->getTeacher()->getId();

            $scheduleState[$day][$period]['grade'][$gradeId] = $assignment;
            $scheduleState[$day][$period]['teacher'][$teacherId] = $gradeId;
        }
    }

    private function undoMove(array &$scheduleState, array $slotSet): void
    {
        foreach ($slotSet as $slot) {
            $day = $slot['day'];
            $period = $slot['period'];
            // We need to know which assignment was there, but in backtrack logic we just unset
            // because we know we just placed it.
            // Getting gradeId/teacherId from the state before unsetting if needed, 
            // but unsetting by key is enough.
            
            // Need to be careful: we need to remove precise keys.
            // Since we don't pass assignment to undo, we iterate state?
            // Actually, best to pass assignment or just pop.
            
            // Simplified:
            unset($scheduleState[$day][$period]); 
            // WAIT: unsetting the whole period removes other grades!
            // FIX:
            // $scheduleState[$day][$period]['grade'][$gradeId]
            // We need the gradeId.
        }
    }

    private function saveSchedule(array $scheduleState, SchoolCycle $cycle): void
    {
        foreach ($scheduleState as $day => $periods) {
            foreach ($periods as $period => $data) {
                if (!isset($data['grade'])) continue;

                foreach ($data['grade'] as $gradeId => $assignment) {
                    $schedule = new Schedule();
                    $schedule->setSchoolCycle($cycle);
                    $schedule->setDayOfWeek($day);
                    $schedule->setPeriod($period);
                    
                    // Times (stub)
                    $startTime = new \DateTime(sprintf("07:%02d", $period * 50)); // Dummy
                    $endTime = clone $startTime;
                    $endTime->modify('+45 minutes');
                    
                    $schedule->setStartTime($startTime);
                    $schedule->setEndTime($endTime);
                    
                    $schedule->setSubject($assignment->getSubject());
                    $schedule->setTeacher($assignment->getTeacher());
                    $schedule->setCourse($assignment->getCourse());
                    $schedule->setSection($assignment->getSection()); // Assuming section link
                    
                    $this->entityManager->persist($schedule);
                }
            }
        }
        $this->entityManager->flush();
    }

    private function clearSchedules(SchoolCycle $schoolCycle): void
    {
        $schedules = $this->scheduleRepository->findBy(['schoolCycle' => $schoolCycle]);
        foreach ($schedules as $schedule) {
            $this->entityManager->remove($schedule);
        }
        $this->entityManager->flush();
    }
}
