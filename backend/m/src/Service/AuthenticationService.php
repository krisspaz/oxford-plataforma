<?php

namespace App\Service;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authentication Service
 * Handles JWT tokens with HttpOnly cookies and refresh token rotation
 */
class AuthenticationService
{
    private const ACCESS_TOKEN_COOKIE = 'access_token';
    private const REFRESH_TOKEN_COOKIE = 'refresh_token';
    private const CSRF_TOKEN_COOKIE = 'csrf_token';

    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private EntityManagerInterface $em,
        private RefreshTokenRepository $refreshTokenRepository,
        private int $accessTokenTtl = 3600,
        private int $refreshTokenTtl = 2592000, // 30 days
        private bool $secureCookies = true,
        private string $cookieDomain = ''
    ) {}

    /**
     * Create authentication cookies after successful login
     */
    public function createAuthCookies(User $user, Request $request, Response $response): void
    {
        // Generate JWT access token
        $accessToken = $this->jwtManager->create($user);
        
        // Create refresh token
        $refreshToken = $this->createRefreshToken($user, $request);
        
        // Generate CSRF token
        $csrfToken = bin2hex(random_bytes(32));
        
        // Set HttpOnly cookies
        $response->headers->setCookie($this->createCookie(
            self::ACCESS_TOKEN_COOKIE,
            $accessToken,
            time() + $this->accessTokenTtl
        ));
        
        $response->headers->setCookie($this->createCookie(
            self::REFRESH_TOKEN_COOKIE,
            $refreshToken->getToken(),
            time() + $this->refreshTokenTtl
        ));
        
        // CSRF token is NOT HttpOnly (needs to be read by JavaScript)
        $response->headers->setCookie(Cookie::create(
            self::CSRF_TOKEN_COOKIE,
            $csrfToken,
            time() + $this->accessTokenTtl,
            '/',
            $this->cookieDomain,
            $this->secureCookies,
            false, // NOT HttpOnly
            false,
            Cookie::SAMESITE_STRICT
        ));
    }

    /**
     * Refresh the access token using refresh token
     */
    public function refreshAccessToken(Request $request, Response $response): ?User
    {
        $refreshTokenValue = $request->cookies->get(self::REFRESH_TOKEN_COOKIE);
        
        if (!$refreshTokenValue) {
            return null;
        }
        
        $refreshToken = $this->refreshTokenRepository->findValidToken($refreshTokenValue);
        
        if (!$refreshToken) {
            $this->clearAuthCookies($response);
            return null;
        }
        
        $user = $refreshToken->getUser();
        
        // Rotate refresh token (security best practice)
        $refreshToken->rotate();
        $refreshToken->setExpiresAt(new \DateTime("+{$this->refreshTokenTtl} seconds"));
        $this->em->flush();
        
        // Create new cookies
        $this->createAuthCookies($user, $request, $response);
        
        return $user;
    }

    /**
     * Logout - revoke tokens and clear cookies
     */
    public function logout(Request $request, Response $response): void
    {
        $refreshTokenValue = $request->cookies->get(self::REFRESH_TOKEN_COOKIE);
        
        if ($refreshTokenValue) {
            $this->refreshTokenRepository->revokeToken($refreshTokenValue);
        }
        
        $this->clearAuthCookies($response);
    }

    /**
     * Logout from all devices
     */
    public function logoutAllDevices(User $user, Response $response): void
    {
        $this->refreshTokenRepository->revokeAllForUser($user);
        $this->clearAuthCookies($response);
    }

    /**
     * Get access token from request (cookie or header)
     */
    public function getAccessToken(Request $request): ?string
    {
        // First check cookie
        $token = $request->cookies->get(self::ACCESS_TOKEN_COOKIE);
        
        if ($token) {
            return $token;
        }
        
        // Fallback to Authorization header for API compatibility
        $authHeader = $request->headers->get('Authorization');
        
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }
        
        return null;
    }

    /**
     * Validate CSRF token
     */
    public function validateCsrfToken(Request $request): bool
    {
        $cookieToken = $request->cookies->get(self::CSRF_TOKEN_COOKIE);
        $headerToken = $request->headers->get('X-CSRF-Token');
        
        if (!$cookieToken || !$headerToken) {
            return false;
        }
        
        return hash_equals($cookieToken, $headerToken);
    }

    /**
     * Get active sessions for a user
     */
    public function getActiveSessions(User $user): array
    {
        $tokens = $this->refreshTokenRepository->findByUser($user);
        
        return array_map(fn($token) => [
            'id' => $token->getId(),
            'ipAddress' => $token->getIpAddress(),
            'userAgent' => $token->getUserAgent(),
            'createdAt' => $token->getCreatedAt()->format('Y-m-d H:i:s'),
            'expiresAt' => $token->getExpiresAt()->format('Y-m-d H:i:s'),
        ], $tokens);
    }

    private function createRefreshToken(User $user, Request $request): RefreshToken
    {
        $refreshToken = new RefreshToken();
        $refreshToken->setUser($user);
        $refreshToken->setExpiresAt(new \DateTime("+{$this->refreshTokenTtl} seconds"));
        $refreshToken->setIpAddress($request->getClientIp());
        $refreshToken->setUserAgent($request->headers->get('User-Agent'));
        
        $this->em->persist($refreshToken);
        $this->em->flush();
        
        return $refreshToken;
    }

    private function createCookie(string $name, string $value, int $expires): Cookie
    {
        return Cookie::create(
            $name,
            $value,
            $expires,
            '/',
            $this->cookieDomain,
            $this->secureCookies,
            true, // HttpOnly
            false,
            Cookie::SAMESITE_STRICT
        );
    }

    private function clearAuthCookies(Response $response): void
    {
        $response->headers->clearCookie(self::ACCESS_TOKEN_COOKIE, '/', $this->cookieDomain);
        $response->headers->clearCookie(self::REFRESH_TOKEN_COOKIE, '/', $this->cookieDomain);
        $response->headers->clearCookie(self::CSRF_TOKEN_COOKIE, '/', $this->cookieDomain);
    }
}
