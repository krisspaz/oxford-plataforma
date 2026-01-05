<?php

namespace App\Tests\Entity;

use App\Entity\RefreshToken;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class RefreshTokenTest extends TestCase
{
    public function testNewTokenHasCorrectDefaults(): void
    {
        $token = new RefreshToken();

        $this->assertNotNull($token->getToken());
        $this->assertNotNull($token->getCreatedAt());
        $this->assertFalse($token->isRevoked());
        $this->assertEquals(128, strlen($token->getToken()));
    }

    public function testTokenRotationChangesValue(): void
    {
        $token = new RefreshToken();
        $originalToken = $token->getToken();

        $token->rotate();

        $this->assertNotEquals($originalToken, $token->getToken());
        $this->assertEquals(128, strlen($token->getToken()));
    }

    public function testIsExpiredReturnsTrueForPastDate(): void
    {
        $token = new RefreshToken();
        $token->setExpiresAt(new \DateTime('-1 day'));

        $this->assertTrue($token->isExpired());
    }

    public function testIsExpiredReturnsFalseForFutureDate(): void
    {
        $token = new RefreshToken();
        $token->setExpiresAt(new \DateTime('+1 day'));

        $this->assertFalse($token->isExpired());
    }

    public function testIsValidReturnsTrueForActiveToken(): void
    {
        $token = new RefreshToken();
        $token->setExpiresAt(new \DateTime('+1 day'));
        $token->setIsRevoked(false);

        $this->assertTrue($token->isValid());
    }

    public function testIsValidReturnsFalseForRevokedToken(): void
    {
        $token = new RefreshToken();
        $token->setExpiresAt(new \DateTime('+1 day'));
        $token->setIsRevoked(true);

        $this->assertFalse($token->isValid());
    }

    public function testIsValidReturnsFalseForExpiredToken(): void
    {
        $token = new RefreshToken();
        $token->setExpiresAt(new \DateTime('-1 day'));

        $this->assertFalse($token->isValid());
    }

    public function testUserAgentIsTruncated(): void
    {
        $token = new RefreshToken();
        $longUserAgent = str_repeat('a', 600);

        $token->setUserAgent($longUserAgent);

        $this->assertEquals(500, strlen($token->getUserAgent()));
    }

    public function testSetUser(): void
    {
        $token = new RefreshToken();
        $user = new User();
        $user->setEmail('test@example.com');

        $token->setUser($user);

        $this->assertSame($user, $token->getUser());
    }
}
