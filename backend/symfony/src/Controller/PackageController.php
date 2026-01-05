<?php

namespace App\Controller;

use App\Entity\Package;
use App\Entity\PackageDetail;
use App\Repository\PackageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/packages')]
class PackageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private PackageRepository $packageRepository
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $cycle = $request->query->get('cycle');
        $active = $request->query->get('active');
        
        $criteria = [];
        if ($cycle) $criteria['schoolCycle'] = $cycle;
        if ($active !== null) $criteria['isActive'] = $active === 'true';
        
        $packages = $this->packageRepository->findBy($criteria, ['name' => 'ASC']);
        
        return $this->json([
            'success' => true,
            'data' => array_map(fn($p) => $this->serializePackage($p), $packages)
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $package = $this->packageRepository->find($id);
        
        if (!$package) {
            return $this->json(['success' => false, 'error' => 'Paquete no encontrado'], 404);
        }
        
        return $this->json([
            'success' => true,
            'data' => $this->serializePackage($package, true)
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $package = new Package();
        $package->setName($data['name']);
        $package->setDescription($data['description'] ?? null);
        // TODO: Set schoolCycle and grades
        
        // Crear detalles si vienen
        if (isset($data['details']) && is_array($data['details'])) {
            foreach ($data['details'] as $detailData) {
                $detail = new PackageDetail();
                $detail->setProductName($detailData['productName']);
                $detail->setPrice($detailData['price']);
                $detail->setDocumentType($detailData['documentType'] ?? 'RECIBO_SAT');
                $detail->setProductType($detailData['productType'] ?? 'Servicio');
                $detail->setQuantity($detailData['quantity'] ?? 1);
                $package->addDetail($detail);
            }
        }
        
        $package->calculateTotalPrice();
        
        $this->em->persist($package);
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializePackage($package, true)
        ], 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $package = $this->packageRepository->find($id);
        
        if (!$package) {
            return $this->json(['success' => false, 'error' => 'Paquete no encontrado'], 404);
        }
        
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) $package->setName($data['name']);
        if (isset($data['description'])) $package->setDescription($data['description']);
        if (isset($data['isActive'])) $package->setIsActive($data['isActive']);
        
        $package->calculateTotalPrice();
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializePackage($package, true)
        ]);
    }

    #[Route('/{id}/details', methods: ['POST'])]
    public function addDetail(int $id, Request $request): JsonResponse
    {
        $package = $this->packageRepository->find($id);
        
        if (!$package) {
            return $this->json(['success' => false, 'error' => 'Paquete no encontrado'], 404);
        }
        
        $data = json_decode($request->getContent(), true);
        
        $detail = new PackageDetail();
        $detail->setProductName($data['productName']);
        $detail->setPrice($data['price']);
        $detail->setDocumentType($data['documentType'] ?? 'RECIBO_SAT');
        $detail->setProductType($data['productType'] ?? 'Servicio');
        $detail->setQuantity($data['quantity'] ?? 1);
        $package->addDetail($detail);
        
        $package->calculateTotalPrice();
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializePackage($package, true)
        ]);
    }

    private function serializePackage(Package $p, bool $includeDetails = false): array
    {
        $data = [
            'id' => $p->getId(),
            'name' => $p->getName(),
            'totalPrice' => $p->getTotalPrice(),
            'description' => $p->getDescription(),
            'isActive' => $p->getIsActive(),
            'schoolCycle' => $p->getSchoolCycle()?->getId(),
            'gradeCount' => $p->getApplicableGrades()->count(),
        ];
        
        if ($includeDetails) {
            $data['details'] = array_map(fn($d) => [
                'id' => $d->getId(),
                'productName' => $d->getProductName(),
                'price' => $d->getPrice(),
                'documentType' => $d->getDocumentType(),
                'productType' => $d->getProductType(),
                'quantity' => $d->getQuantity(),
                'sortOrder' => $d->getSortOrder(),
            ], $p->getDetails()->toArray());
        }
        
        return $data;
    }
}
