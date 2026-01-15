<?php

namespace App\Controller;

use App\Entity\Attendance;
use App\Entity\SubjectAssignment;
use App\Entity\Student;
use App\Repository\AttendanceRepository;
use App\Repository\SubjectAssignmentRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/attendance')]
class AttendanceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AttendanceRepository $attendanceRepository,
        private SubjectAssignmentRepository $subjectAssignmentRepository,
        private StudentRepository $studentRepository
    ) {}

    #[Route('/by-schedule/{scheduleId}', methods: ['GET'])]
    public function getBySchedule(int $scheduleId, Request $request): JsonResponse
    {
        $dateStr = $request->query->get('date');
        if (!$dateStr) {
            return $this->json(['error' => 'Date parameter is required'], 400);
        }

        try {
            $date = new \DateTime($dateStr);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid date format'], 400);
        }

        $schedule = $this->subjectAssignmentRepository->find($scheduleId);
        if (!$schedule) {
            return $this->json(['error' => 'Schedule not found'], 404);
        }

        $attendances = $this->attendanceRepository->findBy([
            'subjectAssignment' => $schedule,
            'date' => $date
        ]);

        return $this->json($attendances, 200, [], ['groups' => ['attendance:read']]);
    }

    #[Route('/batch', methods: ['POST'])]
    public function saveBatch(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $scheduleId = $data['scheduleId'] ?? null;
        $dateStr = $data['date'] ?? null;
        $items = $data['attendances'] ?? [];

        if (!$scheduleId || !$dateStr) {
            return $this->json(['error' => 'Missing scheduleId or date'], 400);
        }

        $schedule = $this->subjectAssignmentRepository->find($scheduleId);
        if (!$schedule) {
            return $this->json(['error' => 'Schedule not found'], 404);
        }

        $date = new \DateTime($dateStr);

        foreach ($items as $item) {
            $studentId = $item['studentId'];
            $status = $item['status'];
            $notes = $item['notes'] ?? null;

            $student = $this->studentRepository->find($studentId);
            if (!$student) continue;

            // Check if exists
            $attendance = $this->attendanceRepository->findOneBy([
                'subjectAssignment' => $schedule,
                'student' => $student,
                'date' => $date
            ]);

            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->setSubjectAssignment($schedule);
                $attendance->setStudent($student);
                $attendance->setDate($date);
            }

            $attendance->setStatus($status);
            $attendance->setNotes($notes);
            
            $this->entityManager->persist($attendance);
        }

        $this->entityManager->flush();

        return $this->json(['success' => true, 'message' => 'Attendance saved successfully']);
    }

    #[Route('/report/{studentId}/bimester/{bimesterId}', methods: ['GET'])]
    public function getStudentReport(int $studentId, int $bimesterId): JsonResponse
    {
        // Placeholder implementation - requires Bimester logic
        return $this->json(['message' => 'Report not implemented yet but endpoint exists'], 200);
    }
    
    #[Route('/teacher-report/{teacherId}/bimester/{bimesterId}', methods: ['GET'])]
    public function getTeacherReport(int $teacherId, int $bimesterId): JsonResponse
    {
         // Placeholder
         return $this->json(['message' => 'Report not implemented yet but endpoint exists'], 200);
    }
}
