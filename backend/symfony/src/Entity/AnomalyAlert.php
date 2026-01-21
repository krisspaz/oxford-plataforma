<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Anomaly Alert Entity
 * 
 * Stores detected security anomalies
 */
#[ORM\Entity]
#[ORM\Table(name: 'anomaly_alerts')]
#[ORM\Index(name: 'idx_anomaly_resolved', columns: ['resolved'])]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['alert:read']]),
        new GetCollection(normalizationContext: ['groups' => ['alert:read']]),
    ],
    order: ['createdAt' => 'DESC'],
    security: "is_granted('ROLE_ADMIN')"
)]
class AnomalyAlert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['alert:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['alert:read'])]
    private string $action;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['alert:read'])]
    private string $type; // unusual_hours, excessive_queries, sensitive_action, etc.

    #[ORM\Column(type: 'string', length: 20)]
    #[Groups(['alert:read'])]
    private string $severity; // critical, high, medium, low

    #[ORM\Column(type: 'text')]
    #[Groups(['alert:read'])]
    private string $description;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $rawData = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['alert:read'])]
    private bool $resolved = false;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $resolvedBy = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $resolvedAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['alert:read'])]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    
    public function getUser(): User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }
    
    public function getAction(): string { return $this->action; }
    public function setAction(string $action): self { $this->action = $action; return $this; }
    
    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    
    public function getSeverity(): string { return $this->severity; }
    public function setSeverity(string $severity): self { $this->severity = $severity; return $this; }
    
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }
    
    public function getRawData(): ?array { return $this->rawData; }
    public function setRawData(?array $rawData): self { $this->rawData = $rawData; return $this; }
    
    public function isResolved(): bool { return $this->resolved; }
    public function setResolved(bool $resolved): self { $this->resolved = $resolved; return $this; }
    
    public function getResolvedBy(): ?User { return $this->resolvedBy; }
    public function setResolvedBy(?User $resolvedBy): self { $this->resolvedBy = $resolvedBy; return $this; }
    
    public function getResolvedAt(): ?\DateTimeImmutable { return $this->resolvedAt; }
    public function setResolvedAt(?\DateTimeImmutable $resolvedAt): self { $this->resolvedAt = $resolvedAt; return $this; }
    
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }
}
