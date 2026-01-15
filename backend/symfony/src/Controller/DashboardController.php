<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Subject;
use App\Entity\SchoolCycle;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/dashboard')]
class DashboardController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/stats', name: 'dashboard_stats', methods: ['GET'])]
    public function index(): JsonResponse
    {
        // General Stats for Admin
        $totalUsers = $this->entityManager->getRepository(User::class)->count([]);
        $totalStudents = $this->entityManager->getRepository(Student::class)->count([]); // Assuming Student entity
        $totalTeachers = $this->entityManager->getRepository(Teacher::class)->count([]); // Assuming Teacher entity
        $activeSubjects = $this->entityManager->getRepository(Subject::class)->count([]);
        
        $activeCycle = $this->entityManager->getRepository(SchoolCycle::class)->findOneBy(['isActive' => true]);

        return $this->json([
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents, 
            'totalTeachers' => $totalTeachers,
            'activeSubjects' => $activeSubjects,
            'activeCycle' => $activeCycle ? $activeCycle->getName() : '2026',
            'recentStudents' => [] // Todo: Fetch recent 5 students
        ]);
    }
}
