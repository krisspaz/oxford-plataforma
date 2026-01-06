<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\AnomalyAlert;
use App\Entity\TemporaryPermission;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * FASE 2: Banking-Grade Security Service
 * 
 * Provides:
 * - Anomaly detection
 * - Temporary permissions
 * - DLP (Data Loss Prevention)
 * - Action logging for security
 */
class BankingSecurityService
{
    // Anomaly patterns
    private const EXCESSIVE_QUERIES_THRESHOLD = 100;
    private const EXCESSIVE_QUERIES_WINDOW_MINUTES = 5;
    private const UNUSUAL_HOURS_START = 22;
    private const UNUSUAL_HOURS_END = 6;
    private const FAILED_LOGIN_THRESHOLD = 5;

    // Severity levels
    public const SEVERITY_CRITICAL = 'critical';
    public const SEVERITY_HIGH = 'high';
    public const SEVERITY_MEDIUM = 'medium';
    public const SEVERITY_LOW = 'low';

    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        private RequestStack $requestStack
    ) {}

    /**
     * Log action for security analysis
     */
    public function logAction(User $user, string $action, array $details = []): void
    {
        $request = $this->requestStack->getCurrentRequest();
        
        $this->logger->info('Security action: {action}', [
            'userId' => $user->getId(),
            'userEmail' => $user->getEmail(),
            'action' => $action,
            'details' => $details,
            'ip' => $request?->getClientIp(),
            'userAgent' => $request?->headers->get('User-Agent'),
            'timestamp' => date('c'),
        ]);

        // Check for anomalies
        $this->checkForAnomalies($user, $action);
    }

    /**
     * Check for anomalous behavior
     */
    public function checkForAnomalies(User $user, string $action): void
    {
        $anomalies = [];

        // Check unusual hours
        $hour = (int) date('H');
        if ($hour >= self::UNUSUAL_HOURS_START || $hour <= self::UNUSUAL_HOURS_END) {
            $anomalies[] = [
                'type' => 'unusual_hours',
                'severity' => self::SEVERITY_MEDIUM,
                'description' => "Acceso a hora inusual ({$hour}:00)",
            ];
        }

        // Check for sensitive actions
        $sensitiveActions = ['export_data', 'delete_batch', 'modify_roles', 'view_all_grades'];
        if (in_array($action, $sensitiveActions)) {
            $anomalies[] = [
                'type' => 'sensitive_action',
                'severity' => self::SEVERITY_HIGH,
                'description' => "Acción sensible ejecutada: {$action}",
            ];
        }

        // Register anomalies
        foreach ($anomalies as $anomaly) {
            $this->createAnomalyAlert($user, $action, $anomaly);
        }
    }

    /**
     * Create anomaly alert
     */
    private function createAnomalyAlert(User $user, string $action, array $anomaly): void
    {
        try {
            $alert = new AnomalyAlert();
            $alert->setUser($user);
            $alert->setAction($action);
            $alert->setType($anomaly['type']);
            $alert->setSeverity($anomaly['severity']);
            $alert->setDescription($anomaly['description']);
            $alert->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($alert);
            $this->em->flush();

            $this->logger->warning('Anomaly detected: {type}', [
                'type' => $anomaly['type'],
                'userId' => $user->getId(),
                'action' => $action,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to create anomaly alert: {error}', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Grant temporary permission
     */
    public function grantTemporaryPermission(
        User $user,
        string $permission,
        User $grantedBy,
        string $reason,
        int $durationHours = 24
    ): TemporaryPermission {
        $tempPermission = new TemporaryPermission();
        $tempPermission->setUser($user);
        $tempPermission->setPermission($permission);
        $tempPermission->setGrantedBy($grantedBy);
        $tempPermission->setReason($reason);
        $tempPermission->setExpiresAt(new \DateTimeImmutable("+{$durationHours} hours"));
        $tempPermission->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($tempPermission);
        $this->em->flush();

        $this->logger->info('Temporary permission granted', [
            'userId' => $user->getId(),
            'permission' => $permission,
            'grantedBy' => $grantedBy->getId(),
            'expiresIn' => $durationHours . ' hours',
        ]);

        return $tempPermission;
    }

    /**
     * Check if user has temporary permission
     */
    public function hasTemporaryPermission(User $user, string $permission): bool
    {
        $repo = $this->em->getRepository(TemporaryPermission::class);
        
        $tempPerm = $repo->createQueryBuilder('tp')
            ->where('tp.user = :user')
            ->andWhere('tp.permission = :permission')
            ->andWhere('tp.expiresAt > :now')
            ->andWhere('tp.revoked = false')
            ->setParameter('user', $user)
            ->setParameter('permission', $permission)
            ->setParameter('now', new \DateTimeImmutable())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $tempPerm !== null;
    }

    /**
     * Revoke temporary permission
     */
    public function revokeTemporaryPermission(int $permissionId): bool
    {
        $repo = $this->em->getRepository(TemporaryPermission::class);
        $tempPerm = $repo->find($permissionId);

        if (!$tempPerm) {
            return false;
        }

        $tempPerm->setRevoked(true);
        $this->em->flush();

        return true;
    }

    /**
     * Check DLP rules (Data Loss Prevention)
     */
    public function checkDLP(string $data, string $action = 'export'): array
    {
        $violations = [];

        // Check for email patterns
        if (preg_match('/[\w\.-]+@[\w\.-]+\.\w+/', $data)) {
            $violations[] = ['pattern' => 'email', 'action' => 'warn'];
        }

        // Check for phone patterns (8+ digits)
        if (preg_match('/\d{8,}/', $data)) {
            $violations[] = ['pattern' => 'phone', 'action' => 'warn'];
        }

        // Check for password-related content
        if (preg_match('/password|contraseña|clave|secret/i', $data)) {
            $violations[] = ['pattern' => 'sensitive_keyword', 'action' => 'block'];
        }

        // Check for credit card patterns
        if (preg_match('/\d{4}[\s-]?\d{4}[\s-]?\d{4}[\s-]?\d{4}/', $data)) {
            $violations[] = ['pattern' => 'credit_card', 'action' => 'block'];
        }

        if (!empty($violations)) {
            $this->logger->warning('DLP violation detected', [
                'action' => $action,
                'violations' => $violations,
            ]);
        }

        return [
            'allowed' => !array_filter($violations, fn($v) => $v['action'] === 'block'),
            'violations' => $violations,
        ];
    }

    /**
     * Get unresolved anomaly alerts
     */
    public function getUnresolvedAlerts(): array
    {
        $repo = $this->em->getRepository(AnomalyAlert::class);
        
        return $repo->createQueryBuilder('a')
            ->where('a.resolved = false')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }

    /**
     * Resolve anomaly alert
     */
    public function resolveAlert(int $alertId, User $resolvedBy): bool
    {
        $repo = $this->em->getRepository(AnomalyAlert::class);
        $alert = $repo->find($alertId);

        if (!$alert) {
            return false;
        }

        $alert->setResolved(true);
        $alert->setResolvedBy($resolvedBy);
        $alert->setResolvedAt(new \DateTimeImmutable());

        $this->em->flush();

        return true;
    }

    /**
     * Check if action requires MFA
     */
    public function requiresMFA(string $action): bool
    {
        $mfaRequired = [
            'delete_data',
            'modify_rules',
            'export_all_data',
            'modify_permissions',
            'change_password',
            'access_sensitive_reports',
            'bulk_grade_modification',
        ];

        return in_array($action, $mfaRequired);
    }
}
