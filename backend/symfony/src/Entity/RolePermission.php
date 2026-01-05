<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RolePermissionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Relación Rol-Permiso
 */
#[ORM\Entity(repositoryClass: RolePermissionRepository::class)]
#[ApiResource]
class RolePermission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $role = null; // ROLE_SECRETARIA, ROLE_CONTABILIDAD, etc

    #[ORM\ManyToOne(targetEntity: Permission::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Permission $permission = null;

    #[ORM\Column]
    private ?bool $isGranted = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getPermission(): ?Permission
    {
        return $this->permission;
    }

    public function setPermission(?Permission $permission): static
    {
        $this->permission = $permission;
        return $this;
    }

    public function isGranted(): ?bool
    {
        return $this->isGranted;
    }

    public function setIsGranted(bool $isGranted): static
    {
        $this->isGranted = $isGranted;
        return $this;
    }
}
