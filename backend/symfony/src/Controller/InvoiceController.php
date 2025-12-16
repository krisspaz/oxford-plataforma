<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use App\Service\CorpoSistemasService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/invoices')]
class InvoiceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private InvoiceRepository $invoiceRepository,
        private CorpoSistemasService $corpoSistemas
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $type = $request->query->get('type');
        $status = $request->query->get('status');
        $dateFrom = $request->query->get('dateFrom');
        $dateTo = $request->query->get('dateTo');
        
        $criteria = [];
        if ($type) $criteria['documentType'] = $type;
        if ($status) $criteria['status'] = $status;
        
        $invoices = $this->invoiceRepository->findBy($criteria, ['issuedAt' => 'DESC'], 100);
        
        return $this->json([
            'success' => true,
            'data' => array_map(fn($i) => $this->serializeInvoice($i), $invoices)
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $invoice = $this->invoiceRepository->find($id);
        
        if (!$invoice) {
            return $this->json(['success' => false, 'error' => 'Comprobante no encontrado'], 404);
        }
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeInvoice($invoice)
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $invoice = new Invoice();
        $invoice->setDocumentType($data['documentType'] ?? Invoice::TYPE_RECIBO_SAT);
        $invoice->setRecipientNit($data['nit'] ?? 'CF');
        $invoice->setRecipientName($data['name'] ?? 'Consumidor Final');
        $invoice->setTotalAmount($data['total'] ?? 0);
        $invoice->setIssuedAt(new \DateTime());
        $invoice->setStatus(Invoice::STATUS_EMITIDO);
        
        // Si es documento SAT, enviar a CorpoSistemas
        if (in_array($invoice->getDocumentType(), [Invoice::TYPE_FACTURA_SAT, Invoice::TYPE_RECIBO_SAT])) {
            $result = $invoice->getDocumentType() === Invoice::TYPE_FACTURA_SAT
                ? $this->corpoSistemas->emitirFactura($data)
                : $this->corpoSistemas->emitirRecibo($data);
            
            if ($result['success']) {
                $invoice->setUuid($result['uuid']);
                $invoice->setSeries($result['serie']);
                $invoice->setNumber($result['numero']);
                $invoice->setSatResponse(json_encode($result['raw'] ?? []));
            } else {
                return $this->json(['success' => false, 'error' => $result['error']], 400);
            }
        } else {
            // Recibo interno
            $invoice->setSeries('RI');
            $invoice->setNumber((string) time());
        }
        
        $this->em->persist($invoice);
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeInvoice($invoice)
        ], 201);
    }

    #[Route('/{id}/annul', methods: ['POST'])]
    public function annul(int $id, Request $request): JsonResponse
    {
        $invoice = $this->invoiceRepository->find($id);
        
        if (!$invoice) {
            return $this->json(['success' => false, 'error' => 'Comprobante no encontrado'], 404);
        }
        
        if ($invoice->getStatus() === Invoice::STATUS_ANULADO) {
            return $this->json(['success' => false, 'error' => 'Documento ya está anulado'], 400);
        }
        
        $data = json_decode($request->getContent(), true);
        $reason = $data['reason'] ?? 'Anulación solicitada';
        
        // Si es documento SAT, anular en CorpoSistemas
        if ($invoice->getUuid()) {
            $result = $this->corpoSistemas->anularDocumento($invoice->getUuid(), $reason);
            if (!$result['success']) {
                return $this->json(['success' => false, 'error' => $result['error']], 400);
            }
        }
        
        $invoice->annul($reason, $this->getUser()?->getUserIdentifier() ?? 'system');
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeInvoice($invoice)
        ]);
    }

    #[Route('/corte-dia', methods: ['GET'])]
    public function corteDia(Request $request): JsonResponse
    {
        $date = $request->query->get('date', date('Y-m-d'));
        
        // Buscar pagos del día
        $invoices = $this->invoiceRepository->findByDate($date);
        
        $totals = [
            'efectivo' => 0,
            'tarjeta' => 0,
            'deposito' => 0,
            'total' => 0,
        ];
        
        foreach ($invoices as $invoice) {
            if ($invoice->getStatus() === Invoice::STATUS_EMITIDO) {
                // TODO: Agregar método de pago al invoice
                $totals['total'] += $invoice->getTotalAmount();
            }
        }
        
        return $this->json([
            'success' => true,
            'date' => $date,
            'totals' => $totals,
            'invoices' => array_map(fn($i) => $this->serializeInvoice($i), $invoices)
        ]);
    }

    private function serializeInvoice(Invoice $i): array
    {
        return [
            'id' => $i->getId(),
            'uuid' => $i->getUuid(),
            'type' => $i->getDocumentType(),
            'series' => $i->getSeries(),
            'number' => $i->getNumber(),
            'nit' => $i->getRecipientNit(),
            'name' => $i->getRecipientName(),
            'total' => $i->getTotalAmount(),
            'status' => $i->getStatus(),
            'issuedAt' => $i->getIssuedAt()?->format('Y-m-d H:i:s'),
            'annulledAt' => $i->getAnnulledAt()?->format('Y-m-d H:i:s'),
            'annulmentReason' => $i->getAnnulmentReason(),
        ];
    }
}
