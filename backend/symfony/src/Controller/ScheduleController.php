<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/schedule')]
class ScheduleController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/student/{studentId}', name: 'schedule_student_get', methods: ['GET'])]
    public function getStudentSchedule(int $studentId): JsonResponse
    {
        // Security check: Ensure the logged-in user (Parent) has access to this student's data
        $user = $this->getUser();
        //$this->denyAccessUnlessGranted('ROLE_PADRE'); // Or specific voter

        // Validation logic (omitted for brevity, but should check if User -> Guardian -> Student link exists)
        
        $student = $this->entityManager->getRepository(Student::class)->find($studentId);
        if (!$student) {
            return $this->json(['error' => 'Student not found'], 404);
        }

        // TODO: Replace with real DB query once Schedule/TimeTable entities are defined
        // For now, return a consistent mock schedule based on the student's ID (to vary slightly)
        
        $mockSchedule = [
            [
                'id' => 100 + $studentId,
                'dayOfWeek' => 1, // Lunes
                'startTime' => '07:30',
                'endTime' => '08:15',
                'subject' => ['name' => 'Matemáticas'],
                'classroom' => 'A-101',
                'teacher' => ['name' => 'Prof. Juan Pérez']
            ],
            [
                'id' => 101 + $studentId,
                'dayOfWeek' => 1,
                'startTime' => '08:15',
                'endTime' => '09:00',
                'subject' => ['name' => 'Ciencias Naturales'],
                'classroom' => 'Lab-1',
                'teacher' => ['name' => 'Dra. Ana López']
            ],
            [
                'id' => 102 + $studentId,
                'dayOfWeek' => 2, // Martes
                'startTime' => '09:00',
                'endTime' => '09:45',
                'subject' => ['name' => 'Idioma Español'],
                'classroom' => 'A-101',
                'teacher' => ['name' => 'Lic. Carlos Ruiz']
            ],
            [
                'id' => 103 + $studentId,
                'dayOfWeek' => 3, // Miércoles
                'startTime' => '10:00',
                'endTime' => '10:45',
                'subject' => ['name' => 'Inglés'], // Use standard name
                'classroom' => 'B-202',
                'teacher' => ['name' => 'Miss Sarah']
            ],
             [
                'id' => 104 + $studentId,
                'dayOfWeek' => 4, // Jueves
                'startTime' => '07:30',
                'endTime' => '09:00',
                'subject' => ['name' => 'Educación Física'],
                'classroom' => 'Cancha',
                'teacher' => ['name' => 'Entrenador Mike']
            ],
            [
                'id' => 105 + $studentId,
                'dayOfWeek' => 5, // Viernes
                'startTime' => '08:00',
                'endTime' => '09:30',
                'subject' => ['name' => 'Computación'],
                'classroom' => 'Lab Comp',
                'teacher' => ['name' => 'Ing. Tech']
            ]
        ];

        return $this->json($mockSchedule);
    }

    #[Route('/my-student-schedule', name: 'schedule_my_student', methods: ['GET'])]
    public function getMyStudentSchedule(): JsonResponse
    {
         // For ROLE_STUDENT
         $user = $this->getUser();
         // Logic to get student from user...
         
         // Mock for now
         return $this->getStudentSchedule(0); 
    }
    
    // Stub for PDF generation if needed to avoid 404s
    #[Route('/pdf', name: 'schedule_pdf', methods: ['GET'])]
    public function generatePdf(): JsonResponse
    {
        return $this->json(['message' => 'PDF generation not fully implemented in this controller stub'], 200);
    }
}
