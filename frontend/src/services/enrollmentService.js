import api from './api';

export const enrollmentService = {
    // Get all enrollments with optional filters
    getAll: (params = {}) => {
        const queryString = new URLSearchParams(params).toString();
        return api.get(`/enrollments${queryString ? `?${queryString}` : ''}`);
    },

    // Get single enrollment
    getById: (id) => api.get(`/enrollments/${id}`),

    // Create new enrollment
    create: (data) => api.post('/enrollments', data),

    // Update enrollment status
    updateStatus: (id, status) => api.patch(`/enrollments/${id}/status`, { status }),

    // Get enrollments by student
    getByStudent: (studentId) => api.get(`/enrollments?student=${studentId}`),

    // Get enrollments by cycle
    getByCycle: (cycleId) => api.get(`/enrollments?cycle=${cycleId}`),
};

export default enrollmentService;
