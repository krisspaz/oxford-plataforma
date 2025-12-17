<?php

namespace App\EventListener;

use App\Entity\AuditLog;
use App\Event\AuditEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * Audit Event Listener - persists audit events to database and logs
 */
#[AsEventListener(event: AuditEvent::class, method: 'onAuditEvent')]
class AuditEventListener
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger
    ) {}

    public function onAuditEvent(AuditEvent $event): void
    {
        // Log to structured logs
        $this->logger->info('Audit: {action} on {entityType}', [
            'action' => $event->getAction(),
            'entityType' => $event->getEntityType(),
            'entityId' => $event->getEntityId(),
            'userId' => $event->getUser()?->getId(),
            'userEmail' => $event->getUser()?->getEmail(),
            'changes' => $event->getChanges(),
            'ipAddress' => $event->getIpAddress(),
            'occurredAt' => $event->getOccurredAt()->format('c'),
        ]);

        // Persist to database
        try {
            $auditLog = new AuditLog();
            $auditLog->setAction($event->getAction());
            $auditLog->setEntityType($event->getEntityType());
            $auditLog->setEntityId($event->getEntityId());
            $auditLog->setUser($event->getUser());
            $auditLog->setChanges($event->getChanges());
            $auditLog->setIpAddress($event->getIpAddress());
            $auditLog->setUserAgent($event->getUserAgent());
            $auditLog->setCreatedAt($event->getOccurredAt());

            $this->em->persist($auditLog);
            $this->em->flush();
        } catch (\Exception $e) {
            $this->logger->error('Failed to persist audit log: {error}', [
                'error' => $e->getMessage(),
                'event' => $event->toArray(),
            ]);
        }
    }
}
