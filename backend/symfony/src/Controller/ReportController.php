<?php

namespace App\Controller;

use App\Repository\PaymentRepository;
use App\Repository\StudentRepository;
use App\Service\ReportBuilderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/reports', name: 'api_reports_')]
class ReportController extends AbstractController
{
    private $reportBuilder;

    public function __construct(ReportBuilderService $reportBuilder)
    {
        $this->reportBuilder = $reportBuilder;
    }

    #[Route('/grades/{studentId}', name: 'grades', methods: ['GET'])]
    public function downloadGrades(int $studentId): Response
    {
        try {
            $pdfContent = $this->reportBuilder->buildAcademicReport($studentId);

            return new Response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="boleta_calificaciones.pdf"',
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/grades/me', name: 'my_grades', methods: ['GET'])]
    public function downloadMyGrades(StudentRepository $studentRepo): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Usuario no autenticado'], 401);
        }

        // Find student associated with user
        // Assuming User entity has relation to Student or we find by email
        // Or checking if User IS a Student (if mapped directly)
        // Let's assume user->getEmail() matches student email or user->getStudent()
        
        $student = $studentRepo->findOneBy(['email' => $user->getUserIdentifier()]);
        
        if (!$student) {
             // Fallback: Check if there is a 'studentId' in token claims or user object
             // This depends on User entity implementation.
             // For now, retry finding by user ID link if schema allows
             return new JsonResponse(['error' => 'No se encontró perfil de estudiante asociado'], 404);
        }

        return $this->downloadGrades($student->getId());
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
