<?php

namespace App\EventListener;

use App\Entity\Tenant;
use App\Model\TenantAwareInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TenantListener
{
    private ?Tenant $currentTenant = null;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[AsEventListener(event: KernelEvents::REQUEST, priority: 5)]
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        
        // 1. Skip Tenant Filter for Public/Login Routes
        // We verify the user FIRST, then the token contains the tenant.
        $path = $request->getPathInfo();
        $publicRoutes = [
            '/api/login',
            '/api/login_check',
            '/api/health',
            '/api/doc',
            '/api/seed' // seed might need tenant but handled in cli mostly
        ];

        foreach ($publicRoutes as $route) {
            if (str_starts_with($path, $route)) {
                return; // Do NOT enable filter
            }
        }
        
        // Also skip for OPTIONS (CORS)
        if ($request->getMethod() === 'OPTIONS') {
            return;
        }

        $tenantId = null;

        // 2. Check Explicit Header (e.g. Super Admin switching context)
        if ($request->headers->has('X-Tenant-ID')) {
            $tenantId = $request->headers->get('X-Tenant-ID');
        } 
        // 3. Extract from JWT Token (Standard Flow)
        elseif ($request->headers->has('Authorization')) {
            $authHeader = $request->headers->get('Authorization');
            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                $parts = explode('.', $token);
                if (count($parts) === 3) {
                    $payload = json_decode(base64_decode($parts[1]), true);
                    if (isset($payload['tenant_id'])) {
                        $tenantId = $payload['tenant_id'];
                    }
                }
            }
        }

        // 4. Resolve Tenant
        if ($tenantId) {
            // Validate it exists (lightweight query or cached)
            $tenant = $this->entityManager->getRepository(Tenant::class)->find($tenantId);
            
            if ($tenant) {
                $this->currentTenant = $tenant;
                
                // Enable Filter
                $filter = $this->entityManager->getFilters()->enable('tenant_filter');
                $filter->setParameter('tenant_id', $tenant->getId());
            }
        }
        
        // 5. Hardcoded Fallback (Safe Migration / Dev)
        // Only if NO tenant found and we are NOT in production strictly yet.
        // Or if we want to allow "Global Admin" to see everything (disable filter)?
        // For now, if no tenant is resolved, we do NOT enable the filter.
        // This means queries will return ALL data (dangerous for multi-tenant, perfect for Super Admin)
        // BUT, our User entity is TenantAware. If we query Users without filter, we see all users.
        // Access Control (Voters) should handle permissions.
   }

    #[AsEventListener(event: 'prePersist')]
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof TenantAwareInterface && $this->currentTenant) {
            if ($entity->getTenant() === null) {
                $entity->setTenant($this->currentTenant);
            }
        }
    }
}
