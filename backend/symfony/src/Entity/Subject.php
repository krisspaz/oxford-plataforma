<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Model\TenantAwareInterface;
use App\Model\TenantAwareTrait;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['subject:read']],
    denormalizationContext: ['groups' => ['subject:write']]
)]
class Subject implements TenantAwareInterface
{
    use TenantAwareTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['subject:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['subject:read', 'subject:write'])]
    private ?string $name = null; // Matemáticas, Historia

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Groups(['subject:read', 'subject:write'])]
    private ?string $code = null; // MAT101

    #[ORM\Column(nullable: true)]
    #[Groups(['subject:read', 'subject:write'])]
    private ?int $hoursWeek = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    #[Groups(['subject:read', 'subject:write'])]
    private bool $active = true;

    #[ORM\ManyToMany(targetEntity: Course::class, inversedBy: 'subjects')]
    private Collection $courses;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: Task::class)]
    private Collection $tasks;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->tasks = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getHoursWeek(): ?int
    {
        return $this->hoursWeek;
    }

    public function setHoursWeek(?int $hoursWeek): static
    {
        $this->hoursWeek = $hoursWeek;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setIsActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
        }

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        $this->courses->removeElement($course);

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setSubject($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getSubject() === $this) {
                $task->setSubject(null);
            }
        }

        return $this;
    }
}
