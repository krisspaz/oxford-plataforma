<?php

namespace App\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Student;
use App\DTO\StudentRequest;
use App\DTO\StudentResponse;
use App\DTO\PaginatedResponse;
use App\DTO\ApiResponse;

/**
 * API v1 Student Controller
 * 
 * Versioned API endpoints for student management
 * 
 * @Route("/api/v1/students", name="api_v1_students_")
 */
class StudentController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    /**
     * List students with pagination
     * 
     * @Route("", name="list", methods={"GET"})
     */
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 20);
        $search = $request->query->get('search', '');

        $qb = $this->em->getRepository(Student::class)->createQueryBuilder('s')
            ->leftJoin('s.user', 'u')
            ->orderBy('s.id', 'DESC');

        if ($search) {
            $qb->andWhere('u.firstName LIKE :search OR u.lastName LIKE :search OR s.code LIKE :search')
               ->setParameter('search', "%{$search}%");
        }

        // Count total
        $countQb = clone $qb;
        $total = $countQb->select('COUNT(s.id)')->getQuery()->getSingleScalarResult();

        // Paginate
        $students = $qb
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $items = array_map(
            fn($s) => StudentResponse::fromEntity($s),
            $students
        );

        return $this->json(
            PaginatedResponse::create($items, $total, $page, $limit)
        );
    }

    /**
     * Get single student
     * 
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $student = $this->em->getRepository(Student::class)->find($id);

        if (!$student) {
            return $this->json(
                ApiResponse::error('Estudiante no encontrado'),
                404
            );
        }

        return $this->json(
            ApiResponse::success(StudentResponse::fromEntity($student))
        );
    }

    /**
     * Create student
     * 
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate
        if (empty($data['firstName']) || empty($data['lastName'])) {
            return $this->json(
                ApiResponse::error('Nombre y apellido son requeridos'),
                422
            );
        }

        $student = new Student();
        // Set properties...

        $this->em->persist($student);
        $this->em->flush();

        return $this->json(
            ApiResponse::success(
                StudentResponse::fromEntity($student),
                'Estudiante creado exitosamente'
            ),
            201
        );
    }

    /**
     * Update student
     * 
     * @Route("/{id}", name="update", methods={"PUT", "PATCH"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $student = $this->em->getRepository(Student::class)->find($id);

        if (!$student) {
            return $this->json(
                ApiResponse::error('Estudiante no encontrado'),
                404
            );
        }

        $data = json_decode($request->getContent(), true);
        // Update properties...

        $this->em->flush();

        return $this->json(
            ApiResponse::success(
                StudentResponse::fromEntity($student),
                'Estudiante actualizado'
            )
        );
    }

    /**
     * Delete student
     * 
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $student = $this->em->getRepository(Student::class)->find($id);

        if (!$student) {
            return $this->json(
                ApiResponse::error('Estudiante no encontrado'),
                404
            );
        }

        $this->em->remove($student);
        $this->em->flush();

        return $this->json(
            ApiResponse::success(null, 'Estudiante eliminado')
        );
    }
}
