<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/api/ai', name: 'api_ai_')]
class AIController extends AbstractController
{
    private $httpClient;
    private $aiServiceUrl;
    private $aiInternalKey;

    public function __construct(HttpClientInterface $httpClient, string $aiServiceUrl, string $aiInternalKey)
    {
        $this->httpClient = $httpClient;
        $this->aiServiceUrl = $aiServiceUrl; // e.g. http://ai_service:8001
        $this->aiInternalKey = $aiInternalKey;
    }

    #[Route('/process-command', name: 'process_command', methods: ['POST'])]
    public function processCommand(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        try {
            $response = $this->httpClient->request('POST', $this->aiServiceUrl . '/ask', [
                'headers' => [
                    'X-Internal-Key' => $this->aiInternalKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'prompt' => $data['text'] ?? '',
                ],
            ]);

            return new JsonResponse($response->toArray(false), $response->getStatusCode());
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'AI Service Unavailable',
                'message' => $e->getMessage()
            ], 503);
        }
    }

    #[Route('/generate-schedule', name: 'generate_schedule', methods: ['POST'])]
    public function generateSchedule(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        try {
            $response = $this->httpClient->request('POST', $this->aiServiceUrl . '/generate-schedule', [
                'headers' => [
                    'X-Internal-Key' => $this->aiInternalKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
            ]);

            return new JsonResponse($response->toArray(false), $response->getStatusCode());
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'AI Schedule Generator Unavailable',
                'message' => $e->getMessage()
            ], 503);
        }
    }

    #[Route('/health', name: 'health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        try {
            $response = $this->httpClient->request('GET', $this->aiServiceUrl . '/health');
            return new JsonResponse($response->toArray(false), $response->getStatusCode());
        } catch (\Exception $e) {
            return $this->json(['status' => 'unhealthy', 'error' => $e->getMessage()], 503);
        }
    }
}
