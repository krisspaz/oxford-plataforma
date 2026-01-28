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
        return api.get('/teachers', { params: filters });
    },

    /**
     * Get teacher by ID
     * @param {number} id - Teacher ID
     */
    getById: async (id) => {
        return api.get(`/teachers/${id}`);
    },

    /**
     * Get current logged-in teacher's profile
     */
    getMyProfile: async () => {
        return api.get('/teachers/me');
    },

    /**
     * Create a new teacher
     * @param {Object} data - Teacher data
     */
    /**
     * Create a new teacher
     * @param {Object} data - Teacher data
     */
    create: async (data) => {
        return api.post('/teachers', data, {
            headers: { 'Content-Type': 'application/ld+json' }
        });
    },

    /**
     * Update teacher
     * @param {number} id - Teacher ID
     * @param {Object} data - Updated data
     */
    update: async (id, data) => {
        return api.patch(`/teachers/${id}`, data, {
            headers: { 'Content-Type': 'application/merge-patch+json' }
        });
    },

    /**
     * Delete teacher
     * @param {number} id - Teacher ID
     */
    delete: async (id) => {
        return api.delete(`/teachers/${id}`);
    },

    /**
     * Get subjects assigned to a teacher
     * @param {number} teacherId - Teacher ID
     */
    getSubjects: async (teacherId) => {
        return api.get(`/teachers/${teacherId}/subjects`);
    },

    /**
     * Get subjects assigned to the current logged-in teacher
     */
    getMyAssignments: async () => {
        return api.get('/teachers/me/assignments');
    },

    /**
     * Get students for a teacher
     * @param {number} teacherId - Teacher ID
     */
    getStudents: async (teacherId) => {
        return api.get(`/teachers/${teacherId}/students`);
    },

    /**
     * Get chat history with a student
     * @param {number} studentId - Student ID
     */
    getChatHistory: async (studentId) => {
        return api.get(`/teachers/chat/${studentId}`);
    },

    /**
     * Send message to a student
     * @param {Object} data - { studentId, message }
     */
    sendMessage: async (data) => {
        return api.post('/teachers/chat', data);
    },
};

export default teacherService;

