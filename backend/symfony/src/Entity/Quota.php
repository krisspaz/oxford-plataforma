<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\QuotaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cuota individual de un plan de pago
 */
#[ORM\Entity(repositoryClass: QuotaRepository::class)]
#[ApiResource]
class Quota
{
    public const STATUS_PENDING = 'PENDIENTE';
    public const STATUS_PARTIAL = 'PARCIAL';
    public const STATUS_PAID = 'PAGADO';
    public const STATUS_OVERDUE = 'VENCIDO';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: PaymentPlan::class, inversedBy: 'quotas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PaymentPlan $paymentPlan = null;

    #[ORM\Column]
    private ?int $quotaNumber = 1;

    #[ORM\Column(length: 150)]
    private ?string $concept = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $amount = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $paidAmount = '0.00';

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $paidDate = null;

    #[ORM\Column(length: 20)]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\ManyToOne(targetEntity: Invoice::class)]
    private ?Invoice $invoice = null; // Factura/recibo generado

    #[ORM\Column(length: 50)]
    private ?string $documentType = 'RECIBO_SAT'; // Tipo de documento a generar

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaymentPlan(): ?PaymentPlan
    {
        return $this->paymentPlan;
    }

    public function setPaymentPlan(?PaymentPlan $paymentPlan): static
    {
        $this->paymentPlan = $paymentPlan;
        return $this;
    }

    public function getQuotaNumber(): ?int
    {
        return $this->quotaNumber;
    }

    public function setQuotaNumber(int $quotaNumber): static
    {
        $this->quotaNumber = $quotaNumber;
        return $this;
    }

    public function getConcept(): ?string
    {
        return $this->concept;
    }

    public function setConcept(string $concept): static
    {
        $this->concept = $concept;
        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    public function getPaidAmount(): ?string
    {
        return $this->paidAmount;
    }

    public function setPaidAmount(string $paidAmount): static
    {
        $this->paidAmount = $paidAmount;
        $this->updateStatus();
        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeInterface $dueDate): static
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getPaidDate(): ?\DateTimeInterface
    {
        return $this->paidDate;
    }

    public function setPaidDate(?\DateTimeInterface $paidDate): static
    {
        $this->paidDate = $paidDate;
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

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): static
    {
        $this->invoice = $invoice;
        return $this;
    }

    public function getDocumentType(): ?string
    {
        return $this->documentType;
    }

    public function setDocumentType(string $documentType): static
    {
        $this->documentType = $documentType;
        return $this;
    }

    public function getPendingAmount(): string
    {
        return bcsub($this->amount, $this->paidAmount ?? '0.00', 2);
    }

    public function isOverdue(): bool
    {
        if ($this->status === self::STATUS_PAID) {
            return false;
        }
        return $this->dueDate < new \DateTime();
    }

    private function updateStatus(): void
    {
        $pending = $this->getPendingAmount();
        
        if (bccomp($pending, '0.00', 2) <= 0) {
            $this->status = self::STATUS_PAID;
            $this->paidDate = new \DateTime();
        } elseif (bccomp($this->paidAmount, '0.00', 2) > 0) {
            $this->status = self::STATUS_PARTIAL;
        } elseif ($this->isOverdue()) {
            $this->status = self::STATUS_OVERDUE;
        } else {
            $this->status = self::STATUS_PENDING;
        }
    }
}
