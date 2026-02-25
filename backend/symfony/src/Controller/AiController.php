<?php

namespace App\Controller;

use App\Service\AiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ai', name: 'api_ai_')]
class AiController extends AbstractController
{
    public function __construct(
        private AiService $aiService,
    ) {
    }

    #[Route('/process-command', name: 'process_command', methods: ['POST'])]
    public function processCommand(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        // Adaptamos el payload del frontend (text, role, etc.) al contrato del microservicio
        $payload = [
            'text' => $data['text'] ?? ($data['message'] ?? ''),
            'role' => $data['role'] ?? 'admin',
        ];

        $responseData = $this->aiService->ask($payload, '/process-command');

        return $this->json($responseData);
    }

    #[Route('/chat', name: 'chat', methods: ['POST'])]
    public function chat(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $payload = [
            'message' => $data['message'] ?? '',
            'user_id' => $data['user_id'] ?? null,
            'role' => $data['role'] ?? 'student',
            'context' => $data['context'] ?? [],
        ];

        $responseData = $this->aiService->ask($payload, '/chat/message');

        return $this->json($responseData);
    }

    #[Route('/generate-schedule', name: 'generate_schedule', methods: ['POST'])]
    public function generateSchedule(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $responseData = $this->aiService->ask($data, '/generate-schedule');

        return $this->json($responseData);
    }

    #[Route('/health', name: 'health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        $responseData = $this->aiService->get('/health');
        $status = $responseData['status'] ?? $responseData['ok'] ?? null;
        if ($status === 'healthy' || $responseData['ok'] === true) {
            return $this->json($responseData);
        }
        return $this->json($responseData, 503);
    }

    #[Route('/chat/suggestions', name: 'chat_suggestions', methods: ['GET'])]
    public function chatSuggestions(Request $request): JsonResponse
    {
        $role = $request->query->get('role', 'student');
        $responseData = $this->aiService->get('/chat/suggestions', ['role' => $role]);
        return $this->json($responseData);
    }

    #[Route('/risk-analysis', name: 'risk_analysis', methods: ['POST'])]
    public function riskAnalysis(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        if (isset($data['student_id']) && !isset($data['id'])) {
            $data['id'] = $data['student_id'];
        }
        $responseData = $this->aiService->ask($data, '/analytics/predict-risk');
        return $this->json($responseData);
    }

    #[Route('/analyze-burnout', name: 'analyze_burnout', methods: ['POST'])]
    public function analyzeBurnout(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $responseData = $this->aiService->ask($data, '/analytics/analyze-burnout');
        return $this->json($responseData);
    }

    #[Route('/institutional-health', name: 'institutional_health', methods: ['GET'])]
    public function institutionalHealth(): JsonResponse
    {
        $responseData = $this->aiService->get('/analytics/institutional-health');
        return $this->json($responseData);
    }
}
