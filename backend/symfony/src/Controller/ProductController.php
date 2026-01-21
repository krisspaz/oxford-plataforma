<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\LevelCostRepository;
use App\Entity\Product;
use App\Entity\LevelCost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/products')]
class ProductController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProductRepository $productRepository
    ) {}

    // Generic CRUD removed in favor of API Platform standard implementation

    #[Route('/types', methods: ['GET'])]
    public function getTypes(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => [
                ['code' => 'INSCRIPCION', 'name' => 'Inscripción'],
                ['code' => 'MENSUALIDAD', 'name' => 'Mensualidad'],
                ['code' => 'MATERIAL', 'name' => 'Material Didáctico'],
                ['code' => 'UNIFORME', 'name' => 'Uniforme'],
                ['code' => 'TRANSPORTE', 'name' => 'Transporte'],
                ['code' => 'ALIMENTACION', 'name' => 'Alimentación'],
                ['code' => 'EVENTO', 'name' => 'Evento'],
                ['code' => 'OTRO', 'name' => 'Otro'],
            ]
        ]);
    }
}
