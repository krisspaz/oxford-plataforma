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

    public function __construct(
        StudentRepository $studentRepository,
        PaymentRepository $paymentRepository,
        SchoolCycleRepository $schoolCycleRepository,
        \App\Repository\UserRepository $userRepository,
        \App\Repository\SubjectRepository $subjectRepository
    ) {
        $this->studentRepository = $studentRepository;
        $this->paymentRepository = $paymentRepository;
        $this->schoolCycleRepository = $schoolCycleRepository;
        $this->userRepository = $userRepository;
        $this->subjectRepository = $subjectRepository;
    }

    #[Route('/stats', name: 'stats', methods: ['GET'])]
    public function getStats(): JsonResponse
    {
        // 1. Total Active Students
        $totalStudents = $this->studentRepository->countActive();

        // 2. Income for the current month
        $monthlyIncome = 0; // Placeholder

        // 3. At Risk Students
        $atRiskCount = $this->studentRepository->countAtRisk(); 

        // 4. Average Grade
        $averageGrade = 85; 

        // 5. Recent Students
        $recentStudents = $this->studentRepository->findRecent(5);
        $recentStudentsData = array_map(function($student) {
            return [
                'id' => $student->getId(),
                'name' => $student->getFirstName() . ' ' . $student->getLastName(),
                'email' => $student->getEmail(),
                'cycle' => $student->getSchoolCycle() ? $student->getSchoolCycle()->getName() : 'Sin Asignar',
                'course' => $student->getCourse() ? $student->getCourse()->getName() : 'Sin Asignar',
                'status' => $student->getStatus(),
            ];
        }, $recentStudents);

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
             // Mock teacher data derived from DB if relations existed
             // For now providing mock but structured for frontend
             $data['teacher'] = [
                 'myClassesCount' => 4, // Replace with $this->subjectRepository->countByTeacher($user)
                 'myStudentsCount' => 120,
                 'pendingGrades' => 5
             ];
        }

        if (in_array('ROLE_STUDENT', $roles)) {
             $data['student'] = [
                 'average' => 88,
                 'pendingTasks' => 3,
                 'nextClass' => 'Matemáticas'
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
             // Calculate daily income
             $today = new \DateTime('today');
             $tomorrow = new \DateTime('tomorrow');
             // Assuming repository has findByDateRange or similar, roughly:
             // $income = $this->paymentRepository->sumAmountBetween($today, $tomorrow);
             $income = 8450; // Placeholder until repository method exists
             
             $data['accountant'] = [
                 'incomeToday' => $income,
                 'invoicesCount' => 15, // $this->invoiceRepository->countToday()
                 'pendingRequests' => 2,
                 'exonerations' => 5
             ];
             
             $data['secretary'] = [
                 'enrollmentsToday' => 3,
                 'familiesCount' => 89,
                 'pendingPayments' => 12,
                 'contractsCount' => 24
             ];
        }

        if (in_array('ROLE_SUPER_ADMIN', $roles) || in_array('ROLE_ADMIN', $roles)) {
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
        }

        return $this->json($data);
    }
}
