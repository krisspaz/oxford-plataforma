<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
            'message' => 'Backend is running correctly',
            'service' => 'Oxford Plataforma API',
            'version' => '1.0.0'
        ]);
    }

    #[Route('/login', name: 'app_login_redirect')]
    public function loginInfo(): JsonResponse
    {
        return $this->json([
            'status' => 'info',
            'message' => 'This is an API. For authentication, use POST /api/login_check',
            'frontend_url' => 'http://localhost:5173',
        ]);
    }
}
