<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin')]
class AdminController extends AbstractController
{
    #[Route('/logs', name: 'admin_logs', methods: ['GET'])]
    public function logs(): JsonResponse
    {
        // Generate mock recent logs for now
        // In production, this would read from a Log entity or Monolog handler
        $mockLogs = [
            [
                'id' => 1,
                'date' => date('Y-m-d H:i:s', strtotime('-5 minutes')),
                'action' => 'LOGIN_SUCCESS',
                'user' => 'admin@oxford.edu',
                'ip' => '::1',
                'details' => 'Inicio de sesión exitoso'
            ],
            [
                'id' => 2,
                'date' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
                'action' => 'USER_CREATED',
                'user' => 'admin@oxford.edu',
                'ip' => '::1',
                'details' => 'Nuevo usuario creado: test@oxford.edu'
            ],
            [
                'id' => 3,
                'date' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'action' => 'GRADE_CREATED',
                'user' => 'admin@oxford.edu',
                'ip' => '::1',
                'details' => 'Nivel académico creado: Preprimaria'
            ],
            [
                'id' => 4,
                'date' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'action' => 'CYCLE_CREATED',
                'user' => 'system',
                'ip' => '::1',
                'details' => 'Ciclo Escolar 2026 creado'
            ],
            [
                'id' => 5,
                'date' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'action' => 'LOGIN_FAILED',
                'user' => 'unknown@test.com',
                'ip' => '192.168.1.50',
                'details' => 'Intento de login fallido - credenciales inválidas'
            ],
        ];

        return $this->json($mockLogs);
    }
}
