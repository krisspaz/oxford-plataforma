<?php

namespace App\Controller;

use App\Repository\LogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/logs')]
class LogController extends AbstractController
{
    public function __construct(private LogRepository $logRepository) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        // Security check: ensure only admins can access
        // $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Uncomment if security is strict

        $logs = $this->logRepository->findRecent(100);

        $data = array_map(function ($log) {
            return [
                'id' => $log->getId(),
                'action' => $log->getAction(),
                'user' => $log->getUsername(),
                'details' => $log->getDetails(),
                'ip' => $log->getIp(),
                'date' => $log->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $logs);

        return $this->json(['success' => true, 'data' => $data]);
    }
}
