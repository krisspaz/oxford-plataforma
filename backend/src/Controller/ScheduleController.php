<?php

namespace App\Controller;

use App\Service\ScheduleGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController extends AbstractController
{
    private ScheduleGeneratorService $generatorService;

    public function __construct(ScheduleGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    #[Route('/api/schedule/generate', name: 'api_schedule_generate', methods: ['POST'])]
    public function generate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
             // Mock Data for Demo purposes if no body provided
             $data = [
                "slots" => ["Lun 07:00", "Lun 08:00", "Lun 09:00", "Mar 07:00", "Mar 08:00"],
                "teachers" => [
                    ["id" => 1, "name" => "Juan Perez", "subjects" => ["Matemáticas", "Física"]],
                    ["id" => 2, "name" => "Maria Lopez", "subjects" => ["Literatura"]]
                ],
                "groups" => [
                    ["id" => "5to Bachillerato", "subjects_needed" => [
                        ["subject" => "Matemáticas", "hours" => 2],
                        ["subject" => "Física", "hours" => 1],
                        ["subject" => "Literatura", "hours" => 1]
                    ]]
                ]
             ];
        }

        try {
            $schedule = $this->generatorService->generate($data);
            return $this->json($schedule);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
