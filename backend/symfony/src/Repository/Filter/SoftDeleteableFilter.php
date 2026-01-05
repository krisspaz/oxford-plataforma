<?php

namespace App\Repository\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Doctrine SQL Filter to automatically exclude soft-deleted entities
 * Enable in doctrine.yaml: orm.filters.soft_deleteable.class
 */
class SoftDeleteableFilter extends SQLFilter
{
    /**
     * Gets the SQL query part to add to a query.
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        // Check if the entity has the deletedAt field (uses SoftDeleteableTrait)
        if (!$targetEntity->hasField('deletedAt')) {
            return '';
        }

        return sprintf('%s.deleted_at IS NULL', $targetTableAlias);
    }
}
