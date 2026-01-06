<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Circuit Breaker Service
 * 
 * Implements circuit breaker pattern for resilient microservice communication.
 * States: CLOSED -> OPEN -> HALF_OPEN -> CLOSED
 */
class CircuitBreakerService
{
    private const STATE_CLOSED = 'closed';
    private const STATE_OPEN = 'open';
    private const STATE_HALF_OPEN = 'half_open';
    
    private const FAILURE_THRESHOLD = 5;
    private const RECOVERY_TIMEOUT = 30; // seconds
    private const SUCCESS_THRESHOLD = 3;
    
    private string $state = self::STATE_CLOSED;
    private int $failureCount = 0;
    private int $successCount = 0;
    private ?float $lastFailureTime = null;
    
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly ?object $cache = null
    ) {}
    
    /**
     * Execute request with circuit breaker protection
     * 
     * @param callable $operation The operation to execute
     * @param mixed $fallback Fallback value if circuit is open
     * @return mixed
     */
    public function execute(callable $operation, mixed $fallback = null): mixed
    {
        // Check if circuit is open
        if ($this->isOpen()) {
            if ($this->shouldAttemptRecovery()) {
                $this->state = self::STATE_HALF_OPEN;
                $this->logger->info('Circuit breaker entering HALF_OPEN state');
            } else {
                $this->logger->warning('Circuit breaker is OPEN, returning fallback');
                return $fallback;
            }
        }
        
        try {
            $result = $operation();
            $this->recordSuccess();
            return $result;
        } catch (\Exception $e) {
            $this->recordFailure();
            $this->logger->error('Circuit breaker recorded failure: ' . $e->getMessage());
            
            if ($this->isOpen()) {
                return $fallback;
            }
            
            throw $e;
        }
    }
    
    /**
     * Make HTTP request with circuit breaker
     */
    public function request(
        string $method,
        string $url,
        array $options = [],
        mixed $fallback = null
    ): mixed {
        return $this->execute(
            fn() => $this->httpClient->request($method, $url, $options)->toArray(),
            $fallback
        );
    }
    
    /**
     * Call AI service with fallback
     */
    public function callAiService(string $endpoint, array $data, mixed $fallback = null): mixed
    {
        $aiBaseUrl = $_ENV['AI_SERVICE_URL'] ?? 'http://localhost:8001';
        
        return $this->request(
            'POST',
            $aiBaseUrl . $endpoint,
            [
                'json' => $data,
                'timeout' => 10
            ],
            $fallback
        );
    }
    
    private function isOpen(): bool
    {
        return $this->state === self::STATE_OPEN;
    }
    
    private function shouldAttemptRecovery(): bool
    {
        if ($this->lastFailureTime === null) {
            return false;
        }
        
        return (time() - $this->lastFailureTime) >= self::RECOVERY_TIMEOUT;
    }
    
    private function recordSuccess(): void
    {
        $this->failureCount = 0;
        
        if ($this->state === self::STATE_HALF_OPEN) {
            $this->successCount++;
            
            if ($this->successCount >= self::SUCCESS_THRESHOLD) {
                $this->state = self::STATE_CLOSED;
                $this->successCount = 0;
                $this->logger->info('Circuit breaker CLOSED after recovery');
            }
        }
    }
    
    private function recordFailure(): void
    {
        $this->failureCount++;
        $this->lastFailureTime = time();
        $this->successCount = 0;
        
        if ($this->failureCount >= self::FAILURE_THRESHOLD) {
            $this->state = self::STATE_OPEN;
            $this->logger->warning(
                "Circuit breaker OPEN after {$this->failureCount} failures"
            );
        }
    }
    
    public function getState(): string
    {
        return $this->state;
    }
    
    public function getStats(): array
    {
        return [
            'state' => $this->state,
            'failure_count' => $this->failureCount,
            'success_count' => $this->successCount,
            'last_failure' => $this->lastFailureTime 
                ? date('Y-m-d H:i:s', (int)$this->lastFailureTime) 
                : null,
        ];
    }
    
    /**
     * Reset circuit breaker (for testing)
     */
    public function reset(): void
    {
        $this->state = self::STATE_CLOSED;
        $this->failureCount = 0;
        $this->successCount = 0;
        $this->lastFailureTime = null;
    }
}
