<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/reports', name: 'api_reports_')]
class ReportController extends AbstractController
{
    #[Route('/grades/{studentId}', name: 'grades', methods: ['GET'])]
    public function downloadGrades(int $studentId): JsonResponse
    {
        // Logic to generate PDF would go here.
        // For now, returning a success message to simulate the action.
        return $this->json(['status' => 'PDF Generated', 'link' => '/downloads/boleta_2025.pdf']);
    }

    #[Route('/certificates', name: 'certificates', methods: ['GET'])]
    public function downloadCertificates(): JsonResponse
    {
        return $this->json(['status' => 'Certificates Generated', 'link' => '/downloads/certificados_batch.zip']);
    }
}
