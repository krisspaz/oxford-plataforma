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

    #[Route('', name: 'list', methods: ['GET'])]
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

        return $this->json([
            'success' => true,
            'data' => $result
        ]);
    }

    #[Route('/{id}/download', name: 'download', methods: ['GET'])]
    public function downloadPdf(int $id): JsonResponse
    {
        $contract = $this->contractRepository->find($id);
        
        if (!$contract) {
            return $this->json(['success' => false, 'error' => 'Contrato no encontrado'], 404);
        }

        // Return PDF data for frontend to generate
        $student = $contract->getStudent();
        $cycle = $contract->getSchoolCycle();
        
        return $this->json([
            'success' => true,
            'data' => [
                'id' => $contract->getId(),
                'studentName' => $student ? $student->getName() . ' ' . $student->getLastname() : 'N/A',
                'studentCarnet' => $student ? $student->getCarnet() : 'N/A',
                'cycleName' => $cycle ? $cycle->getName() : 'N/A',
                'status' => $contract->getStatus(),
                'createdAt' => $contract->getCreatedAt()->format('Y-m-d'),
                'filePath' => $contract->getFilePath()
            ]
        ]);
    }

    #[Route('/{id}/upload', name: 'upload', methods: ['POST'])]
    public function uploadSignedContract(int $id, Request $request): JsonResponse
    {
        $contract = $this->contractRepository->find($id);
        
        if (!$contract) {
            return $this->json(['success' => false, 'error' => 'Contrato no encontrado'], 404);
        }

        $uploadedFile = $request->files->get('file');
        
        if (!$uploadedFile) {
            return $this->json(['success' => false, 'error' => 'No se recibió ningún archivo'], 400);
        }

        // Validate file type
        $mimeType = $uploadedFile->getMimeType();
        if ($mimeType !== 'application/pdf') {
            return $this->json(['success' => false, 'error' => 'Solo se permiten archivos PDF'], 400);
        }

        // Generate unique filename
        $student = $contract->getStudent();
        $cycle = $contract->getSchoolCycle();
        $filename = sprintf(
            'signed_%s_%s_%s.pdf',
            $student ? $student->getId() : 'unknown',
            $cycle ? $cycle->getName() : date('Y'),
            time()
        );

        // Move file to contracts directory
        $contractsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/contracts';
        if (!is_dir($contractsDir)) {
            mkdir($contractsDir, 0755, true);
        }

        try {
            $uploadedFile->move($contractsDir, $filename);
            
            // Update contract with signed file path
            $contract->setFilePath('/uploads/contracts/' . $filename);
            $contract->setStatus('SIGNED');
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Contrato firmado subido exitosamente',
                'data' => [
                    'id' => $contract->getId(),
                    'status' => $contract->getStatus(),
                    'filePath' => $contract->getFilePath()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => 'Error al guardar el archivo: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/{id}/signed', name: 'get_signed', methods: ['GET'])]
    public function getSignedContract(int $id): JsonResponse
    {
        $contract = $this->contractRepository->find($id);
        
        if (!$contract) {
            return $this->json(['success' => false, 'error' => 'Contrato no encontrado'], 404);
        }

        if ($contract->getStatus() !== 'SIGNED' || !$contract->getFilePath()) {
            return $this->json(['success' => false, 'error' => 'Contrato no está firmado'], 400);
        }

        return $this->json([
            'success' => true,
            'data' => [
                'id' => $contract->getId(),
                'filePath' => $contract->getFilePath(),
                'signedAt' => $contract->getCreatedAt()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
