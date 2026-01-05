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
        private StudentRepository $studentRepository
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $search = $request->query->get('search');
        $grade = $request->query->get('grade');
        $section = $request->query->get('section');
        
        if ($search) {
            $students = $this->studentRepository->search($search);
        } else {
            $criteria = [];
            // TODO: Add filter by grade/section via enrollment
            $students = $this->studentRepository->findBy($criteria, ['lastName' => 'ASC'], 100);
        }
        
        return $this->json([
            'success' => true,
            'data' => array_map(fn($s) => $this->serializeStudent($s), $students)
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $student = $this->studentRepository->find($id);
        
        if (!$student) {
            return $this->json(['success' => false, 'error' => 'Estudiante no encontrado'], 404);
        }
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeStudent($student, true)
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $student = new Student();
        $student->setFirstName($data['firstName']);
        $student->setLastName($data['lastName']);
        $student->setCarnet($data['carnet'] ?? $this->generateCarnet());
        
        if (isset($data['birthDate'])) {
            $student->setBirthDate(new \DateTime($data['birthDate']));
        }
        if (isset($data['email'])) $student->setEmail($data['email']);
        if (isset($data['phone'])) $student->setPhone($data['phone']);
        if (isset($data['address'])) $student->setAddress($data['address']);
        if (isset($data['gender'])) $student->setGender($data['gender']);
        
        $this->em->persist($student);
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeStudent($student)
        ], 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $student = $this->studentRepository->find($id);
        
        if (!$student) {
            return $this->json(['success' => false, 'error' => 'Estudiante no encontrado'], 404);
        }
        
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['firstName'])) $student->setFirstName($data['firstName']);
        if (isset($data['lastName'])) $student->setLastName($data['lastName']);
        if (isset($data['email'])) $student->setEmail($data['email']);
        if (isset($data['phone'])) $student->setPhone($data['phone']);
        if (isset($data['address'])) $student->setAddress($data['address']);
        if (isset($data['birthDate'])) $student->setBirthDate(new \DateTime($data['birthDate']));
        
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeStudent($student)
        ]);
    }

    #[Route('/{id}/account', methods: ['GET'])]
    public function accountStatus(int $id): JsonResponse
    {
        $student = $this->studentRepository->find($id);
        
        if (!$student) {
            return $this->json(['success' => false, 'error' => 'Estudiante no encontrado'], 404);
        }
        
        // TODO: Get actual quotas from Quota entity
        $quotas = [
            ['id' => 1, 'concept' => 'Inscripción', 'amount' => 1000, 'paid' => 1000, 'pending' => 0, 'dueDate' => '2025-01-15', 'status' => 'PAGADO'],
            ['id' => 2, 'concept' => 'Mensualidad Enero', 'amount' => 750, 'paid' => 750, 'pending' => 0, 'dueDate' => '2025-01-30', 'status' => 'PAGADO'],
            ['id' => 3, 'concept' => 'Mensualidad Febrero', 'amount' => 750, 'paid' => 0, 'pending' => 750, 'dueDate' => '2025-02-28', 'status' => 'PENDIENTE'],
        ];
        
        $totalAssigned = array_sum(array_column($quotas, 'amount'));
        $totalPaid = array_sum(array_column($quotas, 'paid'));
        
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
        
        $bimesterId = $request->query->get('bimester');
        
        // TODO: Get actual grades from GradeRecord entity
        $grades = [
            ['subject' => 'Matemáticas', 'bim1' => 85, 'bim2' => 78, 'bim3' => null, 'bim4' => null, 'average' => 81.5],
            ['subject' => 'Comunicación y Lenguaje', 'bim1' => 92, 'bim2' => 88, 'bim3' => null, 'bim4' => null, 'average' => 90],
        ];
        
        return $this->json([
            'success' => true,
            'data' => [
                'student' => $this->serializeStudent($student),
                'grades' => $grades
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
