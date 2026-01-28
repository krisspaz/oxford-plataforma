<?php

namespace App\Controller;

use App\Entity\Bimester;
use App\Entity\SchoolCycle;
use App\Repository\BimesterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/bimesters')]
class BimesterController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private BimesterRepository $bimesterRepository
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $cycle = $request->query->get('cycle');
        
        $criteria = [];
        if ($cycle) $criteria['schoolCycle'] = $cycle;
        
        $bimesters = $this->bimesterRepository->findBy($criteria, ['number' => 'ASC']);
        
        return $this->json([
            'success' => true,
            'data' => array_map(fn($b) => $this->serializeBimester($b), $bimesters)
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $bimester = $this->bimesterRepository->find($id);
        
        if (!$bimester) {
            return $this->json(['success' => false, 'error' => 'Bimestre no encontrado'], 404);
        }
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeBimester($bimester)
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $bimester = new Bimester();
        $bimester->setNumber($data['number']);
        $bimester->setName($data['name']);
        $bimester->setStartDate(new \DateTime($data['startDate']));
        $bimester->setEndDate(new \DateTime($data['endDate']));
        $bimester->setMaxScore($data['maxScore'] ?? 100);
        $bimester->setPercentage($data['percentage'] ?? 25);
        $bimester->setPercentage($data['percentage'] ?? 25);
        
        // Find or create SchoolCycle
        $year = $data['year'] ?? (new \DateTime($data['startDate']))->format('Y');
        $cycle = $this->em->getRepository(SchoolCycle::class)->findOneBy(['name' => $year]);
        
        if (!$cycle) {
            $cycle = new SchoolCycle();
            $cycle->setName((string)$year);
            $cycle->setStartDate(new \DateTime($year . '-01-01'));
            $cycle->setEndDate(new \DateTime($year . '-12-31'));
            $cycle->setIsActive(true);
            $this->em->persist($cycle);
        }
        
        $bimester->setSchoolCycle($cycle);
        
        $this->em->persist($bimester);
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeBimester($bimester)
        ], 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $bimester = $this->bimesterRepository->find($id);
        
        if (!$bimester) {
            return $this->json(['success' => false, 'error' => 'Bimestre no encontrado'], 404);
        }
        
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) $bimester->setName($data['name']);
        if (isset($data['startDate'])) $bimester->setStartDate(new \DateTime($data['startDate']));
        if (isset($data['endDate'])) $bimester->setEndDate(new \DateTime($data['endDate']));
        if (isset($data['maxScore'])) $bimester->setMaxScore($data['maxScore']);
        if (isset($data['percentage'])) $bimester->setPercentage($data['percentage']);
        
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'data' => $this->serializeBimester($bimester)
        ]);
    }

    #[Route('/{id}/close', methods: ['POST'])]
    public function close(int $id): JsonResponse
    {
        $bimester = $this->bimesterRepository->find($id);
        
        if (!$bimester) {
            return $this->json(['success' => false, 'error' => 'Bimestre no encontrado'], 404);
        }
        
        $this->denyAccessUnlessGranted('ROLE_COORDINACION');
        
        $bimester->close();
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'message' => 'Bimestre cerrado correctamente',
            'data' => $this->serializeBimester($bimester)
        ]);
    }

    #[Route('/{id}/open', methods: ['POST'])]
    public function open(int $id): JsonResponse
    {
        $bimester = $this->bimesterRepository->find($id);
        
        if (!$bimester) {
            return $this->json(['success' => false, 'error' => 'Bimestre no encontrado'], 404);
        }
        
        $this->denyAccessUnlessGranted('ROLE_COORDINACION');
        
        $bimester->open();
        $this->em->flush();
        
        return $this->json([
            'success' => true,
            'message' => 'Bimestre abierto correctamente',
            'data' => $this->serializeBimester($bimester)
        ]);
    }

    #[Route('/current', methods: ['GET'])]
    public function current(): JsonResponse
    {
        $today = new \DateTime();
        $bimesters = $this->bimesterRepository->findAll();
        
        $current = null;
        foreach ($bimesters as $b) {
            if ($b->getIsActive() && $b->getStartDate() <= $today && $today <= $b->getEndDate()) {
                $current = $b;
                break;
            }
        }
        
        return $this->json([
            'success' => true,
            'data' => $current ? $this->serializeBimester($current) : null
        ]);
    }

    private function serializeBimester(Bimester $b): array
    {
        return [
            'id' => $b->getId(),
            'number' => $b->getNumber(),
            'name' => $b->getName(),
            'startDate' => $b->getStartDate()?->format('Y-m-d'),
            'endDate' => $b->getEndDate()?->format('Y-m-d'),
            'maxScore' => $b->getMaxScore(),
            'percentage' => $b->getPercentage(),
            'isClosed' => $b->getIsClosed(),
            'isActive' => $b->getIsActive(),
            'shouldAutoClose' => $b->shouldAutoClose(),
            'schoolCycle' => $b->getSchoolCycle()?->getId(),
        ];
    }
}
