<?php

namespace App\Controller;

use App\Entity\SchoolCycle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/cycle', name: 'api_cycle_')]
class CycleController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/close', name: 'close', methods: ['POST'])]
    public function closeCycle(): JsonResponse
    {
        // 1. Find active cycle
        // $cycle = $repo->findOneBy(['isActive' => true]);
        // $cycle->setIsActive(false);
        
        // 2. Archive data (dummy logic)
        
        // 3. Create new cycle
        $newCycle = new SchoolCycle();
        $newCycle->setName('Ciclo Escolar ' . (date('Y') + 1));
        $newCycle->setStartDate(new \DateTime((date('Y') + 1) . '-01-15'));
        $newCycle->setEndDate(new \DateTime((date('Y') + 1) . '-10-31'));
        $newCycle->setIsActive(true);

        $this->entityManager->persist($newCycle);
        $this->entityManager->flush();

        return $this->json(['status' => 'Cycle closed successfully and new cycle created.']);
    }
}
