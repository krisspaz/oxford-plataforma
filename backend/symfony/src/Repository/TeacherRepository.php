<?php

namespace App\Repository;

use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Teacher>
 */
class TeacherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Teacher::class);
    }

    public function findActive(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.isActive = true')
            ->orderBy('t.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBySpecialization(string $specialization): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.specialization LIKE :spec')
            ->setParameter('spec', "%$specialization%")
            ->getQuery()
            ->getResult();
    }

    public function search(string $term): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.firstName LIKE :term OR t.lastName LIKE :term OR t.email LIKE :term')
            ->setParameter('term', "%$term%")
            ->orderBy('t.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
