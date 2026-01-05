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
