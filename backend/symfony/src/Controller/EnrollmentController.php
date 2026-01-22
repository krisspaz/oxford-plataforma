<?php

namespace App\Controller;

use App\Entity\Enrollment;
use App\Repository\EnrollmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/enrollments')]
class EnrollmentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private EnrollmentRepository $enrollmentRepository
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $cycle = $request->query->get('cycle');
        $status = $request->query->get('status');
        
        $criteria = [];
        if ($cycle) $criteria['schoolCycle'] = $cycle;
        if ($status) $criteria['status'] = $status;
        
        $enrollments = $this->enrollmentRepository->findBy($criteria, ['createdAt' => 'DESC']);
        
        return $this->json([
            'success' => true,
            'data' => array_map(fn($e) => $this->serializeEnrollment($e), $enrollments)
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $enrollment = $this->enrollmentRepository->find($id);
        
        if (!$enrollment) {
            return $this->json(['success' => false, 'error' => 'Inscripción no encontrada'], 404);
        }
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeEnrollment($enrollment)
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Required data
        $studentData = $data['student'] ?? null;
        $guardianData = $data['guardian'] ?? null;
        
        // Handle disparate frontend structure (flat vs nested)
        $enrollmentData = $data['enrollmentData'] ?? ($data['enrollment'] ?? []);
        
        // Merge flat keys into enrollmentData if active
        if (isset($data['gradeId'])) $enrollmentData['grade'] = $data['gradeId'];
        if (isset($data['sectionId'])) $enrollmentData['section'] = $data['sectionId'];
        if (isset($data['packageId'])) $enrollmentData['package'] = $data['packageId'];

        if (!$studentData) {
            return $this->json(['success' => false, 'error' => 'Datos del estudiante incompletos'], 400);
        }

        $this->em->beginTransaction();
        try {
            // 1. Create/Find Student
            // Check if student exists (by carnet or generated logic) - for now create new
            $student = new \App\Entity\Student();
            $student->setFirstName($studentData['firstName']);
            $student->setLastName($studentData['lastName']);
            $student->setBirthDate(new \DateTime($studentData['birthDate']));
            $student->setGender($studentData['gender'] ?? 'M');
            // Link School Cycle (Active one)
            $cycle = $this->em->getRepository(\App\Entity\SchoolCycle::class)->findOneBy(['isActive' => true]);
            if (!$cycle) {
                 return $this->json(['success' => false, 'error' => 'No existe un Ciclo Escolar activo. Contacte al administrador.'], 400); 
            }
            $enrollment->setSchoolCycle($cycle);

            // Generate Carnet with Collision Check
            $year = date('Y');
            $baseCarnet = $year . '-' . strtoupper(substr($studentData['lastName'], 0, 2));
            $unique = false;
            $attempts = 0;
            
            while (!$unique && $attempts < 5) {
                $carnet = $baseCarnet . rand(1000, 9999);
                // Check uniqueness
                $existing = $this->em->getRepository(\App\Entity\Student::class)->findOneBy(['carnet' => $carnet]);
                if (!$existing) {
                    $student->setCarnet($carnet);
                    $unique = true;
                }
                $attempts++;
            }
            
            if (!$unique) {
                // Fallback to timestamp if random fails
                $student->setCarnet($baseCarnet . substr(time(), -4));
            }

            $enrollment->setStatus('INSCRITO');
            $this->em->persist($enrollment);

            $this->em->flush();
            $this->em->commit();

            return $this->json([
                'success' => true,
                'data' => $this->serializeEnrollment($enrollment),
                'message' => 'Estudiante inscrito correctamente'
            ], 201);

        } catch (\Exception $e) {
            $this->em->rollback();
            return $this->json(['success' => false, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }

    #[Route('/{id}/status', methods: ['PATCH'])]
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $enrollment = $this->enrollmentRepository->find($id);
        
        if (!$enrollment) {
            return $this->json(['success' => false, 'error' => 'Inscripción no encontrada'], 404);
        }
        
        $data = json_decode($request->getContent(), true);
        $enrollment->setStatus($data['status']);
        
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeEnrollment($enrollment)
        ]);
    }

    private function serializeEnrollment(Enrollment $e): array
    {
        return [
            'id' => $e->getId(),
            'student' => $e->getStudent()?->getId(),
            'studentName' => $e->getStudent() ? $e->getStudent()->getFirstName() . ' ' . $e->getStudent()->getLastName() : null,
            'schoolCycle' => $e->getSchoolCycle()?->getId(),
            'grade' => $e->getGrade()?->getId(),
            'section' => $e->getSection()?->getId(),
            'package' => $e->getPackage()?->getId(),
            'status' => $e->getStatus(),
            'enrollmentDate' => $e->getEnrollmentDate()?->format('Y-m-d'),
            'createdAt' => $e->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
