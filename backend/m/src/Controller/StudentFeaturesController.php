<?php

namespace App\Controller;

use App\Entity\StudentMessage;
use App\Entity\HelpTicket;
use App\Entity\TeacherRating;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Repository\StudentMessageRepository;
use App\Repository\TeacherRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/student')]
class StudentFeaturesController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    // --- 1. CHAT FEATURES ---

    #[Route('/teachers/{studentId}', name: 'student_get_teachers', methods: ['GET'])]
    public function getMyTeachers(int $studentId, StudentRepository $studentRepo): JsonResponse
    {
        // In a real app, this would query SubjectAssignments for the student's Grade/Section
        // For now, we return ALL teachers to facilitate testing
        // TODO: Filter by student's section assignments
        
        $teachers = $this->em->getRepository(Teacher::class)->findAll();
        
        $data = [];
        foreach ($teachers as $t) {
            $data[] = [
                'id' => $t->getId(),
                'name' => $t->getFirstName() . ' ' . $t->getLastName(),
                'subject' => 'Docente General', // TODO: Get from assignment
                'avatar' => substr($t->getFirstName(), 0, 1) . substr($t->getLastName(), 0, 1),
                'online' => (bool)random_int(0, 1) // Mock status
            ];
        }

        return $this->json($data);
    }

    #[Route('/chat/{studentId}/{teacherId}', name: 'student_get_chat', methods: ['GET'])]
    public function getChatHistory(int $studentId, int $teacherId, StudentMessageRepository $msgRepo): JsonResponse
    {
        $messages = $msgRepo->findBy(
            ['student' => $studentId, 'teacher' => $teacherId],
            ['createdAt' => 'ASC']
        );

        $data = [];
        foreach ($messages as $m) {
            $data[] = [
                'id' => $m->getId(),
                'text' => $m->getContent(),
                'sender' => $m->getSenderType() === 'student' ? 'me' : 'them',
                'time' => $m->getCreatedAt()->format('c'),
                'status' => $m->isRead() ? 'read' : 'sent'
            ];
        }

        return $this->json($data);
    }

    #[Route('/chat', name: 'student_send_chat', methods: ['POST'])]
    public function sendMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $student = $this->em->getRepository(Student::class)->find($data['studentId']);
        $teacher = $this->em->getRepository(Teacher::class)->find($data['teacherId']);

        if (!$student || !$teacher) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $msg = new StudentMessage();
        $msg->setStudent($student);
        $msg->setTeacher($teacher);
        $msg->setSenderType('student');
        $msg->setContent($data['content']);
        $msg->setCreatedAt(new \DateTimeImmutable());
        
        $this->em->persist($msg);
        $this->em->flush();

        return $this->json(['success' => true, 'id' => $msg->getId()]);
    }

    // --- 2. FEEDBACK FEATURES ---

    #[Route('/help-ticket', name: 'student_submit_ticket', methods: ['POST'])]
    public function submitTicket(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $student = $this->em->getRepository(Student::class)->find($data['studentId'] ?? 0); 

        $ticket = new HelpTicket();
        if ($student) $ticket->setStudent($student); // Can be anonymous
        
        $ticket->setType($data['type']);
        $ticket->setCategory($data['area']);
        $ticket->setSubject($data['subject']);
        $ticket->setMessage($data['message']);
        
        $this->em->persist($ticket);
        $this->em->flush();

        return $this->json(['success' => true, 'ticketId' => $ticket->getId()]);
    }

    // --- 3. RATING FEATURES ---

    #[Route('/rating', name: 'student_submit_rating', methods: ['POST'])]
    public function submitRating(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $student = $this->em->getRepository(Student::class)->find($data['studentId']);
        $teacher = $this->em->getRepository(Teacher::class)->find($data['teacherId']);

        if (!$student || !$teacher) return $this->json(['error' => 'Not found'], 404);

        $rating = new TeacherRating();
        $rating->setStudent($student);
        $rating->setTeacher($teacher);
        $rating->setRating($data['rating']);
        $rating->setComment($data['comment'] ?? null);
        
        $this->em->persist($rating);
        $this->em->flush();

        return $this->json(['success' => true]);
    }
}
