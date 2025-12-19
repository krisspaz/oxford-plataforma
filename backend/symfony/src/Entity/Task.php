<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ApiResource]
class Task
{
    public const TYPE_HOMEWORK = 'tarea';
    public const TYPE_EXAM = 'examen';
    public const TYPE_PROJECT = 'proyecto';
    public const TYPE_ACTIVITY = 'actividad';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: [self::TYPE_HOMEWORK, self::TYPE_EXAM, self::TYPE_PROJECT, self::TYPE_ACTIVITY])]
    private ?string $type = self::TYPE_HOMEWORK;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: [self::STATUS_ACTIVE, self::STATUS_COMPLETED, self::STATUS_CANCELLED])]
    private ?string $status = self::STATUS_ACTIVE;

    #[ORM\Column]
    #[Assert\Range(min: 1, max: 100)]
    private ?int $points = 10;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Teacher $teacher = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bimester $bimester = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SchoolCycle $schoolCycle = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Course $course = null;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskGrade::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $taskGrades;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskSubmission::class)]
    private Collection $submissions;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->taskGrades = new ArrayCollection();
        $this->submissions = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
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

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeInterface $dueDate): static
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
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

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;
        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): static
    {
        $this->teacher = $teacher;
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

    public function getSchoolCycle(): ?SchoolCycle
    {
        return $this->schoolCycle;
    }

    public function setSchoolCycle(?SchoolCycle $schoolCycle): static
    {
        $this->schoolCycle = $schoolCycle;
        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;
        return $this;
    }

    /**
     * @return Collection<int, TaskGrade>
     */
    public function getTaskGrades(): Collection
    {
        return $this->taskGrades;
    }

    public function addTaskGrade(TaskGrade $taskGrade): static
    {
        if (!$this->taskGrades->contains($taskGrade)) {
            $this->taskGrades->add($taskGrade);
            $taskGrade->setTask($this);
        }
        return $this;
    }

    public function removeTaskGrade(TaskGrade $taskGrade): static
    {
        if ($this->taskGrades->removeElement($taskGrade)) {
            if ($taskGrade->getTask() === $this) {
                $taskGrade->setTask(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, TaskSubmission>
     */
    public function getSubmissions(): Collection
    {
        return $this->submissions;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getSubmissionCount(): int
    {
        return $this->submissions->count();
    }

    public function getPendingCount(): int
    {
        $total = 0;
        foreach ($this->taskGrades as $taskGrade) {
            // Count students in each grade/section
            $total += 25; // Placeholder - would need actual student count
        }
        return max(0, $total - $this->getSubmissionCount());
    }
}
