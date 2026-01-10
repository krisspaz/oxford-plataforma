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
            // Generate Carnet: Year + Random for now
            $year = date('Y');
            $carnet = $year . '-' . strtoupper(substr($studentData['lastName'], 0, 2)) . rand(1000, 9999);
            $student->setCarnet($carnet);
            $student->setEmail($studentData['email'] ?? null);
            $student->setAddress($studentData['address'] ?? null);
            $student->setIsActive(true);
            $this->em->persist($student);

            // 2. Handle Family Logic
            if ($guardianData) {
                // Determine logic for Family? 
                // For this implementation, we assume creating a new Family based on Guardian or linking.
                // Simplified: Create Family if not exists (check by father/mother name?)
                // Actually, let's create a new Family entity for grouped management
                $family = new \App\Entity\Family();
                $family->setFamilyName($studentData['lastName'] . ' ' . ($guardianData['name'] ?? 'Familia'));
                $family->setAddress($studentData['address'] ?? '');
                $family->setHomePhone($guardianData['phone'] ?? '');
                $this->em->persist($family);

                // Create Guardian person entry? Or just store basic info in Family?
                // The Family Entity has relations to Person (father/mother).
                // Let's create the Guardian as a Person (if we had Person entity access here easily).
                // Reviewing FamilyController, it expects detailed objects.
                // To keep this robust but simple given the timeframe: 
                // We will rely on FamilyStudent to store the relationship (Primary Guardian).
                
                $familyStudent = new \App\Entity\FamilyStudent();
                $familyStudent->setFamily($family);
                $familyStudent->setStudent($student);
                $familyStudent->setRelationship($guardianData['relationship'] ?? 'ENCARGADO');
                $familyStudent->setIsPrimary(true);
                $this->em->persist($familyStudent);
            }

            // 3. Create Enrollment
            $enrollment = new Enrollment();
            $enrollment->setStudent($student);
            
            // Link Grade
            if (!empty($enrollmentData['grade'])) {
                $gradeId = is_array($enrollmentData['grade']) ? $enrollmentData['grade']['id'] : $enrollmentData['grade'];
                $grade = $this->em->getRepository(\App\Entity\Grade::class)->find($gradeId);
                if ($grade) $enrollment->setGrade($grade);
            }
            
            // Link Section
            if (!empty($enrollmentData['section'])) {
                // section might be ID or "A"/"B". If "A", we need to find the Section entity for this Grade with name "A".
                $sectionVal = $enrollmentData['section'];
                $section = null;
                if (is_numeric($sectionVal)) {
                    $section = $this->em->getRepository(\App\Entity\Section::class)->find($sectionVal);
                } elseif ($grade) {
                    $section = $this->em->getRepository(\App\Entity\Section::class)->findOneBy(['grade' => $grade, 'name' => $sectionVal]);
                }
                
                if ($section) $enrollment->setSection($section);
            }

            // Link Package
            if (!empty($enrollmentData['package'])) {
                $packageId = is_array($enrollmentData['package']) ? $enrollmentData['package']['id'] : $enrollmentData['package'];
                $package = $this->em->getRepository(\App\Entity\Package::class)->find($packageId);
                if ($package) $enrollment->setPackage($package);
            }

            // Link School Cycle (Active one)
            // Or assume 2026 if created?
            // Better: find active cycle
            $cycle = $this->em->getRepository(\App\Entity\SchoolCycle::class)->findOneBy(['isActive' => true]);
            if ($cycle) {
                $enrollment->setSchoolCycle($cycle);
            } else {
                 // Fallback or error? Let's just create without cycle or throw critical error
                 // return $this->json(['error' => 'No active school cycle found'], 400); 
                 // For now, allow null or try to find latest.
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
