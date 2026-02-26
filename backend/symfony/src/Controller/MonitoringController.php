<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/monitoring')]
class MonitoringController extends AbstractController
{
    #[Route('/stats', methods: ['GET'])]
    public function stats(): JsonResponse
    {
        $diskTotal = disk_total_space('/');
        $diskFree = disk_free_space('/');
        $diskUsedPercent = $diskTotal > 0 ? (($diskTotal - $diskFree) / $diskTotal) * 100 : 0;

        $stats = [
            'cpu' => function_exists('sys_getloadavg') ? round(sys_getloadavg()[0], 2) : 0,
            'memory' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'disk' => round($diskUsedPercent, 2),
            'uptime' => 'Realtime',
            'activeUsers' => 1,
            'apiRequests' => 0,
            'errors' => 0
        ];

        return $this->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    #[Route('/logs', methods: ['GET'])]
    public function logs(): JsonResponse
    {
        // Implement actual log parsing here if needed in the future
        $logs = [];

        return $this->json([
            'success' => true,
            'data' => $logs
        ]);
    }
}
