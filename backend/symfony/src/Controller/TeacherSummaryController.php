<?php

namespace App\Controller;

use App\Repository\GradeRecordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/teacher')]
class TeacherSummaryController extends AbstractController
{
    public function __construct(
        private GradeRecordRepository $gradeRecordRepository
    ) {}

    #[Route('/summary', name: 'api_teacher_summary_isolated', methods: ['GET'])]
    public function summary(Request $request): JsonResponse
    {
        $assignmentId = $request->query->get('assignmentId');
        if (!$assignmentId) {
            return $this->json(['error' => 'Subject Assignment ID is required'], 400);
        }

        // Fetch all records for this assignment
        $records = $this->gradeRecordRepository->findBy([
            'subjectAssignment' => $assignmentId
        ]);

        $studentsData = [];
        
        foreach ($records as $record) {
            $student = $record->getStudent();
            if (!$student) continue;

            $studentId = $student->getId();

            if (!isset($studentsData[$studentId])) {
                $studentsData[$studentId] = [
                    'id' => $studentId,
                    'student' => $student->getFirstName() . ' ' . $student->getLastName(), // Fallback if getFullName not exits
                    'b1' => null, 'b2' => null, 'b3' => null, 'b4' => null,
                    'final' => 0
                ];
            }
            
            $bimester = $record->getBimester();
            if (!$bimester) continue;
            
            $bimesterName = $bimester->getName();
            
            // Mapping logic
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
            
            $data['final'] = $count > 0 ? round($sum / 4, 2) : 0;
            $data['status'] = $data['final'] >= 60 ? 'APROBADO' : 'REPROBADO';
        }

        return $this->json([
            'success' => true,
            'data' => array_values($studentsData)
        ]);
    }
}
