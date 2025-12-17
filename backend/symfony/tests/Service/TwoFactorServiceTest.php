<?php

namespace App\Tests\Service;

use App\Entity\TwoFactorAuth;
use App\Entity\User;
use App\Service\TwoFactorService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TwoFactorServiceTest extends TestCase
{
    private TwoFactorService $service;
    private $em;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new TwoFactorService($this->em);
    }

    public function testIsEnabledReturnsFalseWhenNotConfigured(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getTwoFactorAuth')->willReturn(null);

        $result = $this->service->isEnabled($user);

        $this->assertFalse($result);
    }

    public function testIsEnabledReturnsFalseWhenNotEnabled(): void
    {
        $twoFactor = new TwoFactorAuth();
        $twoFactor->setEnabled(false);

        $user = $this->createMock(User::class);
        $user->method('getTwoFactorAuth')->willReturn($twoFactor);

        $result = $this->service->isEnabled($user);

        $this->assertFalse($result);
    }

    public function testIsEnabledReturnsTrueWhenEnabled(): void
    {
        $twoFactor = new TwoFactorAuth();
        $twoFactor->setEnabled(true);
        $twoFactor->generateSecret();

        $user = $this->createMock(User::class);
        $user->method('getTwoFactorAuth')->willReturn($twoFactor);

        $result = $this->service->isEnabled($user);

        $this->assertTrue($result);
    }

    public function testGetBackupCodesCountReturnsZeroWhenNotConfigured(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getTwoFactorAuth')->willReturn(null);

        $count = $this->service->getBackupCodesCount($user);

        $this->assertEquals(0, $count);
    }

    public function testGetBackupCodesCountReturnsCorrectCount(): void
    {
        $twoFactor = new TwoFactorAuth();
        $twoFactor->generateBackupCodes(); // Generates 10 codes

        $user = $this->createMock(User::class);
        $user->method('getTwoFactorAuth')->willReturn($twoFactor);

        $count = $this->service->getBackupCodesCount($user);

        $this->assertEquals(10, $count);
    }

    public function testVerifyCodeReturnsTrueWhenNotEnabled(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getTwoFactorAuth')->willReturn(null);

        $result = $this->service->verifyCode($user, '123456');

        $this->assertTrue($result); // Should pass when 2FA not configured
    }

    public function testInitializeGeneratesSecret(): void
    {
        $twoFactor = new TwoFactorAuth();
        $user = $this->createMock(User::class);
        $user->method('getTwoFactorAuth')->willReturn($twoFactor);
        $user->method('getEmail')->willReturn('test@example.com');

        $this->em->expects($this->once())->method('flush');

        $result = $this->service->initialize($user);

        $this->assertArrayHasKey('secret', $result);
        $this->assertArrayHasKey('qrCodeUri', $result);
        $this->assertArrayHasKey('manualEntryKey', $result);
        $this->assertNotEmpty($result['secret']);
    }
}
