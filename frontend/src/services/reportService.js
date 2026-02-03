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
            // We use fetch directly here to handle the blob with the token
            const token = localStorage.getItem('token')?.replace(/^"(.*)"$/, '$1');
            const response = await fetch(`/api/reports/grades/${studentId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            if (!response.ok) throw new Error('Error al descargar reporte');
            return await response.blob();
        } catch (error) {
            console.error('Error in getGrades:', error);
            throw error;
        }
    }
};

export default reportService;

