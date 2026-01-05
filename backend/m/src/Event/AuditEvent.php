<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Base Audit Event - dispatched for all auditable actions
 */
class AuditEvent extends Event
{
    public const TYPE_CREATE = 'create';
    public const TYPE_UPDATE = 'update';
    public const TYPE_DELETE = 'delete';
    public const TYPE_LOGIN = 'login';
    public const TYPE_LOGOUT = 'logout';
    public const TYPE_ACCESS = 'access';
    public const TYPE_EXPORT = 'export';

    private \DateTimeImmutable $occurredAt;

    public function __construct(
        private string $action,
        private string $entityType,
        private ?int $entityId = null,
        private ?User $user = null,
        private ?array $changes = null,
        private ?string $ipAddress = null,
        private ?string $userAgent = null
    ) {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getChanges(): ?array
    {
        return $this->changes;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function toArray(): array
    {
        return [
            'action' => $this->action,
            'entityType' => $this->entityType,
            'entityId' => $this->entityId,
            'userId' => $this->user?->getId(),
            'userEmail' => $this->user?->getEmail(),
            'changes' => $this->changes,
            'ipAddress' => $this->ipAddress,
            'userAgent' => $this->userAgent,
            'occurredAt' => $this->occurredAt->format('c'),
        ];
    }
}
