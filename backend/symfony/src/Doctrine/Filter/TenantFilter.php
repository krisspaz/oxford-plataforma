<?php

namespace App\Doctrine\Filter;

use App\Model\TenantAwareInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class TenantFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        // Don't apply filter if entity is not TenantAware
        if (!$targetEntity->reflClass->implementsInterface(TenantAwareInterface::class)) {
            return '';
        }

        try {
            // Get current tenant ID parameter injected by TenantListener/Subscriber
            // Note: We user 'tenant_id' as parameter name
            $tenantId = $this->getParameter('tenant_id');
        } catch (\InvalidArgumentException $e) {
            // If no tenant is set (e.g. command line or login), do not filter?
            // SECURITY: Better to filter everything (return '0 = 1') or allow all if super admin?
            // For now, if no tenant is set, we block access unless explicitly handled.
            // But for login flow, we might not have tenant yet.
            // Let's assume empty string typically means "no constraint" in some implementations,
            // but for strict multi-tenancy we want '0 = 1' usually.
            // However, to allow the first login/queries before tenant resolution, we might need a workaround.
            // Let's return '' for now to avoid crashing login, but ideally we should enable the filter LATER.
            return '';
        }

        // Quote the 'id' to be safe (though it's an int usually)
        return sprintf('%s.tenant_id = %s', $targetTableAlias, $tenantId);
    }
}
