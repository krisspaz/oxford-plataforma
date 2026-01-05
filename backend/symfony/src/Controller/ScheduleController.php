<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Entity\Teacher;
use App\Entity\User;
use App\Repository\ScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/schedule')]
class ScheduleController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
        private ScheduleRepository $scheduleRepository,
        private \App\Service\ScheduleGeneratorService $generatorService,
        private \App\Service\AiService $aiService
    ) {}

    /**
     * Generate automatic schedules for a cycle
     */
    #[Route('/generate', name: 'schedule_generate_auto', methods: ['POST'])]
    public function generateSchedules(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $cycleId = $data['cycleId'] ?? null;

        if (!$cycleId) {
            return $this->json(['error' => 'cycleId is required'], Response::HTTP_BAD_REQUEST);
        }

        $cycle = $this->em->getRepository(\App\Entity\SchoolCycle::class)->find($cycleId);
        if (!$cycle) {
            return $this->json(['error' => 'SchoolCycle not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $result = $this->generatorService->generateSchedules($cycle);
            return $this->json([
                'success' => true,
                'message' => "Horarios generados: {$result['generated']}. Conflictos: {$result['conflicts']}.",
                'details' => $result
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get the current student's schedule
     */
    #[Route('/my-student-schedule', name: 'schedule_my_student_schedule', methods: ['GET'])]
    public function getMyStudentSchedule(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        
        // Find student linked to this user
        $student = $this->em->getRepository(\App\Entity\Student::class)->findOneBy(['email' => $user->getEmail()]);
        
        if (!$student) {
            return $this->json(['error' => 'Student profile not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->getStudentSchedule($student->getId(), $request);
    }

    /**
     * Get schedule for the current logged-in teacher
     */
    #[Route('/my-schedule', name: 'schedule_my_schedule', methods: ['GET'])]
    public function getMySchedule(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        
        // Find teacher linked to this user
        $teacher = $this->em->getRepository(Teacher::class)->findOneBy(['email' => $user->getEmail()]);
        
        if (!$teacher) {
            return $this->json(['error' => 'Teacher profile not found'], Response::HTTP_NOT_FOUND);
        }

        $cycleId = $request->query->get('cycleId');
        $schedules = $this->scheduleRepository->findByTeacher($teacher->getId(), $cycleId);

        $data = array_map(fn($s) => $this->serializeSchedule($s), $schedules);

        return $this->json($data);
    }

    /**
     * Get schedule by teacher ID
     */
    #[Route('/teacher/{teacherId}', name: 'schedule_by_teacher', methods: ['GET'])]
    public function getByTeacher(int $teacherId, Request $request): JsonResponse
    {
        $cycleId = $request->query->get('cycleId');
        $schedules = $this->scheduleRepository->findByTeacher($teacherId, $cycleId);

        return $this->json(array_map(fn($s) => $this->serializeSchedule($s), $schedules));
    }

    /**
     * Get schedule for a specific day
     */
    #[Route('/teacher/{teacherId}/day/{day}', name: 'schedule_by_day', methods: ['GET'])]
    public function getByDay(int $teacherId, int $day, Request $request): JsonResponse
    {
        $cycleId = $request->query->get('cycleId');
        $schedules = $this->scheduleRepository->findByTeacherAndDay($teacherId, $day, $cycleId);

        return $this->json(array_map(fn($s) => $this->serializeSchedule($s), $schedules));
    }

    /**
     * Get current class for a teacher
     */
    #[Route('/current-class/{teacherId}', name: 'schedule_current_class', methods: ['GET'])]
    public function getCurrentClass(int $teacherId): JsonResponse
    {
        $schedule = $this->scheduleRepository->getCurrentClass($teacherId);

        if (!$schedule) {
            return $this->json(['message' => 'No class at this time'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serializeSchedule($schedule));
    }

    /**
     * Get weekly schedule organized by day
     */
    #[Route('/weekly/{teacherId}', name: 'schedule_weekly', methods: ['GET'])]
    public function getWeeklySchedule(int $teacherId, Request $request): JsonResponse
    {
        $cycleId = $request->query->get('cycleId');
        
        if (!$cycleId) {
            return $this->json(['error' => 'cycleId is required'], Response::HTTP_BAD_REQUEST);
        }

        $weekly = $this->scheduleRepository->findWeeklySchedule($teacherId, (int)$cycleId);

        $result = [];
        foreach (Schedule::DAYS as $day => $dayName) {
            $result[$dayName] = array_map(fn($s) => $this->serializeSchedule($s), $weekly[$day] ?? []);
        }

        return $this->json($result);
    }

    /**
     * Get schedule by course ID
     */
    #[Route('/course/{courseId}', name: 'schedule_by_course', methods: ['GET'])]
    public function getByCourse(int $courseId, Request $request): JsonResponse
    {
        $cycleId = $request->query->get('cycleId');

        $schedules = $this->scheduleRepository->findByCourse($courseId, $cycleId);

        return $this->json(array_map(fn($s) => $this->serializeSchedule($s), $schedules));
    }

    /**
     * Create or update schedule entries (for admin/direccion)
     */
    #[Route('', name: 'schedule_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $teacher = $this->em->getRepository(Teacher::class)->find($data['teacherId']);
        if (!$teacher) {
            return $this->json(['error' => 'Teacher not found'], Response::HTTP_NOT_FOUND);
        }

        $schedule = new Schedule();
        $schedule->setTeacher($teacher);
        $schedule->setSubject($this->em->getReference('App\Entity\Subject', $data['subjectId']));
        
        if (isset($data['courseId'])) {
             $schedule->setCourse($this->em->getReference('App\Entity\Course', $data['courseId']));
        } else {
             return $this->json(['error' => 'Course ID is required'], Response::HTTP_BAD_REQUEST);
        }
        
        $schedule->setSchoolCycle($this->em->getReference('App\Entity\SchoolCycle', $data['cycleId']));
        $schedule->setDayOfWeek($data['dayOfWeek']);
        $schedule->setPeriod($data['period']);
        $schedule->setStartTime(new \DateTime($data['startTime']));
        $schedule->setEndTime(new \DateTime($data['endTime']));
        $schedule->setClassroom($data['classroom'] ?? null);

        $this->em->persist($schedule);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'id' => $schedule->getId(),
            'message' => 'Horario creado correctamente'
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'schedule_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $schedule = $this->scheduleRepository->find($id);
        if (!$schedule) {
            return $this->json(['error' => 'Schedule not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        // --- AI LEARNING LOGIC ---
        // Detect if day changed
        $oldDayName = $schedule->getDayName(); // e.g., 'Monday'
        $newDay = $data['dayOfWeek'] ?? null;
        
        if ($newDay && $newDay !== $schedule->getDayOfWeek()) {
            // Day changed!
            $teacher = $schedule->getTeacher();
            if ($teacher) {
                // Map int day to name for AI
                $daysMap = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles', 4 => 'Jueves', 5 => 'Viernes'];
                $newDayName = $daysMap[$newDay] ?? 'Unknown';
                
                // Report to AI
                $this->aiService->reportScheduleChange([
                    'teacher_id' => $teacher->getId(),
                    'day_from' => $oldDayName,
                    'day_to' => $newDayName,
                    'reason' => 'manual_update'
                ]);
            }
        }
        // -------------------------

        if (isset($data['dayOfWeek'])) $schedule->setDayOfWeek($data['dayOfWeek']);
        if (isset($data['period'])) $schedule->setPeriod($data['period']);
        if (isset($data['startTime'])) $schedule->setStartTime(new \DateTime($data['startTime']));
        if (isset($data['endTime'])) $schedule->setEndTime(new \DateTime($data['endTime']));
        if (isset($data['classroom'])) $schedule->setClassroom($data['classroom']);

        $this->em->flush();

        return $this->json([
            'success' => true, 
            'message' => 'Horario actualizado',
            'ai_learning' => 'Change analyzed'
        ]);
    }

    // ... delete method ...

    // ... student methods ...

    /**
     * Get schedule for a student (based on enrollment)
     */
    #[Route('/student/{studentId}', name: 'schedule_student', methods: ['GET'])]
    public function getStudentSchedule(int $studentId, Request $request): JsonResponse
    {
        $cycleId = $request->query->get('cycleId');
        
        // Find active enrollment
        $qb = $this->em->createQueryBuilder();
        $qb->select('e')
           ->from('App\Entity\Enrollment', 'e')
           ->where('e.student = :studentId')
           ->orderBy('e.id', 'DESC') // Get latest
           ->setMaxResults(1)
           ->setParameter('studentId', $studentId);
           
        if ($cycleId) {
             $qb->andWhere('e.schoolCycle = :cycleId')
                ->setParameter('cycleId', $cycleId);
        }
           
        $enrollment = $qb->getQuery()->getOneOrNullResult();
        
        if (!$enrollment) {
            return $this->json(['error' => 'Student not enrolled'], Response::HTTP_NOT_FOUND);
        }
        
        $course = $enrollment->getCourse();
        
        if (!$course) {
             return $this->json(['error' => 'Enrollment has no course assigned'], Response::HTTP_BAD_REQUEST);
        }

        $schedules = $this->scheduleRepository->findByCourse(
            $course->getId(), 
            $cycleId ? (int)$cycleId : null
        );

        return $this->json(array_map(fn($s) => $this->serializeSchedule($s), $schedules));
    }

    // ... getMyStudentSchedule ...

    private function serializeSchedule(Schedule $s): array
    {
        return [
            'id' => $s->getId(),
            'dayOfWeek' => $s->getDayOfWeek(),
            'dayName' => $s->getDayName(),
            'period' => $s->getPeriod(),
            'startTime' => $s->getStartTime()?->format('H:i'),
            'endTime' => $s->getEndTime()?->format('H:i'),
            'timeRange' => $s->getTimeRange(),
            'classroom' => $s->getClassroom(),
            'subject' => $s->getSubject() ? [
                'id' => $s->getSubject()->getId(),
                'name' => $s->getSubject()->getName(),
                'code' => $s->getSubject()->getCode(),
            ] : null,
            'course' => $s->getCourse() ? [
                'id' => $s->getCourse()->getId(),
                'name' => $s->getCourse()->getName(),
                'gradeLevel' => $s->getCourse()->getGradeLevel(),
                'section' => $s->getCourse()->getSection(),
            ] : null,
            'teacher' => $s->getTeacher() ? [
                'id' => $s->getTeacher()->getId(),
                'name' => $s->getTeacher()->getFullName(),
            ] : null,
        ];
    }
}
