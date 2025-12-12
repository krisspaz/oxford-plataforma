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

    public function __construct(PaymentPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
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
