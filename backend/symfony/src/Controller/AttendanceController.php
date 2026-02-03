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
        $student = $this->studentRepository->find($studentId);
        if (!$student) {
            return $this->json(['error' => 'Student not found'], 404);
        }

        $attendances = $this->attendanceRepository->createQueryBuilder('a')
            ->join('a.subjectAssignment', 'sa')
            ->where('a.student = :student')
            ->andWhere('sa.schoolCycle = :cycle') // Simplification: assuming current cycle or fetch bimester's cycle
            ->setParameter('student', $student)
            ->setParameter('cycle', $student->getSchoolCycle())
            ->getQuery()
            ->getResult();

        $summary = [
            'PRESENT' => 0,
            'ABSENT' => 0,
            'LATE' => 0,
            'JUSTIFIED' => 0,
        ];

        foreach ($attendances as $a) {
            $status = $a->getStatus();
            if (isset($summary[$status])) {
                $summary[$status]++;
            }
        }

        $total = array_sum($summary);
        $attendancePct = $total > 0 ? round(($summary['PRESENT'] / $total) * 100, 2) : 100;

        return $this->json([
            'student' => $student->getFullName(),
            'summary' => $summary,
            'attendancePct' => $attendancePct,
            'totalClasses' => $total
        ]);
    }
    
    #[Route('/teacher-report/{teacherId}/bimester/{bimesterId}', methods: ['GET'])]
    public function getTeacherReport(int $teacherId, int $bimesterId): JsonResponse
    {
        $attendances = $this->attendanceRepository->createQueryBuilder('a')
            ->join('a.subjectAssignment', 'sa')
            ->where('sa.teacher = :teacherId')
            ->setParameter('teacherId', $teacherId)
            ->getQuery()
            ->getResult();

        $stats = [];
        foreach ($attendances as $a) {
            $subId = $a->getSubjectAssignment()->getId();
            $subName = $a->getSubjectAssignment()->getSubject()->getName();
            
            if (!isset($stats[$subId])) {
                $stats[$subId] = ['name' => $subName, 'present' => 0, 'absent' => 0, 'total' => 0];
            }
            
            $stats[$subId]['total']++;
            if ($a->getStatus() === 'PRESENT') $stats[$subId]['present']++;
            else $stats[$subId]['absent']++;
        }

        return $this->json(array_values($stats));
    }
}
