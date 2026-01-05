<?php

namespace App\Entity;

use App\Repository\TaskGradeRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

/**
 * Represents the assignment of a Task to a specific Grade/Section
 * Allows a single task to be assigned to multiple grades
 */
#[ORM\Entity(repositoryClass: TaskGradeRepository::class)]
#[ApiResource]
class TaskGrade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'taskGrades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Task $task = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Grade $grade = null;

    #[ORM\ManyToOne]
    private ?Section $section = null;

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

    public function getGrade(): ?Grade
    {
        return $this->grade;
    }

    public function setGrade(?Grade $grade): static
    {
        $this->grade = $grade;
        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): static
    {
        $this->section = $section;
        return $this;
    }
}
