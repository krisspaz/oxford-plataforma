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

    #[Route('', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $type = $request->query->get('type');
        
        $products = $type 
            ? $this->productRepository->findActiveByType($type)
            : $this->productRepository->findBy(['isActive' => true], ['name' => 'ASC']);

        return $this->json([
            'success' => true,
            'data' => array_map(fn($p) => [
                'id' => $p->getId(),
                'code' => $p->getCode(),
                'name' => $p->getName(),
                'description' => $p->getDescription(),
                'type' => $p->getType(),
                'basePrice' => $p->getBasePrice(),
                'documentType' => $p->getDocumentType(),
                'isTaxable' => $p->isTaxable(),
                'isRecurring' => $p->isRecurring(),
                'recurringMonths' => $p->getRecurringMonths(),
            ], $products)
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setCode($data['code']);
        $product->setName($data['name']);
        $product->setDescription($data['description'] ?? null);
        $product->setType($data['type'] ?? 'OTRO');
        $product->setBasePrice($data['basePrice']);
        $product->setDocumentType($data['documentType'] ?? 'FACTURA_SAT');
        $product->setIsTaxable($data['isTaxable'] ?? true);
        $product->setIsRecurring($data['isRecurring'] ?? false);
        $product->setRecurringMonths($data['recurringMonths'] ?? 0);

        $this->em->persist($product);
        $this->em->flush();

        return $this->json(['success' => true, 'id' => $product->getId()], 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $product = $this->productRepository->find($id);
        
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['code'])) $product->setCode($data['code']);
        if (isset($data['name'])) $product->setName($data['name']);
        if (isset($data['description'])) $product->setDescription($data['description']);
        if (isset($data['type'])) $product->setType($data['type']);
        if (isset($data['basePrice'])) $product->setBasePrice($data['basePrice']);
        if (isset($data['documentType'])) $product->setDocumentType($data['documentType']);
        if (isset($data['isTaxable'])) $product->setIsTaxable($data['isTaxable']);
        if (isset($data['isRecurring'])) $product->setIsRecurring($data['isRecurring']);
        if (isset($data['recurringMonths'])) $product->setRecurringMonths($data['recurringMonths']);

        $this->em->flush();

        return $this->json(['success' => true]);
    }

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
