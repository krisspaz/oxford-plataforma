<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\BimesterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BimesterRepository::class)]
#[ApiResource]
class Bimester
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null; // Primer Bimestre, Segundo Bimestre, etc.

    #[ORM\Column]
    private ?int $number = null; // 1, 2, 3, 4

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column]
    private ?float $maxScore = 100.0;

    #[ORM\Column]
    private ?float $percentage = 25.0; // Porcentaje del año

    #[ORM\Column]
    private ?bool $isClosed = false;

    #[ORM\Column]
    private ?bool $isActive = true;

    #[ORM\ManyToOne(targetEntity: SchoolCycle::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?SchoolCycle $schoolCycle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getMaxScore(): ?float
    {
        return $this->maxScore;
    }

    public function setMaxScore(float $maxScore): static
    {
        $this->maxScore = $maxScore;
        return $this;
    }

    public function getPercentage(): ?float
    {
        return $this->percentage;
    }

    public function setPercentage(float $percentage): static
    {
        $this->percentage = $percentage;
        return $this;
    }

    public function isClosed(): ?bool
    {
        return $this->isClosed;
    }

    public function setIsClosed(bool $isClosed): static
    {
        $this->isClosed = $isClosed;
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

    public function getSchoolCycle(): ?SchoolCycle
    {
        return $this->schoolCycle;
    }

    public function setSchoolCycle(?SchoolCycle $schoolCycle): static
    {
        $this->schoolCycle = $schoolCycle;
        return $this;
    }

    public function shouldAutoClose(): bool
    {
        return !$this->isClosed && $this->endDate && new \DateTime() > $this->endDate;
    }

    public function close(): static
    {
        $this->isClosed = true;
        return $this;
    }

    public function open(): static
    {
        $this->isClosed = false;
        return $this;
    }
}
