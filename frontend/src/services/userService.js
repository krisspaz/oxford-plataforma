import api from './api';

export const userService = {
    // Get all users
    getAll: (params = {}) => {
        const queryString = new URLSearchParams(params).toString();
        return api.get(`/users${queryString ? `?${queryString}` : ''}`);
    },

    // Get single user
    getById: (id) => api.get(`/users/${id}`),

    // Create new user
    create: (data) => api.post('/users', data),

    // Update user
    update: (id, data) => api.patch(`/users/${id}`, data),

    // Toggle user status
    toggleStatus: (id) => api.patch(`/users/${id}/status`),

    // Update password
    updatePassword: (id, password) => api.patch(`/users/${id}/password`, { password }),

    // Get available roles
    getRoles: () => api.get('/users/roles'),
};

export default userService;
