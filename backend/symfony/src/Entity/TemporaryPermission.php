<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Temporary Permission Entity
 * 
 * Stores time-limited permissions granted to users
 */
#[ORM\Entity]
#[ORM\Table(name: 'temporary_permissions')]
#[ORM\Index(name: 'idx_temp_perm_expires', columns: ['expires_at'])]
class TemporaryPermission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: 'string', length: 100)]
    private string $permission;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $grantedBy;

    #[ORM\Column(type: 'text')]
    private string $reason;

    #[ORM\Column(type: 'boolean')]
    private bool $revoked = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    
    public function getUser(): User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }
    
    public function getPermission(): string { return $this->permission; }
    public function setPermission(string $permission): self { $this->permission = $permission; return $this; }
    
    public function getGrantedBy(): User { return $this->grantedBy; }
    public function setGrantedBy(User $grantedBy): self { $this->grantedBy = $grantedBy; return $this; }
    
    public function getReason(): string { return $this->reason; }
    public function setReason(string $reason): self { $this->reason = $reason; return $this; }
    
    public function isRevoked(): bool { return $this->revoked; }
    public function setRevoked(bool $revoked): self { $this->revoked = $revoked; return $this; }
    
    public function getExpiresAt(): \DateTimeImmutable { return $this->expiresAt; }
    public function setExpiresAt(\DateTimeImmutable $expiresAt): self { $this->expiresAt = $expiresAt; return $this; }
    
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }
    
    public function isActive(): bool
    {
        return !$this->revoked && $this->expiresAt > new \DateTimeImmutable();
    }
}
