<?php

namespace App\Repository;

use App\Entity\RolePermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RolePermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RolePermission::class);
    }

    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('rp')
            ->join('rp.permission', 'p')
            ->andWhere('rp.role = :role')
            ->andWhere('rp.isGranted = :granted')
            ->setParameter('role', $role)
            ->setParameter('granted', true)
            ->getQuery()
            ->getResult();
    }

    public function getPermissionCodesForRole(string $role): array
    {
        $rolePermissions = $this->findByRole($role);
        return array_map(fn($rp) => $rp->getPermission()->getCode(), $rolePermissions);
    }

    public function hasPermission(string $role, string $permissionCode): bool
    {
        $result = $this->createQueryBuilder('rp')
            ->join('rp.permission', 'p')
            ->andWhere('rp.role = :role')
            ->andWhere('p.code = :code')
            ->andWhere('rp.isGranted = :granted')
            ->setParameter('role', $role)
            ->setParameter('code', $permissionCode)
            ->setParameter('granted', true)
            ->getQuery()
            ->getOneOrNullResult();

        return $result !== null;
    }
}
