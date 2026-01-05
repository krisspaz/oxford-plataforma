<?php

namespace App\Service;

use App\Entity\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadService
{
    private string $uploadDir;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, string $uploadDir = '/var/www/symfony/public/uploads')
    {
        $this->em = $em;
        $this->uploadDir = $uploadDir;
    }

    public function upload(UploadedFile $file, string $entityType, int $entityId, string $documentType = 'OTRO', ?int $userId = null): Document
    {
        // Create directory if not exists
        $targetDir = $this->uploadDir . '/' . $entityType . '/' . $entityId;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $extension = $file->guessExtension() ?? 'bin';
        $newFilename = uniqid() . '_' . time() . '.' . $extension;

        // Move file
        $file->move($targetDir, $newFilename);

        // Create document record
        $document = new Document();
        $document->setEntityType($entityType);
        $document->setEntityId($entityId);
        $document->setDocumentType($documentType);
        $document->setOriginalName($originalName);
        $document->setStoredPath($entityType . '/' . $entityId . '/' . $newFilename);
        $document->setMimeType($file->getClientMimeType() ?? 'application/octet-stream');
        $document->setFileSize($file->getSize());

        $this->em->persist($document);
        $this->em->flush();

        return $document;
    }

    public function delete(Document $document): void
    {
        $fullPath = $this->uploadDir . '/' . $document->getStoredPath();
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        
        $document->setIsActive(false);
        $this->em->flush();
    }

    public function getPublicUrl(Document $document): string
    {
        return '/uploads/' . $document->getStoredPath();
    }
}
