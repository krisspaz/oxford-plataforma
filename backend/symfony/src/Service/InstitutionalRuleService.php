<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\InstitutionalRule;
use App\Repository\InstitutionalRuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * FASE 0: Institutional Rule Contract Service
 * 
 * Manages non-negotiable rules that govern the platform
 */
class InstitutionalRuleService
{
    // Rule types
    public const TYPE_SCHEDULE = 'schedule';
    public const TYPE_ACADEMIC = 'academic';
    public const TYPE_FINANCIAL = 'financial';
    public const TYPE_SECURITY = 'security';
    public const TYPE_ETHICAL = 'ethical';

    // Priority levels
    public const PRIORITY_CRITICAL = 'critical';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_LOW = 'low';

    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger
    ) {}

    /**
     * Initialize default institutional rules
     */
    public function initializeDefaultRules(): void
    {
        $defaults = [
            // Schedule rules
            [
                'code' => 'MAX_TEACHER_HOURS',
                'name' => 'Máximo horas semanales docente',
                'description' => 'Un docente no puede tener más de 40 horas semanales',
                'type' => self::TYPE_SCHEDULE,
                'priority' => self::PRIORITY_CRITICAL,
                'condition' => 'teacher_weekly_hours > 40',
                'action' => 'REJECT',
            ],
            [
                'code' => 'MAX_CONSECUTIVE_PERIODS',
                'name' => 'Máximo períodos consecutivos',
                'description' => 'Un docente no debe tener más de 4 períodos consecutivos',
                'type' => self::TYPE_SCHEDULE,
                'priority' => self::PRIORITY_HIGH,
                'condition' => 'consecutive_periods > 4',
                'action' => 'WARN',
            ],
            // Academic rules
            [
                'code' => 'MIN_PASSING_GRADE',
                'name' => 'Nota mínima aprobatoria',
                'description' => 'La nota mínima para aprobar es 60',
                'type' => self::TYPE_ACADEMIC,
                'priority' => self::PRIORITY_CRITICAL,
                'condition' => 'final_grade < 60',
                'action' => 'FAIL_COURSE',
            ],
            // Ethical rules
            [
                'code' => 'STUDENT_WELLBEING_FIRST',
                'name' => 'Bienestar estudiantil primero',
                'description' => 'Toda decisión prioriza el bienestar del estudiante',
                'type' => self::TYPE_ETHICAL,
                'priority' => self::PRIORITY_CRITICAL,
                'condition' => 'always',
                'action' => 'ENFORCE',
            ],
            [
                'code' => 'TEACHER_RESPECT',
                'name' => 'Respeto al docente',
                'description' => 'Los docentes no son recursos intercambiables',
                'type' => self::TYPE_ETHICAL,
                'priority' => self::PRIORITY_CRITICAL,
                'condition' => 'always',
                'action' => 'ENFORCE',
            ],
            [
                'code' => 'TRANSPARENCY',
                'name' => 'Transparencia en decisiones',
                'description' => 'Toda decisión algorítmica debe ser explicable',
                'type' => self::TYPE_ETHICAL,
                'priority' => self::PRIORITY_CRITICAL,
                'condition' => 'ai_decision == true',
                'action' => 'LOG',
            ],
        ];

        foreach ($defaults as $ruleData) {
            $this->createOrUpdateRule($ruleData);
        }
    }

    /**
     * Create or update a rule
     */
    public function createOrUpdateRule(array $data): InstitutionalRule
    {
        $repo = $this->em->getRepository(InstitutionalRule::class);
        $rule = $repo->findOneBy(['code' => $data['code']]);

        if (!$rule) {
            $rule = new InstitutionalRule();
            $rule->setCode($data['code']);
        }

        $rule->setName($data['name']);
        $rule->setDescription($data['description']);
        $rule->setType($data['type']);
        $rule->setPriority($data['priority']);
        $rule->setCondition($data['condition']);
        $rule->setAction($data['action']);
        $rule->setActive(true);

        $this->em->persist($rule);
        $this->em->flush();

        return $rule;
    }

    /**
     * Validate an action against institutional rules
     */
    public function validate(string $actionType, array $context): array
    {
        $repo = $this->em->getRepository(InstitutionalRule::class);
        $rules = $repo->findBy(['type' => $actionType, 'active' => true]);

        $violations = [];
        $warnings = [];
        $appliedRules = [];

        foreach ($rules as $rule) {
            $result = $this->evaluateRule($rule, $context);
            
            if ($result['violated']) {
                if ($rule->getAction() === 'REJECT') {
                    $violations[] = [
                        'code' => $rule->getCode(),
                        'name' => $rule->getName(),
                        'description' => $rule->getDescription(),
                    ];
                } else {
                    $warnings[] = [
                        'code' => $rule->getCode(),
                        'name' => $rule->getName(),
                        'description' => $rule->getDescription(),
                    ];
                }
            }
            
            $appliedRules[] = $rule->getCode();
        }

        return [
            'valid' => empty($violations),
            'violations' => $violations,
            'warnings' => $warnings,
            'appliedRules' => $appliedRules,
        ];
    }

    /**
     * Evaluate a single rule against context
     */
    private function evaluateRule(InstitutionalRule $rule, array $context): array
    {
        $condition = $rule->getCondition();
        $violated = false;

        // Simple condition evaluation (expand as needed)
        if ($condition === 'always') {
            $violated = false; // Principle, not a violation check
        } elseif (str_contains($condition, 'teacher_weekly_hours')) {
            $violated = ($context['teacher_weekly_hours'] ?? 0) > 40;
        } elseif (str_contains($condition, 'consecutive_periods')) {
            $violated = ($context['consecutive_periods'] ?? 0) > 4;
        } elseif (str_contains($condition, 'final_grade')) {
            $violated = ($context['final_grade'] ?? 100) < 60;
        }

        return ['violated' => $violated, 'rule' => $rule->getCode()];
    }

    /**
     * Get all active rules
     */
    public function getActiveRules(): array
    {
        $repo = $this->em->getRepository(InstitutionalRule::class);
        return $repo->findBy(['active' => true], ['priority' => 'DESC']);
    }

    /**
     * Get the institutional contract (all critical rules)
     */
    public function getContract(): array
    {
        $repo = $this->em->getRepository(InstitutionalRule::class);
        $rules = $repo->findBy([
            'active' => true, 
            'priority' => self::PRIORITY_CRITICAL
        ]);

        return [
            'version' => '1.0',
            'lastUpdated' => date('Y-m-d'),
            'rules' => array_map(fn($r) => [
                'code' => $r->getCode(),
                'name' => $r->getName(),
                'description' => $r->getDescription(),
                'type' => $r->getType(),
            ], $rules),
        ];
    }

    /**
     * Log rule application
     */
    public function logRuleApplication(string $ruleCode, array $context, bool $violated): void
    {
        $this->logger->info('Rule applied: {rule}', [
            'rule' => $ruleCode,
            'violated' => $violated,
            'context' => $context,
        ]);
    }
}
