<?php

namespace App\Controller;

use App\Service\AiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/ai', name: 'api_ai_')]
class AiController extends AbstractController
{
    private AiService $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    #[Route('/risk-analysis', name: 'risk_analysis', methods: ['POST'])]
    public function riskAnalysis(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Basic Validation
        if (!isset($data['student_id']) || !isset($data['grades']) || !is_array($data['grades'])) {
            return $this->json(['error' => 'Invalid data. Required: student_id (int), grades (array of floats)'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->aiService->getRiskAnalysis($data['student_id'], $data['grades']);

        return $this->json($result);
    }

    #[Route('/generate-schedule', name: 'generate_schedule', methods: ['POST'])]
    public function generateSchedule(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Basic Validation
        if (!isset($data['slots']) || !isset($data['teachers']) || !isset($data['groups'])) {
             return $this->json(['error' => 'Invalid data. Required: slots, teachers, groups'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->aiService->generateSchedule($data);
            return $this->json($result);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
