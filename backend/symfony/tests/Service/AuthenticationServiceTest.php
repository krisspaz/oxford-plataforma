<?php

namespace App\Tests\Service;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Repository\RefreshTokenRepository;
use App\Service\AuthenticationService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationServiceTest extends TestCase
{
    private AuthenticationService $service;
    private $jwtManager;
    private $em;
    private $refreshTokenRepository;

    protected function setUp(): void
    {
        $this->jwtManager = $this->createMock(JWTTokenManagerInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->refreshTokenRepository = $this->createMock(RefreshTokenRepository::class);

        $this->service = new AuthenticationService(
            $this->jwtManager,
            $this->em,
            $this->refreshTokenRepository,
            3600, // accessTokenTtl
            2592000, // refreshTokenTtl
            false, // secureCookies (false for testing)
            '' // cookieDomain
        );
    }

    public function testGetAccessTokenFromCookie(): void
    {
        $request = new Request();
        $request->cookies->set('access_token', 'test_jwt_token');

        $token = $this->service->getAccessToken($request);

        $this->assertEquals('test_jwt_token', $token);
    }

    public function testGetAccessTokenFromHeader(): void
    {
        $request = new Request();
        $request->headers->set('Authorization', 'Bearer test_jwt_token');

        $token = $this->service->getAccessToken($request);

        $this->assertEquals('test_jwt_token', $token);
    }

    public function testGetAccessTokenReturnsNullWhenMissing(): void
    {
        $request = new Request();

        $token = $this->service->getAccessToken($request);

        $this->assertNull($token);
    }

    public function testValidateCsrfTokenSuccess(): void
    {
        $request = new Request();
        $csrfToken = 'valid_csrf_token';
        $request->cookies->set('csrf_token', $csrfToken);
        $request->headers->set('X-CSRF-Token', $csrfToken);

        $result = $this->service->validateCsrfToken($request);

        $this->assertTrue($result);
    }

    public function testValidateCsrfTokenFailsWhenMismatched(): void
    {
        $request = new Request();
        $request->cookies->set('csrf_token', 'token1');
        $request->headers->set('X-CSRF-Token', 'token2');

        $result = $this->service->validateCsrfToken($request);

        $this->assertFalse($result);
    }

    public function testValidateCsrfTokenFailsWhenMissing(): void
    {
        $request = new Request();

        $result = $this->service->validateCsrfToken($request);

        $this->assertFalse($result);
    }

    public function testRefreshAccessTokenWithInvalidToken(): void
    {
        $request = new Request();
        $request->cookies->set('refresh_token', 'invalid_token');
        $response = new Response();

        $this->refreshTokenRepository
            ->expects($this->once())
            ->method('findValidToken')
            ->with('invalid_token')
            ->willReturn(null);

        $result = $this->service->refreshAccessToken($request, $response);

        $this->assertNull($result);
    }

    public function testCreateAuthCookiesSetsCorrectCookies(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $this->jwtManager
            ->expects($this->once())
            ->method('create')
            ->with($user)
            ->willReturn('jwt_access_token');

        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $request = new Request([], [], [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $response = new Response();

        $this->service->createAuthCookies($user, $request, $response);

        $cookies = $response->headers->getCookies();
        $cookieNames = array_map(fn($c) => $c->getName(), $cookies);

        $this->assertContains('access_token', $cookieNames);
        $this->assertContains('refresh_token', $cookieNames);
        $this->assertContains('csrf_token', $cookieNames);
    }
}
