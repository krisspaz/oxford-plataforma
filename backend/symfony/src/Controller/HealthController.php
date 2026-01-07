<?php

namespace App\Controller;

use App\Service\MetricsService;
use App\Service\AuditService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Health and Metrics Endpoints
 * ============================
 * For monitoring, load balancers, and observability
 */
#[Route('/api/health')]
class HealthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private MetricsService $metrics,
        private AuditService $audit,
    ) {}

    /**
     * Basic health check - for load balancers
     */
    #[Route('', name: 'health_check', methods: ['GET'])]
    public function health(): JsonResponse
    {
        return $this->json([
            'status' => 'healthy',
            'timestamp' => (new \DateTime())->format('c'),
        ]);
    }

    /**
     * Detailed health check - for monitoring dashboards
     */
    #[Route('/detailed', name: 'health_detailed', methods: ['GET'])]
    public function detailedHealth(): JsonResponse
    {
        $checks = [];

        // Database check
        try {
            $this->em->getConnection()->executeQuery('SELECT 1');
            $checks['database'] = ['status' => 'healthy'];
        } catch (\Exception $e) {
            $checks['database'] = ['status' => 'unhealthy', 'error' => $e->getMessage()];
        }

        // Redis check (if available)
        try {
            $redis = new \Redis();
            $redis->connect($_ENV['REDIS_HOST'] ?? 'localhost', (int)($_ENV['REDIS_PORT'] ?? 6379));
            $redis->ping();
            $checks['redis'] = ['status' => 'healthy'];
        } catch (\Exception $e) {
            $checks['redis'] = ['status' => 'unavailable', 'note' => 'Optional service'];
        }

        // Memory usage
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $memoryPercent = $memoryLimit > 0 ? round(($memoryUsage / $memoryLimit) * 100, 2) : 0;

        $checks['memory'] = [
            'used_bytes' => $memoryUsage,
            'limit_bytes' => $memoryLimit,
            'percent' => $memoryPercent,
            'status' => $memoryPercent > 90 ? 'warning' : 'healthy',
        ];

        // Disk space
        $diskFree = disk_free_space('/');
        $diskTotal = disk_total_space('/');
        $diskPercent = round((($diskTotal - $diskFree) / $diskTotal) * 100, 2);

        $checks['disk'] = [
            'free_bytes' => $diskFree,
            'total_bytes' => $diskTotal,
            'percent_used' => $diskPercent,
            'status' => $diskPercent > 90 ? 'warning' : 'healthy',
        ];

        // Overall status
        $overallStatus = 'healthy';
        foreach ($checks as $check) {
            if (($check['status'] ?? '') === 'unhealthy') {
                $overallStatus = 'unhealthy';
                break;
            }
            if (($check['status'] ?? '') === 'warning') {
                $overallStatus = 'degraded';
            }
        }

        return $this->json([
            'status' => $overallStatus,
            'checks' => $checks,
            'version' => $_ENV['APP_VERSION'] ?? '1.0.0',
            'environment' => $_ENV['APP_ENV'] ?? 'unknown',
            'timestamp' => (new \DateTime())->format('c'),
        ]);
    }

    /**
     * Prometheus metrics endpoint
     */
    #[Route('/metrics', name: 'health_metrics', methods: ['GET'])]
    public function metrics(): Response
    {
        $content = $this->metrics->exportPrometheus();
        
        return new Response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }

    /**
     * Readiness probe - for Kubernetes
     */
    #[Route('/ready', name: 'health_ready', methods: ['GET'])]
    public function ready(): JsonResponse
    {
        try {
            // Quick DB check
            $this->em->getConnection()->executeQuery('SELECT 1');
            
            return $this->json(['status' => 'ready']);
        } catch (\Exception $e) {
            return $this->json(['status' => 'not_ready', 'reason' => 'database'], 503);
        }
    }

    /**
     * Liveness probe - for Kubernetes
     */
    #[Route('/live', name: 'health_live', methods: ['GET'])]
    public function live(): JsonResponse
    {
        return $this->json(['status' => 'alive']);
    }

    private function parseMemoryLimit(string $limit): int
    {
        $unit = strtolower(substr($limit, -1));
        $value = (int)substr($limit, 0, -1);

        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => (int)$limit,
        };
    }
}
