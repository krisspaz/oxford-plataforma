import api from './api';

export const packageService = {
    // Get all packages with filters
    getAll: (params = {}) => {
        const queryString = new URLSearchParams(params).toString();
        return api.get(`/packages${queryString ? `?${queryString}` : ''}`);
    },

    // Get single package with details
    getById: (id) => api.get(`/packages/${id}`),

    // Create new package
    create: (data) => api.post('/packages', data),

    // Update package
    update: (id, data) => api.put(`/packages/${id}`, data),

    // Add detail to package
    addDetail: (packageId, detail) => api.post(`/packages/${packageId}/details`, detail),

    // Get active packages by cycle
    getActiveByCycle: (cycleId) => api.get(`/packages?cycle=${cycleId}&active=true`),

    // Get packages by grade
    getByGrade: (gradeId) => api.get(`/packages?grade=${gradeId}`),
};

export default packageService;
