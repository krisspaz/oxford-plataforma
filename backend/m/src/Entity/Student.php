<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\StudentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[ApiResource]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2)]
    private ?string $lastName = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Assert\NotBlank]
    private ?string $carnet = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $address = null;

    #[ORM\Column]
    private ?bool $isActive = true;

    // Risks calculated by AI
    #[ORM\Column(nullable: true)]
    private ?float $academicRiskScore = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCarnet(): ?string
    {
        return $this->carnet;
    }

    public function setCarnet(string $carnet): static
    {
        $this->carnet = $carnet;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

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

    #[ORM\ManyToMany(targetEntity: Guardian::class, mappedBy: 'students')]
    private Collection $guardians;

    public function __construct()
    {
        $this->guardians = new ArrayCollection();
    }

    #[ORM\ManyToOne(inversedBy: 'students')]
    private ?SchoolCycle $schoolCycle = null;

    public function getSchoolCycle(): ?SchoolCycle
    {
        return $this->schoolCycle;
    }

    public function setSchoolCycle(?SchoolCycle $schoolCycle): static
    {
        $this->schoolCycle = $schoolCycle;

        return $this;
    }

    #[ORM\ManyToOne(inversedBy: 'students')]
    private ?Course $course = null;

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;

        return $this;
    }

    public function getAcademicRiskScore(): ?float
    {
        return $this->academicRiskScore;
    }

    public function setAcademicRiskScore(?float $academicRiskScore): static
    {
        $this->academicRiskScore = $academicRiskScore;

        return $this;
    }

    /**
     * @return Collection<int, Guardian>
     */
    public function getGuardians(): Collection
    {
        return $this->guardians;
    }

    public function addGuardian(Guardian $guardian): static
    {
        if (!$this->guardians->contains($guardian)) {
            $this->guardians->add($guardian);
            $guardian->addStudent($this);
        }

        return $this;
    }

    public function removeGuardian(Guardian $guardian): static
    {
        if ($this->guardians->removeElement($guardian)) {
            $guardian->removeStudent($this);
        }

        return $this;
    }
}
