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

    // Get all selected packages (StudentPackage entity)
    getAllSelections: (params = {}) => {
        const queryString = new URLSearchParams(params).toString();
        return api.get(`/student_packages${queryString ? `?${queryString}` : ''}`);
        // If the endpoint is different, it needs to be adjusted here later.
    },

    // Approve selected package
    approveSelection: (id) => api.post(`/student_packages/${id}/approve`),

    // Reject selected package
    rejectSelection: (id, reason) => api.post(`/student_packages/${id}/reject`, { reason }),
};

export default packageService;
