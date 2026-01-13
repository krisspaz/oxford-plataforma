<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Connection;
use Aws\S3\S3Client;
use Redis;

#[Route('/api/system')]
class SystemStatusController extends AbstractController
{
    private Connection $connection;
    private S3Client $s3Client;
    private Redis $redis;

    public function __construct(Connection $connection, S3Client $minioS3Client, Redis $redis)
    {
        $this->connection = $connection;
        $this->s3Client = $minioS3Client;
        $this->redis = $redis;
    }

    #[Route('/status', name: 'system_status', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'queues' => $this->getQueueStatus(),
            'services' => $this->getServiceStatus(),
            'timestamp' => time()
        ]);
    }

    private function getQueueStatus(): array
    {
        // Check 'messages' stream used by Messenger
        try {
            // Redis Stream length
            $pending = $this->redis->xLen('messages');
        } catch (\Exception $e) {
            $pending = -1; // Error
        }

        return [
            'async' => $pending,
            'failed' => 0 // Would need to query Failed transport specific queue
        ];
    }

    private function getServiceStatus(): array
    {
        $status = [
            'database' => 'down',
            'minio' => 'down',
            'redis' => 'down'
        ];

        // 1. Database
        try {
            if ($this->connection->executeQuery('SELECT 1')->fetchOne()) {
                $status['database'] = 'up';
            }
        } catch (\Exception $e) {}

        // 2. MinIO
        try {
             // List buckets as a ping
            $this->s3Client->listBuckets();
            $status['minio'] = 'up';
        } catch (\Exception $e) {}

        // 3. Redis
        try {
            if ($this->redis->ping()) {
                $status['redis'] = 'up';
            }
        } catch (\Exception $e) {}

        return $status;
    }
}
