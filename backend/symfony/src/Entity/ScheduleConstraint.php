<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ScheduleConstraintRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ScheduleConstraintRepository::class)]
#[ApiResource]
class ScheduleConstraint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: ['teacher_gap', 'max_daily_hours', 'subject_sequence', 'room_type'])]
    private ?string $type = null;

    #[ORM\Column(type: 'json')]
    private array $parameters = []; // { "max_gaps": 2, "subject_id": 5 }

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: ['hard', 'soft'])]
    private ?string $severity = 'hard';

    #[ORM\Column]
    private ?int $weight = 10; // For soft constraints: higher = more important

    #[ORM\ManyToOne(nullable: true)]
    private ?SchoolCycle $schoolCycle = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getSeverity(): ?string
    {
        return $this->severity;
    }

    public function setSeverity(string $severity): static
    {
        $this->severity = $severity;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): static
    {
        $this->weight = $weight;

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
}
