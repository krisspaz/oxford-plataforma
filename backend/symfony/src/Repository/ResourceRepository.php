<?php

namespace App\Repository;

use App\Entity\Resource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Resource>
 */
class ResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resource::class);
    }

    /**
     * Find all resources by teacher ID
     */
    public function findByTeacher(int $teacherId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.teacher = :teacherId')
            ->setParameter('teacherId', $teacherId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find resources by subject ID
     */
    public function findBySubject(int $subjectId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.subjectId = :subjectId')
            ->setParameter('subjectId', $subjectId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find resources by teacher and subject
     */
    public function findByTeacherAndSubject(int $teacherId, int $subjectId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.teacher = :teacherId')
            ->andWhere('r.subjectId = :subjectId')
            ->setParameter('teacherId', $teacherId)
            ->setParameter('subjectId', $subjectId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
