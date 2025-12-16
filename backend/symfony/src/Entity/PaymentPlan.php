<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PaymentPlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Plan de pago / Convenio de pago
 */
#[ORM\Entity(repositoryClass: PaymentPlanRepository::class)]
#[ApiResource]
class PaymentPlan
{
    public const STATUS_ACTIVE = 'ACTIVO';
    public const STATUS_COMPLETED = 'COMPLETADO';
    public const STATUS_CANCELLED = 'CANCELADO';
    public const STATUS_DEFAULTED = 'MORA';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne(targetEntity: SchoolCycle::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?SchoolCycle $schoolCycle = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $totalAmount = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $downPayment = '0.00'; // Enganche

    #[ORM\Column]
    private ?int $numberOfQuotas = 1;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $quotaAmount = '0.00';

    #[ORM\Column(length: 20)]
    private ?string $status = self::STATUS_ACTIVE;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $createdBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'paymentPlan', targetEntity: Quota::class, cascade: ['persist', 'remove'])]
    private Collection $quotas;

    public function __construct()
    {
        $this->quotas = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;
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

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getDownPayment(): ?string
    {
        return $this->downPayment;
    }

    public function setDownPayment(string $downPayment): static
    {
        $this->downPayment = $downPayment;
        return $this;
    }

    public function getNumberOfQuotas(): ?int
    {
        return $this->numberOfQuotas;
    }

    public function setNumberOfQuotas(int $numberOfQuotas): static
    {
        $this->numberOfQuotas = $numberOfQuotas;
        return $this;
    }

    public function getQuotaAmount(): ?string
    {
        return $this->quotaAmount;
    }

    public function setQuotaAmount(string $quotaAmount): static
    {
        $this->quotaAmount = $quotaAmount;
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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, Quota>
     */
    public function getQuotas(): Collection
    {
        return $this->quotas;
    }

    public function addQuota(Quota $quota): static
    {
        if (!$this->quotas->contains($quota)) {
            $this->quotas->add($quota);
            $quota->setPaymentPlan($this);
        }
        return $this;
    }

    public function getPaidAmount(): string
    {
        $paid = '0.00';
        foreach ($this->quotas as $quota) {
            $paid = bcadd($paid, $quota->getPaidAmount() ?? '0.00', 2);
        }
        return $paid;
    }

    public function getPendingAmount(): string
    {
        return bcsub($this->totalAmount, $this->getPaidAmount(), 2);
    }
}
