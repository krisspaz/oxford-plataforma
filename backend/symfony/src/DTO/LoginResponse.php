<?php

namespace App\DTO;

class LoginResponse
{
    public function __construct(
        public string $token,
        public ?string $refreshToken = null,
        public ?array $user = null,
        public bool $requiresTwoFactor = false,
    ) {}
}
