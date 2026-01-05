<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait for soft delete functionality
 * Entities using this trait will not be physically deleted, only marked as deleted
 */
trait SoftDeleteableTrait
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $deletedBy = null;

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): static
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function getDeletedBy(): ?int
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?int $deletedBy): static
    {
        $this->deletedBy = $deletedBy;
        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    /**
     * Soft delete the entity
     */
    public function softDelete(?int $userId = null): static
    {
        $this->deletedAt = new \DateTime();
        $this->deletedBy = $userId;
        return $this;
    }

    /**
     * Restore a soft-deleted entity
     */
    public function restore(): static
    {
        $this->deletedAt = null;
        $this->deletedBy = null;
        return $this;
    }
}
