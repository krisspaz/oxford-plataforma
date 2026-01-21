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
        
        $studentId = $data['studentId'] ?? $data['student'] ?? null;
        if (!$studentId) {
             return $this->json(['error' => 'Student ID is required'], Response::HTTP_BAD_REQUEST);
        }
        
        $student = $em->getRepository(\App\Entity\Student::class)->find($studentId);
        if (!$student) {
             return $this->json(['error' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }

        $payment = new Payment();
        $payment->setStudent($student);
        $payment->setAmount((string)($data['amount'] ?? 0));
        
        // Handle date
        try {
            $date = isset($data['date']) ? new \DateTime($data['date']) : new \DateTime();
            $payment->setPaymentDate($date);
        } catch (\Exception $e) {
            $payment->setPaymentDate(new \DateTime());
        }

        $payment->setStatus($data['status'] ?? 'PAID');
        $payment->setConcept($data['concept'] ?? 'Pago General');
        $payment->setMethod($data['method'] ?? 'cash');
        $payment->setType('RECEIPT'); // Default

        // Billing Info
        $payment->setBillingIdentifier($data['nit'] ?? 'CF');
        $payment->setBillingName($data['billingName'] ?? 'Consumidor Final');

        $em->persist($payment);
        $em->flush();

        return $this->json([
            'success' => true, 
            'message' => 'Payment registered successfully',
            'data' => [
                'id' => $payment->getId(),
                'series' => 'A',
                'number' => $payment->getId()
            ]
        ], 201);
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
    public function applyToQuotas(\Symfony\Component\HttpFoundation\Request $request, \Doctrine\ORM\EntityManagerInterface $em): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // $data should contain: studentId, total, method, nit, billingName (optional), quotaIds
        
        $student = $em->getRepository(\App\Entity\Student::class)->find($data['studentId'] ?? 0);
        if (!$student) {
             return $this->json(['error' => 'Student not found'], Response::HTTP_BAD_REQUEST);
        }

        $payment = new Payment();
        $payment->setStudent($student);
        $payment->setAmount((string)($data['total'] ?? 0));
        $payment->setPaymentDate(new \DateTime());
        $payment->setStatus('PAID');
        $payment->setConcept('Pago de Cuotas (' . count($data['quotaIds'] ?? []) . ')');
        $payment->setMethod($data['method'] ?? 'cash');
        
        // Billing Info
        $payment->setBillingIdentifier($data['nit'] ?? 'CF');
        $payment->setBillingName($data['billingName'] ?? 'Consumidor Final'); // Should ideally come from frontend or lookup
        $payment->setType(str_contains(strtoupper($data['nit'] ?? ''), 'CF') ? 'RECEIPT' : 'INVOICE');

        $em->persist($payment);
        
        // Process quotas... (Assuming simple marking for now as we don't have Quota entity logic fully exposed in snippet)
        // For now, allow the payment to be recorded.

        $em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Payment applied successfully',
            'data' => [
                 'series' => 'A',
                 'number' => $payment->getId(),
                 'total' => $payment->getAmount()
            ]
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
