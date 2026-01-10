import api from './api';

/**
 * Grade Service - API calls for grade management
 */
const gradeService = {
    /**
     * Get all grades
     * @param {number} levelId - Optional academic level filter
     */
    getAll: async (levelId = null) => {
        const params = levelId ? { levelId } : {};
        const response = await api.get('/grades', { params });
        return response.data;
    },

    /**
     * Get grade by ID with sections
     * @param {number} id - Grade ID
     */
    getById: async (id) => {
        const response = await api.get(`/grades/${id}`);
        return response.data;
    },

    /**
     * Create a new grade
     * @param {Object} data - Grade data { name, code, levelId, sortOrder }
     */
    create: async (data) => {
        const response = await api.post('/grades', data);
        return response.data;
    },

    /**
     * Update grade
     * @param {number} id - Grade ID
     * @param {Object} data - Updated data
     */
    update: async (id, data) => {
        const response = await api.put(`/grades/${id}`, data);
        return response.data;
    },

    /**
     * Delete grade
     * @param {number} id - Grade ID
     */
    delete: async (id) => {
        const response = await api.delete(`/grades/${id}`);
        return response.data;
    },

    /**
     * Get sections for a grade
     * @param {number} gradeId - Grade ID
     */
    getSections: async (gradeId) => {
        const response = await api.get(`/sections`, { params: { 'grade': gradeId } });
        // Return hydra:member or member or data
        const res = response.data;
        if (res['hydra:member']) return res['hydra:member'];
        if (res.member) return res.member;
        return res;
    },

    /**
     * Create section for a grade
     * @param {number} gradeId - Grade ID
     * @param {Object} data - Section data { name, code, capacity }
     */
    createSection: async (gradeId, data) => {
        // Construct payload with IRI for API Platform
        const payload = {
            ...data,
            grade: `/api/grades/${gradeId}`,
            isActive: true
        };
        const response = await api.post(`/sections`, payload, {
            headers: { 'Content-Type': 'application/ld+json' }
        });
        return response.data;
    },

    /**
     * Get students in a grade
     * @param {number} gradeId - Grade ID
     * @param {number} sectionId - Optional section filter
     */
    getStudents: async (gradeId, sectionId = null) => {
        const params = sectionId ? { sectionId } : {};
        const response = await api.get(`/grades/${gradeId}/students`, { params });
        return response.data;
    },
};

export default gradeService;
