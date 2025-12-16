<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GuardianRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GuardianRepository::class)]
#[ApiResource]
class Guardian extends Person
{
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $occupation = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $workplace = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $workPhone = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nit = null;

    #[ORM\Column(length: 50)]
    private ?string $relationship = 'Padre'; // Padre, Madre, Encargado

    #[ORM\ManyToMany(targetEntity: Student::class, inversedBy: 'guardians')]
    private Collection $students;

    public function __construct()
    {
        parent::__construct();
        $this->students = new ArrayCollection();
    }

    public function getOccupation(): ?string
    {
        return $this->occupation;
    }

    public function setOccupation(?string $occupation): static
    {
        $this->occupation = $occupation;
        return $this;
    }

    public function getWorkplace(): ?string
    {
        return $this->workplace;
    }

    public function setWorkplace(?string $workplace): static
    {
        $this->workplace = $workplace;
        return $this;
    }

    public function getWorkPhone(): ?string
    {
        return $this->workPhone;
    }

    public function setWorkPhone(?string $workPhone): static
    {
        $this->workPhone = $workPhone;
        return $this;
    }

    public function getNit(): ?string
    {
        return $this->nit;
    }

    public function setNit(?string $nit): static
    {
        $this->nit = $nit;
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

    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): static
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
        }
        return $this;
    }

    public function removeStudent(Student $student): static
    {
        $this->students->removeElement($student);
        return $this;
    }
}
