<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/requests')]
class RequestController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $status = $request->query->get('status');
        // Mock data
        return $this->json([
            'success' => true,
            'data' => [
                [
                    'id' => 1,
                    'type' => 'ANULACION_FACTURA',
                    'status' => 'PENDIENTE',
                    'description' => 'Solicitud anulación factura #123',
                    'createdAt' => date('Y-m-d'),
                    'studentName' => 'Juan Perez'
                ]
            ]
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => [
                'id' => $id,
                'type' => 'ANULACION_FACTURA',
                'status' => 'PENDIENTE',
                'description' => 'Solicitud anulación factura #123',
                'createdAt' => date('Y-m-d'),
                'studentName' => 'Juan Perez'
            ]
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->json([
            'success' => true,
            'data' => ['id' => rand(1, 100)]
        ], 201);
    }

    #[Route('/{id}/approve', methods: ['POST'])]
    public function approve(int $id, Request $request): JsonResponse
    {
        return $this->json(['success' => true, 'message' => 'Solicitud aprobada']);
    }

    #[Route('/{id}/reject', methods: ['POST'])]
    public function reject(int $id, Request $request): JsonResponse
    {
        return $this->json(['success' => true, 'message' => 'Solicitud rechazada']);
    }
}
