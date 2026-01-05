<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\RolePermissionRepository;

class PermissionService
{
    private RolePermissionRepository $rolePermissionRepo;
    private array $permissionCache = [];

    public function __construct(RolePermissionRepository $rolePermissionRepo)
    {
        $this->rolePermissionRepo = $rolePermissionRepo;
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(User $user, string $permissionCode): bool
    {
        // Admin has all permissions
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        foreach ($user->getRoles() as $role) {
            if ($this->roleHasPermission($role, $permissionCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a role has a specific permission
     */
    public function roleHasPermission(string $role, string $permissionCode): bool
    {
        $cacheKey = $role . ':' . $permissionCode;
        
        if (!isset($this->permissionCache[$cacheKey])) {
            $this->permissionCache[$cacheKey] = $this->rolePermissionRepo->hasPermission($role, $permissionCode);
        }

        return $this->permissionCache[$cacheKey];
    }

    /**
     * Get all permissions for a user
     */
    public function getUserPermissions(User $user): array
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return ['*']; // All permissions
        }

        $permissions = [];
        foreach ($user->getRoles() as $role) {
            $rolePermissions = $this->rolePermissionRepo->getPermissionCodesForRole($role);
            $permissions = array_merge($permissions, $rolePermissions);
        }

        return array_unique($permissions);
    }

    /**
     * Check if user can access a module
     */
    public function canAccessModule(User $user, string $module): bool
    {
        return $this->hasPermission($user, $module . '.view');
    }

    /**
     * Check if user can perform CRUD actions on a module
     */
    public function canCreate(User $user, string $module): bool
    {
        return $this->hasPermission($user, $module . '.create');
    }

    public function canEdit(User $user, string $module): bool
    {
        return $this->hasPermission($user, $module . '.edit');
    }

    public function canDelete(User $user, string $module): bool
    {
        return $this->hasPermission($user, $module . '.delete');
    }

    public function canApprove(User $user, string $module): bool
    {
        return $this->hasPermission($user, $module . '.approve');
    }
}
