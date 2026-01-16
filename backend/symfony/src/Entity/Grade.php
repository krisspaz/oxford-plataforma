<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\GradeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GradeRepository::class)]
// Disable API Platform auto-routes - using custom GradeController instead
// #[ApiResource(...)] is removed intentionally
class Grade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['grade:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups(['grade:read', 'grade:write'])]
    private ?string $name = null; // e.g. "First Grade", "5to Bachillerato"

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['grade:read', 'grade:write'])]
    private ?string $code = null; // e.g. "1PRI", "5BAC"

    #[ORM\ManyToOne(targetEntity: AcademicLevel::class, inversedBy: 'grades')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['grade:read', 'grade:write'])]
    private ?AcademicLevel $level = null;

    #[ORM\Column]
    #[Groups(['grade:read', 'grade:write'])]
    private ?bool $isActive = true;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['grade:read', 'grade:write'])]
    private ?int $sortOrder = 0;

    #[ORM\OneToMany(mappedBy: 'grade', targetEntity: Section::class)]
    #[Groups(['grade:read'])]
    private Collection $sections;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
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

    public function getLevel(): ?AcademicLevel
    {
        return $this->level;
    }

    public function setLevel(?AcademicLevel $level): static
    {
        $this->level = $level;
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

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    /**
     * @return Collection<int, Section>
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): static
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
            $section->setGrade($this);
        }
        return $this;
    }

    public function removeSection(Section $section): static
    {
        if ($this->sections->removeElement($section)) {
            // set the owning side to null (unless already changed)
            if ($section->getGrade() === $this) {
                $section->setGrade(null);
            }
        }
        return $this;
    }
}
