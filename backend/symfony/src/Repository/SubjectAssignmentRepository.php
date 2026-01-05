<?php

namespace App\Repository;

use App\Entity\SubjectAssignment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubjectAssignment>
 *
 * @method SubjectAssignment|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubjectAssignment|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubjectAssignment[]    findAll()
 * @method SubjectAssignment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubjectAssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubjectAssignment::class);
    }

    public function save(SubjectAssignment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SubjectAssignment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
