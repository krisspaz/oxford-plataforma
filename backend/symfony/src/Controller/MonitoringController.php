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
        // Mock System Stats
        // In production, these could come from actual system calls or specialized libraries
        
        $stats = [
            'cpu' => rand(5, 30),
            'memory' => rand(40, 60),
            'disk' => 45, // % used
            'uptime' => '12d 4h 32m',
            'activeUsers' => rand(15, 50),
            'apiRequests' => rand(1000, 5000), // Today
            'errors' => rand(0, 5) // Today's error count
        ];

        return $this->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    #[Route('/logs', methods: ['GET'])]
    public function logs(): JsonResponse
    {
        // Mock Logs
        $logs = [
            ['id' => 1, 'level' => 'INFO', 'message' => 'User login successful (user_id: 45)', 'timestamp' => date('Y-m-d H:i:s', strtotime('-2 minutes'))],
            ['id' => 2, 'level' => 'WARNING', 'message' => 'Failed login attempt from IP 192.168.1.5', 'timestamp' => date('Y-m-d H:i:s', strtotime('-15 minutes'))],
            ['id' => 3, 'level' => 'ERROR', 'message' => 'Payment gateway timeout (Transaction #5544)', 'timestamp' => date('Y-m-d H:i:s', strtotime('-1 hour'))],
            ['id' => 4, 'level' => 'INFO', 'message' => 'Daily backup completed successfully', 'timestamp' => date('Y-m-d H:i:s', strtotime('-4 hours'))],
            ['id' => 5, 'level' => 'INFO', 'message' => 'New student enrollment (ID: 102)', 'timestamp' => date('Y-m-d H:i:s', strtotime('-5 hours'))],
        ];

        return $this->json([
            'success' => true,
            'data' => $logs
        ]);
    }
}
