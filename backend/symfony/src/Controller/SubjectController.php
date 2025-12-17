<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Entity\SubjectAssignment;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/subjects')]
class SubjectController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SubjectRepository $subjectRepository
    ) {}

    /**
     * List all subjects
     */
    #[Route('', name: 'subject_list', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $isActive = $request->query->get('active');

        $qb = $this->subjectRepository->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC');

        if ($isActive !== null) {
            $qb->andWhere('s.isActive = :active')
               ->setParameter('active', $isActive === 'true');
        }

        $subjects = $qb->getQuery()->getResult();

        return $this->json(array_map(fn($s) => [
            'id' => $s->getId(),
            'name' => $s->getName(),
            'code' => $s->getCode(),
            'description' => $s->getDescription(),
            'isActive' => $s->isActive(),
        ], $subjects));
    }

    /**
     * Get subject by ID
     */
    #[Route('/{id}', name: 'subject_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $subject = $this->subjectRepository->find($id);
        if (!$subject) {
            return $this->json(['error' => 'Subject not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $subject->getId(),
            'name' => $subject->getName(),
            'code' => $subject->getCode(),
            'description' => $subject->getDescription(),
            'isActive' => $subject->isActive(),
        ]);
    }

    /**
     * Create a new subject
     */
    #[Route('', name: 'subject_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $subject = new Subject();
        $subject->setName($data['name']);
        $subject->setCode($data['code'] ?? strtoupper(substr($data['name'], 0, 3)));
        $subject->setDescription($data['description'] ?? null);
        $subject->setIsActive(true);

        $this->em->persist($subject);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'id' => $subject->getId(),
            'message' => 'Materia creada correctamente'
        ], Response::HTTP_CREATED);
    }

    /**
     * Update subject
     */
    #[Route('/{id}', name: 'subject_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $subject = $this->subjectRepository->find($id);
        if (!$subject) {
            return $this->json(['error' => 'Subject not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) $subject->setName($data['name']);
        if (isset($data['code'])) $subject->setCode($data['code']);
        if (isset($data['description'])) $subject->setDescription($data['description']);
        if (isset($data['isActive'])) $subject->setIsActive($data['isActive']);

        $this->em->flush();

        return $this->json(['success' => true, 'message' => 'Materia actualizada']);
    }

    /**
     * Delete subject
     */
    #[Route('/{id}', name: 'subject_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $subject = $this->subjectRepository->find($id);
        if (!$subject) {
            return $this->json(['error' => 'Subject not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($subject);
        $this->em->flush();

        return $this->json(['success' => true, 'message' => 'Materia eliminada']);
    }

    /**
     * Assign subject to teacher/grade/section
     */
    #[Route('/assign', name: 'subject_assign', methods: ['POST'])]
    public function assign(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $assignment = new SubjectAssignment();
        $assignment->setSubject($this->em->getReference(Subject::class, $data['subjectId']));
        $assignment->setTeacher($this->em->getReference('App\Entity\Teacher', $data['teacherId']));
        $assignment->setGrade($this->em->getReference('App\Entity\Grade', $data['gradeId']));
        
        if (isset($data['sectionId'])) {
            $assignment->setSection($this->em->getReference('App\Entity\Section', $data['sectionId']));
        }
        
        $assignment->setSchoolCycle($this->em->getReference('App\Entity\SchoolCycle', $data['cycleId']));
        $assignment->setHoursPerWeek($data['hoursPerWeek'] ?? 5);

        $this->em->persist($assignment);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'id' => $assignment->getId(),
            'message' => 'Materia asignada correctamente'
        ], Response::HTTP_CREATED);
    }
}
