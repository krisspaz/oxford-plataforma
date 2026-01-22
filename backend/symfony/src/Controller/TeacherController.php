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

    // Generic CRUD removed in favor of API Platform standard implementation

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


    /**
     * Get chat history with a student
     */
    #[Route('/chat/{studentId}', name: 'teacher_chat_history', methods: ['GET'])]
    public function getChatHistory(int $studentId): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);

        // Find teacher profile
        $teacher = $this->teacherRepository->findOneBy(['user' => $user]);
        if (!$teacher) $teacher = $this->teacherRepository->findOneBy(['email' => $user->getEmail()]);
        
        if (!$teacher) return $this->json(['error' => 'Teacher profile not found'], 404);

        $messages = $this->em->getRepository(\App\Entity\StudentMessage::class)->createQueryBuilder('m')
            ->where('m.student = :studentId AND m.teacher = :teacherId')
            ->setParameter('studentId', $studentId)
            ->setParameter('teacherId', $teacher->getId())
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();

        $data = array_map(fn($m) => [
            'id' => $m->getId(),
            'text' => $m->getContent(),
            'sender' => $m->getSenderType() === 'teacher' ? 'me' : 'student',
            'time' => $m->getCreatedAt(),
            'status' => $m->isRead() ? 'read' : 'sent'
        ], $messages);

        return $this->json($data);
    }

    /**
     * Send a message to a student
     */
    #[Route('/chat', name: 'teacher_chat_send', methods: ['POST'])]
    public function sendMessage(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);

        $teacher = $this->teacherRepository->findOneBy(['user' => $user]);
        if (!$teacher) $teacher = $this->teacherRepository->findOneBy(['email' => $user->getEmail()]);
        if (!$teacher) return $this->json(['error' => 'Teacher profile not found'], 404);

        $data = json_decode($request->getContent(), true);
        $studentId = $data['studentId'] ?? null;
        $content = $data['message'] ?? null;

        if (!$studentId || !$content) {
            return $this->json(['error' => 'Missing studentId or message'], 400);
        }

        $student = $this->em->getRepository(\App\Entity\Student::class)->find($studentId);
        if (!$student) return $this->json(['error' => 'Student not found'], 404);

        $message = new \App\Entity\StudentMessage();
        $message->setTeacher($teacher);
        $message->setStudent($student);
        $message->setContent($content);
        $message->setSenderType('teacher'); // 'me' from teacher perspective
        $message->setIsRead(false);
        
        $this->em->persist($message);
        $this->em->flush();

        return $this->json(['success' => true, 'id' => $message->getId()]);
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
