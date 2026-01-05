<?php

namespace App\Controller;

use App\Entity\RequestEntity;
use App\Repository\RequestEntityRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/requests')]
class RequestController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestEntityRepository $requestRepository,
        private StudentRepository $studentRepository,
        private UserRepository $userRepository
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $status = $request->query->get('status');
        
        $criteria = [];
        if ($status) {
            $criteria['status'] = $status;
        }

        $requests = $this->requestRepository->findBy($criteria, ['createdAt' => 'DESC']);
        
        return $this->json([
            'success' => true,
            'data' => array_map(fn($r) => $this->serializeRequest($r), $requests)
        ]);
    }

    #[Route('/pending', methods: ['GET'])]
    public function getPending(): JsonResponse
    {
        $requests = $this->requestRepository->findPending();
        
        return $this->json([
            'success' => true,
            'data' => array_map(fn($r) => $this->serializeRequest($r), $requests)
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $request = $this->requestRepository->find($id);
        
        if (!$request) {
            return $this->json(['error' => 'Not found'], 404);
        }

        return $this->json([
            'success' => true,
            'data' => $this->serializeRequest($request)
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $reqEntity = new RequestEntity();
        $reqEntity->setType($data['type']); // ANULACION_FACTURA, etc
        $reqEntity->setReason($data['reason']);
        $reqEntity->setDocumentReference($data['document'] ?? null);
        
        if (isset($data['studentId'])) {
            $student = $this->studentRepository->find($data['studentId']);
            if ($student) $reqEntity->setStudent($student);
        }

        // Assuming user is authenticated and we can get it. For now, using fallback or passed ID
        // In real app: $this->getUser()
        if (isset($data['userId'])) {
             $user = $this->userRepository->find($data['userId']);
             if ($user) $reqEntity->setRequestedBy($user);
        } else {
            // Mock user for now if no auth logic ready
            $user = $this->userRepository->findOneBy([]); // Get first user
            if ($user) $reqEntity->setRequestedBy($user);
        }
        
        $this->em->persist($reqEntity);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'data' => $this->serializeRequest($reqEntity)
        ], 201);
    }

    #[Route('/{id}/approve', methods: ['POST'])]
    public function approve(int $id, Request $request): JsonResponse
    {
        $reqEntity = $this->requestRepository->find($id);
        if (!$reqEntity) return $this->json(['error' => 'Not found'], 404);

        $reqEntity->setStatus('APROBADA');
        $reqEntity->setProcessedAt(new \DateTime());
        
        // Logic to actually Perform the action could go here
        // e.g. if type == ANULACION_FACTURA, call InvoiceService->annul(...)
        
        $this->em->flush();

        return $this->json(['success' => true, 'message' => 'Solicitud aprobada', 'data' => $this->serializeRequest($reqEntity)]);
    }

    #[Route('/{id}/reject', methods: ['POST'])]
    public function reject(int $id, Request $request): JsonResponse
    {
        $reqEntity = $this->requestRepository->find($id);
        if (!$reqEntity) return $this->json(['error' => 'Not found'], 404);

        $reqEntity->setStatus('RECHAZADA');
        $reqEntity->setProcessedAt(new \DateTime());
        
        $this->em->flush();

        return $this->json(['success' => true, 'message' => 'Solicitud rechazada', 'data' => $this->serializeRequest($reqEntity)]);
    }

    private function serializeRequest(RequestEntity $r): array
    {
        return [
            'id' => $r->getId(),
            'type' => $r->getType(),
            'status' => $r->getStatus(),
            'description' => $r->getReason(), // Map reason to description for frontend
            'reason' => $r->getReason(),
            'document' => $r->getDocumentReference(),
            'createdAt' => $r->getCreatedAt()->format('Y-m-d H:i'),
            'date' => $r->getCreatedAt()->format('Y-m-d H:i'), // Frontend uses 'date'
            'student' => $r->getStudent() ? $r->getStudent()->getFullName() : 'N/A',
            'requestedBy' => $r->getRequestedBy() ? $r->getRequestedBy()->getUsername() : 'Sistema',
            'studentId' => $r->getStudent()?->getId()
        ];
    }
}
