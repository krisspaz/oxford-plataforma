<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\AuthenticationService;
use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AuthenticationServiceTest extends TestCase
{
    private $authService;
    private $passwordHasher;
    private $jwtManager;
    private $em;
    private $refreshTokenRepository;

    protected function setUp(): void
    {
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->jwtManager = $this->createMock(JWTTokenManagerInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->refreshTokenRepository = $this->createMock(RefreshTokenRepository::class);
        
        $this->authService = new AuthenticationService(
            $this->jwtManager,
            $this->em,
            $this->refreshTokenRepository,
            $this->passwordHasher
        );
    }

    public function testAuthenticateSuccess(): void
    {
        $user = new User();
        $user->setEmail('test@oxford.edu');
        $user->setPassword('hashed_password');
        $user->setIsActive(true);

        // Mock Repository to return user
        $userRepo = $this->createMock(EntityRepository::class);
        $userRepo->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@oxford.edu'])
            ->willReturn($user);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($userRepo);

        $this->passwordHasher->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, 'plain_password')
            ->willReturn(true);

        $result = $this->authService->authenticate('test@oxford.edu', 'plain_password');
        $this->assertSame($user, $result);
    }

    public function testAuthenticateFailureInvalidCredentials(): void
    {
        $user = new User();
        $user->setEmail('test@oxford.edu');

        // Mock Repository
        $userRepo = $this->createMock(EntityRepository::class);
        $userRepo->expects($this->once())
            ->method('findOneBy')
            ->willReturn($user);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->willReturn($userRepo);

        $this->passwordHasher->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(false);

        $this->expectException(\Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('Credenciales inválidas');

        $this->authService->authenticate('test@oxford.edu', 'wrong_password');
    }

    public function testGenerateAccessToken(): void
    {
        $user = new User();
        $user->setEmail('test@oxford.edu');

        $this->jwtManager->expects($this->once())
            ->method('create')
            ->with($user)
            ->willReturn('fake.jwt.token');

        $token = $this->authService->generateAccessToken($user);
        $this->assertEquals('fake.jwt.token', $token);
    }
}
