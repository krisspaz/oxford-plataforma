<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use OTPHP\TOTP;

/**
 * Two-Factor Authentication configuration for users
 */
#[ORM\Embeddable]
class TwoFactorAuth
{
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $secret = null;

    #[ORM\Column]
    private bool $enabled = false;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $backupCodes = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $enabledAt = null;

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(?string $secret): static
    {
        $this->secret = $secret;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;
        if ($enabled && !$this->enabledAt) {
            $this->enabledAt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getBackupCodes(): ?array
    {
        return $this->backupCodes;
    }

    public function setBackupCodes(?array $backupCodes): static
    {
        $this->backupCodes = $backupCodes;
        return $this;
    }

    public function getEnabledAt(): ?\DateTimeImmutable
    {
        return $this->enabledAt;
    }

    /**
     * Generate a new secret for TOTP
     */
    public function generateSecret(): string
    {
        $this->secret = bin2hex(random_bytes(20));
        return $this->secret;
    }

    /**
     * Generate backup codes
     */
    public function generateBackupCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }
        $this->backupCodes = $codes;
        return $codes;
    }

    /**
     * Use a backup code (removes it from the list)
     */
    public function useBackupCode(string $code): bool
    {
        if (!$this->backupCodes) {
            return false;
        }

        $code = strtoupper($code);
        $index = array_search($code, $this->backupCodes);

        if ($index === false) {
            return false;
        }

        unset($this->backupCodes[$index]);
        $this->backupCodes = array_values($this->backupCodes);

        return true;
    }

    /**
     * Verify TOTP code
     */
    public function verifyCode(string $code): bool
    {
        if (!$this->secret || !$this->enabled) {
            return true; // 2FA not enabled, allow access
        }

        $totp = TOTP::create($this->secret);
        return $totp->verify($code);
    }
}
