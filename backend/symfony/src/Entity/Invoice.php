<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\InvoiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ApiResource]
class Invoice
{
    public const TYPE_FACTURA_SAT = 'FACTURA_SAT';
    public const TYPE_RECIBO_SAT = 'RECIBO_SAT';
    public const TYPE_RECIBO_INTERNO = 'RECIBO_INTERNO';
    
    public const STATUS_EMITIDO = 'EMITIDO';
    public const STATUS_ANULADO = 'ANULADO';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $uuid = null; // UUID de SAT

    #[ORM\Column(length: 50)]
    private ?string $documentType = self::TYPE_RECIBO_INTERNO;

    #[ORM\Column(length: 20)]
    private ?string $series = null;

    #[ORM\Column(length: 20)]
    private ?string $number = null;

    #[ORM\Column(length: 20)]
    private ?string $nit = null;

    #[ORM\Column(length: 255)]
    private ?string $recipientName = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $total = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $issueDate = null;

    #[ORM\Column(length: 20)]
    private ?string $status = self::STATUS_EMITIDO;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $annulmentDate = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $annulledBy = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $annulmentReason = null;

    #[ORM\ManyToOne(targetEntity: Payment::class)]
    private ?Payment $payment = null;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    private ?Student $student = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $satResponse = null; // JSON response from CorpoSistemas

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $createdBy = null;

    #[ORM\Column(length: 50)]
    private ?string $paymentMethod = 'Efectivo'; // Efectivo, Tarjeta, Deposito

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->issueDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): static
    {
        $this->uuid = $uuid;
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

    public function getSeries(): ?string
    {
        return $this->series;
    }

    public function setSeries(string $series): static
    {
        $this->series = $series;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;
        return $this;
    }

    public function getNit(): ?string
    {
        return $this->nit;
    }

    public function setNit(string $nit): static
    {
        $this->nit = $nit;
        return $this;
    }

    public function getRecipientName(): ?string
    {
        return $this->recipientName;
    }

    public function setRecipientName(string $recipientName): static
    {
        $this->recipientName = $recipientName;
        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;
        return $this;
    }

    public function getIssueDate(): ?\DateTimeInterface
    {
        return $this->issueDate;
    }

    public function setIssueDate(\DateTimeInterface $issueDate): static
    {
        $this->issueDate = $issueDate;
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

    public function getAnnulmentDate(): ?\DateTimeInterface
    {
        return $this->annulmentDate;
    }

    public function setAnnulmentDate(?\DateTimeInterface $annulmentDate): static
    {
        $this->annulmentDate = $annulmentDate;
        return $this;
    }

    public function getAnnulledBy(): ?User
    {
        return $this->annulledBy;
    }

    public function setAnnulledBy(?User $annulledBy): static
    {
        $this->annulledBy = $annulledBy;
        return $this;
    }

    public function getAnnulmentReason(): ?string
    {
        return $this->annulmentReason;
    }

    public function setAnnulmentReason(?string $annulmentReason): static
    {
        $this->annulmentReason = $annulmentReason;
        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): static
    {
        $this->payment = $payment;
        return $this;
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

    public function getSatResponse(): ?string
    {
        return $this->satResponse;
    }

    public function setSatResponse(?string $satResponse): static
    {
        $this->satResponse = $satResponse;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
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

    public function isAnnulled(): bool
    {
        return $this->status === self::STATUS_ANULADO;
    }
}
