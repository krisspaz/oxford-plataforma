<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\StudentScholarshipRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StudentScholarshipRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['student_scholarship:read']],
    denormalizationContext: ['groups' => ['student_scholarship:write']]
)]
class StudentScholarship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['student_scholarship:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'scholarships')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['student_scholarship:read', 'student_scholarship:write'])]
    private ?Student $student = null;

    #[ORM\ManyToOne(targetEntity: Scholarship::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['student_scholarship:read', 'student_scholarship:write'])]
    private ?Scholarship $scholarship = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['student_scholarship:read', 'student_scholarship:write'])]
    private ?\DateTimeInterface $assignedAt = null;

    #[ORM\ManyToOne(targetEntity: SchoolCycle::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['student_scholarship:read', 'student_scholarship:write'])]
    private ?SchoolCycle $schoolCycle = null;

    #[ORM\Column]
    #[Groups(['student_scholarship:read', 'student_scholarship:write'])]
    private ?bool $isActive = true;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['student_scholarship:read', 'student_scholarship:write'])]
    private ?string $notes = null;

    public function __construct()
    {
        $this->assignedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getScholarship(): ?Scholarship
    {
        return $this->scholarship;
    }

    public function setScholarship(?Scholarship $scholarship): static
    {
        $this->scholarship = $scholarship;
        return $this;
    }

    public function getAssignedAt(): ?\DateTimeInterface
    {
        return $this->assignedAt;
    }

    public function setAssignedAt(\DateTimeInterface $assignedAt): static
    {
        $this->assignedAt = $assignedAt;
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

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
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
}
