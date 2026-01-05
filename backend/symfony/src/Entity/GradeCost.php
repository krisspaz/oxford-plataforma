<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GradeCostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GradeCostRepository::class)]
#[ApiResource]
class GradeCost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $gradeLevel = null; // e.g., "1ro Primaria"

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $enrollmentFee = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $monthlyFee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGradeLevel(): ?string
    {
        return $this->gradeLevel;
    }

    public function setGradeLevel(string $gradeLevel): static
    {
        $this->gradeLevel = $gradeLevel;

        return $this;
    }

    public function getEnrollmentFee(): ?string
    {
        return $this->enrollmentFee;
    }

    public function setEnrollmentFee(string $enrollmentFee): static
    {
        $this->enrollmentFee = $enrollmentFee;

        return $this;
    }

    public function getMonthlyFee(): ?string
    {
        return $this->monthlyFee;
    }

    public function setMonthlyFee(string $monthlyFee): static
    {
        $this->monthlyFee = $monthlyFee;

        return $this;
    }
}
