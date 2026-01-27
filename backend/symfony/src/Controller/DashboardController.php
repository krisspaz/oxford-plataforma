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
    public function index(
        \App\Repository\ScheduleRepository $scheduleRepo,
        \App\Repository\TaskRepository $taskRepo,
        \App\Repository\PaymentRepository $paymentRepo,
        \App\Repository\StudentRepository $studentRepo
    ): JsonResponse
    {
        $user = $this->getUser();
        
        // General Stats for Admin
        $totalUsers = $this->entityManager->getRepository(User::class)->count([]);
        $totalStudents = $this->entityManager->getRepository(Student::class)->count([]);
        $totalTeachers = $this->entityManager->getRepository(Teacher::class)->count([]);
        $activeSubjects = $this->entityManager->getRepository(Subject::class)->count([]);
        
        $activeCycle = $this->entityManager->getRepository(SchoolCycle::class)->findOneBy(['isActive' => true]);

        // Specific Role Stats
        $parentStats = null;
        $teacherStats = null;
        $studentStats = null;

        if ($this->isGranted('ROLE_PADRE')) {
            $guardian = $user->getPerson(); // Assuming User -> Person (Guardian)
            if ($guardian instanceof \App\Entity\Guardian) {
                $childrenCount = $guardian->getStudents()->count();
                
                // Real pending payments calculation
                // For simplicity, count unpaid quotas for all children
                $pendingPayments = 0;
                foreach($guardian->getStudents() as $child) {
                    // Count pending quotas
                    $pendingQuotas = $this->entityManager->getRepository(\App\Entity\Quota::class)->createQueryBuilder('q')
                        ->select('count(q.id)')
                        ->join('q.paymentPlan', 'pp')
                        ->where('pp.student = :student')
                        ->andWhere('q.status != :paidStatus')
                        ->setParameter('student', $child)
                        ->setParameter('paidStatus', \App\Entity\Quota::STATUS_PAID)
                        ->getQuery()
                        ->getSingleScalarResult();
                    $pendingPayments += $pendingQuotas;
                }
                
                $parentStats = [
                    'childrenCount' => $childrenCount,
                    'pendingPayments' => (int)$pendingPayments
                ];
            }
        }

        if ($this->isGranted('ROLE_DOCENTE')) {
             // Find teacher profile securely
             $teacher = $this->entityManager->getRepository(\App\Entity\Teacher::class)->findOneBy(['user' => $user]);

             if ($teacher) {
                 // 1. My Classes (Schedule entries)
                 // Count distinct subjects/assignments ideally, but schedule entries is a good proxy for "Clases Asignadas"
                 $myClassesCount = $scheduleRepo->count(['teacher' => $teacher]);

                 // 2. My Tasks (Active)
                 // Assuming Task entity has 'teacher' relation
                 $activeTasks = $taskRepo->count(['teacher' => $teacher]); // Add status filter if available

                 // 3. My Students (Approximate: Students in grades I teach)
                 // Get distict grades from schedule
                 $schedules = $scheduleRepo->findBy(['teacher' => $teacher]);
                 $gradeIds = [];
                 foreach($schedules as $s) {
                     if ($s->getGrade()) $gradeIds[] = $s->getGrade()->getId();
                 }
                 $gradeIds = array_unique($gradeIds);
                 
                 $myStudentsCount = 0;
                 if (!empty($gradeIds)) {
                    $myStudentsCount = $studentRepo->createQueryBuilder('s')
                        ->select('count(s.id)')
                        ->where('s.grade IN (:grades)')
                        ->setParameter('grades', $gradeIds)
                        ->getQuery()
                        ->getSingleScalarResult();
                 }

                 $teacherStats = [
                     'myClassesCount' => $myClassesCount, 
                     'myStudentsCount' => (int)$myStudentsCount,
                     'activeTasks' => $activeTasks,
                     'pendingGrades' => 0 // Implement grade logic later
                 ];
             }
        }
        
        // ... Teacher stats logic above ...

        // Secretary Stats
        $secretaryStats = null;
        if ($this->isGranted('ROLE_SECRETARIA') || $this->isGranted('ROLE_SECRETARY') || $this->isGranted('ROLE_ADMIN')) {
             $today = new \DateTime('today');
             $tomorrow = new \DateTime('tomorrow');
             
             // Enrollments today (using enrollment date or student creation date as fallback)
             $todayEnrollments = $this->entityManager->getRepository(\App\Entity\Enrollment::class)->createQueryBuilder('e')
                 ->select('count(e.id)')
                 ->where('e.enrollmentDate >= :today')
                 ->andWhere('e.enrollmentDate < :tomorrow')
                 ->setParameter('today', $today)
                 ->setParameter('tomorrow', $tomorrow)
                 ->getQuery()
                 ->getSingleScalarResult();
            
             // Total Families
             $totalFamilies = $this->entityManager->getRepository(\App\Entity\Family::class)->count([]);
             
             // Total Contracts
             $totalContracts = $this->entityManager->getRepository(\App\Entity\Contract::class)->count([]);

             $secretaryStats = [
                 'todayEnrollments' => (int)$todayEnrollments,
                 'totalFamilies' => (int)$totalFamilies,
                 'totalContracts' => (int)$totalContracts
             ];
        }

        // Accounting Stats
        $accountingStats = null;
        if ($this->isGranted('ROLE_CONTABILIDAD') || $this->isGranted('ROLE_ACCOUNTANT') || $this->isGranted('ROLE_ADMIN')) {
             $today = new \DateTime('today');
             $tomorrow = new \DateTime('tomorrow');

             // Income Today
             $todayIncome = $this->entityManager->getRepository(\App\Entity\Payment::class)->createQueryBuilder('p')
                 ->select('SUM(p.amount)')
                 ->where('p.date >= :today')
                 ->andWhere('p.date < :tomorrow')
                 ->setParameter('today', $today)
                 ->setParameter('tomorrow', $tomorrow)
                 ->getQuery()
                 ->getSingleScalarResult();
             
             // Invoices Count
             $totalInvoices = $this->entityManager->getRepository(\App\Entity\Invoice::class)->count([]);
             
             // Null/Canceled Invoices (assuming status field)
             $canceledInvoices = 0; // Implement if status exists
             // $canceledInvoices = $this->entityManager->getRepository(\App\Entity\Invoice::class)->count(['status' => 'ANULADO']);

             $accountingStats = [
                 'todayIncome' => (float)$todayIncome,
                 'totalInvoices' => (int)$totalInvoices,
                 'canceledInvoices' => $canceledInvoices,
                 'exonerations' => 0 // Implement if Exoneration entity exists
             ];
        }

        // Real Recent Students
        $recentStudentsEntities = $this->entityManager->getRepository(Student::class)->findBy([], ['id' => 'DESC'], 5);
        $recentStudents = [];

        foreach ($recentStudentsEntities as $student) {
            $courseName = $student->getCourse() ? $student->getCourse()->getName() : 'N/A';
            $cycle = $student->getSchoolCycle(); 
            $cycleName = $cycle ? $cycle->getName() : '2026';
            
            $recentStudents[] = [
                'name' => $student->getFullName(),
                'cycle' => $cycleName,
                'course' => $student->getGrade() ? $student->getGrade()->getName() : 'General', 
                'status' => $student->isActive() ? 'ACTIVE' : 'INACTIVE'
            ];
        }

        return $this->json([
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents, 
            'totalTeachers' => $totalTeachers,
            'totalSubjects' => $activeSubjects, // Add this alias for Director
            'activeSubjects' => $activeSubjects,
            'activeCycle' => $activeCycle ? $activeCycle->getName() : '2026',
            'recentStudents' => $recentStudents,
            'parent' => $parentStats,
            'teacher' => $teacherStats,
            'student' => $studentStats,
            'secretary' => $secretaryStats,
            'accounting' => $accountingStats
        ]);
    }
}
