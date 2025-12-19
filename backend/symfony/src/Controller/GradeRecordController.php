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
        $records = $this->gradeRecordRepository->findBy([
            'subjectAssignment' => $assignmentId,
            'bimester' => $bimesterId
        ]);
        
        $bimester = $this->bimesterRepository->find($bimesterId);
        
        return $this->json([
            'success' => true,
            'bimester' => $bimester ? [
                'id' => $bimester->getId(),
                'name' => $bimester->getName(),
                'isClosed' => $bimester->getIsClosed(),
                'endDate' => $bimester->getEndDate()?->format('Y-m-d')
            ] : null,
            'records' => array_map(fn($r) => $this->serializeGradeRecord($r), $records)
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
                // TODO: Set bimester, student, subjectAssignment relations
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

    #[Route('/teacher-summary', methods: ['GET'])]
    public function teacherSummary(Request $request): JsonResponse
    {
        $assignmentId = $request->query->get('assignmentId');
        if (!$assignmentId) {
            return $this->json(['error' => 'Subject Assignment ID is required'], 400);
        }

        // Fetch all records for this assignment (across all bimesters)
        $records = $this->gradeRecordRepository->findBy([
            'subjectAssignment' => $assignmentId
        ]);

        $studentsData = [];
        
        foreach ($records as $record) {
            $studentId = $record->getStudent()->getId();
            if (!isset($studentsData[$studentId])) {
                $studentsData[$studentId] = [
                    'id' => $studentId,
                    'student' => $record->getStudent()->getFullName(),
                    'b1' => null, 'b2' => null, 'b3' => null, 'b4' => null,
                    'final' => 0
                ];
            }
            
            // Map bimester IDs to columns (Assuming IDs 1-4 match B1-B4, otherwise need logic)
            // Just for simplicity we assume bimester ID maps roughly or we use bimester name matching
            // Ideally we should use Bimester Name or SortOrder
            $bimesterName = $record->getBimester()->getName();
            
            // Simple mapping logic (adapt based on real DB names)
            if (stripos($bimesterName, '1') !== false || stripos($bimesterName, 'Primer') !== false) $studentsData[$studentId]['b1'] = $record->getScore();
            if (stripos($bimesterName, '2') !== false || stripos($bimesterName, 'Segundo') !== false) $studentsData[$studentId]['b2'] = $record->getScore();
            if (stripos($bimesterName, '3') !== false || stripos($bimesterName, 'Tercer') !== false) $studentsData[$studentId]['b3'] = $record->getScore();
            if (stripos($bimesterName, '4') !== false || stripos($bimesterName, 'Cuarto') !== false) $studentsData[$studentId]['b4'] = $record->getScore();
        }

        // Calculate Final and Status
        foreach ($studentsData as &$data) {
            $sum = 0;
            $count = 0;
            if ($data['b1'] !== null) { $sum += $data['b1']; $count++; }
            if ($data['b2'] !== null) { $sum += $data['b2']; $count++; }
            if ($data['b3'] !== null) { $sum += $data['b3']; $count++; }
            if ($data['b4'] !== null) { $sum += $data['b4']; $count++; }
            
            $data['final'] = $count > 0 ? round($sum / 4, 2) : 0; // Average over 4 bimesters generally
            $data['status'] = $data['final'] >= 60 ? 'APROBADO' : 'REPROBADO';
        }

        return $this->json([
            'success' => true,
            'data' => array_values($studentsData)
        ]);
    }
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
