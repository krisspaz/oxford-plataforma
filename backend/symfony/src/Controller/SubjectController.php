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

    // Generic CRUD removed in favor of API Platform standard implementation

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
