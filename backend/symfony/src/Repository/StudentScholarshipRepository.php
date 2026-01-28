<?php

namespace App\Repository;

use App\Entity\StudentScholarship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StudentScholarship>
 *
 * @method StudentScholarship|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentScholarship|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentScholarship[]    findAll()
 * @method StudentScholarship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentScholarshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentScholarship::class);
    }
}
