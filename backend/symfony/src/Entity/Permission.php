<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Permisos granulares por módulo
 */
#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[ApiResource]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $module = null; // inscripciones, pagos, notas, etc

    #[ORM\Column(length: 50)]
    private ?string $action = null; // view, create, edit, delete, approve

    #[ORM\Column(length: 100)]
    private ?string $code = null; // inscripciones.create, pagos.view, etc

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function setModule(string $module): static
    {
        $this->module = $module;
        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }
}
