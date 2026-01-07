<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Unified Permission Entity
 * =========================
 * Consolidates: Permission + TemporaryPermission
 * 
 * Handles both permanent module permissions and temporary/time-limited permissions.
 * If expiresAt is set, the permission is temporary.
 */
#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[ORM\Index(name: 'idx_perm_user', columns: ['user_id'])]
#[ORM\Index(name: 'idx_perm_code', columns: ['code'])]
#[ORM\Index(name: 'idx_perm_expires', columns: ['expires_at'])]
#[ApiResource]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $module = null; // inscripciones, pagos, notas, etc

    #[ORM\Column(length: 50)]
    private ?string $action = null; // view, create, edit, delete, approve

    #[ORM\Column(length: 100)]
    private ?string $code = null; // inscripciones.create, pagos.view, etc

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private bool $isActive = true;

    // === Temporal Permission Fields (from TemporaryPermission) ===

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null; // null = global permission, set = user-specific

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $grantedBy = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reason = null;

    #[ORM\Column]
    private bool $isRevoked = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $expiresAt = null; // null = permanent

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function setModule(string $module): static
    {
        $this->module = $module;
        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    // === Temporal Permission Methods ===

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getGrantedBy(): ?User
    {
        return $this->grantedBy;
    }

    public function setGrantedBy(?User $grantedBy): static
    {
        $this->grantedBy = $grantedBy;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;
        return $this;
    }

    public function isRevoked(): bool
    {
        return $this->isRevoked;
    }

    public function setIsRevoked(bool $isRevoked): static
    {
        $this->isRevoked = $isRevoked;
        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Check if this is a temporary permission
     */
    public function isTemporary(): bool
    {
        return $this->expiresAt !== null;
    }

    /**
     * Check if permission has expired
     */
    public function isExpired(): bool
    {
        if (!$this->expiresAt) {
            return false;
        }
        return $this->expiresAt < new \DateTimeImmutable();
    }

    /**
     * Check if permission is currently valid
     */
    public function isValid(): bool
    {
        return $this->isActive && !$this->isRevoked && !$this->isExpired();
    }

    /**
     * Revoke this permission
     */
    public function revoke(): static
    {
        $this->isRevoked = true;
        return $this;
    }
}

