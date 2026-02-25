<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Psr\Log\LoggerInterface;

class AiService
{
    private const CIRCUIT_OPEN_TTL = 30; // Seconds to keep circuit open
    private const FAILURE_THRESHOLD = 3;  // Max failures before opening
    private const CACHE_KEY_STATE = 'ai_service_circuit_state';
    private const CACHE_KEY_FAILURES = 'ai_service_failures';

    public function __construct(
        private HttpClientInterface $client,
        private CacheInterface $cache,
        private LoggerInterface $logger,
        #[Autowire(env: 'AI_SERVICE_URL')]
        private string $aiUrl,
        #[Autowire(env: 'AI_INTERNAL_KEY')]
        private string $internalKey = 'dev_secret'
    ) {}

    /**
     * GET request to AI service (health, suggestions, etc.)
     */
    public function get(string $endpoint, array $query = []): array
    {
        if ($this->isCircuitOpen()) {
            $this->logger->warning('[CircuitBreaker] Circuit is OPEN. GET blocked.');
            return $endpoint === '/health'
                ? ['status' => 'unhealthy', 'circuit_broken' => true]
                : [];
        }

        try {
            $url = $this->aiUrl . $endpoint;
            if ($query !== []) {
                $url .= '?' . http_build_query($query);
            }
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'X-INTERNAL-KEY' => $this->internalKey,
                ],
                'timeout' => 5.0,
            ]);
            $data = $response->toArray();
            $this->resetFailures();
            return $data;
        } catch (\Throwable $e) {
            $this->recordFailure();
            $this->logger->error('[CircuitBreaker] GET failed: ' . $e->getMessage());
            return $endpoint === '/health'
                ? ['status' => 'unhealthy', 'error' => $e->getMessage()]
                : [];
        }
    }

    public function ask(array $payload, string $endpoint = '/process-command'): array
    {
        if ($this->isCircuitOpen()) {
            $this->logger->warning('[CircuitBreaker] Circuit is OPEN. Request blocked.');
            return $this->fallbackResponse($payload);
        }

        try {
            $response = $this->client->request('POST', $this->aiUrl . $endpoint, [
                'headers' => [
                    'X-INTERNAL-KEY' => $this->internalKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => $payload,
                'timeout' => 5.0 // Fail fast
            ]);

            $data = $response->toArray();
            $this->resetFailures(); // Success! Reset counter.
            return $data;

        } catch (\Throwable $e) {
            $this->recordFailure();
            $this->logger->error('[CircuitBreaker] Call failed: ' . $e->getMessage());
            return $this->fallbackResponse($payload);
        }
    }



    private function isCircuitOpen(): bool
    {
        $state = $this->cache->get(self::CACHE_KEY_STATE, function (ItemInterface $item) {
            return 'CLOSED';
        });

        return $state === 'OPEN';
    }

    private function recordFailure(): void
    {
        $failures = $this->cache->get(self::CACHE_KEY_FAILURES, function (ItemInterface $item) {
            $item->expiresAfter(60); // Reset count every minute window
            return 0;
        });

        $failures++;

        // Save new failure count
        $this->cache->delete(self::CACHE_KEY_FAILURES);
        $this->cache->get(self::CACHE_KEY_FAILURES, function (ItemInterface $item) use ($failures) {
            $item->expiresAfter(60);
            return $failures;
        });

        if ($failures >= self::FAILURE_THRESHOLD) {
            $this->openCircuit();
        }
    }

    private function openCircuit(): void
    {
        $this->cache->delete(self::CACHE_KEY_STATE);
        $this->cache->get(self::CACHE_KEY_STATE, function (ItemInterface $item) {
            $item->expiresAfter(self::CIRCUIT_OPEN_TTL); // Keep open for 30s
            return 'OPEN';
        });
        $this->logger->critical('[CircuitBreaker] Threshold reached. Circuit OPENED.');
    }

    private function resetFailures(): void
    {
        $this->cache->delete(self::CACHE_KEY_FAILURES);
        $this->cache->delete(self::CACHE_KEY_STATE);
    }

    private function fallbackResponse(array $payload): array
    {
        // Degraded mode response
        return [
            'intent' => 'system_busy',
            'response_text' => '⚠️ Los sistemas de IA están experimentando alta carga. Por favor intenta más tarde.',
            'action' => null,
            'confidence' => 0.0,
            'circuit_broken' => true
        ];
    }
    public function getRiskAnalysis(int $studentId, array $grades): array
    {
        return $this->ask([
            'id' => $studentId,
            'grades' => $grades,
            'attendance' => 80 // Mock attendance for now if not passed
        ], '/risk-analysis');
    }
}
