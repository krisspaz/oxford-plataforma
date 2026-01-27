<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/api/test-route', name: 'api_test_route', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Routing is working correctly!',
            'timestamp' => time()
        ]);
    }
}

