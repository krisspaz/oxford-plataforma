<?php

namespace App\Controller;

use App\Entity\GradeCost;
use App\Repository\GradeCostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/financial/costs')]
class GradeCostController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private GradeCostRepository $repository
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $costs = $this->repository->findAll();
        $data = array_map(fn($c) => [
            'id' => $c->getId(),
            'gradeLevel' => $c->getGradeLevel(),
            'enrollmentFee' => (float)$c->getEnrollmentFee(),
            'monthlyFee' => (float)$c->getMonthlyFee()
        ], $costs);

        return $this->json(['success' => true, 'data' => $data]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $cost = new GradeCost();
        $cost->setGradeLevel($data['gradeLevel']);
        $cost->setEnrollmentFee($data['enrollmentFee']);
        $cost->setMonthlyFee($data['monthlyFee']);

        $this->em->persist($cost);
        $this->em->flush();

        return $this->json(['success' => true, 'data' => [
            'id' => $cost->getId(),
            'gradeLevel' => $cost->getGradeLevel()
        ]], 201);
    }
    
    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $cost = $this->repository->find($id);
        if (!$cost) return $this->json(['error' => 'Not found'], 404);
        
        $data = json_decode($request->getContent(), true);
        if(isset($data['enrollmentFee'])) $cost->setEnrollmentFee($data['enrollmentFee']);
        if(isset($data['monthlyFee'])) $cost->setMonthlyFee($data['monthlyFee']);
        
        $this->em->flush();
        
        return $this->json(['success' => true]);
    }
}
