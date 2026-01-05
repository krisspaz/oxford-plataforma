<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PackageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PackageRepository::class)]
#[ApiResource]
class Package
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null; // Normal, Becado, etc.

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $totalPrice = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: SchoolCycle::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?SchoolCycle $schoolCycle = null;

    #[ORM\ManyToMany(targetEntity: Grade::class)]
    private Collection $applicableGrades;

    #[ORM\OneToMany(mappedBy: 'package', targetEntity: PackageDetail::class, cascade: ['persist', 'remove'])]
    private Collection $details;

    #[ORM\Column]
    private ?bool $isActive = true;

    public function __construct()
    {
        $this->applicableGrades = new ArrayCollection();
        $this->details = new ArrayCollection();
    }

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

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): static
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
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

    /**
     * @return Collection<int, Grade>
     */
    public function getApplicableGrades(): Collection
    {
        return $this->applicableGrades;
    }

    public function addApplicableGrade(Grade $grade): static
    {
        if (!$this->applicableGrades->contains($grade)) {
            $this->applicableGrades->add($grade);
        }
        return $this;
    }

    /**
     * @return Collection<int, PackageDetail>
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(PackageDetail $detail): static
    {
        if (!$this->details->contains($detail)) {
            $this->details->add($detail);
            $detail->setPackage($this);
        }
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

    #[ApiResource]
    public function getCycle(): ?string
    {
        return $this->schoolCycle ? $this->schoolCycle->getName() : null;
    }
}
