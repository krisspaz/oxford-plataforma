<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/documents')]
class DocumentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private DocumentRepository $documentRepository,
        private FileUploadService $uploadService
    ) {}

    #[Route('', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $entityType = $request->query->get('entityType');
        $entityId = $request->query->get('entityId');

        if (!$entityType || !$entityId) {
            return $this->json(['error' => 'entityType and entityId required'], 400);
        }

        $documents = $this->documentRepository->findByEntity($entityType, (int)$entityId);

        return $this->json([
            'success' => true,
            'data' => array_map(fn($doc) => [
                'id' => $doc->getId(),
                'documentType' => $doc->getDocumentType(),
                'originalName' => $doc->getOriginalName(),
                'mimeType' => $doc->getMimeType(),
                'fileSize' => $doc->getFileSize(),
                'url' => $this->uploadService->getPublicUrl($doc),
                'uploadedAt' => $doc->getUploadedAt()->format('Y-m-d H:i:s'),
            ], $documents)
        ]);
    }

    #[Route('/upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $file = $request->files->get('file');
        $entityType = $request->request->get('entityType');
        $entityId = $request->request->get('entityId');
        $documentType = $request->request->get('documentType', 'OTRO');

        if (!$file || !$entityType || !$entityId) {
            return $this->json(['error' => 'Missing required fields'], 400);
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        if (!in_array($file->getClientMimeType(), $allowedTypes)) {
            return $this->json(['error' => 'Invalid file type'], 400);
        }

        // Max 5MB
        if ($file->getSize() > 5 * 1024 * 1024) {
            return $this->json(['error' => 'File too large (max 5MB)'], 400);
        }

        try {
            $document = $this->uploadService->upload($file, $entityType, (int)$entityId, $documentType);
            
            return $this->json([
                'success' => true,
                'data' => [
                    'id' => $document->getId(),
                    'url' => $this->uploadService->getPublicUrl($document),
                    'originalName' => $document->getOriginalName(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $document = $this->documentRepository->find($id);
        
        if (!$document) {
            return $this->json(['error' => 'Document not found'], 404);
        }

        $this->uploadService->delete($document);

        return $this->json(['success' => true]);
    }

    #[Route('/photo/{entityType}/{entityId}', methods: ['GET'])]
    public function getPhoto(string $entityType, int $entityId): JsonResponse
    {
        $photo = $this->documentRepository->findPhotoByEntity($entityType, $entityId);

        if (!$photo) {
            return $this->json(['url' => null]);
        }

        return $this->json([
            'url' => $this->uploadService->getPublicUrl($photo)
        ]);
    }
}
