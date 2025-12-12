<?php

namespace App\Controller;

use App\Entity\Student;
use App\Service\AcademicRiskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class RiskAnalysisController extends AbstractController
{
    private AcademicRiskService $riskService;

    public function __construct(AcademicRiskService $riskService)
    {
        $this->riskService = $riskService;
    }

    #[Route('/api/students/{id}/risk', name: 'api_student_risk', methods: ['GET'])]
    public function analyze(Student $student): JsonResponse
    {
        // In a real scenario, we would fetch grades from the database relation
        // For now, we mock some data to test the Python integration
        $mockData = [
            'id' => $student->getId(),
            'grades' => [65, 70, 55, 80, 40], // Random grades that might trigger high risk
        ];

        try {
            $analysis = $this->riskService->analyzeRisk($mockData);
            
            // Optionally save the score back to the student entity
            // $student->setAcademicRiskScore($analysis['risk_score']);
            // $entityManager->flush();

            return $this->json($analysis);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
