<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Model\TenantAwareInterface;
use App\Model\TenantAwareTrait;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[ORM\Table(name: 'student')]
#[ORM\Index(columns: ['is_active'], name: 'idx_student_active')]
#[ORM\Index(columns: ['first_name', 'last_name'], name: 'idx_student_full_name')]
#[ORM\Index(columns: ['school_cycle_id'], name: 'idx_student_cycle')]
#[ORM\Index(columns: ['course_id'], name: 'idx_student_course')]
#[ORM\Index(columns: ['tenant_id'], name: 'idx_student_tenant')]
#[ApiResource(
    processor: \App\State\StudentProcessor::class,
    normalizationContext: ['groups' => ['student:read', 'person:read']],
    denormalizationContext: ['groups' => ['student:write', 'person:write']]
)]
class Student extends Person
{
    use TenantAwareTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['student:read'])]
    #[ORM\Column(length: 20, unique: true, nullable: true)]
    #[Groups(['student:read', 'student:write'])]
    private ?string $carnet = null;

    // Risks calculated by AI
    #[ORM\Column(nullable: true)]
    private ?float $academicRiskScore = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Version]
    #[Groups(['student:read'])]
    private ?int $version = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCarnet(): ?string
    {
        return $this->carnet;
    }

    public function setCarnet(?string $carnet): static
    {
        $this->carnet = $carnet;
        return $this;
    }

    public function getStudentCode(): ?string
    {
        return $this->carnet;
    }


    // Alias for compatibility
    public function setStudentCode(string $code): static
    {
        return $this->setCarnet($code);
    }

    #[ORM\ManyToMany(targetEntity: Guardian::class, mappedBy: 'students')]
    private Collection $guardians;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: StudentScholarship::class)]
    private Collection $scholarships;

    public function __construct()
    {
        parent::__construct();
        $this->guardians = new ArrayCollection();
        $this->scholarships = new ArrayCollection();
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


    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Enrollment::class)]
    private Collection $enrollments;

    public function getEnrollments(): Collection
    {
        return $this->enrollments;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * @return Collection<int, StudentScholarship>
     */
    public function getScholarships(): Collection
    {
        return $this->scholarships;
    }
}
