<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
#[ApiResource]
class Teacher extends Person
{
    #[ORM\Column(length: 50, unique: true, nullable: true)]
    private ?string $employeeCode = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $specialization = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $hireDate = null;

    #[ORM\Column(length: 50)]
    private ?string $contractType = 'Tiempo Completo'; // Tiempo Completo, Medio Tiempo, Por Horas

    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: SubjectAssignment::class)]
    private Collection $subjectAssignments;

    public function __construct()
    {
        parent::__construct();
        $this->subjectAssignments = new ArrayCollection();
    }

    public function getEmployeeCode(): ?string
    {
        return $this->employeeCode;
    }

    public function setEmployeeCode(?string $employeeCode): static
    {
        $this->employeeCode = $employeeCode;
        return $this;
    }

    public function getSpecialization(): ?string
    {
        return $this->specialization;
    }

    public function setSpecialization(?string $specialization): static
    {
        $this->specialization = $specialization;
        return $this;
    }

    public function getHireDate(): ?\DateTimeInterface
    {
        return $this->hireDate;
    }

    public function setHireDate(?\DateTimeInterface $hireDate): static
    {
        $this->hireDate = $hireDate;
        return $this;
    }

    public function getContractType(): ?string
    {
        return $this->contractType;
    }

    public function setContractType(string $contractType): static
    {
        $this->contractType = $contractType;
        return $this;
    }

    /**
     * @return Collection<int, SubjectAssignment>
     */
    public function getSubjectAssignments(): Collection
    {
        return $this->subjectAssignments;
    }

    public function addSubjectAssignment(SubjectAssignment $assignment): static
    {
        if (!$this->subjectAssignments->contains($assignment)) {
            $this->subjectAssignments->add($assignment);
            $assignment->setTeacher($this);
        }
        return $this;
    }
}
