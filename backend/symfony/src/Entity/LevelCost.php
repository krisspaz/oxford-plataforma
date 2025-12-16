<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\LevelCostRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Costos autorizados por MINEDUC por nivel académico
 */
#[ORM\Entity(repositoryClass: LevelCostRepository::class)]
#[ApiResource]
class LevelCost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: AcademicLevel::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?AcademicLevel $academicLevel = null;

    #[ORM\ManyToOne(targetEntity: SchoolCycle::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?SchoolCycle $schoolCycle = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $inscriptionCost = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $monthlyCost = '0.00';

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $minedResolution = null; // Número de resolución MINEDUC

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $resolutionDate = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private ?bool $isApproved = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAcademicLevel(): ?AcademicLevel
    {
        return $this->academicLevel;
    }

    public function setAcademicLevel(?AcademicLevel $academicLevel): static
    {
        $this->academicLevel = $academicLevel;
        return $this;
    }

    public function getSchoolCycle(): ?SchoolCycle
    {
        return $this->schoolCycle;
    }

    public function setSchoolCycle(?SchoolCycle $schoolCycle): static
    {
        $this->schoolCycle = $schoolCycle;
        return $this;
    }

    public function getInscriptionCost(): ?string
    {
        return $this->inscriptionCost;
    }

    public function setInscriptionCost(string $inscriptionCost): static
    {
        $this->inscriptionCost = $inscriptionCost;
        return $this;
    }

    public function getMonthlyCost(): ?string
    {
        return $this->monthlyCost;
    }

    public function setMonthlyCost(string $monthlyCost): static
    {
        $this->monthlyCost = $monthlyCost;
        return $this;
    }

    public function getMinedResolution(): ?string
    {
        return $this->minedResolution;
    }

    public function setMinedResolution(?string $minedResolution): static
    {
        $this->minedResolution = $minedResolution;
        return $this;
    }

    public function getResolutionDate(): ?\DateTimeInterface
    {
        return $this->resolutionDate;
    }

    public function setResolutionDate(?\DateTimeInterface $resolutionDate): static
    {
        $this->resolutionDate = $resolutionDate;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function isApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): static
    {
        $this->isApproved = $isApproved;
        return $this;
    }
}
