<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Metrics and Alerting Service
 * ============================
 * Real-time metrics collection and automated alerts
 */
class MetricsService
{
    private array $metrics = [];
    private float $startTime;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ?HttpClientInterface $httpClient = null,
    ) {
        $this->startTime = microtime(true);
    }

    // ============================================
    // METRICS COLLECTION
    // ============================================

    /**
     * Increment a counter metric
     */
    public function increment(string $name, int $value = 1, array $tags = []): void
    {
        $key = $this->buildKey($name, $tags);
        $this->metrics[$key] = ($this->metrics[$key] ?? 0) + $value;
        
        $this->logger->debug("METRIC: {$name} +{$value}", ['tags' => $tags]);
    }

    /**
     * Set a gauge metric
     */
    public function gauge(string $name, float $value, array $tags = []): void
    {
        $key = $this->buildKey($name, $tags);
        $this->metrics[$key] = $value;
        
        $this->logger->debug("METRIC: {$name} = {$value}", ['tags' => $tags]);
    }

    /**
     * Record timing metric
     */
    public function timing(string $name, float $milliseconds, array $tags = []): void
    {
        $key = $this->buildKey($name . '_timing', $tags);
        $this->metrics[$key] = $milliseconds;
        
        // Alert on slow operations
        if ($milliseconds > 5000) {
            $this->alert('slow_operation', "Operation {$name} took {$milliseconds}ms", 'warning');
        }
    }

    /**
     * Time a callable
     */
    public function time(string $name, callable $callback, array $tags = []): mixed
    {
        $start = microtime(true);
        $result = $callback();
        $duration = (microtime(true) - $start) * 1000;
        
        $this->timing($name, $duration, $tags);
        
        return $result;
    }

    // ============================================
    // APPLICATION METRICS
    // ============================================

    /**
     * Record API request
     */
    public function recordApiRequest(string $endpoint, string $method, int $statusCode, float $duration): void
    {
        $this->increment('api_requests_total', 1, [
            'endpoint' => $endpoint,
            'method' => $method,
            'status' => (string)$statusCode,
        ]);
        
        $this->timing('api_request_duration', $duration, ['endpoint' => $endpoint]);

        // Alert on error spike
        if ($statusCode >= 500) {
            $this->increment('api_errors_total', 1, ['status' => (string)$statusCode]);
        }
    }

    /**
     * Record authentication event
     */
    public function recordAuth(string $event, bool $success, ?string $userId = null): void
    {
        $this->increment('auth_events_total', 1, [
            'event' => $event,
            'success' => $success ? 'true' : 'false',
        ]);

        if (!$success && $event === 'login') {
            $this->increment('failed_logins_total');
            
            // Alert on repeated failures
            $failedLogins = $this->metrics['failed_logins_total'] ?? 0;
            if ($failedLogins > 10) {
                $this->alert('auth_failure_spike', "High number of failed logins: {$failedLogins}", 'critical');
            }
        }
    }

    /**
     * Record database query
     */
    public function recordQuery(string $query, float $duration): void
    {
        $this->increment('db_queries_total');
        $this->timing('db_query_duration', $duration);

        // Alert on slow queries
        if ($duration > 1000) {
            $this->alert('slow_query', "Slow query ({$duration}ms): " . substr($query, 0, 100), 'warning');
        }
    }

    // ============================================
    // ALERTING
    // ============================================

    /**
     * Send alert
     */
    public function alert(string $type, string $message, string $severity = 'warning'): void
    {
        $alert = [
            'type' => $type,
            'message' => $message,
            'severity' => $severity,
            'timestamp' => (new \DateTime())->format('c'),
            'service' => 'oxford-plataforma',
        ];

        $this->logger->log(
            $severity === 'critical' ? 'critical' : 'warning',
            "ALERT [{$type}]: {$message}",
            $alert
        );

        // Send to external alerting service (Slack, PagerDuty, etc.)
        $this->sendExternalAlert($alert);
    }

    /**
     * Send external alert (webhook)
     */
    private function sendExternalAlert(array $alert): void
    {
        $webhookUrl = $_ENV['ALERT_WEBHOOK_URL'] ?? null;
        
        if (!$webhookUrl || !$this->httpClient) {
            return;
        }

        try {
            $this->httpClient->request('POST', $webhookUrl, [
                'json' => $alert,
            ]);
        } catch (\Exception $e) {
            $this->logger->error("Failed to send alert: " . $e->getMessage());
        }
    }

    // ============================================
    // HEALTH CHECKS
    // ============================================

    /**
     * Get application health
     */
    public function getHealth(): array
    {
        $uptime = microtime(true) - $this->startTime;
        
        return [
            'status' => 'healthy',
            'uptime_seconds' => round($uptime, 2),
            'metrics_collected' => count($this->metrics),
            'timestamp' => (new \DateTime())->format('c'),
        ];
    }

    /**
     * Get all metrics
     */
    public function getMetrics(): array
    {
        return $this->metrics;
    }

    /**
     * Export metrics in Prometheus format
     */
    public function exportPrometheus(): string
    {
        $output = "# Oxford Plataforma Metrics\n";
        
        foreach ($this->metrics as $key => $value) {
            $output .= "{$key} {$value}\n";
        }
        
        return $output;
    }

    private function buildKey(string $name, array $tags = []): string
    {
        if (empty($tags)) {
            return $name;
        }
        
        $tagStr = implode(',', array_map(
            fn($k, $v) => "{$k}=\"{$v}\"",
            array_keys($tags),
            array_values($tags)
        ));
        
        return "{$name}{{$tagStr}}";
    }
}
