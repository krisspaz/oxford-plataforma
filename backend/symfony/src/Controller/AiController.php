<?php

namespace App\Controller;

use App\DTO\Response\ApiResponse;
use App\Service\AiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

/**
 * AI Service Controller
 * Handles communication between frontend and AI microservice
 */
#[Route('/api/ai', name: 'api_ai_')]
class AiController extends AbstractController
{
    private string $aiServiceUrl;

    public function __construct(
        private AiService $aiService,
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger
    ) {
        $this->aiServiceUrl = $_ENV['AI_SERVICE_URL'] ?? 'http://ai_service:8001';
    }

    /**
     * Health check for AI service
     */
    #[Route('/health', name: 'health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        try {
            $response = $this->httpClient->request('GET', "{$this->aiServiceUrl}/health", [
                'timeout' => 5,
            ]);
            
            $data = $response->toArray();
            return $this->json([
                'status' => 'healthy',
                'service' => 'ai_proxy',
                'ai_service' => $data,
            ]);
        } catch (\Exception $e) {
            $this->logger->warning('AI Service health check failed', [
                'error' => $e->getMessage(),
            ]);
            
            return $this->json([
                'status' => 'degraded',
                'service' => 'ai_proxy',
                'ai_service' => 'unreachable',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    /**
     * Process chat message through AI
     */
    #[Route('/chat', name: 'chat', methods: ['POST'])]
    public function chat(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['message']) || empty(trim($data['message']))) {
            return $this->json([
                'success' => false,
                'error' => 'Message is required',
            ], Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $response = $this->httpClient->request('POST', "{$this->aiServiceUrl}/chat", [
                'json' => [
                    'text' => $data['message'],
                    'context' => $data['context'] ?? [],
                ],
                'timeout' => 30,
            ]);
            
            $result = $response->toArray();
            
            $this->logger->info('AI Chat processed', [
                'message' => substr($data['message'], 0, 100),
                'intent' => $result['intent'] ?? 'unknown',
            ]);
            
            return $this->json([
                'success' => true,
                'data' => $result,
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('AI Chat error', [
                'error' => $e->getMessage(),
                'message' => $data['message'] ?? '',
            ]);
            
            // Return fallback response
            return $this->json([
                'success' => true,
                'data' => [
                    'intent' => 'unknown',
                    'confidence' => 0,
                    'response_text' => 'El servicio de IA no está disponible. Por favor, intenta más tarde.',
                    'fallback' => true,
                ],
            ]);
        }
    }

    /**
     * Process command for schedule configuration
     */
    #[Route('/process', name: 'process', methods: ['POST'])]
    public function process(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['text'])) {
            return $this->json([
                'success' => false,
                'error' => 'Text is required',
            ], Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $response = $this->httpClient->request('POST', "{$this->aiServiceUrl}/process", [
                'json' => [
                    'text' => $data['text'],
                    'current_config' => $data['current_config'] ?? null,
                ],
                'timeout' => 30,
            ]);
            
            return $this->json([
                'success' => true,
                'data' => $response->toArray(),
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('AI Process error', ['error' => $e->getMessage()]);
            
            return $this->json([
                'success' => false,
                'error' => 'Error processing command',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Risk analysis for student (original endpoint)
     */
    #[Route('/risk-analysis', name: 'risk_analysis', methods: ['POST'])]
    public function riskAnalysis(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['student_id']) || !isset($data['grades']) || !is_array($data['grades'])) {
            return $this->json(['error' => 'Invalid data. Required: student_id (int), grades (array of floats)'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->aiService->getRiskAnalysis($data['student_id'], $data['grades']);

        return $this->json($result);
    }

    /**
     * Generate schedule using AI
     */
    #[Route('/generate-schedule', name: 'generate_schedule', methods: ['POST'])]
    public function generateSchedule(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Support both old and new API format
        if (isset($data['slots']) && isset($data['teachers']) && isset($data['groups'])) {
            // Old format
            try {
                $result = $this->aiService->generateSchedule($data);
                return $this->json($result);
            } catch (\Exception $e) {
                return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        
        // New format with config
        if (!isset($data['config'])) {
            return $this->json([
                'success' => false,
                'error' => 'Config is required',
            ], Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $response = $this->httpClient->request('POST', "{$this->aiServiceUrl}/generate", [
                'json' => [
                    'config' => $data['config'],
                    'teachers' => $data['teachers'] ?? [],
                    'subjects' => $data['subjects'] ?? [],
                    'constraints' => $data['constraints'] ?? [], // PASS CONSTRAINTS
                ],
                'timeout' => 60,
            ]);
            
            $result = $response->toArray();
            
            $this->logger->info('Schedule generated', [
                'success' => $result['success'] ?? false,
                'schedule_count' => count($result['schedule'] ?? []),
                'conflicts' => count($result['conflicts'] ?? []),
            ]);
            
            return $this->json([
                'success' => true,
                'data' => $result,
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('Schedule generation error', ['error' => $e->getMessage()]);
            
            return $this->json([
                'success' => false,
                'error' => 'Error generating schedule. Please try again.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Validate schedule for conflicts
     */
    #[Route('/validate-schedule', name: 'validate_schedule', methods: ['POST'])]
    public function validateSchedule(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['schedule']) || !is_array($data['schedule'])) {
            return $this->json([
                'success' => false,
                'error' => 'Schedule array is required',
            ], Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $response = $this->httpClient->request('POST', "{$this->aiServiceUrl}/validate", [
                'json' => ['schedule' => $data['schedule']],
                'timeout' => 30,
            ]);
            
            return $this->json([
                'success' => true,
                'data' => $response->toArray(),
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('Schedule validation error', ['error' => $e->getMessage()]);
            
            return $this->json([
                'success' => false,
                'error' => 'Error validating schedule',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get AI suggestions for schedule optimization
     */
    #[Route('/suggestions', name: 'suggestions', methods: ['POST'])]
    public function suggestions(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        try {
            $response = $this->httpClient->request('POST', "{$this->aiServiceUrl}/suggestions", [
                'json' => ['schedule' => $data['schedule'] ?? []],
                'timeout' => 30,
            ]);
            
            return $this->json([
                'success' => true,
                'data' => $response->toArray(),
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('AI Suggestions error', ['error' => $e->getMessage()]);
            
            return $this->json([
                'success' => true,
                'data' => ['suggestions' => []],
            ]);
        }
    }
}
