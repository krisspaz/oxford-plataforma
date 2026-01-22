<?php

namespace App\Controller\Api\V1;

use App\Entity\Teacher;
use App\Entity\SubjectAssignment;
use App\Entity\User;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/teachers')]
class TeacherController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private TeacherRepository $teacherRepository,
        private Security $security
    ) {}

    /**
     * Get current teacher profile
     */
    #[Route('/me', name: 'api_v1_teacher_me', methods: ['GET'], priority: 10)]
    #[IsGranted('ROLE_USER')]
    public function getMyProfile(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        
        if (!$user) {
            return $this->json(['error' => 'No authenticated user'], Response::HTTP_UNAUTHORIZED);
        }

        $teacher = $this->teacherRepository->findOneBy(['user' => $user]);
        if (!$teacher) {
            $teacher = $this->teacherRepository->findOneBy(['email' => $user->getEmail()]);
        }
        
        if (!$teacher) {
            return $this->json(['error' => 'Teacher profile not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serializeTeacher($teacher, true));
    }

    private function serializeTeacher(Teacher $t, bool $detailed = false): array
    {
        $data = [
            'id' => $t->getId(),
            'firstName' => $t->getFirstName(),
            'lastName' => $t->getLastName(),
            'fullName' => $t->getFullName(),
            'email' => $t->getEmail(),
            'phone' => $t->getPhone(),
            'specialization' => $t->getSpecialization(),
        ];

        if ($detailed) {
            $data['subjectAssignments'] = array_map(fn($a) => [
                'id' => $a->getId(),
                'subjectName' => $a->getSubject()->getName(),
                'gradeName' => $a->getGrade()->getName(),
            ], $t->getSubjectAssignments()->toArray());
        }

        return $data;
    }
}
