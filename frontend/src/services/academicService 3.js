import api from './api';

const academicService = {
    // Assign multiple subjects to a grade
    assignSubjects: (data) => api.post('/academic/assign-subjects', data),

    // Get schedule preview
    getSchedulePreview: (gradeId) => api.get(`/academic/schedule-preview/${gradeId}`),
};

export default academicService;
