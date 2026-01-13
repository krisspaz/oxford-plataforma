<?php

namespace App\Service\Security;

class AuditLoggerService
{
    public function log(string $action, string $user, array $details): void
    {
        // Log to database or elasticsearch
    }
}
