<?php

namespace App\Controller\Api\V1;

use App\Entity\Grade;
use App\Entity\Section;
use App\Repository\GradeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/grades')]
class GradeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private GradeRepository $gradeRepository
    ) {}

    /**
     * List all grades
     */
    #[Route('', name: 'api_v1_grade_list', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $levelId = $request->query->get('levelId');

        $qb = $this->gradeRepository->createQueryBuilder('g')
            ->leftJoin('g.level', 'l')
            ->addSelect('l')
            ->orderBy('g.sortOrder', 'ASC');

        if ($levelId) {
            $qb->andWhere('g.level = :levelId')
               ->setParameter('levelId', $levelId);
        }

        $grades = $qb->getQuery()->getResult();

        return $this->json(array_map(fn($g) => $this->serializeGrade($g), $grades));
    }

    /**
     * Get grade by ID with sections
     */
    #[Route('/{id}', name: 'api_v1_grade_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $grade = $this->gradeRepository->find($id);
        if (!$grade) {
            return $this->json(['error' => 'Grade not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serializeGrade($grade, true));
    }

    private function serializeGrade(Grade $g, bool $detailed = false): array
    {
        $data = [
            'id' => $g->getId(),
            'name' => $g->getName(),
            'code' => $g->getCode(),
            'sortOrder' => $g->getSortOrder(),
            'level' => $g->getLevel() ? [
                'id' => $g->getLevel()->getId(),
                'name' => $g->getLevel()->getName(),
            ] : null,
        ];

        if ($detailed) {
            $sections = $this->em->getRepository(Section::class)->findBy(['grade' => $g]);
            $data['sections'] = array_map(fn($s) => [
                'id' => $s->getId(),
                'name' => $s->getName(),
                'capacity' => $s->getCapacity(),
            ], $sections);
        }

        return $data;
    }
}
