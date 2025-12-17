<?php

namespace App\Controller;

use App\Service\TwoFactorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth/2fa')]
class TwoFactorController extends AbstractController
{
    public function __construct(
        private TwoFactorService $twoFactorService
    ) {}

    /**
     * Check 2FA status for current user
     */
    #[Route('/status', name: '2fa_status', methods: ['GET'])]
    public function status(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'enabled' => $this->twoFactorService->isEnabled($user),
            'backupCodesRemaining' => $this->twoFactorService->getBackupCodesCount($user),
        ]);
    }

    /**
     * Initialize 2FA setup (generates QR code)
     */
    #[Route('/initialize', name: '2fa_initialize', methods: ['POST'])]
    public function initialize(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $result = $this->twoFactorService->initialize($user);
            return $this->json($result);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Enable 2FA after verifying code
     */
    #[Route('/enable', name: '2fa_enable', methods: ['POST'])]
    public function enable(Request $request): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        $code = $data['code'] ?? '';

        if (empty($code)) {
            return $this->json(['error' => 'Verification code required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->twoFactorService->enable($user, $code);
            return $this->json([
                'success' => true,
                'backupCodes' => $result['backupCodes'],
                'message' => '2FA habilitado correctamente. Guarda estos códigos de respaldo.'
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Disable 2FA
     */
    #[Route('/disable', name: '2fa_disable', methods: ['POST'])]
    public function disable(Request $request): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        $code = $data['code'] ?? '';

        if (empty($code)) {
            return $this->json(['error' => 'Verification code required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->twoFactorService->disable($user, $code);
            return $this->json([
                'success' => true,
                'message' => '2FA deshabilitado correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Verify 2FA code (for login flow)
     */
    #[Route('/verify', name: '2fa_verify', methods: ['POST'])]
    public function verify(Request $request): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        $code = $data['code'] ?? '';

        if (empty($code)) {
            return $this->json(['error' => 'Verification code required'], Response::HTTP_BAD_REQUEST);
        }

        $isValid = $this->twoFactorService->verifyCode($user, $code);

        if (!$isValid) {
            return $this->json(['error' => 'Código inválido'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'success' => true,
            'verified' => true
        ]);
    }

    /**
     * Regenerate backup codes
     */
    #[Route('/backup-codes', name: '2fa_backup_codes', methods: ['POST'])]
    public function regenerateBackupCodes(Request $request): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        $code = $data['code'] ?? '';

        if (empty($code)) {
            return $this->json(['error' => 'Verification code required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $backupCodes = $this->twoFactorService->regenerateBackupCodes($user, $code);
            return $this->json([
                'success' => true,
                'backupCodes' => $backupCodes,
                'message' => 'Nuevos códigos de respaldo generados'
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
