<?php

namespace App\Controller;

use App\Entity\Attendance;
use App\Entity\Schedule;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Bimester;
use App\Repository\AttendanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/attendance')]
class AttendanceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private AttendanceRepository $attendanceRepository
    ) {}

    /**
     * Get attendance for a specific class/date
     */
    #[Route('/by-schedule/{scheduleId}', name: 'attendance_by_schedule', methods: ['GET'])]
    public function getBySchedule(int $scheduleId, Request $request): JsonResponse
    {
        $date = $request->query->get('date', date('Y-m-d'));

        $schedule = $this->em->getRepository(Schedule::class)->find($scheduleId);
        if (!$schedule) {
            return $this->json(['error' => 'Schedule not found'], Response::HTTP_NOT_FOUND);
        }

        $attendances = $this->attendanceRepository->createQueryBuilder('a')
            ->andWhere('a.schedule = :schedule')
            ->andWhere('a.date = :date')
            ->setParameter('schedule', $schedule)
            ->setParameter('date', new \DateTime($date))
            ->leftJoin('a.student', 's')
            ->addSelect('s')
            ->getQuery()
            ->getResult();

        $data = array_map(fn($a) => [
            'id' => $a->getId(),
            'studentId' => $a->getStudent()->getId(),
            'studentName' => $a->getStudent()->getFullName(),
            'studentCode' => $a->getStudent()->getStudentCode(),
            'status' => $a->getStatus(),
            'notes' => $a->getNotes(),
        ], $attendances);

        return $this->json($data);
    }

    /**
     * Save attendance for multiple students at once
     */
    #[Route('/batch', name: 'attendance_save_batch', methods: ['POST'])]
    public function saveBatch(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['scheduleId'], $data['date'], $data['attendances'])) {
            return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $schedule = $this->em->getRepository(Schedule::class)->find($data['scheduleId']);
        if (!$schedule) {
            return $this->json(['error' => 'Schedule not found'], Response::HTTP_NOT_FOUND);
        }

        $date = new \DateTime($data['date']);
        $teacher = $schedule->getTeacher();
        $subject = $schedule->getSubject();
        
        // Find current bimester
        $bimester = $this->em->getRepository(Bimester::class)->createQueryBuilder('b')
            ->andWhere(':date BETWEEN b.startDate AND b.endDate')
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();

        $saved = 0;
        foreach ($data['attendances'] as $item) {
            $student = $this->em->getRepository(Student::class)->find($item['studentId']);
            if (!$student) continue;

            // Check if attendance already exists
            $existing = $this->attendanceRepository->findOneBy([
                'student' => $student,
                'schedule' => $schedule,
                'date' => $date
            ]);

            if ($existing) {
                $existing->setStatus($item['status']);
                $existing->setNotes($item['notes'] ?? null);
            } else {
                $attendance = new Attendance();
                $attendance->setStudent($student);
                $attendance->setSchedule($schedule);
                $attendance->setSubject($subject);
                $attendance->setTeacher($teacher);
                $attendance->setBimester($bimester);
                $attendance->setDate($date);
                $attendance->setStatus($item['status']);
                $attendance->setNotes($item['notes'] ?? null);
                $this->em->persist($attendance);
            }
            $saved++;
        }

        $this->em->flush();

        return $this->json([
            'success' => true,
            'saved' => $saved,
            'message' => "Asistencia guardada para $saved estudiantes"
        ]);
    }

    /**
     * Get bimester attendance report for a student
     */
    #[Route('/report/{studentId}/bimester/{bimesterId}', name: 'attendance_report_student', methods: ['GET'])]
    public function getStudentReport(int $studentId, int $bimesterId): JsonResponse
    {
        $student = $this->em->getRepository(Student::class)->find($studentId);
        $bimester = $this->em->getRepository(Bimester::class)->find($bimesterId);

        if (!$student || !$bimester) {
            return $this->json(['error' => 'Student or Bimester not found'], Response::HTTP_NOT_FOUND);
        }

        $attendances = $this->attendanceRepository->createQueryBuilder('a')
            ->andWhere('a.student = :student')
            ->andWhere('a.bimester = :bimester')
            ->setParameter('student', $student)
            ->setParameter('bimester', $bimester)
            ->getQuery()
            ->getResult();

        $present = count(array_filter($attendances, fn($a) => $a->getStatus() === Attendance::STATUS_PRESENT));
        $absent = count(array_filter($attendances, fn($a) => $a->getStatus() === Attendance::STATUS_ABSENT));
        $late = count(array_filter($attendances, fn($a) => $a->getStatus() === Attendance::STATUS_LATE));
        $excused = count(array_filter($attendances, fn($a) => $a->getStatus() === Attendance::STATUS_EXCUSED));
        $total = count($attendances);

        return $this->json([
            'studentId' => $studentId,
            'studentName' => $student->getFullName(),
            'bimester' => $bimester->getName(),
            'stats' => [
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'excused' => $excused,
                'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ],
        ]);
    }

    /**
     * Get attendance report for a teacher's students in a bimester
     */
    #[Route('/teacher-report/{teacherId}/bimester/{bimesterId}', name: 'attendance_teacher_report', methods: ['GET'])]
    public function getTeacherReport(int $teacherId, int $bimesterId): JsonResponse
    {
        $teacher = $this->em->getRepository(Teacher::class)->find($teacherId);
        $bimester = $this->em->getRepository(Bimester::class)->find($bimesterId);

        if (!$teacher || !$bimester) {
            return $this->json(['error' => 'Teacher or Bimester not found'], Response::HTTP_NOT_FOUND);
        }

        $attendances = $this->attendanceRepository->createQueryBuilder('a')
            ->andWhere('a.teacher = :teacher')
            ->andWhere('a.bimester = :bimester')
            ->setParameter('teacher', $teacher)
            ->setParameter('bimester', $bimester)
            ->leftJoin('a.student', 's')
            ->addSelect('s')
            ->getQuery()
            ->getResult();

        // Group by student
        $studentStats = [];
        foreach ($attendances as $a) {
            $sid = $a->getStudent()->getId();
            if (!isset($studentStats[$sid])) {
                $studentStats[$sid] = [
                    'studentId' => $sid,
                    'studentName' => $a->getStudent()->getFullName(),
                    'studentCode' => $a->getStudent()->getStudentCode(),
                    'present' => 0,
                    'absent' => 0,
                    'late' => 0,
                    'excused' => 0,
                    'total' => 0,
                ];
            }
            $studentStats[$sid]['total']++;
            $studentStats[$sid][$a->getStatus()]++;
        }

        // Calculate percentages
        foreach ($studentStats as &$stat) {
            $stat['percentage'] = $stat['total'] > 0 
                ? round(($stat['present'] / $stat['total']) * 100, 1) 
                : 0;
        }

        return $this->json([
            'teacherId' => $teacherId,
            'bimester' => $bimester->getName(),
            'students' => array_values($studentStats),
        ]);
    }
}
