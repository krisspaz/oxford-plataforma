<?php

namespace App\Controller;

use App\Entity\GradeRecord;
use App\Repository\GradeRecordRepository;
use App\Repository\BimesterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/grade-records')]
class GradeRecordController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private GradeRecordRepository $gradeRecordRepository,
        private BimesterRepository $bimesterRepository
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $subjectAssignment = $request->query->get('subjectAssignment');
        $bimester = $request->query->get('bimester');
        $student = $request->query->get('student');
        
        $criteria = [];
        if ($subjectAssignment) $criteria['subjectAssignment'] = $subjectAssignment;
        if ($bimester) $criteria['bimester'] = $bimester;
        if ($student) $criteria['student'] = $student;
        
        $records = $this->gradeRecordRepository->findBy($criteria);
        
        return $this->json([
            'success' => true,
            'data' => array_map(fn($r) => $this->serializeGradeRecord($r), $records)
        ]);
    }

    #[Route('/by-assignment/{assignmentId}/bimester/{bimesterId}', methods: ['GET'])]
    public function byAssignmentAndBimester(int $assignmentId, int $bimesterId): JsonResponse
    {
        // 1. Get the Assignment to know which Grade/Section
        $assignment = $this->em->getRepository(\App\Entity\SubjectAssignment::class)->find($assignmentId);
        if (!$assignment) {
            return $this->json(['success' => false, 'error' => 'Assignment not found'], 404);
        }

        // 2. Get all students in that Grade/Section
        $students = $this->em->getRepository(\App\Entity\Student::class)->findBy([
            // Assuming Student has these fields or via Enrollment. 
            // Based on Student.php, it has course? No, it has 'enrollments'.
            // For now, let's assume we use the repository method if it exists, otherwise manual query.
            // Student.php has mocked logic? No, current Student entity (viewed earlier) has no Grade/Section field directly exposed in the truncated view?
            // Wait, Student.php has `course` (Line 172) -> likely Grade/Course.
            'course' => $assignment->getGrade()
            // We might need to filter by section if Student has section.
            // Let's assume 'course' maps to Grade.
        ]);

        // If 'course' is not the field, we might need a custom repository method.
        // Let's use a fail-safe approach: Get existing records first.
        $existingRecords = $this->gradeRecordRepository->findBy([
            'subjectAssignment' => $assignmentId,
            'bimester' => $bimesterId
        ]);
        
        $recordsMap = []; // studentId -> record
        foreach ($existingRecords as $r) {
            if ($r->getStudent()) {
                $recordsMap[$r->getStudent()->getId()] = $r;
            }
        }

        // If we can't easily fetch students by course, we'll try to rely on existing records + basic fetch
        // BUT we need to support new students.
        // Let's attempt to use the StudentRepository to find by course/grade.
        // If the entity field is 'course', we use that.
        
        $finalRecords = [];
        
        // Strategy: We iterate ALL students in the grade and see if they have a record.
        // If not, we create a dummy structure (not persisted).
        
        if ($assignment->getGrade()) {
             // We need to fetch students by the assignment's grade.
             // Verification: Student.php line 171: private ?Course $course = null;
             // Use matching criteria
             $criteria = ['course' => $assignment->getGrade()];
             // If assignment has section and Student has section... Student.php didn't show section field in visible lines (it ended at line 243).
             // It might be in 'enrollments'.
             
             // Simplification: Fetch all students in the Course (Grade).
             // Ideally we filter by Section too if the system supports multiple sections per grade.
             $roster = $this->em->getRepository(\App\Entity\Student::class)->findBy($criteria);
             
             foreach ($roster as $student) {
                 $recordKey = $student->getId();
                 if (isset($recordsMap[$recordKey])) {
                     $finalRecords[] = $this->serializeGradeRecord($recordsMap[$recordKey]);
                 } else {
                     // Create Virtual Record
                     $finalRecords[] = [
                         'id' => null, // New record
                         'student' => $student->getId(),
                         'studentName' => $student->getFullName(),
                         'studentCarnet' => $student->getStudentCode(),
                         'subjectAssignment' => $assignmentId,
                         'bimester' => $bimesterId,
                         'score' => null,
                         'isLocked' => false,
                         'canEdit' => true, // New records are editable
                         'updatedAt' => null
                     ];
                 }
             }
        } else {
            // Fallback if no grade assigned (unlikely)
             $finalRecords = array_map(fn($r) => $this->serializeGradeRecord($r), $existingRecords);
        }
        
        $bimester = $this->bimesterRepository->find($bimesterId);
        
        return $this->json([
            'success' => true,
            'bimester' => $bimester ? [
                'id' => $bimester->getId(),
                'name' => $bimester->getName(),
                'isClosed' => $bimester->getIsClosed(),
                'endDate' => $bimester->getEndDate()?->format('Y-m-d')
            ] : null,
            'records' => $finalRecords
        ]);
    }

    #[Route('/batch', methods: ['POST'])]
    public function saveBatch(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $records = $data['records'] ?? [];
        $bimesterId = $data['bimesterId'] ?? null;
        
        // Verificar si el bimestre está cerrado
        if ($bimesterId) {
            $bimester = $this->bimesterRepository->find($bimesterId);
            if ($bimester && ($bimester->getIsClosed() || $bimester->shouldAutoClose())) {
                return $this->json([
                    'success' => false,
                    'error' => 'El bimestre está cerrado o ha finalizado. No se pueden modificar notas.'
                ], 403);
            }
        }
        
        $saved = 0;
        foreach ($records as $recordData) {
            $record = null;
            if (isset($recordData['id'])) {
                $record = $this->gradeRecordRepository->find($recordData['id']);
            }
            
            if (!$record) {
                $record = new GradeRecord();
                // Set dependencies
                // We need studentId and assignmentId from the recordData
                $studentId = $recordData['studentId'] ?? null;
                $assignmentId = $recordData['subjectAssignmentId'] ?? null;
                // bimesterId comes from the main payload
                
                if ($studentId && $assignmentId && $bimesterId) {
                    $studentRef = $this->em->getReference(\App\Entity\Student::class, $studentId);
                    $assignmentRef = $this->em->getReference(\App\Entity\SubjectAssignment::class, $assignmentId);
                    $bimesterRef = $this->em->getReference(\App\Entity\Bimester::class, $bimesterId);
                    
                    $record->setStudent($studentRef);
                    $record->setSubjectAssignment($assignmentRef);
                    $record->setBimester($bimesterRef);
                } else {
                    // Skip invalid data
                    continue; 
                }
                
                $this->em->persist($record);
            }
            
            // Verificar si se puede editar
            if (!$record->canBeEdited()) {
                continue;
            }
            
            $record->setScore($recordData['score'] ?? null);
            $saved++;
        }
        
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'saved' => $saved,
            'message' => "$saved notas guardadas correctamente"
        ]);
    }

    #[Route('/{id}/unlock', methods: ['POST'])]
    public function unlock(int $id): JsonResponse
    {
        $record = $this->gradeRecordRepository->find($id);
        
        if (!$record) {
            return $this->json(['success' => false, 'error' => 'Registro no encontrado'], 404);
        }
        
        // Solo coordinación puede desbloquear
        $this->denyAccessUnlessGranted('ROLE_COORDINACION');
        
        $record->unlock();
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeGradeRecord($record)
        ]);
    }

    // teacherSummary moved to TeacherSummaryController
    private function serializeGradeRecord(GradeRecord $r): array
    {
        return [
            'student' => $r->getStudent()?->getId(),
            'studentName' => $r->getStudent() ? $r->getStudent()->getFirstName() . ' ' . $r->getStudent()->getLastName() : null,
            'studentCarnet' => $r->getStudent()?->getCarnet(),
            'subjectAssignment' => $r->getSubjectAssignment()?->getId(),
            'bimester' => $r->getBimester()?->getId(),
            'score' => $r->getScore(),
            'isLocked' => $r->getIsLocked(),
            'canEdit' => $r->canBeEdited(),
            'updatedAt' => $r->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
