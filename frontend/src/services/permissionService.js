import { api } from './api';

export const permissionService = {
    // Get all permissions grouped by module
    getAll: () => api.get('/permissions'),

    // Get permissions for a role
    getByRole: (role) => api.get(`/permissions/role/${role}`),

    // Set permissions for a role
    setRolePermissions: (role, permissions) =>
        api.post(`/permissions/role/${role}`, { permissions }),

    // Check if role has permission
    check: (role, permission) =>
        api.post('/permissions/check', { role, permission }),

    // Seed default permissions
    seed: () => api.post('/permissions/seed'),
};
