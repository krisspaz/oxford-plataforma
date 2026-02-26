<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use App\Repository\PackageRepository;
use App\Repository\ScholarshipRepository;
use App\Repository\StudentRepository;
use App\Repository\SchoolCycleRepository;
use App\Entity\Enrollment;
use App\Repository\EnrollmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/financial')]
class FinancialController extends AbstractController
{
    #[Route('/daily-cut', name: 'api_financial_daily_cut', methods: ['GET'])]
    public function getDailyCut(Request $request, InvoiceRepository $invoiceRepo): JsonResponse
    {
        $dateFrom = $request->query->get('from');
        $dateTo = $request->query->get('to');

        if (!$dateFrom || !$dateTo) {
            return $this->json(['error' => 'Dates required'], 400);
        }

        $from = new \DateTime($dateFrom . ' 00:00:00');
        $to = new \DateTime($dateTo . ' 23:59:59');

        // Fetch Invoices
        $invoices = $invoiceRepo->createQueryBuilder('i')
            ->where('i.issueDate BETWEEN :from AND :to')
            ->andWhere("i.status != 'ANULADO'")
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('i.issueDate', 'DESC')
            ->getQuery()
            ->getResult();

        $payments = [];
        $totals = ['efectivo' => 0, 'tarjeta' => 0, 'deposito' => 0, 'total' => 0];

        foreach ($invoices as $inv) {
            $amount = (float) $inv->getTotal();
            $method = $inv->getPaymentMethod() ?? 'Efectivo';
            
            $payments[] = [
                'id' => $inv->getId(),
                'name' => $inv->getRecipientName(),
                'products' => 'Pago Ref: ' . $inv->getId(), // Idealmente, listar detalles
                'method' => $method,
                'series' => $inv->getSeries(),
                'number' => $inv->getNumber(),
                'total' => $amount
            ];

            // Normalize method for summing
            $methodKey = strtolower($method);
            if (isset($totals[$methodKey])) {
                $totals[$methodKey] += $amount;
            } else {
                // Fallback mapping
                if (str_contains($methodKey, 'tarjeta')) $totals['tarjeta'] += $amount;
                elseif (str_contains($methodKey, 'dep')) $totals['deposito'] += $amount;
                else $totals['efectivo'] += $amount;
            }
            $totals['total'] += $amount;
        }

        return $this->json([
            'payments' => $payments,
            'totals' => $totals
        ]);
    }

    #[Route('/assign-package', name: 'api_financial_assign_package', methods: ['POST'])]
    public function assignPackage(
        Request $request, 
        StudentRepository $studentRepo, 
        PackageRepository $packageRepo,
        SchoolCycleRepository $cycleRepo,
        EntityManagerInterface $em,
        EnrollmentRepository $enrollmentRepo
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $studentId = $data['studentId'] ?? null;
        $packageId = $data['packageId'] ?? null;
        $cycleId = $data['cycleId'] ?? null;

        if (!$studentId || !$packageId) {
            return $this->json(['error' => 'Missing parameters'], 400);
        }

        $student = $studentRepo->find($studentId);
        $package = $packageRepo->find($packageId);

        if (!$student || !$package) {
            return $this->json(['error' => 'Not found'], 404);
        }

        // Find or Create Enrollment
        // Assuming cycle is current if not provided
        $enrollment = $enrollmentRepo->findOneBy(['student' => $student]); // Simplification
        
        if (!$enrollment) {
             return $this->json(['error' => 'Student not enrolled yet (No enrollment found)'], 400);
        }

        $enrollment->setPackage($package);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Package assigned']);
    }
}
