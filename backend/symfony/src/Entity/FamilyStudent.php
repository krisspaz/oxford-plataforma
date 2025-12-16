<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\FamilyStudentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Relación Familia-Estudiante con tipo de relación
 */
#[ORM\Entity(repositoryClass: FamilyStudentRepository::class)]
#[ApiResource]
class FamilyStudent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Family::class, inversedBy: 'familyStudents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Family $family = null;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\Column(length: 50)]
    private ?string $relationship = 'HIJO'; // HIJO, HIJASTRO, ADOPTADO, etc

    #[ORM\Column]
    private ?bool $isPrimary = false; // Es el responsable económico principal

    #[ORM\Column]
    private ?\DateTimeImmutable $linkedAt = null;

    public function __construct()
    {
        $this->linkedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFamily(): ?Family
    {
        return $this->family;
    }

    public function setFamily(?Family $family): static
    {
        $this->family = $family;
        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;
        return $this;
    }

    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    public function setRelationship(string $relationship): static
    {
        $this->relationship = $relationship;
        return $this;
    }

    public function isPrimary(): ?bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): static
    {
        $this->isPrimary = $isPrimary;
        return $this;
    }

    public function getLinkedAt(): ?\DateTimeImmutable
    {
        return $this->linkedAt;
    }
}
