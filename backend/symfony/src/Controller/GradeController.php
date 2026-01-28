<?php

namespace App\Controller;

use App\Entity\Grade;
use App\Entity\Section;
use App\Repository\GradeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/grades')]
class GradeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private GradeRepository $gradeRepository
    ) {}

    /**
     * List all grades
     */
    #[Route('', name: 'grade_list', methods: ['GET'])]
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
    #[Route('/{id}', name: 'grade_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $grade = $this->gradeRepository->find($id);
        if (!$grade) {
            return $this->json(['error' => 'Grade not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serializeGrade($grade, true));
    }

    /**
     * Create a new grade
     */
    #[Route('', name: 'grade_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $grade = new Grade();
            $grade->setName($data['name']);
            $grade->setCode($data['code'] ?? null);
            $grade->setSortOrder($data['sortOrder'] ?? 0);
            
            if (isset($data['levelId'])) {
                $grade->setLevel($this->em->getReference('App\Entity\AcademicLevel', $data['levelId']));
            }

            $this->em->persist($grade);
            $this->em->flush();

            return $this->json([
                'success' => true,
                'data' => $this->serializeGrade($grade),
                'message' => 'Grado creado correctamente'
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => 'Server Error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Update grade
     */
    #[Route('/{id}', name: 'grade_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $grade = $this->gradeRepository->find($id);
        if (!$grade) {
            return $this->json(['error' => 'Grade not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) $grade->setName($data['name']);
        if (isset($data['code'])) $grade->setCode($data['code']);
        if (isset($data['sortOrder'])) $grade->setSortOrder($data['sortOrder']);
        if (isset($data['levelId'])) {
            $grade->setLevel($this->em->getReference('App\Entity\AcademicLevel', $data['levelId']));
        }

        $this->em->flush();

        return $this->json([
            'success' => true,
            'data' => $this->serializeGrade($grade),
            'message' => 'Grado actualizado'
        ]);
    }

    /**
     * Delete grade
     */
    #[Route('/{id}', name: 'grade_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $grade = $this->gradeRepository->find($id);
        if (!$grade) {
            return $this->json(['error' => 'Grade not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($grade);
        $this->em->flush();

        return $this->json(['success' => true, 'message' => 'Grado eliminado']);
    }

    /**
     * Get sections for a grade
     */
    #[Route('/{id}/sections', name: 'grade_sections', methods: ['GET'])]
    public function getSections(int $id): JsonResponse
    {
        $grade = $this->gradeRepository->find($id);
        if (!$grade) {
            return $this->json(['error' => 'Grade not found'], Response::HTTP_NOT_FOUND);
        }

        $sections = $this->em->getRepository(Section::class)->findBy(['grade' => $grade]);

        return $this->json(array_map(fn($s) => [
            'id' => $s->getId(),
            'name' => $s->getName(),
            'code' => $s->getCode(),
            'capacity' => $s->getCapacity(),
        ], $sections));
    }

    /**
     * Create section for a grade
     */
    #[Route('/{id}/sections', name: 'grade_create_section', methods: ['POST'])]
    public function createSection(int $id, Request $request): JsonResponse
    {
        try {
            $grade = $this->gradeRepository->find($id);
            if (!$grade) {
                return $this->json(['error' => 'Grade not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);

            $section = new Section();
            $section->setName($data['name']);
            $section->setGrade($grade);
            $section->setCapacity($data['capacity'] ?? 30);
            $section->setIsActive(true);

            $this->em->persist($section);
            $this->em->flush();

            return $this->json([
                'success' => true,
                'id' => $section->getId(),
                'message' => 'Sección creada correctamente'
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => 'Error al crear sección: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get students in a grade
     */
    #[Route('/{id}/students', name: 'grade_students', methods: ['GET'])]
    public function getStudents(int $id, Request $request): JsonResponse
    {
        $grade = $this->gradeRepository->find($id);
        if (!$grade) {
            return $this->json(['error' => 'Grade not found'], Response::HTTP_NOT_FOUND);
        }

        $sectionId = $request->query->get('sectionId');

        $qb = $this->em->createQueryBuilder()
            ->select('s', 'e')
            ->from('App\Entity\Student', 's')
            ->join('s.enrollments', 'e')
            ->andWhere('e.grade = :grade')
            ->setParameter('grade', $grade)
            ->orderBy('s.lastName', 'ASC');

        if ($sectionId) {
            $qb->andWhere('e.section = :section')
               ->setParameter('section', $sectionId);
        }

        $students = $qb->getQuery()->getResult();

        return $this->json(array_map(fn($s) => [
            'id' => $s->getId(),
            'code' => $s->getStudentCode(),
            'firstName' => $s->getFirstName(),
            'lastName' => $s->getLastName(),
            'fullName' => $s->getFullName(),
            'email' => $s->getEmail(),
        ], $students));
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
