import { api } from './api';

export const familyService = {
    // Get all families
    getAll: (search = '') =>
        api.get(`/families${search ? `?search=${encodeURIComponent(search)}` : ''}`),

    // Get single family
    getById: (id) => api.get(`/families/${id}`),

    // Create family
    create: (data) => api.post('/families', data, {
        headers: { 'Content-Type': 'application/ld+json' }
    }),

    // Update family
    update: (id, data) => api.patch(`/families/${id}`, data, {
        headers: { 'Content-Type': 'application/merge-patch+json' }
    }),

    // Get siblings of a student
    getSiblings: (familyId, studentId) =>
        api.get(`/families/${familyId}/siblings?studentId=${studentId}`),

    // Find family by student
    getByStudent: (studentId) => api.get(`/families/by-student/${studentId}`),

    // Get my children (for parents)
    getMyChildren: () => api.get('/families/my-children'),
};
export default familyService;
