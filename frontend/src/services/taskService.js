import api from './api';

/**
 * Task Service - API calls for task/assignment management
 */
const taskService = {
    /**
     * Get tasks with optional filters
     * @param {Object} filters - Filter options { teacherId, bimesterId, subjectId, gradeId, status }
     */
    getAll: async (filters = {}) => {
        const response = await api.get('/tasks', { params: filters });
        return response.data;
    },

    /**
     * Get tasks for the current student
     */
    getMyTasks: async () => {
        const response = await api.get('/tasks/my-tasks');
        return response.data;
    },

    /**
     * Get a single task by ID
     * @param {number} id - Task ID
     */
    getById: async (id) => {
        const response = await api.get(`/tasks/${id}`);
        return response.data;
    },

    /**
     * Create a new task
     * @param {Object} data - Task data
     * @param {string} data.title - Task title
     * @param {string} data.description - Task description
     * @param {string} data.type - Task type (tarea, examen, proyecto, actividad)
     * @param {string} data.dueDate - Due date (YYYY-MM-DD)
     * @param {number} data.points - Point value
     * @param {number} data.teacherId - Teacher ID
     * @param {number} data.subjectId - Subject ID
     * @param {number} data.bimesterId - Bimester ID
     * @param {number} data.cycleId - School cycle ID
     * @param {Array} data.grades - Array of { gradeId, sectionId? }
     */
    create: async (data) => {
        const response = await api.post('/tasks', data);
        return response.data;
    },

    /**
     * Update a task
     * @param {number} id - Task ID
     * @param {Object} data - Updated task data
     */
    update: async (id, data) => {
        const response = await api.put(`/tasks/${id}`, data);
        return response.data;
    },

    /**
     * Delete a task
     * @param {number} id - Task ID
     */
    delete: async (id) => {
        const response = await api.delete(`/tasks/${id}`);
        return response.data;
    },

    /**
     * Get submissions for a task
     * @param {number} taskId - Task ID
     */
    getSubmissions: async (taskId) => {
        const response = await api.get(`/tasks/${taskId}/submissions`);
        return response.data;
    },

    /**
     * Submit a task (for students)
     * @param {number} taskId - Task ID
     * @param {Object} data - Submission data { studentId, content?, attachmentUrl? }
     */
    submit: async (taskId, data) => {
        const response = await api.post(`/tasks/${taskId}/submit`, data);
        return response.data;
    },

    /**
     * Grade a submission
     * @param {number} submissionId - Submission ID
     * @param {Object} data - Grading data { score, feedback?, gradedById? }
     */
    gradeSubmission: async (submissionId, data) => {
        const response = await api.post(`/tasks/submissions/${submissionId}/grade`, data);
        return response.data;
    },

    /**
     * Get tasks by bimester
     * @param {number} bimesterId - Bimester ID
     * @param {number} teacherId - Optional teacher ID
     */
    getByBimester: async (bimesterId, teacherId = null) => {
        const filters = { bimesterId };
        if (teacherId) filters.teacherId = teacherId;
        return taskService.getAll(filters);
    },

    /**
     * Mark task as completed
     * @param {number} id - Task ID
     */
    markCompleted: async (id) => {
        return taskService.update(id, { status: 'completed' });
    },
};

export default taskService;
