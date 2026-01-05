<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\StaffRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StaffRepository::class)]
#[ApiResource]
class Staff extends Person
{
    #[ORM\Column(length: 50, unique: true, nullable: true)]
    private ?string $employeeCode = null;

    #[ORM\Column(length: 100)]
    private ?string $position = null; // Cargo administrativo

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $department = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $hireDate = null;

    #[ORM\Column(length: 50)]
    private ?string $contractType = 'Tiempo Completo';

    public function getEmployeeCode(): ?string
    {
        return $this->employeeCode;
    }

    public function setEmployeeCode(?string $employeeCode): static
    {
        $this->employeeCode = $employeeCode;
        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;
        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): static
    {
        $this->department = $department;
        return $this;
    }

    public function getHireDate(): ?\DateTimeInterface
    {
        return $this->hireDate;
    }

    public function setHireDate(?\DateTimeInterface $hireDate): static
    {
        $this->hireDate = $hireDate;
        return $this;
    }

    public function getContractType(): ?string
    {
        return $this->contractType;
    }

    public function setContractType(string $contractType): static
    {
        $this->contractType = $contractType;
        return $this;
    }
}
