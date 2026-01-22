<?php

namespace App\Controller;

use App\Repository\FamilyRepository;
use App\Entity\Family;
use App\Entity\FamilyStudent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/families')]
class FamilyController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private FamilyRepository $familyRepository
    ) {}

    // Generic CRUD methods removed in favor of API Platform standard implementation

    #[Route('/{id}/siblings', methods: ['GET'])]
    public function getSiblings(int $id, Request $request): JsonResponse
    {
        $studentId = $request->query->get('studentId');
        $family = $this->familyRepository->find($id);
        
        if (!$family) {
            return $this->json(['error' => 'Family not found'], 404);
        }

        $siblings = [];
        foreach ($family->getFamilyStudents() as $fs) {
            $student = $fs->getStudent();
            if ($studentId && $student->getId() == $studentId) {
                continue;
            }
            $siblings[] = [
                'id' => $student->getId(),
                'carnet' => $student->getCarnet(),
                'name' => $student->getFirstName() . ' ' . $student->getLastName(),
            ];
        }

        return $this->json([
            'success' => true,
            'data' => $siblings
        ]);
    }

    #[Route('/by-student/{studentId}', methods: ['GET'])]
    public function findByStudent(int $studentId): JsonResponse
    {
        $family = $this->familyRepository->findByStudent($studentId);
        
        if (!$family) {
            return $this->json(['success' => true, 'data' => null]);
        }

        return $this->json([
            'success' => true,
            'data' => $this->serializeFamily($family, true)
        ]);
    }

    private function serializeFamily(Family $family, bool $detailed = false): array
    {
        $data = [
            'id' => $family->getId(),
            'familyName' => $family->getFamilyName(),
            'address' => $family->getAddress(),
            'homePhone' => $family->getHomePhone(),
            'father' => $family->getFather() ? [
                'id' => $family->getFather()->getId(),
                'name' => $family->getFather()->getFirstName() . ' ' . $family->getFather()->getLastName(),
                'phone' => $family->getFather()->getPhone(),
                'email' => $family->getFather()->getEmail(),
            ] : null,
            'mother' => $family->getMother() ? [
                'id' => $family->getMother()->getId(),
                'name' => $family->getMother()->getFirstName() . ' ' . $family->getMother()->getLastName(),
                'phone' => $family->getMother()->getPhone(),
                'email' => $family->getMother()->getEmail(),
            ] : null,
            'studentsCount' => count($family->getFamilyStudents()),
        ];

        if ($detailed) {
            $data['students'] = [];
            foreach ($family->getFamilyStudents() as $fs) {
                $student = $fs->getStudent();
                $data['students'][] = [
                    'id' => $student->getId(),
                    'carnet' => $student->getCarnet(),
                    'name' => $student->getFirstName() . ' ' . $student->getLastName(),
                    'relationship' => $fs->getRelationship(),
                    'isPrimary' => $fs->isPrimary(),
                ];
            }
            $data['notes'] = $family->getNotes();
        }

        return $data;
    }
}
