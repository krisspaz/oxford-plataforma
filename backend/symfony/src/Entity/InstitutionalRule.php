<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Institutional Rule Entity
 * 
 * Stores non-negotiable rules for platform governance
 */
#[ORM\Entity]
#[ORM\Table(name: 'institutional_rules')]
class InstitutionalRule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $type; // schedule, academic, financial, security, ethical

    #[ORM\Column(type: 'string', length: 20)]
    private string $priority; // critical, high, medium, low

    #[ORM\Column(name: 'rule_condition', type: 'text')]
    private string $condition;

    #[ORM\Column(type: 'string', length: 50)]
    private string $action; // REJECT, WARN, LOG, ENFORCE

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    
    public function getCode(): string { return $this->code; }
    public function setCode(string $code): self { $this->code = $code; return $this; }
    
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    
    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    
    public function getPriority(): string { return $this->priority; }
    public function setPriority(string $priority): self { $this->priority = $priority; return $this; }
    
    public function getCondition(): string { return $this->condition; }
    public function setCondition(string $condition): self { $this->condition = $condition; return $this; }
    
    public function getAction(): string { return $this->action; }
    public function setAction(string $action): self { $this->action = $action; return $this; }
    
    public function isActive(): bool { return $this->active; }
    public function setActive(bool $active): self { $this->active = $active; return $this; }
    
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }
}
