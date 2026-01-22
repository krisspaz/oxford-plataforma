<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\FamilyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Familia - agrupa estudiantes y padres
 */
#[ORM\Entity(repositoryClass: FamilyRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['family:read']],
    denormalizationContext: ['groups' => ['family:write']]
)]
class Family
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['family:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['family:read', 'family:write'])]
    private ?string $familyName = null; // Apellido principal

    #[ORM\ManyToOne(targetEntity: Guardian::class)]
    #[Groups(['family:read', 'family:write'])]
    private ?Guardian $father = null;

    #[ORM\ManyToOne(targetEntity: Guardian::class)]
    #[Groups(['family:read', 'family:write'])]
    private ?Guardian $mother = null;

    #[ORM\ManyToOne(targetEntity: Guardian::class)]
    #[Groups(['family:read', 'family:write'])]
    private ?Guardian $primaryGuardian = null; // Encargado principal

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['family:read', 'family:write'])]
    private ?string $address = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['family:read', 'family:write'])]
    private ?string $homePhone = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['family:read', 'family:write'])]
    private ?string $notes = null;

    #[ORM\OneToMany(mappedBy: 'family', targetEntity: FamilyStudent::class, cascade: ['persist', 'remove'])]
    #[Groups(['family:read'])]
    private Collection $familyStudents;

    #[ORM\Column]
    #[Groups(['family:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['family:read', 'family:write'])]
    private ?bool $isActive = true;

    public function __construct()
    {
        $this->familyStudents = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(?string $familyName): static
    {
        $this->familyName = $familyName;
        return $this;
    }

    public function getFather(): ?Guardian
    {
        return $this->father;
    }

    public function setFather(?Guardian $father): static
    {
        $this->father = $father;
        return $this;
    }

    public function getMother(): ?Guardian
    {
        return $this->mother;
    }

    public function setMother(?Guardian $mother): static
    {
        $this->mother = $mother;
        return $this;
    }

    public function getPrimaryGuardian(): ?Guardian
    {
        return $this->primaryGuardian;
    }

    public function setPrimaryGuardian(?Guardian $primaryGuardian): static
    {
        $this->primaryGuardian = $primaryGuardian;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getHomePhone(): ?string
    {
        return $this->homePhone;
    }

    public function setHomePhone(?string $homePhone): static
    {
        $this->homePhone = $homePhone;
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

    /**
     * @return Collection<int, FamilyStudent>
     */
    public function getFamilyStudents(): Collection
    {
        return $this->familyStudents;
    }

    public function addFamilyStudent(FamilyStudent $familyStudent): static
    {
        if (!$this->familyStudents->contains($familyStudent)) {
            $this->familyStudents->add($familyStudent);
            $familyStudent->setFamily($this);
        }
        return $this;
    }

    /**
     * Get all students in this family
     */
    public function getStudents(): array
    {
        return $this->familyStudents->map(fn($fs) => $fs->getStudent())->toArray();
    }

    /**
     * Get siblings of a student
     */
    public function getSiblings(Student $student): array
    {
        return $this->familyStudents
            ->filter(fn($fs) => $fs->getStudent()->getId() !== $student->getId())
            ->map(fn($fs) => $fs->getStudent())
            ->toArray();
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
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
