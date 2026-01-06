<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use RuntimeException;

class AiService
{
    private HttpClientInterface $client;
    private string $aiServiceUrl;

    public function __construct(
        HttpClientInterface $client,
        #[Autowire(env: 'AI_SERVICE_URL')] string $aiServiceUrl = 'http://ai_service:8000'
    ) {
        $this->client = $client;
        $this->aiServiceUrl = $aiServiceUrl;
    }

    public function getRiskAnalysis(int $studentId, array $grades): array
    {
        try {
            $response = $this->client->request('POST', "{$this->aiServiceUrl}/predict-risk", [
                'json' => [
                    'id' => $studentId,
                    'grades' => $grades,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new RuntimeException('AI Service returned error: ' . $response->getStatusCode());
            }

            return $response->toArray();
        } catch (\Exception $e) {
            // Fallback or rethrow
            return [
                'student_id' => $studentId,
                'error' => true,
                'message' => 'AI Service unavailable: ' . $e->getMessage(),
                'risk_level' => 'UNKNOWN',
                'risk_score' => 0.0
            ];
        }
    }

    public function generateSchedule(array $data): array
    {
        try {
            $response = $this->client->request('POST', "{$this->aiServiceUrl}/generate-schedule", [
                'json' => $data,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new RuntimeException('AI Service returned error: ' . $response->getStatusCode());
            }

            return $response->toArray();
        } catch (\Exception $e) {
            throw new RuntimeException('Failed to generate schedule: ' . $e->getMessage());
        }
    }

    public function reportScheduleChange(array $changeData): void
    {
        try {
            // Call the python /learn endpoint
            // changeData = { teacher_id, day_from, day_to, reason }
            $this->client->request('POST', "{$this->aiServiceUrl}/learn", [
                'json' => $changeData,
                'timeout' => 2 // Don't block user too long for async learning
            ]);
        } catch (\Exception $e) {
            // Learning failure should not block the main action
            // Just log it silently or via logger
        }
    }
}
