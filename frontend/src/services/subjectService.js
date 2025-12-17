import api from './api';

/**
 * Subject Service - API calls for subject management
 */
const subjectService = {
    /**
     * Get all subjects
     * @param {Object} filters - Filter options { active }
     */
    getAll: async (filters = {}) => {
        const response = await api.get('/subjects', { params: filters });
        return response.data;
    },

    /**
     * Get subject by ID
     * @param {number} id - Subject ID
     */
    getById: async (id) => {
        const response = await api.get(`/subjects/${id}`);
        return response.data;
    },

    /**
     * Create a new subject
     * @param {Object} data - Subject data { name, code, description }
     */
    create: async (data) => {
        const response = await api.post('/subjects', data);
        return response.data;
    },

    /**
     * Update subject
     * @param {number} id - Subject ID
     * @param {Object} data - Updated data
     */
    update: async (id, data) => {
        const response = await api.put(`/subjects/${id}`, data);
        return response.data;
    },

    /**
     * Delete subject
     * @param {number} id - Subject ID
     */
    delete: async (id) => {
        const response = await api.delete(`/subjects/${id}`);
        return response.data;
    },

    /**
     * Assign subject to teacher/grade/section
     * @param {Object} data - Assignment data { subjectId, teacherId, gradeId, sectionId?, cycleId, hoursPerWeek? }
     */
    assign: async (data) => {
        const response = await api.post('/subjects/assign', data);
        return response.data;
    },
};

export default subjectService;
