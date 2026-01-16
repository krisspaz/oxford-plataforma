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
        $totalStudents = $this->entityManager->getRepository(Student::class)->count([]);
        $totalTeachers = $this->entityManager->getRepository(Teacher::class)->count([]);
        $activeSubjects = $this->entityManager->getRepository(Subject::class)->count([]);
        
        $activeCycle = $this->entityManager->getRepository(SchoolCycle::class)->findOneBy(['isActive' => true]);

        // Real Recent Students
        $recentStudentsEntities = $this->entityManager->getRepository(Student::class)->findBy([], ['id' => 'DESC'], 5);
        $recentStudents = [];

        foreach ($recentStudentsEntities as $student) {
            $courseName = $student->getCourse() ? $student->getCourse()->getName() : 'N/A';
            $cycleName = $student->getSchoolCycle() ? $student->getSchoolCycle()->getName() : 'N/A';
            
            $recentStudents[] = [
                'name' => $student->getFullName(),
                'cycle' => $cycleName,
                'course' => $courseName,
                'status' => $student->isActive() ? 'ACTIVE' : 'INACTIVE'
            ];
        }

        return $this->json([
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents, 
            'totalTeachers' => $totalTeachers,
            'activeSubjects' => $activeSubjects,
            'activeCycle' => $activeCycle ? $activeCycle->getName() : '2026',
            'recentStudents' => $recentStudents
        ]);
    }
}
