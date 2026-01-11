import api from './api';

export const studentService = {
    // Get all students
    getAll: (params = {}) => {
        const queryString = new URLSearchParams(params).toString();
        return api.get(`/students${queryString ? `?${queryString}` : ''}`);
    },

    // Get single student
    getById: (id) => api.get(`/students/${id}`),

    // Search students
    search: (query) => api.get(`/students?search=${encodeURIComponent(query)}`),

    // Create new student
    create: (data) => api.post('/students', data),

    // Update student
    update: (id, data) => api.put(`/students/${id}`, data),

    // Get student account status
    getAccountStatus: (id) => api.get(`/students/${id}/account`),

    // Get student grades
    getGrades: (id, bimesterId) =>
        api.get(`/students/${id}/grades${bimesterId ? `?bimester=${bimesterId}` : ''}`),

    // --- Real Methods for New Features ---

    // Get user's teachers (using student ID)
    getMyTeachers: (studentId) => api.get(`/student/teachers/${studentId}`),

    // Get Chat History
    getChatHistory: (studentId, teacherId) => api.get(`/student/chat/${studentId}/${teacherId}`),

    // Send Message
    sendMessage: (data) => api.post('/student/chat', data),

    // Submit teacher evaluation
    submitTeacherRating: (data) => api.post('/student/rating', data),

    // Submit complaint/suggestion
    submitFeedback: (data) => api.post('/student/help-ticket', data),

    // Assign agreement/scholarship
    assignAgreement: (studentId, agreementId) =>
        api.post(`/students/${studentId}/agreements`, { agreementId }),
};

export default studentService;
