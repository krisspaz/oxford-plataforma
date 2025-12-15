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

    public function __construct(
        StudentRepository $studentRepository,
        PaymentRepository $paymentRepository,
        SchoolCycleRepository $schoolCycleRepository
    ) {
        $this->studentRepository = $studentRepository;
        $this->paymentRepository = $paymentRepository;
        $this->schoolCycleRepository = $schoolCycleRepository;
    }

    #[Route('/stats', name: 'stats', methods: ['GET'])]
    public function getStats(): JsonResponse
    {
        // 1. Total Active Students
        $totalStudents = $this->studentRepository->countActive();

        // 2. Income for the current month
        // Placeholder for now
        $monthlyIncome = 0; 

        // 3. At Risk Students
        $atRiskCount = $this->studentRepository->countAtRisk(); // Defaults to 0.7

        // 4. Average Grade
        // Placeholder
        $averageGrade = 85; 

        // 5. Recent Students
        $recentStudents = $this->studentRepository->findRecent(3);
        $recentStudentsData = array_map(function($student) {
            return [
                'name' => $student->getFirstName() . ' ' . $student->getLastName(),
                'time' => 'Reciente' // Placeholder for real time diff
            ];
        }, $recentStudents);

        return $this->json([
            'totalStudents' => $totalStudents,
            'monthlyIncome' => $monthlyIncome,
            'atRiskStudents' => $atRiskCount,
            'averageGrade' => $averageGrade,
            'recentStudents' => $recentStudentsData
        ]);
    }
}
