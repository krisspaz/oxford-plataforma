<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AuthenticationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private AuthenticationService $authService,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {}

    /**
     * Login endpoint - creates HttpOnly cookie with JWT
     */
    #[Route('/login', name: 'auth_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate input
        $errors = $this->validateLoginInput($data);
        if (!empty($errors)) {
            return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Find user
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            // Use consistent response time to prevent timing attacks
            usleep(random_int(100000, 300000)); // 100-300ms
            return $this->json(['error' => 'Credenciales inválidas'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->isActive()) {
            return $this->json(['error' => 'Cuenta desactivada'], Response::HTTP_FORBIDDEN);
        }

        // Create response with auth cookies
        $response = new JsonResponse([
            'success' => true,
            'user' => $this->serializeUser($user),
            'message' => 'Login exitoso'
        ]);

        $this->authService->createAuthCookies($user, $request, $response);

        return $response;
    }

    /**
     * Refresh access token using refresh token cookie
     */
    #[Route('/refresh', name: 'auth_refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        $response = new JsonResponse();
        $user = $this->authService->refreshAccessToken($request, $response);

        if (!$user) {
            return $this->json(['error' => 'Invalid or expired refresh token'], Response::HTTP_UNAUTHORIZED);
        }

        $response->setData([
            'success' => true,
            'user' => $this->serializeUser($user),
            'message' => 'Token refreshed'
        ]);

        return $response;
    }

    /**
     * Logout - revoke tokens and clear cookies
     */
    #[Route('/logout', name: 'auth_logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        $response = new JsonResponse([
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ]);

        $this->authService->logout($request, $response);

        return $response;
    }

    /**
     * Logout from all devices
     */
    #[Route('/logout-all', name: 'auth_logout_all', methods: ['POST'])]
    public function logoutAll(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $response = new JsonResponse([
            'success' => true,
            'message' => 'Todas las sesiones han sido cerradas'
        ]);

        $this->authService->logoutAllDevices($user, $response);

        return $response;
    }

    /**
     * Get current user info
     */
    #[Route('/me', name: 'auth_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'user' => $this->serializeUser($user),
            'sessions' => $this->authService->getActiveSessions($user)
        ]);
    }

    /**
     * Revoke a specific session
     */
    #[Route('/sessions/{sessionId}', name: 'auth_revoke_session', methods: ['DELETE'])]
    public function revokeSession(int $sessionId): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        // Find and revoke the session (refresh token)
        $refreshToken = $this->em->getRepository(\App\Entity\RefreshToken::class)->find($sessionId);

        if (!$refreshToken || $refreshToken->getUser()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Session not found'], Response::HTTP_NOT_FOUND);
        }

        $refreshToken->setIsRevoked(true);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Sesión revocada'
        ]);
    }

    /**
     * Validate CSRF token (for state-changing requests)
     */
    #[Route('/validate-csrf', name: 'auth_validate_csrf', methods: ['POST'])]
    public function validateCsrf(Request $request): JsonResponse
    {
        $isValid = $this->authService->validateCsrfToken($request);

        return $this->json([
            'valid' => $isValid
        ]);
    }

    private function validateLoginInput(?array $data): array
    {
        $errors = [];

        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        }

        return $errors;
    }

    private function serializeUser(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'isActive' => $user->isActive(),
        ];
    }
}
