<?php

namespace App\Controller;

use App\Entity\SchoolCycle;
use App\Entity\Bimester;
use App\Entity\Student;
use App\Entity\GradeRecord;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/school-cycles', name: 'api_school_cycles_')]
class SchoolCycleController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/pre-close-validation', name: 'pre_close_validation', methods: ['GET'])]
    public function preCloseValidation(): JsonResponse
    {
        $activeCycle = $this->entityManager->getRepository(SchoolCycle::class)->findOneBy(['isActive' => true]);
        
        if (!$activeCycle) {
            return $this->json([
                'gradesComplete' => false,
                'periodsComplete' => false,
                'error' => 'No active cycle found'
            ]);
        }

        // Check if all students have grades for all assigned subjects
        $missingGrades = $this->entityManager->getRepository(\App\Entity\Student::class)->createQueryBuilder('s')
            ->select('count(s.id)')
            ->join('s.grade', 'g')
            ->join('g.subjectAssignments', 'sa')
            ->leftJoin(\App\Entity\GradeRecord::class, 'gr', 'WITH', 'gr.student = s.id AND gr.subjectAssignment = sa.id')
            ->where('s.schoolCycle = :cycle')
            ->andWhere('gr.id IS NULL')
            ->setParameter('cycle', $activeCycle)
            ->getQuery()
            ->getSingleScalarResult();

        $gradesComplete = ((int)$missingGrades === 0);

        return $this->json([
            'gradesComplete' => $gradesComplete,
            'periodsComplete' => ($openBimesters === 0),
            'cycleName' => $activeCycle->getName(),
            'missingGradesCount' => (int)$missingGrades
        ]);
    }

    #[Route('/migration-preview', name: 'migration_preview', methods: ['GET'])]
    public function migrationPreview(): JsonResponse
    {
        $activeCycle = $this->entityManager->getRepository(SchoolCycle::class)->findOneBy(['isActive' => true]);
        
        if (!$activeCycle) {
            return $this->json(['error' => 'No active cycle found'], 404);
        }

        $studentCount = $this->entityManager->getRepository(Student::class)->count(['schoolCycle' => $activeCycle]);
        $gradeRecordCount = $this->entityManager->getRepository(GradeRecord::class)->createQueryBuilder('gr')
            ->join('gr.bimester', 'b')
            ->where('b.schoolCycle = :cycle')
            ->setParameter('cycle', $activeCycle)
            ->select('COUNT(gr.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return $this->json([
            'studentsToMigrate' => (int)$studentCount,
            'studentsGraduating' => 0, // Implement based on highest grade logic
            'gradesToArchive' => (int)$gradeRecordCount,
            'financialRecords' => 0 // Implement if needed
        ]);
    }

    #[Route('/execute-close', name: 'execute_close', methods: ['POST'])]
    public function executeClose(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $activeCycle = $this->entityManager->getRepository(SchoolCycle::class)->findOneBy(['isActive' => true]);

        if ($activeCycle) {
            $activeCycle->setIsActive(false);
        }

        $newCycle = new SchoolCycle();
        $newCycle->setName($data['name'] ?? 'Ciclo Escolar ' . ($data['year'] ?? date('Y') + 1));
        $newCycle->setStartDate(new \DateTime($data['startDate'] ?? 'now'));
        $newCycle->setEndDate(new \DateTime($data['endDate'] ?? 'now + 10 months'));
        $newCycle->setIsActive(true);

        $this->entityManager->persist($newCycle);
        
        // Optional: Student migration logic would go here
        
        $this->entityManager->flush();

        return $this->json(['success' => true, 'message' => 'School cycle closed and new one created.']);
    }
}
