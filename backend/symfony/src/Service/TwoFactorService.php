<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\TwoFactorAuth;
use Doctrine\ORM\EntityManagerInterface;
use OTPHP\TOTP;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Two-Factor Authentication Service
 */
class TwoFactorService
{
    private const ISSUER = 'Oxford Sistema';

    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /**
     * Initialize 2FA for a user (generates secret but doesn't enable yet)
     */
    public function initialize(User $user): array
    {
        $twoFactor = $user->getTwoFactorAuth() ?? new TwoFactorAuth();
        $secret = $twoFactor->generateSecret();
        
        // Create TOTP instance
        $totp = TOTP::create($secret);
        $totp->setLabel($user->getEmail());
        $totp->setIssuer(self::ISSUER);
        
        // Generate QR code
        $qrCodeUri = $totp->getProvisioningUri();
        
        $user->setTwoFactorAuth($twoFactor);
        $this->em->flush();
        
        return [
            'secret' => $secret,
            'qrCodeUri' => $qrCodeUri,
            'manualEntryKey' => chunk_split($secret, 4, ' '),
        ];
    }

    /**
     * Enable 2FA after verifying initial code
     */
    public function enable(User $user, string $code): array
    {
        $twoFactor = $user->getTwoFactorAuth();
        
        if (!$twoFactor || !$twoFactor->getSecret()) {
            throw new \Exception('2FA not initialized');
        }
        
        // Verify the code first
        if (!$this->verifyCode($user, $code)) {
            throw new \Exception('Invalid verification code');
        }
        
        // Generate backup codes
        $backupCodes = $twoFactor->generateBackupCodes();
        $twoFactor->setEnabled(true);
        
        $this->em->flush();
        
        return [
            'enabled' => true,
            'backupCodes' => $backupCodes,
        ];
    }

    /**
     * Disable 2FA
     */
    public function disable(User $user, string $code): bool
    {
        $twoFactor = $user->getTwoFactorAuth();
        
        if (!$twoFactor || !$twoFactor->isEnabled()) {
            return true;
        }
        
        // Verify code before disabling
        if (!$this->verifyCode($user, $code)) {
            throw new \Exception('Invalid verification code');
        }
        
        $twoFactor->setEnabled(false);
        $twoFactor->setSecret(null);
        $twoFactor->setBackupCodes(null);
        
        $this->em->flush();
        
        return true;
    }

    /**
     * Verify TOTP or backup code
     */
    public function verifyCode(User $user, string $code): bool
    {
        $twoFactor = $user->getTwoFactorAuth();
        
        if (!$twoFactor) {
            return true; // No 2FA configured
        }
        
        if (!$twoFactor->isEnabled()) {
            return true; // 2FA not enabled
        }
        
        // Try TOTP first
        $totp = TOTP::create($twoFactor->getSecret());
        if ($totp->verify($code)) {
            return true;
        }
        
        // Try backup code
        if ($twoFactor->useBackupCode($code)) {
            $this->em->flush();
            return true;
        }
        
        return false;
    }

    /**
     * Check if user has 2FA enabled
     */
    public function isEnabled(User $user): bool
    {
        $twoFactor = $user->getTwoFactorAuth();
        return $twoFactor && $twoFactor->isEnabled();
    }

    /**
     * Get remaining backup codes count
     */
    public function getBackupCodesCount(User $user): int
    {
        $twoFactor = $user->getTwoFactorAuth();
        if (!$twoFactor || !$twoFactor->getBackupCodes()) {
            return 0;
        }
        return count($twoFactor->getBackupCodes());
    }

    /**
     * Regenerate backup codes
     */
    public function regenerateBackupCodes(User $user, string $code): array
    {
        if (!$this->verifyCode($user, $code)) {
            throw new \Exception('Invalid verification code');
        }
        
        $twoFactor = $user->getTwoFactorAuth();
        $backupCodes = $twoFactor->generateBackupCodes();
        
        $this->em->flush();
        
        return $backupCodes;
    }
}
