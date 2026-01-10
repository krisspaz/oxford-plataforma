<?php

namespace App\Controller;

use App\Entity\SubjectAssignment;
use App\Repository\GradeRepository;
use App\Repository\SubjectRepository;
use App\Repository\TeacherRepository;
use App\Repository\SchoolCycleRepository;
use App\Repository\SectionRepository;
use App\Repository\SubjectAssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/academic')]
class AcademicController extends AbstractController
{
    #[Route('/assign-subjects', name: 'api_academic_assign_subjects', methods: ['POST'])]
    public function assignSubjects(
        Request $request,
        GradeRepository $gradeRepo,
        SubjectRepository $subjectRepo,
        TeacherRepository $teacherRepo,
        SchoolCycleRepository $cycleRepo,
        SectionRepository $sectionRepo,
        SubjectAssignmentRepository $assignmentRepo,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $gradeId = $data['gradeId'] ?? null;
        $assignments = $data['assignments'] ?? []; // [{subjectId, teacherId, hours}]

        if (!$gradeId || empty($assignments)) {
            return $this->json(['error' => 'Invalid data'], 400);
        }

        $grade = $gradeRepo->find($gradeId);
        if (!$grade) return $this->json(['error' => 'Grade not found'], 404);

        // Find active cycle
        $cycle = $cycleRepo->findOneBy(['isActive' => true]); 
        if (!$cycle) {
            // Fallback to latest if no active
            $cycles = $cycleRepo->findBy([], ['startDate' => 'DESC'], 1);
            $cycle = $cycles[0] ?? null;
        }
        
        if (!$cycle) return $this->json(['error' => 'No active cycle found'], 500);

        foreach ($assignments as $item) {
            $subjectId = $item['subjectId'] ?? null;
            $teacherId = $item['teacherId'] ?? null;
            $hours = $item['hours'] ?? 5;

            if (!$subjectId || !$teacherId) continue;

            $subject = $subjectRepo->find($subjectId);
            $teacher = $teacherRepo->find($teacherId);

            if ($subject && $teacher) {
                // Check if assignment exists
                $existing = $assignmentRepo->findOneBy([
                    'grade' => $grade,
                    'subject' => $subject,
                    'schoolCycle' => $cycle
                ]);

                if ($existing) {
                    $existing->setTeacher($teacher);
                    $existing->setHoursPerWeek($hours);
                } else {
                    $assignment = new SubjectAssignment();
                    $assignment->setGrade($grade);
                    $assignment->setSubject($subject);
                    $assignment->setTeacher($teacher);
                    $assignment->setSchoolCycle($cycle);
                    $assignment->setHoursPerWeek($hours);
                    $em->persist($assignment);
                }
            }
        }

        $em->flush();

        return $this->json(['success' => true, 'message' => 'Assignments updated']);
    }

    #[Route('/schedule-preview/{gradeId}', name: 'api_academic_schedule_preview', methods: ['GET'])]
    public function getSchedulePreview(int $gradeId): JsonResponse
    {
        // In a real AI implementation, this would call the Python service.
        // For now, we return a structured response the frontend can use.
        
        return $this->json([
            'status' => 'GENERATED',
            'periods' => [
               ['time' => '07:00 - 07:40', 'mon' => 'Matemáticas', 'tue' => 'Lenguaje', 'wed' => 'Ciencias', 'thu' => 'Matemáticas', 'fri' => 'Física'],
               ['time' => '07:40 - 08:20', 'mon' => 'Matemáticas', 'tue' => 'Lenguaje', 'wed' => 'Ciencias', 'thu' => 'Matemáticas', 'fri' => 'Física'],
               ['time' => '08:20 - 09:00', 'mon' => 'Sociales', 'tue' => 'Inglés', 'wed' => 'Computación', 'thu' => 'Sociales', 'fri' => 'Arte'],
            ]
        ]);
    }
}
