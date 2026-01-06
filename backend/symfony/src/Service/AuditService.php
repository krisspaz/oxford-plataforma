<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AuditLog;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Psr\Log\LoggerInterface;

/**
 * Comprehensive Audit Service
 * 
 * Automatic logging of:
 * - Entity changes (create, update, delete)
 * - Critical actions
 * - Security events
 * - Data access
 */
class AuditService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ?Security $security,
        private readonly RequestStack $requestStack,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * Log entity creation
     */
    public function logCreate(object $entity, array $data = []): void
    {
        $this->log('CREATE', $entity, $data);
    }

    /**
     * Log entity update
     */
    public function logUpdate(object $entity, array $changes = []): void
    {
        $this->log('UPDATE', $entity, $changes);
    }

    /**
     * Log entity deletion
     */
    public function logDelete(object $entity): void
    {
        $this->log('DELETE', $entity);
    }

    /**
     * Log security event
     */
    public function logSecurity(string $event, array $details = []): void
    {
        $this->log('SECURITY', null, array_merge(['event' => $event], $details));
    }

    /**
     * Log data access (GDPR compliance)
     */
    public function logAccess(string $resource, int $resourceId, string $accessType = 'READ'): void
    {
        $this->log('ACCESS', null, [
            'resource' => $resource,
            'resource_id' => $resourceId,
            'access_type' => $accessType,
        ]);
    }

    /**
     * Log critical action
     */
    public function logCritical(string $action, array $details = []): void
    {
        $this->log('CRITICAL', null, array_merge(['action' => $action], $details));
        
        // Also log to external system
        $this->logger->critical("Critical action: {$action}", $details);
    }

    /**
     * Core logging method
     */
    private function log(string $action, ?object $entity = null, array $data = []): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $user = $this->security?->getUser();

        $auditLog = new AuditLog();
        $auditLog->setAction($action);
        $auditLog->setEntityType($entity ? get_class($entity) : 'System');
        $auditLog->setEntityId($entity ? $this->getEntityId($entity) : null);
        $auditLog->setUserId($user?->getId());
        $auditLog->setUserEmail($user?->getEmail() ?? 'anonymous');
        $auditLog->setChanges($data);
        $auditLog->setIpAddress($request?->getClientIp() ?? 'unknown');
        $auditLog->setUserAgent($request?->headers->get('User-Agent') ?? 'unknown');
        $auditLog->setCreatedAt(new \DateTimeImmutable());

        try {
            $this->em->persist($auditLog);
            $this->em->flush();
        } catch (\Exception $e) {
            // Don't let audit failures break the application
            $this->logger->error('Failed to write audit log: ' . $e->getMessage());
        }

        // Structured logging for ELK/centralized logging
        $this->logger->info("AUDIT: {$action}", [
            'action' => $action,
            'entity_type' => $auditLog->getEntityType(),
            'entity_id' => $auditLog->getEntityId(),
            'user_id' => $auditLog->getUserId(),
            'user_email' => $auditLog->getUserEmail(),
            'ip_address' => $auditLog->getIpAddress(),
            'changes' => $data,
            'timestamp' => $auditLog->getCreatedAt()->format('c'),
        ]);
    }

    /**
     * Get entity ID regardless of naming convention
     */
    private function getEntityId(object $entity): ?int
    {
        if (method_exists($entity, 'getId')) {
            return $entity->getId();
        }
        return null;
    }

    /**
     * Get audit trail for entity
     */
    public function getAuditTrail(string $entityType, int $entityId, int $limit = 50): array
    {
        return $this->em->getRepository(AuditLog::class)->findBy(
            ['entityType' => $entityType, 'entityId' => $entityId],
            ['createdAt' => 'DESC'],
            $limit
        );
    }

    /**
     * Get user activity
     */
    public function getUserActivity(int $userId, int $limit = 100): array
    {
        return $this->em->getRepository(AuditLog::class)->findBy(
            ['userId' => $userId],
            ['createdAt' => 'DESC'],
            $limit
        );
    }

    /**
     * Get security events
     */
    public function getSecurityEvents(\DateTimeInterface $since, int $limit = 500): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(AuditLog::class, 'a')
            ->where('a.action = :action')
            ->andWhere('a.createdAt >= :since')
            ->setParameter('action', 'SECURITY')
            ->setParameter('since', $since)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
