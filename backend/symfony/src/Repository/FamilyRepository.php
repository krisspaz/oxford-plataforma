<?php

namespace App\Repository;

use App\Entity\Family;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FamilyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Family::class);
    }

    public function findByStudent(int $studentId): ?Family
    {
        return $this->createQueryBuilder('f')
            ->join('f.familyStudents', 'fs')
            ->andWhere('fs.student = :student')
            ->setParameter('student', $studentId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findWithStudents(): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.familyStudents', 'fs')
            ->leftJoin('fs.student', 's')
            ->leftJoin('f.father', 'father')
            ->leftJoin('f.mother', 'mother')
            ->andWhere('f.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('f.familyName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function search(string $term): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.father', 'father')
            ->leftJoin('f.mother', 'mother')
            ->where('f.familyName LIKE :term')
            ->orWhere('father.firstName LIKE :term')
            ->orWhere('mother.firstName LIKE :term')
            ->andWhere('f.isActive = :active')
            ->setParameter('term', '%' . $term . '%')
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }
}
