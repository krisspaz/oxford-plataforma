<?php

namespace App\Controller;

use App\Entity\Resource;
use App\Repository\ResourceRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/resources')]
class ResourceController extends AbstractController
{
    public function __construct(
        private ResourceRepository $resourceRepository,
        private TeacherRepository $teacherRepository,
        private EntityManagerInterface $em
    ) {}

    /**
     * Get all resources for a teacher
     */
    #[Route('/teacher/{teacherId}', methods: ['GET'])]
    public function getByTeacher(int $teacherId): JsonResponse
    {
        $resources = $this->resourceRepository->findByTeacher($teacherId);
        
        $data = array_map(function(Resource $r) {
            return [
                'id' => $r->getId(),
                'title' => $r->getTitle(),
                'description' => $r->getDescription(),
                'link' => $r->getLink(),
                'subjectId' => $r->getSubjectId(),
                'teacherId' => $r->getTeacher()?->getId(),
                'date' => $r->getCreatedAt()?->format('c'),
            ];
        }, $resources);

        return $this->json(['success' => true, 'data' => $data]);
    }

    /**
     * Get all resources (for admin/coordination view)
     */
    #[Route('', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $resources = $this->resourceRepository->findBy([], ['createdAt' => 'DESC']);
        
        $data = array_map(function(Resource $r) {
            return [
                'id' => $r->getId(),
                'title' => $r->getTitle(),
                'description' => $r->getDescription(),
                'link' => $r->getLink(),
                'subjectId' => $r->getSubjectId(),
                'teacherId' => $r->getTeacher()?->getId(),
                'teacherName' => $r->getTeacher()?->getFirstName() . ' ' . $r->getTeacher()?->getLastName(),
                'date' => $r->getCreatedAt()?->format('c'),
            ];
        }, $resources);

        return $this->json(['success' => true, 'data' => $data]);
    }

    /**
     * Create a new resource
     */
    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['title']) || !isset($data['subjectId']) || !isset($data['teacherId'])) {
            return $this->json([
                'success' => false, 
                'message' => 'Campos requeridos: title, subjectId, teacherId'
            ], 400);
        }

        $teacher = $this->teacherRepository->find($data['teacherId']);
        if (!$teacher) {
            return $this->json([
                'success' => false, 
                'message' => 'Docente no encontrado'
            ], 404);
        }

        $resource = new Resource();
        $resource->setTitle($data['title']);
        $resource->setDescription($data['description'] ?? null);
        $resource->setLink($data['link'] ?? null);
        $resource->setSubjectId((int)$data['subjectId']);
        $resource->setTeacher($teacher);

        $this->em->persist($resource);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Recurso creado correctamente',
            'data' => [
                'id' => $resource->getId(),
                'title' => $resource->getTitle(),
                'description' => $resource->getDescription(),
                'link' => $resource->getLink(),
                'subjectId' => $resource->getSubjectId(),
                'teacherId' => $teacher->getId(),
                'date' => $resource->getCreatedAt()?->format('c'),
            ]
        ], 201);
    }

    /**
     * Delete a resource
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $resource = $this->resourceRepository->find($id);
        
        if (!$resource) {
            return $this->json([
                'success' => false,
                'message' => 'Recurso no encontrado'
            ], 404);
        }

        $this->em->remove($resource);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Recurso eliminado correctamente'
        ]);
    }
}
