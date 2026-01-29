<?php

namespace App\Entity;

use App\Repository\SchoolCycleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Model\TenantAwareInterface;
use App\Model\TenantAwareTrait;

#[ORM\Entity(repositoryClass: SchoolCycleRepository::class)]
#[ApiResource]
class SchoolCycle implements TenantAwareInterface
{
    use TenantAwareTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null; // e.g., "Ciclo Escolar 2024"

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\OneToMany(mappedBy: 'schoolCycle', targetEntity: Student::class)]
    private Collection $students;

    #[ORM\OneToMany(mappedBy: 'schoolCycle', targetEntity: Bimester::class, cascade: ['persist'])]
    #[ORM\OrderBy(['number' => 'ASC'])]
    private Collection $bimesters;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->bimesters = new ArrayCollection();
        $this->isActive = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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
            $student->setSchoolCycle($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): static
    {
        if ($this->students->removeElement($student)) {
            // set the owning side to null (unless already changed)
            if ($student->getSchoolCycle() === $this) {
                $student->setSchoolCycle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bimester>
     */
    public function getBimesters(): Collection
    {
        return $this->bimesters;
    }

    public function addBimester(Bimester $bimester): static
    {
        if (!$this->bimesters->contains($bimester)) {
            $this->bimesters->add($bimester);
            $bimester->setSchoolCycle($this);
        }
        return $this;
    }

    public function getActiveBimester(): ?Bimester
    {
        foreach ($this->bimesters as $bimester) {
            if ($bimester->isActive() && !$bimester->isClosed()) {
                return $bimester;
            }
        }
        return null;
    }
}
