import api from './api';

export const requestService = {
    // Get all requests with filters
    getAll: (params = {}) => {
        const queryString = new URLSearchParams(params).toString();
        return api.get(`/requests${queryString ? `?${queryString}` : ''}`);
    },

    // Get pending requests
    getPending: () => api.get('/requests?status=PENDIENTE'),

    // Get single request
    getById: (id) => api.get(`/requests/${id}`),

    // Create new request
    create: (data) => api.post('/requests', data),

    // Approve request
    approve: (id, notes) => api.post(`/requests/${id}/approve`, { notes }),

    // Reject request
    reject: (id, reason) => api.post(`/requests/${id}/reject`, { reason }),

    // Get requests by type
    getByType: (type) => api.get(`/requests?type=${type}`),
};

export default requestService;
