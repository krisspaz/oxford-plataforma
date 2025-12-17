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
        private ScheduleRepository $scheduleRepository
    ) {}

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
     * Get schedule by grade/section
     */
    #[Route('/grade/{gradeId}', name: 'schedule_by_grade', methods: ['GET'])]
    public function getByGrade(int $gradeId, Request $request): JsonResponse
    {
        $sectionId = $request->query->get('sectionId');
        $cycleId = $request->query->get('cycleId');

        $schedules = $this->scheduleRepository->findByGradeSection(
            $gradeId, 
            $sectionId ? (int)$sectionId : null, 
            $cycleId ? (int)$cycleId : null
        );

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
        $schedule->setGrade($this->em->getReference('App\Entity\Grade', $data['gradeId']));
        
        if (isset($data['sectionId'])) {
            $schedule->setSection($this->em->getReference('App\Entity\Section', $data['sectionId']));
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

    /**
     * Delete schedule entry
     */
    #[Route('/{id}', name: 'schedule_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $schedule = $this->scheduleRepository->find($id);
        if (!$schedule) {
            return $this->json(['error' => 'Schedule not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($schedule);
        $this->em->flush();

        return $this->json(['success' => true, 'message' => 'Horario eliminado']);
    }

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
            ] : null,
            'grade' => $s->getGrade() ? [
                'id' => $s->getGrade()->getId(),
                'name' => $s->getGrade()->getName(),
            ] : null,
            'section' => $s->getSection() ? [
                'id' => $s->getSection()->getId(),
                'name' => $s->getSection()->getName(),
            ] : null,
            'teacher' => $s->getTeacher() ? [
                'id' => $s->getTeacher()->getId(),
                'name' => $s->getTeacher()->getFullName(),
            ] : null,
        ];
    }
}
