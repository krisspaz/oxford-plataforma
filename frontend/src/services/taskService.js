import api from './api';

const taskService = {
    // Get all tasks (Admin filters)
    getAll: (filters) => api.get('/tasks', { params: filters }),

    // Get my tasks (Student: assigned, Teacher: created)
    getMyTasks: () => api.get('/tasks/my-tasks'),

    // Get specific task
    getById: (id) => api.get(`/tasks/${id}`),

    // Create task (Teacher)
    create: (data) => api.post('/tasks', data),

    // Update task
    update: (id, data) => api.put(`/tasks/${id}`, data),

    // Delete task
    delete: (id) => api.delete(`/tasks/${id}`),

    // Get submissions for a task (Teacher view)
    getSubmissions: (id) => api.get(`/tasks/${id}/submissions`),

    // Submit a task (Student)
    submit: (id, data) => api.post(`/tasks/${id}/submit`, data),

    // Grade a submission (Teacher)
    gradeSubmission: (submissionId, data) => api.post(`/tasks/submissions/${submissionId}/grade`, data),
};

export default taskService;
