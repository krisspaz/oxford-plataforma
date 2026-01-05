<?php

namespace App\Repository;

use App\Entity\Permission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    public function findByModule(string $module): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.module = :module')
            ->andWhere('p.isActive = :active')
            ->setParameter('module', $module)
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }

    public function findAllGroupedByModule(): array
    {
        $permissions = $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.module', 'ASC')
            ->addOrderBy('p.action', 'ASC')
            ->getQuery()
            ->getResult();

        $grouped = [];
        foreach ($permissions as $permission) {
            $grouped[$permission->getModule()][] = $permission;
        }
        return $grouped;
    }
}
