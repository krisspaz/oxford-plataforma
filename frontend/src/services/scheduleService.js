import api from './api';

const scheduleService = {
    // Generate schedules (Admin)
    generate: (cycleId) => api.post('/schedule/generate', { cycleId }),

    // Get my schedule (Teacher)
    getMySchedule: (cycleId) => api.get('/schedule/my-schedule', { params: { cycleId } }),

    // Get my student schedule (Student/Parent)
    getMyStudentSchedule: (cycleId) => api.get('/schedule/my-student-schedule', { params: { cycleId } }),

    // Get schedule by teacher (Admin/Coordinator)
    getByTeacher: (teacherId, cycleId) => api.get(`/schedule/teacher/${teacherId}`, { params: { cycleId } }),

    // Get schedule by course/grade (Admin/Coordinator)
    getByCourse: (courseId, cycleId) => api.get(`/schedule/course/${courseId}`, { params: { cycleId } }),

    // CRUD
    create: (data) => api.post('/schedule', data),
    update: (id, data) => api.put(`/schedule/${id}`, data),
    delete: (id) => api.delete(`/schedule/${id}`),
};

export default scheduleService;
