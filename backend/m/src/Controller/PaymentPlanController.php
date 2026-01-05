<?php

namespace App\Controller;

use App\Repository\QuotaRepository;
use App\Repository\PaymentPlanRepository;
use App\Entity\PaymentPlan;
use App\Entity\Quota;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/payment-plans')]
class PaymentPlanController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private PaymentPlanRepository $planRepository,
        private QuotaRepository $quotaRepository
    ) {}

    #[Route('/student/{studentId}', methods: ['GET'])]
    public function getByStudent(int $studentId): JsonResponse
    {
        $plans = $this->planRepository->findByStudent($studentId);

        return $this->json([
            'success' => true,
            'data' => array_map(fn($plan) => $this->serializePlan($plan), $plans)
        ]);
    }

    #[Route('/student/{studentId}/pending', methods: ['GET'])]
    public function getPendingQuotas(int $studentId): JsonResponse
    {
        $quotas = $this->quotaRepository->findPendingByStudent($studentId);

        return $this->json([
            'success' => true,
            'data' => array_map(fn($quota) => $this->serializeQuota($quota), $quotas)
        ]);
    }

    #[Route('/insolvents', methods: ['GET'])]
    public function getInsolvents(): JsonResponse
    {
        $today = new \DateTime('today');
        
        // Find overdue quotas
        $qb = $this->quotaRepository->createQueryBuilder('q')
            ->leftJoin('q.paymentPlan', 'p')
            ->leftJoin('p.student', 's')
            ->where('q.dueDate < :today')
            ->andWhere('q.status != :paidStatus')
            ->andWhere('q.pendingAmount > 0')
            ->setParameter('today', $today)
            ->setParameter('paidStatus', 'PAID')
            ->orderBy('q.dueDate', 'ASC')
            ->addSelect('p') 
            ->addSelect('s'); 

        $quotas = $qb->getQuery()->getResult();

        $data = array_map(function ($q) {
            $plan = $q->getPaymentPlan();
            $student = $plan->getStudent();
            $name = $student->getFirstName() . ' ' . $student->getLastName();
            
            return [
                'id' => $q->getId(),
                'studentId' => $student->getId(),
                'studentName' => $name,
                'studentEmail' => $student->getEmail(),
                'description' => $q->getConcept(),
                'dueDate' => $q->getDueDate()->format('Y-m-d'),
                'amount' => (float) $q->getPendingAmount(),
            ];
        }, $quotas);

        return $this->json([
            'success' => true,
            'data' => $data
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $plan = new PaymentPlan();
        $plan->setTotalAmount($data['totalAmount']);
        $plan->setDownPayment($data['downPayment'] ?? '0.00');
        $plan->setNumberOfQuotas($data['numberOfQuotas']);
        $plan->setQuotaAmount($data['quotaAmount']);
        $plan->setNotes($data['notes'] ?? null);

        // Generate quotas
        $startDate = new \DateTime($data['startDate'] ?? 'now');
        $remaining = bcsub($data['totalAmount'], $data['downPayment'] ?? '0.00', 2);
        $quotaAmount = bcdiv($remaining, (string)$data['numberOfQuotas'], 2);

        for ($i = 1; $i <= $data['numberOfQuotas']; $i++) {
            $quota = new Quota();
            $quota->setQuotaNumber($i);
            $quota->setConcept('Cuota ' . $i . ' de ' . $data['numberOfQuotas']);
            $quota->setAmount($quotaAmount);
            $quota->setDueDate((clone $startDate)->modify('+' . ($i - 1) . ' months'));
            $quota->setDocumentType($data['documentType'] ?? 'RECIBO_SAT');
            
            $plan->addQuota($quota);
        }

        $this->em->persist($plan);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'data' => $this->serializePlan($plan)
        ], 201);
    }

    #[Route('/quotas/{id}/pay', methods: ['POST'])]
    public function payQuota(int $id, Request $request): JsonResponse
    {
        $quota = $this->quotaRepository->find($id);
        
        if (!$quota) {
            return $this->json(['error' => 'Quota not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $amount = $data['amount'];

        $newPaid = bcadd($quota->getPaidAmount(), $amount, 2);
        $quota->setPaidAmount($newPaid);

        $this->em->flush();

        return $this->json([
            'success' => true,
            'data' => $this->serializeQuota($quota)
        ]);
    }

    private function serializePlan(PaymentPlan $plan): array
    {
        return [
            'id' => $plan->getId(),
            'totalAmount' => $plan->getTotalAmount(),
            'downPayment' => $plan->getDownPayment(),
            'numberOfQuotas' => $plan->getNumberOfQuotas(),
            'quotaAmount' => $plan->getQuotaAmount(),
            'status' => $plan->getStatus(),
            'paidAmount' => $plan->getPaidAmount(),
            'pendingAmount' => $plan->getPendingAmount(),
            'quotas' => array_map(fn($q) => $this->serializeQuota($q), $plan->getQuotas()->toArray()),
            'createdAt' => $plan->getCreatedAt()->format('Y-m-d'),
        ];
    }

    private function serializeQuota(Quota $quota): array
    {
        return [
            'id' => $quota->getId(),
            'quotaNumber' => $quota->getQuotaNumber(),
            'concept' => $quota->getConcept(),
            'amount' => $quota->getAmount(),
            'paidAmount' => $quota->getPaidAmount(),
            'pendingAmount' => $quota->getPendingAmount(),
            'dueDate' => $quota->getDueDate()->format('Y-m-d'),
            'paidDate' => $quota->getPaidDate()?->format('Y-m-d'),
            'status' => $quota->getStatus(),
            'isOverdue' => $quota->isOverdue(),
            'documentType' => $quota->getDocumentType(),
        ];
    }
}
