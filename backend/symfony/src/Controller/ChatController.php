<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\StudentMessage;
use App\Repository\StudentMessageRepository;
use App\Repository\StudentRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ChatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private StudentMessageRepository $messageRepository,
        private StudentRepository $studentRepository,
        private TeacherRepository $teacherRepository
    ) {
    }

    // ==========================================
    // STUDENT ENDPOINTS
    // ==========================================

    #[Route('/student/teachers/{studentId}', methods: ['GET'])]
    public function getMyTeachers(int $studentId): JsonResponse
    {
        // Retrieve teachers list
        $teachers = $this->teacherRepository->findAll();

        $data = array_map(function ($teacher) use ($studentId) {
            return [
                'id' => $teacher->getId(),
                'name' => $teacher->getFirstName() . ' ' . $teacher->getLastName(),
                'subject' => 'General',
                'photo' => null,
                'online' => false,
                'unread' => 0
            ];
        }, $teachers);

        return $this->json(['success' => true, 'data' => $data]);
    }

    #[Route('/student/chat/{studentId}/{teacherId}', methods: ['GET'])]
    public function getStudentChatHistory(int $studentId, int $teacherId): JsonResponse
    {
        $messages = $this->messageRepository->findChatHistory($studentId, $teacherId);

        $data = array_map(function ($msg) {
            return [
                'id' => $msg->getId(),
                'from' => $msg->getSenderType(), // 'student' or 'teacher'
                'text' => $msg->getContent(),
                'time' => $msg->getCreatedAt()->format('H:i'),
                'read' => $msg->isRead()
            ];
        }, $messages);

        return $this->json(['success' => true, 'data' => $data]);
    }

    #[Route('/student/chat', methods: ['POST'])]
    public function sendStudentMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $studentId = $data['studentId'] ?? null;
        $teacherId = $data['teacherId'] ?? null;
        $content = $data['message'];

        if (!$studentId || !$teacherId) {
            return $this->json(['success' => false, 'error' => 'Parámetros inválidos'], 400);
        }

        $student = $this->studentRepository->find($studentId);
        $teacher = $this->teacherRepository->find($teacherId);

        if (!$student || !$teacher) {
            return $this->json(['success' => false, 'error' => 'Usuario no encontrado'], 404);
        }

        $message = new StudentMessage();
        $message->setStudent($student);
        $message->setTeacher($teacher);
        $message->setSenderType('student');
        $message->setContent($content);

        $this->em->persist($message);
        $this->em->flush();

        return $this->json(['success' => true]);
    }

    // ==========================================
    // TEACHER ENDPOINTS
    // ==========================================

    #[Route('/chat/students/{teacherId}', methods: ['GET'])]
    public function getMyStudents(int $teacherId): JsonResponse
    {
        $students = $this->studentRepository->findAll();

        $data = array_map(function ($student) {
            return [
                'id' => $student->getId(),
                'name' => $student->getFirstName() . ' ' . $student->getLastName(),
                'grade' => 'General',
                'avatar' => null,
                'online' => false,
            ];
        }, $students);

        return $this->json(['success' => true, 'data' => $data]);
    }

    #[Route('/teachers/chat/{studentId}', methods: ['GET'])]
    public function getTeacherChatHistory(int $studentId): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['success' => false, 'error' => 'No autenticado'], 401);
        }

        // Por ahora asumimos un único Teacher asociado; esto se puede refinar
        $teacher = $this->teacherRepository->findOneBy(['user' => $user]) ?? $this->teacherRepository->findOneBy([]);
        if (!$teacher) {
            return $this->json(['success' => false, 'error' => 'Docente no encontrado'], 404);
        }

        $messages = $this->messageRepository->findChatHistory($studentId, $teacher->getId());

        $data = array_map(function ($msg) {
            return [
                'id' => $msg->getId(),
                'sender' => $msg->getSenderType() === 'teacher' ? 'me' : 'them',
                'text' => $msg->getContent(),
                'time' => $msg->getCreatedAt(),
                'status' => $msg->isRead() ? 'read' : 'sent'
            ];
        }, $messages);

        return $this->json(['success' => true, 'data' => $data]);
    }

    #[Route('/teachers/chat', methods: ['POST'])]
    public function sendTeacherMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $studentId = $data['studentId'] ?? null;
        $content = $data['message'];

        if (!$studentId) {
            return $this->json(['success' => false, 'error' => 'Parámetros inválidos'], 400);
        }

        $user = $this->getUser();
        if (!$user) {
            return $this->json(['success' => false, 'error' => 'No autenticado'], 401);
        }

        $teacher = $this->teacherRepository->findOneBy(['user' => $user]) ?? $this->teacherRepository->findOneBy([]);

        $student = $this->studentRepository->find($studentId);

        if (!$student || !$teacher) {
            return $this->json(['success' => false, 'error' => 'Usuario no encontrado'], 404);
        }

        $message = new StudentMessage();
        $message->setStudent($student);
        $message->setTeacher($teacher);
        $message->setSenderType('teacher');
        $message->setContent($content);

        $this->em->persist($message);
        $this->em->flush();

        return $this->json(['success' => true]);
    }
}
