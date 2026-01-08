<?php

namespace App\Tests\Service;

use App\Service\MetricsService;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class MetricsServiceTest extends TestCase
{
    private MetricsService $metricsService;

    protected function setUp(): void
    {
        $this->metricsService = new MetricsService(new NullLogger());
    }

    public function testIncrement(): void
    {
        $this->metricsService->increment('test_counter');
        $this->metricsService->increment('test_counter');
        $this->metricsService->increment('test_counter', 5);
        
        $metrics = $this->metricsService->getMetrics();
        $this->assertEquals(7, $metrics['test_counter']);
    }

    public function testGauge(): void
    {
        $this->metricsService->gauge('memory_usage', 1024.5);
        
        $metrics = $this->metricsService->getMetrics();
        $this->assertEquals(1024.5, $metrics['memory_usage']);
    }

    public function testTiming(): void
    {
        $this->metricsService->timing('api_request', 150.5);
        
        $metrics = $this->metricsService->getMetrics();
        $this->assertEquals(150.5, $metrics['api_request_timing']);
    }

    public function testTimeCallable(): void
    {
        $result = $this->metricsService->time('test_operation', function() {
            usleep(1000); // 1ms
            return 'done';
        });
        
        $this->assertEquals('done', $result);
        
        $metrics = $this->metricsService->getMetrics();
        $this->assertArrayHasKey('test_operation_timing', $metrics);
        $this->assertGreaterThan(0, $metrics['test_operation_timing']);
    }

    public function testRecordApiRequest(): void
    {
        $this->metricsService->recordApiRequest('/api/users', 'GET', 200, 50.0);
        
        $metrics = $this->metricsService->getMetrics();
        $this->assertArrayHasKey('api_requests_total{endpoint="/api/users",method="GET",status="200"}', $metrics);
    }

    public function testRecordAuth(): void
    {
        $this->metricsService->recordAuth('login', true, '1');
        $this->metricsService->recordAuth('login', false, '2');
        
        $metrics = $this->metricsService->getMetrics();
        $this->assertArrayHasKey('auth_events_total{event="login",success="true"}', $metrics);
        $this->assertArrayHasKey('auth_events_total{event="login",success="false"}', $metrics);
    }

    public function testGetHealth(): void
    {
        $health = $this->metricsService->getHealth();
        
        $this->assertEquals('healthy', $health['status']);
        $this->assertArrayHasKey('uptime_seconds', $health);
        $this->assertArrayHasKey('timestamp', $health);
    }

    public function testExportPrometheus(): void
    {
        $this->metricsService->increment('requests', 10);
        $this->metricsService->gauge('memory', 512);
        
        $export = $this->metricsService->exportPrometheus();
        
        $this->assertStringContainsString('requests 10', $export);
        $this->assertStringContainsString('memory 512', $export);
    }

    public function testTaggedMetrics(): void
    {
        $this->metricsService->increment('http_requests', 1, ['method' => 'GET', 'status' => '200']);
        
        $metrics = $this->metricsService->getMetrics();
        $this->assertArrayHasKey('http_requests{method="GET",status="200"}', $metrics);
    }
}
