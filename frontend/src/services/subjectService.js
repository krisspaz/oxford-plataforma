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
        return api.get('/subjects', { params: filters });
    },

    /**
     * Get subject by ID
     * @param {number} id - Subject ID
     */
    getById: async (id) => {
        return api.get(`/subjects/${id}`);
    },

    /**
     * Create a new subject
     * @param {Object} data - Subject data { name, code, description }
     */
    create: async (data) => {
        return api.post('/subjects', data);
    },

    /**
     * Update subject
     * @param {number} id - Subject ID
     * @param {Object} data - Updated data
     */
    update: async (id, data) => {
        return api.put(`/subjects/${id}`, data);
    },

    /**
     * Delete subject
     * @param {number} id - Subject ID
     */
    delete: async (id) => {
        return api.delete(`/subjects/${id}`);
    },

    /**
     * Assign subject to teacher/grade/section
     * @param {Object} data - Assignment data { subjectId, teacherId, gradeId, sectionId?, cycleId, hoursPerWeek? }
     */
    assign: async (data) => {
        return api.post('/subjects/assign', data);
    },
};

export default subjectService;

