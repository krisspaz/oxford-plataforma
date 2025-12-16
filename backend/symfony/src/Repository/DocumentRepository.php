<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function findByEntity(string $entityType, int $entityId): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.entityType = :type')
            ->andWhere('d.entityId = :id')
            ->andWhere('d.isActive = :active')
            ->setParameter('type', $entityType)
            ->setParameter('id', $entityId)
            ->setParameter('active', true)
            ->orderBy('d.uploadedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPhotoByEntity(string $entityType, int $entityId): ?Document
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.entityType = :type')
            ->andWhere('d.entityId = :id')
            ->andWhere('d.documentType = :docType')
            ->andWhere('d.isActive = :active')
            ->setParameter('type', $entityType)
            ->setParameter('id', $entityId)
            ->setParameter('docType', Document::TYPE_PHOTO)
            ->setParameter('active', true)
            ->orderBy('d.uploadedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
