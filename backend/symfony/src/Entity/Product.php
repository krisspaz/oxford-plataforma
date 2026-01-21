<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['product:read']],
    denormalizationContext: ['groups' => ['product:write']]
)]
class Product
{
    public const TYPE_INSCRIPCION = 'INSCRIPCION';
    public const TYPE_MENSUALIDAD = 'MENSUALIDAD';
    public const TYPE_MATERIAL = 'MATERIAL';
    public const TYPE_UNIFORME = 'UNIFORME';
    public const TYPE_TRANSPORTE = 'TRANSPORTE';
    public const TYPE_ALIMENTACION = 'ALIMENTACION';
    public const TYPE_EVENTO = 'EVENTO';
    public const TYPE_OTRO = 'OTRO';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:read', 'product:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['product:read', 'product:write'])]
    private ?string $code = null;

    #[ORM\Column(length: 150)]
    #[Groups(['product:read', 'product:write'])]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['product:read', 'product:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Groups(['product:read', 'product:write'])]
    private ?string $type = self::TYPE_OTRO;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['product:read', 'product:write'])]
    private ?string $basePrice = '0.00';

    #[ORM\Column(length: 20)]
    #[Groups(['product:read', 'product:write'])]
    private ?string $documentType = 'FACTURA_SAT'; // FACTURA_SAT, RECIBO_SAT, RECIBO_INTERNO

    #[ORM\Column]
    #[Groups(['product:read', 'product:write'])]
    private ?bool $isTaxable = true;

    #[ORM\Column]
    #[Groups(['product:read', 'product:write'])]
    private ?bool $isRecurring = false; // For monthly fees

    #[ORM\Column]
    #[Groups(['product:read', 'product:write'])]
    private ?int $recurringMonths = 0; // Number of months if recurring

    #[ORM\Column]
    #[Groups(['product:read', 'product:write'])]
    private ?bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getBasePrice(): ?string
    {
        return $this->basePrice;
    }

    public function setBasePrice(string $basePrice): static
    {
        $this->basePrice = $basePrice;
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

    public function isTaxable(): ?bool
    {
        return $this->isTaxable;
    }

    public function setIsTaxable(bool $isTaxable): static
    {
        $this->isTaxable = $isTaxable;
        return $this;
    }

    public function isRecurring(): ?bool
    {
        return $this->isRecurring;
    }

    public function setIsRecurring(bool $isRecurring): static
    {
        $this->isRecurring = $isRecurring;
        return $this;
    }

    public function getRecurringMonths(): ?int
    {
        return $this->recurringMonths;
    }

    public function setRecurringMonths(int $recurringMonths): static
    {
        $this->recurringMonths = $recurringMonths;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }
}
