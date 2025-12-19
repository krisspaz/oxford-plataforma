import { api } from './api';

export const familyService = {
    // Get all families
    getAll: (search = '') =>
        api.get(`/families${search ? `?search=${encodeURIComponent(search)}` : ''}`),

    // Get single family
    getById: (id) => api.get(`/families/${id}`),

    // Create family
    create: (data) => api.post('/families', data),

    // Update family
    update: (id, data) => api.put(`/families/${id}`, data),

    // Get siblings of a student
    getSiblings: (familyId, studentId) =>
        api.get(`/families/${familyId}/siblings?studentId=${studentId}`),

    // Find family by student
    getByStudent: (studentId) => api.get(`/families/by-student/${studentId}`),
};
export default familyService;
