<?php

namespace App\Entity;

use App\Repository\TaskSubmissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a student's submission for a Task
 */
#[ORM\Entity(repositoryClass: TaskSubmissionRepository::class)]
#[ApiResource]
class TaskSubmission
{
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_GRADED = 'graded';
    public const STATUS_LATE = 'late';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'submissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Task $task = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $submittedAt = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: [self::STATUS_SUBMITTED, self::STATUS_GRADED, self::STATUS_LATE])]
    private ?string $status = self::STATUS_SUBMITTED;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: 0, max: 100)]
    private ?float $score = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $feedback = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $attachmentUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $gradedAt = null;

    #[ORM\ManyToOne]
    private ?Teacher $gradedBy = null;

    public function __construct()
    {
        $this->submittedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): static
    {
        $this->task = $task;
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

    public function getSubmittedAt(): ?\DateTimeInterface
    {
        return $this->submittedAt;
    }

    public function setSubmittedAt(\DateTimeInterface $submittedAt): static
    {
        $this->submittedAt = $submittedAt;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): static
    {
        $this->score = $score;
        return $this;
    }

    public function getFeedback(): ?string
    {
        return $this->feedback;
    }

    public function setFeedback(?string $feedback): static
    {
        $this->feedback = $feedback;
        return $this;
    }

    public function getAttachmentUrl(): ?string
    {
        return $this->attachmentUrl;
    }

    public function setAttachmentUrl(?string $attachmentUrl): static
    {
        $this->attachmentUrl = $attachmentUrl;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getGradedAt(): ?\DateTimeInterface
    {
        return $this->gradedAt;
    }

    public function setGradedAt(?\DateTimeInterface $gradedAt): static
    {
        $this->gradedAt = $gradedAt;
        return $this;
    }

    public function getGradedBy(): ?Teacher
    {
        return $this->gradedBy;
    }

    public function setGradedBy(?Teacher $gradedBy): static
    {
        $this->gradedBy = $gradedBy;
        return $this;
    }

    public function isLate(): bool
    {
        if ($this->task && $this->submittedAt) {
            return $this->submittedAt > $this->task->getDueDate();
        }
        return false;
    }
}
