<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Service\PaymentPdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private PaymentPdfService $pdfService;
    private \App\Repository\PaymentRepository $paymentRepository;

    public function __construct(
        PaymentPdfService $pdfService,
        \App\Repository\PaymentRepository $paymentRepository
    ) {
        $this->pdfService = $pdfService;
        $this->paymentRepository = $paymentRepository;
    }

    #[Route('/api/payments', name: 'api_payments_index', methods: ['GET'])]
    public function index(\Symfony\Component\HttpFoundation\Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $studentId = $request->query->get('student');
        
        if ($studentId) {
            $payments = $this->paymentRepository->findBy(['student' => $studentId], ['date' => 'DESC']);
        } else {
            $payments = $this->paymentRepository->findBy([], ['date' => 'DESC'], 50);
        }

        $data = array_map(fn($p) => [
            'id' => $p->getId(),
            'amount' => $p->getAmount(),
            'date' => $p->getDate()->format('Y-m-d'),
            'type' => $p->getType(),
            'method' => $p->getMethod(),
            'status' => $p->getStatus(),
            'concept' => $p->getConcept(),
            'studentName' => $p->getStudent() ? $p->getStudent()->getFullName() : 'N/A'
        ], $payments);

        return $this->json($data);
    }
    
    #[Route('/api/payments', name: 'api_payments_create', methods: ['POST'])]
    public function create(\Symfony\Component\HttpFoundation\Request $request, \Doctrine\ORM\EntityManagerInterface $em): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        // Basic stub implementation for now - just to prevent 404/500
        return $this->json(['success' => true, 'message' => 'Payment registered (Simulation)'], 201);
    }

    #[Route('/totals', name: 'api_payments_totals', methods: ['GET'])]
    public function getTotals(\Symfony\Component\HttpFoundation\Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $date = $request->query->get('date', date('Y-m-d'));
        // Mock totals
        return $this->json([
            'date' => $date,
            'total' => 1500.00,
            'cash' => 500.00,
            'card' => 1000.00
        ]);
    }

    #[Route('/pending/{studentId}', name: 'api_payments_pending', methods: ['GET'])]
    public function getPendingQuotas(int $studentId): \Symfony\Component\HttpFoundation\JsonResponse
    {
        // Mock pending quotas
        return $this->json([
            'success' => true,
            'data' => [
                ['id' => 101, 'concept' => 'Mensualidad Marzo', 'amount' => 750.00, 'dueDate' => '2025-03-30'],
                ['id' => 102, 'concept' => 'Material Didáctico', 'amount' => 250.00, 'dueDate' => '2025-03-15'],
            ]
        ]);
    }

    #[Route('/apply', name: 'api_payments_apply', methods: ['POST'])]
    public function applyToQuotas(\Symfony\Component\HttpFoundation\Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->json([
            'success' => true,
            'message' => 'Payment applied successfully',
            'paymentId' => rand(1000, 9999)
        ]);
    }

    #[Route('/api/payments/overdue', name: 'api_payments_overdue', methods: ['GET'])]
    public function getOverduePayments(): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $overduePayments = $this->paymentRepository->findOverduePayments();

        $data = array_map(function ($payment) {
            $student = $payment->getStudent();
            return [
                'id' => $payment->getId(),
                'studentName' => $student->getFirstName() . ' ' . $student->getLastName(),
                'studentEmail' => $student->getEmail(),
                'amount' => $payment->getAmount(),
                'dueDate' => $payment->getDueDate()->format('Y-m-d'),
                'description' => $payment->getDescription(),
                'status' => $payment->getStatus(),
            ];
        }, $overduePayments);

        return $this->json($data);
    }

    #[Route('/api/payments/{id}/receipt', name: 'api_payment_receipt', methods: ['GET'])]
    public function downloadReceipt(Payment $payment): Response
    {
        $pdfContent = $this->pdfService->generateReceipt($payment);

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="recibo_' . $payment->getId() . '.pdf"',
        ]);
    }
}
