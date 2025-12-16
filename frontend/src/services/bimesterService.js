import api from './api';

export const bimesterService = {
    // Get all bimesters
    getAll: (params = {}) => {
        const queryString = new URLSearchParams(params).toString();
        return api.get(`/bimesters${queryString ? `?${queryString}` : ''}`);
    },

    // Get single bimester
    getById: (id) => api.get(`/bimesters/${id}`),

    // Get current bimester
    getCurrent: () => api.get('/bimesters/current'),

    // Create new bimester
    create: (data) => api.post('/bimesters', data),

    // Update bimester
    update: (id, data) => api.put(`/bimesters/${id}`, data),

    // Close bimester (coordinación)
    close: (id) => api.post(`/bimesters/${id}/close`),

    // Open bimester (coordinación)
    open: (id) => api.post(`/bimesters/${id}/open`),

    // Get by cycle
    getByCycle: (cycleId) => api.get(`/bimesters?cycle=${cycleId}`),
};

export default bimesterService;
