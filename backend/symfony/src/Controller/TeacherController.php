<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Entity\SubjectAssignment;
use App\Entity\User;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/teachers')]
class TeacherController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private TeacherRepository $teacherRepository,
        private Security $security
    ) {}

    /**
     * List all teachers
     */
    #[Route('', name: 'teacher_list', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $isActive = $request->query->get('active');
        $search = $request->query->get('search');

        $qb = $this->teacherRepository->createQueryBuilder('t')
            ->orderBy('t.lastName', 'ASC');

        if ($isActive !== null) {
            $qb->andWhere('t.isActive = :active')
               ->setParameter('active', $isActive === 'true');
        }

        if ($search) {
            $qb->andWhere('t.firstName LIKE :search OR t.lastName LIKE :search OR t.email LIKE :search')
               ->setParameter('search', "%$search%");
        }

        $teachers = $qb->getQuery()->getResult();

        return $this->json(array_map(fn($t) => $this->serializeTeacher($t), $teachers));
    }

    /**
     * Get teacher by ID
     */
    #[Route('/{id}', name: 'teacher_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $teacher = $this->teacherRepository->find($id);
        if (!$teacher) {
            return $this->json(['error' => 'Teacher not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serializeTeacher($teacher, true));
    }

    /**
     * Get current teacher profile (for logged-in teacher)
     */
    #[Route('/me', name: 'teacher_me', methods: ['GET'], priority: 10)]
    #[IsGranted('ROLE_USER')]
    public function getMyProfile(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        
        if (!$user) {
            return $this->json(['error' => 'No authenticated user'], Response::HTTP_UNAUTHORIZED);
        }

        // First, try to find teacher by User relationship (Person.user)
        $teacher = $this->teacherRepository->findOneBy(['user' => $user]);
        
        // Fallback: Try to find by email match
        if (!$teacher) {
            $teacher = $this->teacherRepository->findOneBy(['email' => $user->getEmail()]);
        }
        
        if (!$teacher) {
            // Return a helpful message instead of error so frontend can handle it
            return $this->json([
                'error' => 'Teacher profile not found',
                'message' => 'No teacher profile associated with this user. Contact admin.',
                'userId' => $user->getId(),
                'email' => $user->getEmail()
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serializeTeacher($teacher, true));
    }


    /**
     * Create a new teacher
     */
    #[Route('', name: 'teacher_create', methods: ['POST'])]
    public function create(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $teacher = new Teacher();
        $teacher->setFirstName($data['firstName']);
        $teacher->setLastName($data['lastName']);
        $teacher->setEmail($data['email']);
        $teacher->setPhone($data['phone'] ?? null);
        $teacher->setEmployeeCode($data['employeeCode'] ?? null);
        $teacher->setSpecialization($data['specialization'] ?? null);
        $teacher->setContractType($data['contractType'] ?? 'Tiempo Completo');
        
        if (isset($data['hireDate'])) {
            $teacher->setHireDate(new \DateTime($data['hireDate']));
        }

        $this->em->persist($teacher);

        // Create user account if requested
        if (isset($data['createUser']) && $data['createUser']) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setUsername($data['email']);
            $user->setPassword($passwordHasher->hashPassword($user, $data['password'] ?? 'docente123'));
            $user->setRoles(['ROLE_DOCENTE']);
            $user->setIsActive(true);
            $this->em->persist($user);
        }

        $this->em->flush();

        return $this->json([
            'success' => true,
            'id' => $teacher->getId(),
            'message' => 'Docente creado correctamente'
        ], Response::HTTP_CREATED);
    }

    /**
     * Update teacher
     */
    #[Route('/{id}', name: 'teacher_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $teacher = $this->teacherRepository->find($id);
        if (!$teacher) {
            return $this->json(['error' => 'Teacher not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['firstName'])) $teacher->setFirstName($data['firstName']);
        if (isset($data['lastName'])) $teacher->setLastName($data['lastName']);
        if (isset($data['email'])) $teacher->setEmail($data['email']);
        if (isset($data['phone'])) $teacher->setPhone($data['phone']);
        if (isset($data['employeeCode'])) $teacher->setEmployeeCode($data['employeeCode']);
        if (isset($data['specialization'])) $teacher->setSpecialization($data['specialization']);
        if (isset($data['contractType'])) $teacher->setContractType($data['contractType']);
        if (isset($data['hireDate'])) $teacher->setHireDate(new \DateTime($data['hireDate']));
        if (isset($data['isActive'])) $teacher->setIsActive($data['isActive']);

        $this->em->flush();

        return $this->json(['success' => true, 'message' => 'Docente actualizado']);
    }

    /**
     * Delete teacher
     */
    #[Route('/{id}', name: 'teacher_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $teacher = $this->teacherRepository->find($id);
        if (!$teacher) {
            return $this->json(['error' => 'Teacher not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($teacher);
        $this->em->flush();

        return $this->json(['success' => true, 'message' => 'Docente eliminado']);
    }

    /**
     * Get subjects assigned to a teacher
     */
    #[Route('/{id}/subjects', name: 'teacher_subjects', methods: ['GET'])]
    public function getSubjects(int $id): JsonResponse
    {
        $teacher = $this->teacherRepository->find($id);
        if (!$teacher) {
            return $this->json(['error' => 'Teacher not found'], Response::HTTP_NOT_FOUND);
        }

        $assignments = $this->em->getRepository(SubjectAssignment::class)
            ->findBy(['teacher' => $teacher, 'isActive' => true]);

        $data = array_map(fn($a) => [
            'id' => $a->getId(),
            'subject' => [
                'id' => $a->getSubject()->getId(),
                'name' => $a->getSubject()->getName(),
            ],
            'grade' => [
                'id' => $a->getGrade()->getId(),
                'name' => $a->getGrade()->getName(),
            ],
            'section' => $a->getSection() ? [
                'id' => $a->getSection()->getId(),
                'name' => $a->getSection()->getName(),
            ] : null,
            'hoursPerWeek' => $a->getHoursPerWeek(),
        ], $assignments);

        return $this->json($data);
    }

    /**
     * Get students for a teacher (students in their assigned grades/sections)
     */
    #[Route('/{id}/students', name: 'teacher_students', methods: ['GET'])]
    public function getStudents(int $id): JsonResponse
    {
        $teacher = $this->teacherRepository->find($id);
        if (!$teacher) {
            return $this->json(['error' => 'Teacher not found'], Response::HTTP_NOT_FOUND);
        }

        // Get all grade/section combinations this teacher teaches
        $assignments = $this->em->getRepository(SubjectAssignment::class)
            ->findBy(['teacher' => $teacher, 'isActive' => true]);

        $students = [];
        $seenIds = [];

        foreach ($assignments as $assignment) {
            $qb = $this->em->createQueryBuilder()
                ->select('s', 'e')
                ->from('App\Entity\Student', 's')
                ->join('s.enrollments', 'e')
                ->andWhere('e.grade = :grade')
                ->setParameter('grade', $assignment->getGrade());

            if ($assignment->getSection()) {
                $qb->andWhere('e.section = :section')
                   ->setParameter('section', $assignment->getSection());
            }

            $results = $qb->getQuery()->getResult();

            foreach ($results as $student) {
                if (!in_array($student->getId(), $seenIds)) {
                    $seenIds[] = $student->getId();
                    $students[] = [
                        'id' => $student->getId(),
                        'code' => $student->getStudentCode(),
                        'firstName' => $student->getFirstName(),
                        'lastName' => $student->getLastName(),
                        'fullName' => $student->getFullName(),
                        'grade' => $assignment->getGrade()->getName(),
                        'section' => $assignment->getSection()?->getName(),
                        'subject' => $assignment->getSubject()->getName(),
                    ];
                }
            }
        }

        return $this->json($students);
    }

    private function serializeTeacher(Teacher $t, bool $detailed = false): array
    {
        $data = [
            'id' => $t->getId(),
            'employeeCode' => $t->getEmployeeCode(),
            'firstName' => $t->getFirstName(),
            'lastName' => $t->getLastName(),
            'fullName' => $t->getFullName(),
            'email' => $t->getEmail(),
            'phone' => $t->getPhone(),
            'specialization' => $t->getSpecialization(),
            'contractType' => $t->getContractType(),
            'hireDate' => $t->getHireDate()?->format('Y-m-d'),
            'isActive' => $t->isActive(),
        ];

        if ($detailed) {
            $data['subjectAssignments'] = array_map(fn($a) => [
                'id' => $a->getId(),
                'subjectName' => $a->getSubject()->getName(),
                'gradeName' => $a->getGrade()->getName(),
                'sectionName' => $a->getSection()?->getName(),
                'hoursPerWeek' => $a->getHoursPerWeek(),
            ], $t->getSubjectAssignments()->toArray());
        }

        return $data;
    }
}
