<?php

namespace App\Tests\Service;

use App\Service\AuditService;
use App\Entity\AuditLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Psr\Log\LoggerInterface;

class AuditServiceTest extends KernelTestCase
{
    private ?AuditService $auditService;

    protected function setUp(): void
    {
        // Boot kernel if not already booted
        self::bootKernel();
        
        // Use static::getContainer() to access private services in test env
        $this->auditService = static::getContainer()->get(AuditService::class);
    }

    public function testLogSecurityEvent(): void
    {
        // This test verifies the service can be instantiated
        $this->assertInstanceOf(AuditService::class, $this->auditService);
    }

    public function testAuditLogEntity(): void
    {
        $auditLog = new AuditLog();
        $auditLog->setAction('TEST_ACTION');
        $auditLog->setEntityType('TestEntity');
        $auditLog->setEntityId(1);
        $auditLog->setUserEmail('test@example.com');
        $auditLog->setIpAddress('127.0.0.1');
        $auditLog->setUserAgent('Test Agent');
        $auditLog->setChanges(['field' => 'value']);
        $auditLog->setCreatedAt(new \DateTimeImmutable());
        
        $this->assertEquals('TEST_ACTION', $auditLog->getAction());
        $this->assertEquals('TestEntity', $auditLog->getEntityType());
        $this->assertEquals(1, $auditLog->getEntityId());
        $this->assertEquals('test@example.com', $auditLog->getUserEmail());
        $this->assertEquals('127.0.0.1', $auditLog->getIpAddress());
        $this->assertEquals('Test Agent', $auditLog->getUserAgent());
        $this->assertEquals(['field' => 'value'], $auditLog->getChanges());
        $this->assertInstanceOf(\DateTimeImmutable::class, $auditLog->getCreatedAt());
    }
}
