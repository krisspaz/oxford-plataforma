<?php

namespace App\Controller;

use App\Entity\SchoolCycle;
use App\Service\ScheduleGeneratorService;
use App\Repository\SchoolCycleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/schedule-generation')]
class ScheduleGenerationController extends AbstractController
{
    public function __construct(
        private ScheduleGeneratorService $generatorService,
        private SchoolCycleRepository $cycleRepository
    ) {}

    #[Route('/generate/{cycleId}', name: 'api_schedule_generate', methods: ['POST'])]
    public function generate(int $cycleId, Request $request): JsonResponse
    {
        $cycle = $this->cycleRepository->find($cycleId);

        if (!$cycle) {
            return $this->json(['error' => 'School cycle not found'], 404);
        }

        try {
            // In a real production environment, this should be dispatched to a queue (Messenger)
            // For now, we run it synchronously but with updated logic
            $result = $this->generatorService->generateSchedules($cycle);
            
            return $this->json($result);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/validate/{cycleId}', name: 'api_schedule_validate', methods: ['GET'])]
    public function validate(int $cycleId): JsonResponse
    {
        // Placeholder for validation logic without generating
        // Would use similar logic to generator but just checking constraints
        return $this->json(['status' => 'valid', 'conflicts' => []]);
    }
}
