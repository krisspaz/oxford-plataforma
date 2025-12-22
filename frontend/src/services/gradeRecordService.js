import api from './api';

export const gradeRecordService = {
    // Get all grade records with filters
    getAll: (params = {}) => {
        const queryString = new URLSearchParams(params).toString();
        return api.get(`/grade-records${queryString ? `?${queryString}` : ''}`);
    },

    // Get by assignment and bimester
    getByAssignmentAndBimester: (assignmentId, bimesterId) =>
        api.get(`/grade-records/by-assignment/${assignmentId}/bimester/${bimesterId}`),

    // Save batch of grades
    saveBatch: (records, bimesterId) =>
        api.post('/grade-records/batch', { records, bimesterId }),

    // Unlock a grade record (coordinación only)
    unlock: (id) => api.post(`/grade-records/${id}/unlock`),

    // Get student grades
    getByStudent: (studentId) => api.get(`/grade-records?student=${studentId}`),

    // Get teacher summary (final grades)
    getTeacherSummary: (assignmentId) => api.get(`/teacher/summary?assignmentId=${assignmentId}`),
};

export default gradeRecordService;
