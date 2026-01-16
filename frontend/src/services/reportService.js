import api from './api';

const reportService = {
    generate: async (params) => {
        try {
            const data = await api.post('/reports/generate', params);
            return { success: true, data };
        } catch (error) {
            console.error('Error generating report:', error);
            return { success: false, message: error.message };
        }
    },

    getGrades: async (studentId) => {
        try {
            const data = await api.get(`/reports/grades/${studentId}`);
            return { success: true, data };
        } catch (error) {
            return { success: false, error };
        }
    }
};

export default reportService;

