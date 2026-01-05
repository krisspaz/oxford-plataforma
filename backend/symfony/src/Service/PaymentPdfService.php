<?php

namespace App\Service;

use App\Entity\Payment;
use Dompdf\Dompdf;
use Dompdf\Options;

class PaymentPdfService
{
    public function generateReceipt(Payment $payment): string
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($options);
        
        // Simple HTML template for the receipt
        // In a real app, render a Twig template here
        $html = sprintf('
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .header { text-align: center; margin-bottom: 20px; }
                    .details { margin-top: 20px; border: 1px solid #ccc; padding: 10px; }
                    .total { font-weight: bold; font-size: 1.2em; margin-top: 10px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Corpo Oxford</h1>
                    <h2>Recibo de Pago #%d</h2>
                </div>
                <div class="details">
                    <p><strong>Estudiante:</strong> %s %s</p>
                    <p><strong>Carnet:</strong> %s</p>
                    <p><strong>Fecha:</strong> %s</p>
                    <p><strong>Concepto:</strong> %s</p>
                    <hr>
                    <p class="total">Monto Total: Q %s</p>
                </div>
                <div style="margin-top: 50px; text-align: center; color: #777;">
                    <p>Gracias por su pago.</p>
                </div>
            </body>
            </html>
        ',
            $payment->getId(),
            $payment->getStudent()->getFirstName(),
            $payment->getStudent()->getLastName(),
            $payment->getStudent()->getCarnet(),
            $payment->getPaymentDate()->format('d/m/Y'),
            $payment->getConcept(),
            number_format($payment->getAmount(), 2)
        );

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
