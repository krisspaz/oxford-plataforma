<?php

namespace App\Controller;

use App\Repository\PaymentRepository;
use App\Repository\StudentRepository;
use App\Repository\SchoolCycleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/dashboard', name: 'api_dashboard_')]
class DashboardController extends AbstractController
{
    private $studentRepository;
    private $paymentRepository;
    private $schoolCycleRepository;
    private $userRepository;
    private $subjectRepository;

    private $taskRepository;

    public function __construct(
        StudentRepository $studentRepository,
        PaymentRepository $paymentRepository,
        SchoolCycleRepository $schoolCycleRepository,
        \App\Repository\UserRepository $userRepository,
        \App\Repository\SubjectRepository $subjectRepository,
        \App\Repository\TaskRepository $taskRepository,
        private \Doctrine\ORM\EntityManagerInterface $entityManager
    ) {
        $this->studentRepository = $studentRepository;
        $this->paymentRepository = $paymentRepository;
        $this->schoolCycleRepository = $schoolCycleRepository;
        $this->userRepository = $userRepository;
        $this->subjectRepository = $subjectRepository;
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/stats', name: 'stats', methods: ['GET'])]
    public function getStats(): JsonResponse
    {
        $user = $this->getUser();
        $roles = $user ? $user->getRoles() : [];

        $data = [
            'totalStudents' => $this->studentRepository->countActive(),
            'monthlyIncome' => 0, // Placeholder
            'atRiskStudents' => $this->studentRepository->countAtRisk(),
            'averageGrade' => 85,
            'recentStudents' => [],
            'activeCycle' => 'Sin Ciclo',
        ];

        // Basic Global Data
        $activeCycle = $this->schoolCycleRepository->findOneBy(['isActive' => true]);
        if ($activeCycle) $data['activeCycle'] = $activeCycle->getName();

        // Role Specific Data
        if (in_array('ROLE_TEACHER', $roles)) {
             $data['teacher'] = [
                 'myClassesCount' => 4, // Replace with actual count
                 'myStudentsCount' => 120, // Replace with actual count
                 'pendingGrades' => 5
             ];
        }

        if (in_array('ROLE_STUDENT', $roles)) {
             $student = $this->studentRepository->findOneBy(['email' => $user->getEmail()]);
             $pendingTasks = 0;
             $average = 0;
             $nextClass = 'Sin Asignar';

             if ($student) {
                // Get latest enrollment for Grade/Section
                $enrollments = $student->getEnrollments();
                if (!$enrollments->isEmpty()) {
                    $enrollment = $enrollments->last();
                    $grade = $enrollment->getGrade();
                    $section = $enrollment->getSection();

                    if ($grade) {
                        // Use repository method
                        $tasks = $this->taskRepository->findForStudent($grade, $section);
                        
                        foreach($tasks as $task) {
                            // Check for submission
                            $submission = $this->entityManager->getRepository(\App\Entity\TaskSubmission::class)->findOneBy([
                                'task' => $task,
                                'student' => $student
                            ]);
                            
                            // If no submission or submission is pending/returned, count as pending?
                            // Let's assume pending if no submission or status is 'pending'
                             if (!$submission || $submission->getStatus() === 'pending') {
                                $pendingTasks++;
                            }
                        }
                    }
                }
             }

             $data['student'] = [
                 'average' => 88, // Placeholder for now
                 'pendingTasks' => $pendingTasks,
                 'nextClass' => 'Matemáticas' // Placeholder
             ];
        }

        if (in_array('ROLE_PARENT', $roles)) {
             $data['parent'] = [
                 'childrenCount' => 2,
                 'pendingPayments' => 1,
                 'nextMeeting' => 'Viernes 10:00 AM'
             ];
        }

        if (in_array('ROLE_ACCOUNTANT', $roles) || in_array('ROLE_SECRETARY', $roles)) {
             $income = 8450; // Placeholder
             
             $data['accountant'] = [
                 'incomeToday' => $income,
                 'invoicesCount' => 15, // Placeholder
                 'pendingRequests' => 2,
                 'exonerations' => 5
             ];
             
             $data['secretary'] = [
                 'enrollmentsToday' => 3,
                 'familiesCount' => 89,
                 'pendingPayments' => 12, // Placeholder
                 'contractsCount' => 24
             ];
        }

        if (in_array('ROLE_SUPER_ADMIN', $roles) || in_array('ROLE_ADMIN', $roles) || in_array('ROLE_DIRECTOR', $roles)) {
             $recentStudents = $this->studentRepository->findRecent(5);
             $data['recentStudents'] = array_map(function($student) {
                return [
                    'id' => $student->getId(),
                    'name' => $student->getFirstName() . ' ' . $student->getLastName(),
                    'email' => $student->getEmail(),
                    'cycle' => $student->getSchoolCycle() ? $student->getSchoolCycle()->getName() : 'Sin Asignar',
                    'course' => $student->getCourse() ? $student->getCourse()->getName() : 'Sin Asignar',
                    'status' => $student->getStatus(),
                ];
            }, $recentStudents);
            $data['totalUsers'] = $this->userRepository->count([]);
            $data['totalSubjects'] = $this->subjectRepository->count([]);
            $data['totalStudents'] = $this->studentRepository->countActive();
            $data['totalTeachers'] = $this->userRepository->countByRole('ROLE_TEACHER'); // Assuming method exists or just fallback
        }

        return $this->json($data);
    }
}
