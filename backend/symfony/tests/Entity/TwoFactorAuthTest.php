<?php

namespace App\Tests\Entity;

use App\Entity\TwoFactorAuth;
use PHPUnit\Framework\TestCase;

class TwoFactorAuthTest extends TestCase
{
    public function testGenerateSecretCreatesValidSecret(): void
    {
        $tfa = new TwoFactorAuth();

        $secret = $tfa->generateSecret();

        $this->assertNotEmpty($secret);
        $this->assertEquals(40, strlen($secret)); // 20 bytes = 40 hex chars
        $this->assertEquals($secret, $tfa->getSecret());
    }

    public function testGenerateBackupCodesCreates10Codes(): void
    {
        $tfa = new TwoFactorAuth();

        $codes = $tfa->generateBackupCodes();

        $this->assertCount(10, $codes);
        foreach ($codes as $code) {
            $this->assertEquals(8, strlen($code)); // 4 bytes = 8 hex chars
            $this->assertTrue(ctype_xdigit($code));
        }
    }

    public function testUseBackupCodeRemovesCodeFromList(): void
    {
        $tfa = new TwoFactorAuth();
        $codes = $tfa->generateBackupCodes();
        $codeToUse = $codes[0];

        $result = $tfa->useBackupCode($codeToUse);

        $this->assertTrue($result);
        $this->assertCount(9, $tfa->getBackupCodes());
        $this->assertNotContains($codeToUse, $tfa->getBackupCodes());
    }

    public function testUseBackupCodeReturnsFalseForInvalidCode(): void
    {
        $tfa = new TwoFactorAuth();
        $tfa->generateBackupCodes();

        $result = $tfa->useBackupCode('INVALIDCODE');

        $this->assertFalse($result);
        $this->assertCount(10, $tfa->getBackupCodes());
    }

    public function testUseBackupCodeIsCaseInsensitive(): void
    {
        $tfa = new TwoFactorAuth();
        $codes = $tfa->generateBackupCodes();
        $codeToUse = strtolower($codes[0]);

        $result = $tfa->useBackupCode($codeToUse);

        $this->assertTrue($result);
    }

    public function testEnabledSetsTimestamp(): void
    {
        $tfa = new TwoFactorAuth();
        $this->assertNull($tfa->getEnabledAt());

        $tfa->setEnabled(true);

        $this->assertNotNull($tfa->getEnabledAt());
        $this->assertTrue($tfa->isEnabled());
    }

    public function testVerifyCodeReturnsTrueWhenNotEnabled(): void
    {
        $tfa = new TwoFactorAuth();
        $tfa->setEnabled(false);

        $result = $tfa->verifyCode('123456');

        $this->assertTrue($result); // Should pass when not enabled
    }
}
