<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin')]
class AdminController extends AbstractController
{
    #[Route('/logs', name: 'admin_logs', methods: ['GET'])]
    public function __construct(
        private \App\Repository\LogRepository $logRepository
    ) {}

    #[Route('/logs', name: 'admin_logs', methods: ['GET'])]
    public function logs(): JsonResponse
    {
        $logs = $this->logRepository->findBy([], ['createdAt' => 'DESC'], 50);
        
        $data = array_map(function (\App\Entity\Log $log) {
            return [
                'id' => $log->getId(),
                'date' => $log->getCreatedAt()?->format('Y-m-d H:i:s'),
                'action' => $log->getAction(),
                'user' => $log->getUsername(),
                'ip' => $log->getIp(),
                'details' => $log->getDetails()
            ];
        }, $logs);

        return $this->json($data);
    }
}
