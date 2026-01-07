<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Admin 2FA Enforcement Subscriber
 * =================================
 * Ensures administrators must have 2FA enabled to access protected routes.
 */
class Require2FASubscriber implements EventSubscriberInterface
{
    private const EXEMPT_ROUTES = [
        'api_login_check',
        'auth_login',
        'auth_logout',
        'api_auth_setup_2fa',
        'api_auth_verify_2fa',
        'api_doc',
    ];

    private const PROTECTED_ROLES = [
        'ROLE_ADMIN',
        'ROLE_DIRECTOR',
    ];

    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private EntityManagerInterface $em
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        // Skip exempt routes
        if (in_array($route, self::EXEMPT_ROUTES)) {
            return;
        }

        // Get authenticated user
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return;
        }

        // Check if user has protected role
        $roles = $user->getRoles();
        $hasProtectedRole = !empty(array_intersect($roles, self::PROTECTED_ROLES));

        if (!$hasProtectedRole) {
            return;
        }

        // Check if 2FA is enabled
        $twoFactorAuth = $this->em->getRepository(\App\Entity\TwoFactorAuth::class)
            ->findOneBy(['user' => $user, 'isEnabled' => true]);

        if (!$twoFactorAuth) {
            // Allow access to 2FA setup endpoints
            if (str_starts_with($route ?? '', 'api_auth_2fa')) {
                return;
            }

            // For API requests, return JSON error
            if (str_starts_with($request->getPathInfo(), '/api')) {
                throw new AccessDeniedHttpException(
                    '2FA es obligatorio para administradores. Por favor configure la autenticación de dos factores.'
                );
            }
        }
    }
}
