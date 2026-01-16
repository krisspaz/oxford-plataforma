import api from './api';

/**
 * Attendance Service - API calls for attendance management
 */
const attendanceService = {
    /**
     * Get attendance for a schedule/class on a specific date
     * @param {number} scheduleId - Schedule ID
     * @param {string} date - Date in YYYY-MM-DD format
     */
    getBySchedule: async (scheduleId, date = null) => {
        const params = date ? { date } : {};
        return api.get(`/attendance/by-schedule/${scheduleId}`, { params });
    },

    /**
     * Save attendance for multiple students at once
     * @param {number} scheduleId - Schedule ID
     * @param {string} date - Date in YYYY-MM-DD format
     * @param {Array} attendances - Array of { studentId, status, notes? }
     */
    saveBatch: async (scheduleId, date, attendances) => {
        return api.post('/attendance/batch', { scheduleId, date, attendances });
    },

    /**
     * Get attendance report for a student in a bimester
     * @param {number} studentId - Student ID
     * @param {number} bimesterId - Bimester ID
     */
    getStudentReport: async (studentId, bimesterId) => {
        return api.get(`/attendance/report/${studentId}/bimester/${bimesterId}`);
    },

    /**
     * Get attendance report for all students of a teacher in a bimester
     * @param {number} teacherId - Teacher ID
     * @param {number} bimesterId - Bimester ID
     */
    getTeacherReport: async (teacherId, bimesterId) => {
        return api.get(`/attendance/teacher-report/${teacherId}/bimester/${bimesterId}`);
    },
};

export default attendanceService;

