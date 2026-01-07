<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * Unified Pricing Tier Entity
 * ============================
 * Consolidates: LevelCost + GradeCost
 * 
 * Handles all pricing configurations for academic levels and grades.
 * Can be associated with either an AcademicLevel or a specific Grade.
 */
#[ORM\Entity]
#[ORM\Table(name: 'pricing_tier')]
#[ORM\UniqueConstraint(
    name: 'unique_pricing',
    columns: ['school_cycle_id', 'academic_level_id', 'grade_id']
)]
#[ApiResource]
class PricingTier
{
    public const TYPE_LEVEL = 'level';
    public const TYPE_GRADE = 'grade';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: SchoolCycle::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?SchoolCycle $schoolCycle = null;

    #[ORM\ManyToOne(targetEntity: AcademicLevel::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?AcademicLevel $academicLevel = null;

    #[ORM\ManyToOne(targetEntity: Grade::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Grade $grade = null;

    #[ORM\Column(length: 20)]
    private string $type = self::TYPE_LEVEL; // 'level' or 'grade'

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $inscriptionFee = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $monthlyFee = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $annualFee = '0.00';

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $minedResolution = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $resolutionDate = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private bool $isApproved = false;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAcademicLevel(): ?AcademicLevel
    {
        return $this->academicLevel;
    }

    public function setAcademicLevel(?AcademicLevel $academicLevel): static
    {
        $this->academicLevel = $academicLevel;
        $this->type = self::TYPE_LEVEL;
        return $this;
    }

    public function getGrade(): ?Grade
    {
        return $this->grade;
    }

    public function setGrade(?Grade $grade): static
    {
        $this->grade = $grade;
        $this->type = self::TYPE_GRADE;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getInscriptionFee(): string
    {
        return $this->inscriptionFee;
    }

    public function setInscriptionFee(string $inscriptionFee): static
    {
        $this->inscriptionFee = $inscriptionFee;
        return $this;
    }

    public function getMonthlyFee(): string
    {
        return $this->monthlyFee;
    }

    public function setMonthlyFee(string $monthlyFee): static
    {
        $this->monthlyFee = $monthlyFee;
        return $this;
    }

    public function getAnnualFee(): string
    {
        return $this->annualFee;
    }

    public function setAnnualFee(string $annualFee): static
    {
        $this->annualFee = $annualFee;
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

    public function isApproved(): bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): static
    {
        $this->isApproved = $isApproved;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get the name of this pricing tier for display
     */
    public function getDisplayName(): string
    {
        if ($this->grade) {
            return $this->grade->getName() ?? 'Grade';
        }
        if ($this->academicLevel) {
            return $this->academicLevel->getName() ?? 'Level';
        }
        return 'Unknown';
    }

    /**
     * Calculate total annual cost
     */
    public function getTotalAnnualCost(): float
    {
        $monthly = (float)$this->monthlyFee * 10; // 10 months typically
        $inscription = (float)$this->inscriptionFee;
        return $monthly + $inscription;
    }
}
