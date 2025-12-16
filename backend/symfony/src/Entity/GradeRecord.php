<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GradeRecordRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GradeRecordRepository::class)]
#[ApiResource]
class GradeRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne(targetEntity: SubjectAssignment::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?SubjectAssignment $subjectAssignment = null;

    #[ORM\ManyToOne(targetEntity: Bimester::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bimester $bimester = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $score = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comments = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    private ?Teacher $enteredBy = null;

    #[ORM\Column]
    private ?bool $isLocked = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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

    public function getSubjectAssignment(): ?SubjectAssignment
    {
        return $this->subjectAssignment;
    }

    public function setSubjectAssignment(?SubjectAssignment $subjectAssignment): static
    {
        $this->subjectAssignment = $subjectAssignment;
        return $this;
    }

    public function getBimester(): ?Bimester
    {
        return $this->bimester;
    }

    public function setBimester(?Bimester $bimester): static
    {
        $this->bimester = $bimester;
        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(?string $score): static
    {
        $this->score = $score;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): static
    {
        $this->comments = $comments;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getEnteredBy(): ?Teacher
    {
        return $this->enteredBy;
    }

    public function setEnteredBy(?Teacher $enteredBy): static
    {
        $this->enteredBy = $enteredBy;
        return $this;
    }

    public function isLocked(): ?bool
    {
        return $this->isLocked;
    }

    public function setIsLocked(bool $isLocked): static
    {
        $this->isLocked = $isLocked;
        return $this;
    }

    public function canBeEdited(): bool
    {
        if ($this->isLocked) {
            return false;
        }
        
        $bimester = $this->getBimester();
        if ($bimester && $bimester->isClosed()) {
            return false;
        }
        
        return true;
    }
}
