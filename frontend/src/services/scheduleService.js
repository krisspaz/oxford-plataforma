import api from './api';

/**
 * Schedule Service - API calls for schedule/timetable management
 */
const scheduleService = {
    /**
     * Get the current user's schedule (for teachers)
     * @param {number} cycleId - Optional school cycle ID
     */
    getMySchedule: async (cycleId = null) => {
        const params = cycleId ? { cycleId } : {};
        const response = await api.get('/schedule/my-schedule', { params });
        return response.data;
    },

    /**
     * Get the current student's schedule
     */
    getMyStudentSchedule: async (cycleId = null) => {
        const params = cycleId ? { cycleId } : {};
        const response = await api.get('/schedule/my-student-schedule', { params });
        return response.data;
    },


    /**
     * Get schedule by teacher ID
     * @param {number} teacherId - Teacher ID
     * @param {number} cycleId - Optional school cycle ID
     */
    getByTeacher: async (teacherId, cycleId = null) => {
        const params = cycleId ? { cycleId } : {};
        const response = await api.get(`/schedule/teacher/${teacherId}`, { params });
        return response.data;
    },

    /**
     * Get schedule for a specific day
     * @param {number} teacherId - Teacher ID
     * @param {number} day - Day of week (1-5)
     * @param {number} cycleId - Optional school cycle ID
     */
    getByDay: async (teacherId, day, cycleId = null) => {
        const params = cycleId ? { cycleId } : {};
        const response = await api.get(`/schedule/teacher/${teacherId}/day/${day}`, { params });
        return response.data;
    },

    /**
     * Get current class for a teacher
     * @param {number} teacherId - Teacher ID
     */
    getCurrentClass: async (teacherId) => {
        const response = await api.get(`/schedule/current-class/${teacherId}`);
        return response.data;
    },

    /**
     * Get weekly schedule organized by day
     * @param {number} teacherId - Teacher ID
     * @param {number} cycleId - School cycle ID (required)
     */
    getWeeklySchedule: async (teacherId, cycleId) => {
        const response = await api.get(`/schedule/weekly/${teacherId}`, { params: { cycleId } });
        return response.data;
    },

    /**
     * Get schedule by grade/section
     * @param {number} gradeId - Grade ID
     * @param {number} sectionId - Optional section ID
     * @param {number} cycleId - Optional school cycle ID
     */
    getByGrade: async (gradeId, sectionId = null, cycleId = null) => {
        const params = {};
        if (sectionId) params.sectionId = sectionId;
        if (cycleId) params.cycleId = cycleId;
        const response = await api.get(`/schedule/grade/${gradeId}`, { params });
        return response.data;
    },

    /**
     * Create a new schedule entry
     * @param {Object} data - Schedule data
     */
    create: async (data) => {
        const response = await api.post('/schedule', data);
        return response.data;
    },

    /**
     * Delete a schedule entry
     * @param {number} id - Schedule ID
     */
    delete: async (id) => {
        const response = await api.delete(`/schedule/${id}`);
        return response.data;
    },

    /**
     * Trigger automatic schedule generation
     * @param {number} cycleId - School Cycle ID
     */
    generateAuto: async (cycleId) => {
        const response = await api.post('/schedule/generate', { cycleId });
        return response.data;
    }
};

export default scheduleService;
