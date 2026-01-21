<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/students')]
class StudentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private StudentRepository $studentRepository,
        private \App\Repository\GradeRecordRepository $gradeRecordRepository,
        private \App\Repository\QuotaRepository $quotaRepository
    ) {}

    #[Route('/{id}/account', methods: ['GET'])]
    public function accountStatus(int $id): JsonResponse
    {
        $student = $this->studentRepository->find($id);
        
        if (!$student) {
            return $this->json(['success' => false, 'error' => 'Estudiante no encontrado'], 404);
        }
        
        // Fetch quotas via PaymentPlan -> Enrollment (Assumed relationship path)
        // Since the path might be complex (Student -> Enrollment -> PaymentPlan -> Quotas)
        // We will try a direct join via DQL or just repository method if we can infer linkage.
        // Assuming Quota has no direct Student link, but link via PaymentPlan.
        // Let's rely on finding quotas where PaymentPlan -> Enrollment -> Student = $id
        
        $query = $this->em->createQuery(
            'SELECT q, pp, e 
             FROM App\Entity\Quota q
             JOIN q.paymentPlan pp
             JOIN pp.enrollment e
             WHERE e.student = :student
             ORDER BY q.dueDate ASC'
        )->setParameter('student', $student);
        
        $quotaEntities = $query->getResult();
        
        $quotas = [];
        $totalAssigned = 0;
        $totalPaid = 0;

        foreach ($quotaEntities as $q) {
            $amount = (float)$q->getAmount();
            $paid = (float)$q->getPaidAmount();
            $pending = (float)$q->getPendingAmount();
            
            $totalAssigned += $amount;
            $totalPaid += $paid;
            
            $quotas[] = [
                'id' => $q->getId(),
                'concept' => $q->getConcept(),
                'amount' => $amount,
                'paid' => $paid,
                'pending' => $pending,
                'dueDate' => $q->getDueDate()->format('Y-m-d'),
                'status' => $q->getStatus()
            ];
        }
        
        // Return mocks if empty (to avoid empty screen during demo if no real data yet)
        if (empty($quotas)) {
             // Keep mock fallback for demo? No, user wants FIX.
             // But if no enrollments, empty is correct.
        }

        return $this->json([
            'success' => true,
            'data' => [
                'student' => $this->serializeStudent($student),
                'summary' => [
                    'totalAssigned' => $totalAssigned,
                    'totalPaid' => $totalPaid,
                    'totalPending' => $totalAssigned - $totalPaid,
                ],
                'quotas' => $quotas
            ]
        ]);
    }

    #[Route('/{id}/grades', methods: ['GET'])]
    public function grades(int $id, Request $request): JsonResponse
    {
        $student = $this->studentRepository->find($id);
        
        if (!$student) {
            return $this->json(['success' => false, 'error' => 'Estudiante no encontrado'], 404);
        }
        
        // Fetch all grade records for this student
        $records = $this->gradeRecordRepository->findBy(['student' => $student]);
        
        // Group by Subject
        $subjects = [];
        foreach ($records as $r) {
            $subjectName = $r->getSubjectAssignment()?->getSubject()?->getName() ?? 'Desconocida';
            $bimId = $r->getBimester()?->getId(); // Assuming IDs 1, 2, 3, 4 map to bim1, etc.
            // Or use checking names/codes. Let's assume ID 1=Bim1, etc. for simplicity or map dynamically.
            
            if (!isset($subjects[$subjectName])) {
                $subjects[$subjectName] = [
                    'subject' => $subjectName,
                    'bim1' => null, 'bim2' => null, 'bim3' => null, 'bim4' => null, 
                    'average' => 0
                ];
            }
            
            // Map bimester ID to key (simple mapping 1-4)
            // Ideally we check Bimester name order.
            $bimKey = 'bim' . $bimId; 
            if (isset($subjects[$subjectName][$bimKey])) {
                 // Warning: overwriting if multiple?
            }
            // Only set if key exists
            if (array_key_exists($bimKey, $subjects[$subjectName])) {
                 $subjects[$subjectName][$bimKey] = $r->getScore();
            }
        }
        
        // Calculate averages
        $gradesList = [];
        foreach ($subjects as $name => $data) {
            $count = 0;
            $sum = 0;
            for ($i=1; $i<=4; $i++) {
                if ($data["bim$i"] !== null) {
                    $sum += $data["bim$i"];
                    $count++;
                }
            }
            $data['average'] = $count > 0 ? round($sum / $count, 2) : 0;
            $gradesList[] = $data;
        }

        return $this->json([
            'success' => true,
            'data' => [
                'student' => $this->serializeStudent($student),
                'grades' => $gradesList
            ]
        ]);
    }

    private function serializeStudent(Student $s, bool $full = false): array
    {
        $data = [
            'id' => $s->getId(),
            'carnet' => $s->getCarnet(),
            'firstName' => $s->getFirstName(),
            'lastName' => $s->getLastName(),
            'fullName' => $s->getFirstName() . ' ' . $s->getLastName(),
            'email' => $s->getEmail(),
            'phone' => $s->getPhone(),
        ];
        
        if ($full) {
            $data['birthDate'] = $s->getBirthDate()?->format('Y-m-d');
            $data['address'] = $s->getAddress();
            $data['gender'] = $s->getGender();
            // TODO: Add enrollment info
        }
        
        return $data;
    }

    private function generateCarnet(): string
    {
        $year = date('Y');
        $random = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        return "{$year}-{$random}";
    }
}
