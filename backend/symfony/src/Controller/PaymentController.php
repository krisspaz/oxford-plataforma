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
        try {
            $dateStr = $request->query->get('date', date('Y-m-d'));
            $date = new \DateTime($dateStr);
        } catch (\Exception $e) {
            $date = new \DateTime();
        }

        $results = $this->paymentRepository->getTotalsByDate($date);
        
        $totals = [
            'date' => $date->format('Y-m-d'),
            'total' => 0,
            'cash' => 0,
            'card' => 0,
            'deposit' => 0,
            'transfer' => 0
        ];

        foreach ($results as $row) {
            $method = $row['method'] ?? 'unknown';
            $amount = (float)$row['total'];
            $totals['total'] += $amount;
            if (isset($totals[$method])) {
                $totals[$method] = $amount;
            }
        }

        return $this->json($totals);
    }

    #[Route('/pending/{studentId}', name: 'api_payments_pending', methods: ['GET'])]
    public function getPendingQuotas(int $studentId, \App\Repository\QuotaRepository $quotaRepository): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $quotas = $quotaRepository->findPendingByStudent($studentId);
        
        $data = array_map(fn($q) => [
            'id' => $q->getId(),
            'concept' => $q->getConcept(),
            'amount' => (float)$q->getPendingAmount(),
            'totalAmount' => (float)$q->getAmount(),
            'dueDate' => $q->getDueDate()->format('Y-m-d'),
            'status' => $q->getStatus()
        ], $quotas);

        return $this->json([
            'success' => true,
            'data' => $data
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
        
        $em->persist($payment);
        
        // Process quotas
        $quotaIds = $data['quotaIds'] ?? [];
        $remainingPayment = (float)$payment->getAmount();
        
        if (!empty($quotaIds)) {
            $quotas = $em->getRepository(\App\Entity\Quota::class)->findBy(['id' => $quotaIds]);
            
            // Sort by due date ASC to pay oldest first if multiple selected
            usort($quotas, fn($a, $b) => $a->getDueDate() <=> $b->getDueDate());
            
            foreach ($quotas as $quota) {
                if ($remainingPayment <= 0) break;
                
                $pending = (float)$quota->getPendingAmount();
                if ($pending <= 0) continue; // Already paid
                
                $toPay = min($remainingPayment, $pending);
                
                // Update Quota
                $currentPaid = (float)$quota->getPaidAmount();
                $quota->setPaidAmount((string)($currentPaid + $toPay));
                
                // If using Invoice, link it? Payment is linked to Quota implicitly?
                // Quota has 'invoice' field but maybe we should link Payment context?
                // For now, updating amount and status (handled by entity) is key.
                
                $remainingPayment -= $toPay;
                $em->persist($quota);
            }
        }

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
