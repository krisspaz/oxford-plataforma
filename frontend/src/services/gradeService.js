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
        return api.get('/grades', { params });
    },

    /**
     * Get grade by ID with sections
     * @param {number} id - Grade ID
     */
    getById: async (id) => {
        return api.get(`/grades/${id}`);
    },

    /**
     * Create a new grade
     * @param {Object} data - Grade data { name, code, levelId, sortOrder }
     */
    create: async (data) => {
        return api.post('/grades', data);
    },

    /**
     * Update grade
     * @param {number} id - Grade ID
     * @param {Object} data - Updated data
     */
    update: async (id, data) => {
        return api.put(`/grades/${id}`, data);
    },

    /**
     * Delete grade
     * @param {number} id - Grade ID
     */
    delete: async (id) => {
        return api.delete(`/grades/${id}`);
    },

    /**
     * Get sections for a grade
     * @param {number} gradeId - Grade ID
     */
    getSections: async (gradeId) => {
        const data = await api.get(`/grades/${gradeId}/sections`);
        // Handle API Platform or custom controller response
        if (data?.['hydra:member']) return data['hydra:member'];
        if (data?.member) return data.member;
        return Array.isArray(data) ? data : [];
    },

    /**
     * Create section for a grade
     * @param {number} gradeId - Grade ID
     * @param {Object} data - Section data { name, capacity }
     */
    createSection: async (gradeId, data) => {
        return api.post(`/grades/${gradeId}/sections`, data);
    },

    /**
     * Get students in a grade
     * @param {number} gradeId - Grade ID
     * @param {number} sectionId - Optional section filter
     */
    getStudents: async (gradeId, sectionId = null) => {
        const params = sectionId ? { sectionId } : {};
        return api.get(`/grades/${gradeId}/students`, { params });
    },
};

export default gradeService;

