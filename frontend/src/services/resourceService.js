import api from './api';

/**
 * Resource Service - API calls for content/resource management
 */
const resourceService = {
    /**
     * Get all resources for a teacher
     * @param {number} teacherId - Teacher ID
     */
    getByTeacher: async (teacherId) => {
        const response = await api.get(`/resources/teacher/${teacherId}`);
        return response.data || [];
    },

    /**
     * Get all resources (admin view)
     */
    getAll: async () => {
        const response = await api.get('/resources');
        return response.data || [];
    },

    /**
     * Create a new resource
     * @param {Object} data - { title, description?, link?, subjectId, teacherId }
     */
    create: async (data) => {
        return api.post('/resources', data);
    },

    /**
     * Delete a resource
     * @param {number} id - Resource ID
     */
    delete: async (id) => {
        return api.delete(`/resources/${id}`);
    },
};

export default resourceService;
