<?php

namespace App\Controller;

use App\Entity\ExonerationRequest;
use App\Repository\ExonerationRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/exonerations', name: 'api_exonerations_')]
class ExonerationController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(ExonerationRequestRepository $repo): JsonResponse
    {
        $requests = $repo->findAll();
        // Transform...
        return $this->json($requests);
    }
    
    // Stub for other methods
}
