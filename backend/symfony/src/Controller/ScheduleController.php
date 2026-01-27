<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Entity\Student;
use App\Entity\User;
use App\Repository\ScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/schedule')]
class ScheduleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ScheduleRepository $scheduleRepository
    ) {}

    #[Route('/student/{studentId}', name: 'schedule_student_get', methods: ['GET'])]
    public function getStudentSchedule(int $studentId): JsonResponse
    {
        $student = $this->entityManager->getRepository(Student::class)->find($studentId);
        
        if (!$student) {
            return $this->json(['error' => 'Student not found'], 404);
        }

        // Get schedule based on student's grade and section
        $grade = $student->getGrade();
        $section = $student->getSection();

        if (!$grade) {
            return $this->json([]); // No grade assigned
        }

        $schedules = $this->scheduleRepository->findByGradeAndSection($grade->getId(), $section?->getId());
        
        $data = $this->serializeSchedules($schedules);

        return $this->json($data);
    }

    #[Route('/my-student-schedule', name: 'schedule_my_student', methods: ['GET'])]
    public function getMyStudentSchedule(): JsonResponse
    {
        $user = $this->getUser();
        // In a real scenario, we'd link User -> Student. 
        // For now, if user has mapped student, use it. Otherwise, return empty or sample if needed.
        // Assuming we have a way to get the student ID from the user context:
        // $student = $user->getStudent(); 
        
        // Fallback for demo if no direct link:
        // Try to find a student with same email or just first student if in dev
        // This part depends on your User-Student relation structure.
        
        return $this->json([]); // Implementation Pending proper User-Student link
    }

    #[Route('/my-schedule', name: 'schedule_teacher_my', methods: ['GET'])]
    public function getMySchedule(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // Find teacher profile linked to this user
        // Assuming User has a relation or we find Teacher by User ID
        $teacher = $this->entityManager->getRepository(\App\Entity\Teacher::class)->findOneBy(['user' => $user]);

        if (!$teacher) {
            // Fallback: try to find by email or ID if migration isn't perfect
            // For now, return empty if not found to avoid errors
            return $this->json([]);
        }

        $schedules = $this->scheduleRepository->findByTeacher($teacher->getId());
        
        $data = [];
        foreach ($schedules as $s) {
            $data[] = [
                'dayOfWeek' => $s->getDayOfWeek(),
                'startTime' => $s->getStartTime()->format('H:i'),
                'endTime' => $s->getEndTime()->format('H:i'),
                'subject' => ['name' => $s->getSubject()->getName()],
                'classroom' => $s->getClassroom() ?? 'General',
                'teacher' => ['name' => $teacher->getFullName()], // Self
                'grade' => $s->getGrade()->getName(),
                'section' => $s->getSection()?->getName() ?? ''
            ];
        }

        return $this->json($data);
    }
    
    private function serializeSchedules(array $schedules): array
    {
        $data = [];
        foreach ($schedules as $s) {
            $data[] = [
                'id' => $s->getId(),
                'dayOfWeek' => $s->getDayOfWeek(),
                'startTime' => $s->getStartTime()->format('H:i'),
                'endTime' => $s->getEndTime()->format('H:i'),
                'subject' => ['name' => $s->getSubject()->getName()],
                'classroom' => $s->getClassroom() ?? 'General',
                'teacher' => ['name' => $s->getTeacher()->getFullName()],
                'grade' => $s->getGrade()->getName()
            ];
        }
        return $data;
    }

    #[Route('', name: 'schedule_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $schedule = new Schedule();
        $schedule->setDayOfWeek($data['dayOfWeek']);
        $schedule->setStartTime(new \DateTime($data['startTime']));
        $schedule->setEndTime(new \DateTime($data['endTime']));
        $schedule->setClassroom($data['classroom'] ?? null);
        
        // Relations
        $subject = $this->entityManager->getRepository(\App\Entity\Subject::class)->find($data['subjectId']);
        $teacher = $this->entityManager->getRepository(\App\Entity\Teacher::class)->find($data['teacherId']);
        $grade = $this->entityManager->getRepository(\App\Entity\Grade::class)->find($data['gradeId']);
        
        if (!$subject || !$teacher || !$grade) {
            return $this->json(['error' => 'Invalid relations'], 400);
        }
        
        $schedule->setSubject($subject);
        $schedule->setTeacher($teacher);
        $schedule->setGrade($grade);
        
        if (isset($data['sectionId'])) {
             $section = $this->entityManager->getRepository(\App\Entity\Section::class)->find($data['sectionId']);
             $schedule->setSection($section);
        }

        $this->entityManager->persist($schedule);
        $this->entityManager->flush();

        return $this->json(['message' => 'Schedule created', 'id' => $schedule->getId()], 201);
    }
}

