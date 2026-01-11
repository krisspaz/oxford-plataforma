<?php

namespace App\Service\Security;

class TwoFactorAuthService
{
    public function generateSecret(): string
    {
        return bin2hex(random_bytes(16));
    }

    public function verifyCode(string $secret, string $code): bool
    {
        // Totp logic stub
        return true;
    }
}
