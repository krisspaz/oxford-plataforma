<?php

namespace App\Controller;

use App\Entity\SchoolCycle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/cycle', name: 'api_cycle_')]
class CycleController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $cycles = $this->entityManager->getRepository(SchoolCycle::class)->findBy([], ['startDate' => 'DESC']);
        
        return $this->json(array_map(fn($c) => [
            'id' => $c->getId(),
            'name' => $c->getName(),
            'startDate' => $c->getStartDate()->format('Y-m-d'),
            'endDate' => $c->getEndDate()->format('Y-m-d'),
            'isActive' => $c->isIsActive(),
        ], $cycles));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $cycle = new SchoolCycle();
        $cycle->setName($data['name']);
        $cycle->setStartDate(new \DateTime($data['startDate']));
        $cycle->setEndDate(new \DateTime($data['endDate']));
        $cycle->setIsActive($data['isActive'] ?? false);

        if ($cycle->isIsActive()) {
            // Deactivate others
            $others = $this->entityManager->getRepository(SchoolCycle::class)->findAll();
            foreach ($others as $other) {
                $other->setIsActive(false);
            }
        }

        $this->entityManager->persist($cycle);
        $this->entityManager->flush();

        return $this->json(['success' => true, 'id' => $cycle->getId()]);
    }
    
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $cycle = $this->entityManager->getRepository(SchoolCycle::class)->find($id);
        if (!$cycle) return $this->json(['error' => 'Not found'], 404);

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) $cycle->setName($data['name']);
        if (isset($data['startDate'])) $cycle->setStartDate(new \DateTime($data['startDate']));
        if (isset($data['endDate'])) $cycle->setEndDate(new \DateTime($data['endDate']));
        if (isset($data['isActive']) && $data['isActive']) {
            $others = $this->entityManager->getRepository(SchoolCycle::class)->findAll();
            foreach ($others as $other) {
                $other->setIsActive(false);
            }
            $cycle->setIsActive(true);
        }

        $this->entityManager->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/close', name: 'close', methods: ['POST'])]
    public function closeCycle(): JsonResponse
    {
        // ... kept for legacy compatibility but improved
        $repo = $this->entityManager->getRepository(SchoolCycle::class);
        $active = $repo->findOneBy(['isActive' => true]);
        
        if ($active) {
            $active->setIsActive(false);
        }

        $newCycle = new SchoolCycle();
        $year = (int)date('Y') + 1;
        $newCycle->setName('Ciclo Escolar ' . $year);
        $newCycle->setStartDate(new \DateTime("$year-01-15"));
        $newCycle->setEndDate(new \DateTime("$year-10-31"));
        $newCycle->setIsActive(true);

        $this->entityManager->persist($newCycle);
        $this->entityManager->flush();

        return $this->json(['status' => 'Cycle closed successfully and new cycle created.', 'newCycle' => $newCycle->getName()]);
    }
}
