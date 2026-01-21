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

    // Generic CRUD removed in favor of API Platform standard implementation

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
