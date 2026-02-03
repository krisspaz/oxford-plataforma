import api from './api';

const academicService = {
    // Assign multiple subjects to a grade
    assignSubjects: (data) => api.post('/academic/assign-subjects', data),

    // Get schedule preview
    getSchedulePreview: (gradeId) => api.get(`/academic/schedule-preview/${gradeId}`),

    // Get current assignments for a grade
    getAssignments: (gradeId) => api.get(`/academic/assignments/${gradeId}`),
};

export default academicService;
