import api from './api';

const financialService = {
    // Get daily cut report
    getDailyCut: async (from, to) => {
        try {
            const data = await api.get(`/financial/daily-cut?from=${from}&to=${to}`);
            return { success: true, ...data };
        } catch (error) {
            console.error('Error fetching daily cut:', error);
            return { success: false, error: true };
        }
    },

    // Assign package to student
    assignPackage: async (data) => {
        try {
            const response = await api.post('/financial/assign-package', data);
            return { success: true, data: response.data };
        } catch (error) {
            console.error('Error assigning package:', error);
            return { success: false, message: error.response?.data?.error || error.message };
        }
    }
};

export default financialService;
