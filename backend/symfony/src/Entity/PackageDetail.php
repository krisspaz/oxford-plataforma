<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PackageDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PackageDetailRepository::class)]
#[ApiResource]
class PackageDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Package::class, inversedBy: 'details')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Package $package = null;

    #[ORM\Column(length: 100)]
    private ?string $productName = null; // Inscripción, Mensualidad, etc.

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(length: 50)]
    private ?string $documentType = 'RECIBO_SAT'; // FACTURA_SAT, RECIBO_SAT, RECIBO_INTERNO

    #[ORM\Column(length: 50)]
    private ?string $productType = 'Servicio'; // Servicio, Bien

    #[ORM\Column]
    private ?int $quantity = 1;

    #[ORM\Column]
    private ?int $sortOrder = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPackage(): ?Package
    {
        return $this->package;
    }

    public function setPackage(?Package $package): static
    {
        $this->package = $package;
        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): static
    {
        $this->productName = $productName;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
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

    public function getProductType(): ?string
    {
        return $this->productType;
    }

    public function setProductType(string $productType): static
    {
        $this->productType = $productType;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }
}
