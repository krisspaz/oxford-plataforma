import api from './api';

/**
 * Teacher Service - API calls for teacher management
 */
const teacherService = {
    /**
     * Get all teachers
     * @param {Object} filters - Filter options { active, search }
     */
    getAll: async (filters = {}) => {
        const response = await api.get('/teachers', { params: filters });
        return response.data;
    },

    /**
     * Get teacher by ID
     * @param {number} id - Teacher ID
     */
    getById: async (id) => {
        const response = await api.get(`/teachers/${id}`);
        return response.data;
    },

    /**
     * Get current logged-in teacher's profile
     */
    getMyProfile: async () => {
        const response = await api.get('/teachers/me');
        return response.data;
    },

    /**
     * Create a new teacher
     * @param {Object} data - Teacher data
     */
    create: async (data) => {
        const response = await api.post('/teachers', data);
        return response.data;
    },

    /**
     * Update teacher
     * @param {number} id - Teacher ID
     * @param {Object} data - Updated data
     */
    update: async (id, data) => {
        const response = await api.put(`/teachers/${id}`, data);
        return response.data;
    },

    /**
     * Delete teacher
     * @param {number} id - Teacher ID
     */
    delete: async (id) => {
        const response = await api.delete(`/teachers/${id}`);
        return response.data;
    },

    /**
     * Get subjects assigned to a teacher
     * @param {number} teacherId - Teacher ID
     */
    getSubjects: async (teacherId) => {
        const response = await api.get(`/teachers/${teacherId}/subjects`);
        return response.data;
    },

    /**
     * Get students for a teacher
     * @param {number} teacherId - Teacher ID
     */
    getStudents: async (teacherId) => {
        const response = await api.get(`/teachers/${teacherId}/students`);
        return response.data;
    },
    /**
     * Get chat history with a student
     * @param {number} studentId - Student ID
     */
    getChatHistory: async (studentId) => {
        const response = await api.get(`/teachers/chat/${studentId}`); // Adjusted endpoint logic
        return response.data;
    },

    /**
     * Send message to a student
     * @param {Object} data - { studentId, message }
     */
    sendMessage: async (data) => {
        const response = await api.post('/teachers/chat', data);
        return response.data;
    },
};

export default teacherService;
