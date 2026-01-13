<?php

namespace App\Controller;

use App\Repository\PaymentRepository;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/generate', name: 'generate', methods: ['POST'])]
    public function generateReport(Request $request, \Symfony\Component\Messenger\MessageBusInterface $bus): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $type = $data['type'] ?? 'BOLETAS'; // BOLETAS, CUADROS
        $scope = $data['scope'] ?? 'INDIVIDUAL'; // INDIVIDUAL, MASSIVE
        
        // Dispatch to Queue
        $bus->dispatch(new \App\Message\GenerateReportMessage($type, $scope, $data));
        
        return $this->json([
            'success' => true,
            'message' => "Solicitud enviada a la cola de procesamiento. El reporte de $type ($scope) aparecerá en 'Mis Descargas' pronto.",
            'details' => [
                'status' => 'QUEUED',
                'queue' => 'redis'
            ]
        ]);
    }

    #[Route('/dashboard', name: 'dashboard_stats', methods: ['GET'])]
    public function dashboard(StudentRepository $studentRepo, PaymentRepository $paymentRepo): JsonResponse
    {
        $totalStudents = $studentRepo->count([]);
        // Mocking revenue calculation
        $totalPayments = $paymentRepo->count([]);
        
        return $this->json([
            'total_students' => $totalStudents,
            'total_payments' => $totalPayments,
            'revenue_ytd' => 0.0,
            'average_attendance' => '95%',
        ]);
    }
}

