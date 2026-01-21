<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AcademicLevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AcademicLevelRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['academic_level:read']],
    denormalizationContext: ['groups' => ['academic_level:write']]
)]
class AcademicLevel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['grade:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['grade:read', 'academic_level:read', 'academic_level:write'])]
    private ?string $name = null; // Preprimaria, Primaria, Básico, Bachillerato

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['grade:read', 'academic_level:read', 'academic_level:write'])]
    private ?string $code = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['academic_level:read', 'academic_level:write'])]
    private ?string $minedResolution = null; // Resolución MINEDUC

    #[ORM\Column]
    #[Groups(['academic_level:read', 'academic_level:write'])]
    private ?int $sortOrder = 0;

    #[ORM\Column]
    #[Groups(['academic_level:read', 'academic_level:write'])]
    private ?bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'level', targetEntity: Grade::class)]
    private Collection $grades;

    public function __construct()
    {
        $this->grades = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;
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

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;
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

    /**
     * @return Collection<int, Grade>
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }
}
