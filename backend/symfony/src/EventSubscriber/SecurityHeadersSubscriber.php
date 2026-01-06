<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Security Headers Subscriber
 * 
 * Adds security headers to all responses:
 * - Content-Security-Policy
 * - Strict-Transport-Security
 * - X-Frame-Options
 * - X-Content-Type-Options
 * - X-XSS-Protection
 * - Referrer-Policy
 * - Permissions-Policy
 */
class SecurityHeadersSubscriber implements EventSubscriberInterface
{
    private bool $isProduction;
    
    public function __construct(string $environment = 'dev')
    {
        $this->isProduction = $environment === 'prod';
    }
    
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', 0],
        ];
    }
    
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        
        $response = $event->getResponse();
        
        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');
        
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // XSS Protection (legacy but still useful)
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Referrer policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions policy (disable unused features)
        $response->headers->set('Permissions-Policy', implode(', ', [
            'accelerometer=()',
            'camera=()',
            'geolocation=()',
            'gyroscope=()',
            'magnetometer=()',
            'microphone=()',
            'payment=()',
            'usb=()',
        ]));
        
        // Content Security Policy
        $csp = $this->buildCSP();
        $response->headers->set('Content-Security-Policy', $csp);
        
        // HSTS (only in production with HTTPS)
        if ($this->isProduction) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }
        
        // Cache control for sensitive pages
        if ($this->isSensitivePath($event->getRequest()->getPathInfo())) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
    }
    
    private function buildCSP(): string
    {
        $directives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "img-src 'self' data: https: blob:",
            "font-src 'self' https://fonts.gstatic.com",
            "connect-src 'self' http://localhost:* https://*.oxford.edu.gt",
            "frame-ancestors 'none'",
            "form-action 'self'",
            "base-uri 'self'",
            "object-src 'none'",
        ];
        
        return implode('; ', $directives);
    }
    
    private function isSensitivePath(string $path): bool
    {
        $sensitivePaths = [
            '/api/login',
            '/api/register',
            '/api/password',
            '/admin',
            '/api/users',
        ];
        
        foreach ($sensitivePaths as $sensitive) {
            if (str_starts_with($path, $sensitive)) {
                return true;
            }
        }
        
        return false;
    }
}
