<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\JobTitleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: JobTitleRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['job_title:read']],
    denormalizationContext: ['groups' => ['job_title:write']],
    order: ['name' => 'ASC']
)]
class JobTitle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['job_title:read', 'staff:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['job_title:read', 'job_title:write', 'staff:read', 'staff:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['job_title:read', 'job_title:write'])]
    private ?string $department = null;

    #[ORM\OneToMany(mappedBy: 'jobTitle', targetEntity: Staff::class)]
    private Collection $staffMembers;

    public function __construct()
    {
        $this->staffMembers = new ArrayCollection();
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

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(string $department): static
    {
        $this->department = $department;

        return $this;
    }

    /**
     * @return Collection<int, Staff>
     */
    public function getStaffMembers(): Collection
    {
        return $this->staffMembers;
    }

    public function addStaffMember(Staff $staffMember): static
    {
        if (!$this->staffMembers->contains($staffMember)) {
            $this->staffMembers->add($staffMember);
            $staffMember->setJobTitle($this);
        }

        return $this;
    }

    public function removeStaffMember(Staff $staffMember): static
    {
        if ($this->staffMembers->removeElement($staffMember)) {
            // set the owning side to null (unless already changed)
            if ($staffMember->getJobTitle() === $this) {
                $staffMember->setJobTitle(null);
            }
        }

        return $this;
    }
}
