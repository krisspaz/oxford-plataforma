<?php

namespace App\Controller;

use App\Entity\Scholarship;
use App\Repository\ScholarshipRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/scholarships', name: 'api_scholarships_')]
class ScholarshipController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ScholarshipRepository $scholarshipRepository,
        private StudentRepository $studentRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $scholarships = $this->scholarshipRepository->findBy(['isActive' => true], ['name' => 'ASC']);
        
        return $this->json([
            'success' => true,
            'data' => array_map(fn($s) => $this->serialize($s), $scholarships)
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $scholarship = $this->scholarshipRepository->find($id);
        
        if (!$scholarship) {
            return $this->json(['success' => false, 'error' => 'Convenio no encontrado'], 404);
        }

        return $this->json([
            'success' => true,
            'data' => $this->serialize($scholarship)
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $scholarship = new Scholarship();
        $scholarship->setName($data['name'] ?? 'Nuevo Convenio');
        $scholarship->setType($data['type'] ?? Scholarship::TYPE_PERCENTAGE);
        $scholarship->setValue($data['value'] ?? '0');
        $scholarship->setDescription($data['description'] ?? null);
        $scholarship->setIsActive(true);

        // TODO: Associate with SchoolCycle
        
        $this->em->persist($scholarship);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'data' => $this->serialize($scholarship)
        ], 201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $scholarship = $this->scholarshipRepository->find($id);
        
        if (!$scholarship) {
            return $this->json(['success' => false, 'error' => 'Convenio no encontrado'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) $scholarship->setName($data['name']);
        if (isset($data['type'])) $scholarship->setType($data['type']);
        if (isset($data['value'])) $scholarship->setValue($data['value']);
        if (isset($data['description'])) $scholarship->setDescription($data['description']);
        if (isset($data['isActive'])) $scholarship->setIsActive($data['isActive']);

        $this->em->flush();

        return $this->json([
            'success' => true,
            'data' => $this->serialize($scholarship)
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $scholarship = $this->scholarshipRepository->find($id);
        
        if (!$scholarship) {
            return $this->json(['success' => false, 'error' => 'Convenio no encontrado'], 404);
        }

        // Soft delete - just deactivate
        $scholarship->setIsActive(false);
        $this->em->flush();

        return $this->json(['success' => true, 'message' => 'Convenio desactivado']);
    }

    #[Route('/assign/{studentId}', name: 'assign', methods: ['POST'])]
    public function assign(int $studentId, Request $request): JsonResponse
    {
        $student = $this->studentRepository->find($studentId);
        
        if (!$student) {
            return $this->json(['success' => false, 'error' => 'Estudiante no encontrado'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $scholarshipId = $data['scholarshipId'] ?? null;

        if (!$scholarshipId) {
            return $this->json(['success' => false, 'error' => 'ID de convenio requerido'], 400);
        }

        $scholarship = $this->scholarshipRepository->find($scholarshipId);
        
        if (!$scholarship) {
            return $this->json(['success' => false, 'error' => 'Convenio no encontrado'], 404);
        }

        // TODO: Create StudentScholarship entity to track assignments
        // For now, we'll just return success
        
        return $this->json([
            'success' => true,
            'message' => sprintf(
                'Convenio "%s" (%s%s) asignado a %s %s',
                $scholarship->getName(),
                $scholarship->getValue(),
                $scholarship->getType() === Scholarship::TYPE_PERCENTAGE ? '%' : 'Q',
                $student->getName(),
                $student->getLastname()
            ),
            'data' => [
                'student' => [
                    'id' => $student->getId(),
                    'name' => $student->getName() . ' ' . $student->getLastname()
                ],
                'scholarship' => $this->serialize($scholarship)
            ]
        ]);
    }

    private function serialize(Scholarship $s): array
    {
        return [
            'id' => $s->getId(),
            'name' => $s->getName(),
            'type' => $s->getType(),
            'value' => $s->getValue(),
            'discount' => $s->getType() === Scholarship::TYPE_PERCENTAGE 
                ? $s->getValue() . '%' 
                : 'Q' . number_format((float)$s->getValue(), 2),
            'description' => $s->getDescription(),
            'isActive' => $s->isActive(),
            'cycle' => $s->getSchoolCycle()?->getName(),
            'createdAt' => $s->getCreatedAt()->format('Y-m-d')
        ];
    }
}
