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
