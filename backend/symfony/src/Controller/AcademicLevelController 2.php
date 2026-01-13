<?php

namespace App\Controller;

use App\Entity\AcademicLevel;
use App\Repository\AcademicLevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/academic-levels')]
class AcademicLevelController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private AcademicLevelRepository $levelRepository
    ) {}

    #[Route('', name: 'level_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $levels = $this->levelRepository->findBy([], ['sortOrder' => 'ASC']);

        return $this->json(array_map(fn($l) => [
            'id' => $l->getId(),
            'name' => $l->getName(),
            'code' => $l->getCode(),
            'sortOrder' => $l->getSortOrder(),
            'isActive' => $l->isActive(),
            'minedResolution' => $l->getMinedResolution(),
        ], $levels));
    }

    #[Route('/{id}', name: 'level_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $level = $this->levelRepository->find($id);

        if (!$level) {
            return $this->json(['error' => 'Nivel no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $level->getId(),
            'name' => $level->getName(),
            'code' => $level->getCode(),
            'sortOrder' => $level->getSortOrder(),
            'isActive' => $level->isActive(),
            'minedResolution' => $level->getMinedResolution(),
        ]);
    }

    #[Route('', name: 'level_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) {
            return $this->json(['error' => 'El nombre es obligatorio'], Response::HTTP_BAD_REQUEST);
        }

        $level = new AcademicLevel();
        $level->setName($data['name']);
        $level->setCode($data['code'] ?? null);
        $level->setSortOrder($data['sortOrder'] ?? 0);
        $level->setMinedResolution($data['minedResolution'] ?? null);
        $level->setIsActive($data['isActive'] ?? true);

        $this->em->persist($level);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'id' => $level->getId(),
            'message' => 'Nivel académico creado correctamente'
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'level_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $level = $this->levelRepository->find($id);

        if (!$level) {
            return $this->json(['error' => 'Nivel no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) $level->setName($data['name']);
        if (isset($data['code'])) $level->setCode($data['code']);
        if (isset($data['sortOrder'])) $level->setSortOrder($data['sortOrder']);
        if (isset($data['minedResolution'])) $level->setMinedResolution($data['minedResolution']);
        if (isset($data['isActive'])) $level->setIsActive($data['isActive']);

        $this->em->flush();

        return $this->json(['success' => true, 'message' => 'Nivel actualizado correctamente']);
    }

    #[Route('/{id}', name: 'level_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $level = $this->levelRepository->find($id);

        if (!$level) {
            return $this->json(['error' => 'Nivel no encontrado'], Response::HTTP_NOT_FOUND);
        }

        // Check dependencies (Grades)
        if (count($level->getGrades()) > 0) {
            return $this->json(['error' => 'No se puede eliminar el nivel porque tiene grados asociados'], Response::HTTP_CONFLICT);
        }

        $this->em->remove($level);
        $this->em->flush();

        return $this->json(['success' => true, 'message' => 'Nivel eliminado correctamente']);
    }
}
