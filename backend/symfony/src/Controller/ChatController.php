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
        // TODO: In a real app, successful enrollment/schedule links student to teachers.
        // For now, we return all teachers or a subset to ensure data exists.
        $teachers = $this->teacherRepository->findAll();

        // Mock unread count logic
        $data = array_map(function ($teacher) use ($studentId) {
            return [
                'id' => $teacher->getId(),
                'name' => $teacher->getFirstName() . ' ' . $teacher->getLastName(),
                'subject' => 'General', // TODO: Get from SubjectAssignment
                'photo' => null,
                'online' => (bool) random_int(0, 1),
                'unread' => 0 // TODO: Count unread messages
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
        // Assuming user is authenticated and we get studentId from token or body
        // For this MVP, we take it from body or assume ID 1
        $studentId = $data['studentId'] ?? 1;
        $teacherId = $data['teacherId'];
        $content = $data['message'];

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

    #[Route('/teachers/{teacherId}/students', methods: ['GET'])]
    public function getMyStudents(int $teacherId): JsonResponse
    {
        // Return all students for MVP
        $students = $this->studentRepository->findAll();

        $data = array_map(function ($student) {
            return [
                'id' => $student->getId(),
                'name' => $student->getFirstName() . ' ' . $student->getLastName(),
                'grade' => 'General', // TODO: Get from Enrollment
                'avatar' => null,
                'online' => false,
            ];
        }, $students);

        return $this->json(['success' => true, 'data' => $data]);
    }

    #[Route('/teachers/chat/{studentId}', methods: ['GET'])]
    public function getTeacherChatHistory(int $studentId): JsonResponse
    {
        // Assuming we know the logged in teacher. Hardcoded ID 1 for MVP.
        $teacherId = 1;

        $messages = $this->messageRepository->findChatHistory($studentId, $teacherId);

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
        $teacherId = 1; // Mock logged in teacher
        $studentId = $data['studentId'];
        $content = $data['message'];

        $student = $this->studentRepository->find($studentId);
        $teacher = $this->teacherRepository->find($teacherId);

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
