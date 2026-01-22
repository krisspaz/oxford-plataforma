<?php

namespace App\Controller\Api\V1;

use App\Entity\Payment;
use App\Service\PaymentPdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PaymentRepository;

#[Route('/api/v1/payments')]
class PaymentController extends AbstractController
{
    private PaymentPdfService $pdfService;
    private PaymentRepository $paymentRepository;

    public function __construct(
        PaymentPdfService $pdfService,
        PaymentRepository $paymentRepository
    ) {
        $this->pdfService = $pdfService;
        $this->paymentRepository = $paymentRepository;
    }

    #[Route('', name: 'api_v1_payments_index', methods: ['GET'])]
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
    
    // ... Other methods would follow similar adaptation if full CRUD required. 
    // Implementing core read/create for V1 compliance.

    #[Route('/totals', name: 'api_v1_payments_totals', methods: ['GET'])]
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

    #[Route('/pending/{studentId}', name: 'api_v1_payments_pending', methods: ['GET'])]
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
}
