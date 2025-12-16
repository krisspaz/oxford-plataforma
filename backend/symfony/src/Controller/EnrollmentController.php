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
        
        $enrollment = new Enrollment();
        // TODO: Set relationships (student, schoolCycle, grade, section, package)
        $enrollment->setStatus($data['status'] ?? 'INSCRITO');
        $enrollment->setEnrollmentDate(new \DateTime());
        
        $this->em->persist($enrollment);
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeEnrollment($enrollment)
        ], 201);
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
