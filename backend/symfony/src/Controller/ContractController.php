<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Repository\ContractRepository;
use App\Repository\StudentRepository;
use App\Repository\SchoolCycleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/contracts', name: 'api_contracts_')]
class ContractController extends AbstractController
{
    private $entityManager;
    private $contractRepository;
    private $studentRepository;
    private $cycleRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ContractRepository $contractRepository,
        StudentRepository $studentRepository,
        SchoolCycleRepository $cycleRepository
    ) {
        $this->entityManager = $entityManager;
        $this->contractRepository = $contractRepository;
        $this->studentRepository = $studentRepository;
        $this->cycleRepository = $cycleRepository;
    }

    #[Route('/generate', name: 'generate', methods: ['POST'])]
    public function generate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $studentId = $data['studentId'] ?? null;
        $cycleYear = $data['cycle'] ?? date('Y');

        if (!$studentId) {
            return $this->json(['error' => 'Student ID is required'], 400);
        }

        $student = $this->studentRepository->find($studentId);
        if (!$student) {
            return $this->json(['error' => 'Student not found'], 404);
        }

        // Find cycle by name/year (assuming logic to find cycle, fallback to current)
        // For now finding any cycle matching the year or defaulting to first active
        $cycles = $this->cycleRepository->findAll(); 
        $cycle = null;
        foreach ($cycles as $c) {
            if (str_contains($c->getName(), $cycleYear)) {
                $cycle = $c;
                break;
            }
        }
        
        if (!$cycle && count($cycles) > 0) $cycle = $cycles[0]; // Fallback

        if (!$cycle) {
             return $this->json(['error' => 'School Cycle not found'], 404);
        }

        // Check if contract already exists
        $existing = $this->contractRepository->findOneBy(['student' => $student, 'schoolCycle' => $cycle]);
        if ($existing) {
             return $this->json([
                'status' => 'exists',
                'contract' => [
                    'id' => $existing->getId(),
                    'status' => $existing->getStatus(),
                    'filePath' => $existing->getFilePath()
                ]
            ]);
        }

        $contract = new Contract();
        $contract->setStudent($student);
        $contract->setSchoolCycle($cycle);
        $contract->setStatus('PENDING'); // PENDING_SIGNATURE
        // Mock PDF path
        $contract->setFilePath('/contracts/' . $cycleYear . '/' . $student->getId() . '_contract.pdf');
        $contract->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($contract);
        $this->entityManager->flush();

        return $this->json([
            'status' => 'created', 
            'contract' => [
                'id' => $contract->getId(),
                'status' => $contract->getStatus(),
                'filePath' => $contract->getFilePath()
            ]
        ], 201);
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        // Custom list endpoint if API Platform isn't enough or need custom serialization
        $contracts = $this->contractRepository->findAll();
        $result = [];
        
        foreach ($contracts as $c) {
            $result[] = [
                'id' => $c->getId(),
                'student' => $c->getStudent()->getName() . ' ' . $c->getStudent()->getLastname(),
                'cycle' => $c->getSchoolCycle() ? $c->getSchoolCycle()->getName() : 'N/A',
                'status' => $c->getStatus(),
                'date' => $c->getCreatedAt()->format('Y-m-d')
            ];
        }

        return $this->json($result);
    }
}
