import api from './api';

const scholarshipService = {
    getAll: async () => {
        try {
            const response = await api.get('/scholarships');
            return { success: true, data: response.data || response };
        } catch (error) {
            console.error('Error fetching scholarships:', error);
            return { success: false, message: error.message };
        }
    },

    getById: async (id) => {
        try {
            const response = await api.get(`/scholarships/${id}`);
            return { success: true, data: response.data || response };
        } catch (error) {
            console.error('Error fetching scholarship:', error);
            return { success: false, message: error.message };
        }
    },

    create: async (data) => {
        try {
            const response = await api.post('/scholarships', data);
            return { success: true, data: response.data || response };
        } catch (error) {
            console.error('Error creating scholarship:', error);
            return { success: false, message: error.message };
        }
    },

    update: async (id, data) => {
        try {
            const response = await api.put(`/scholarships/${id}`, data);
            return { success: true, data: response.data || response };
        } catch (error) {
            console.error('Error updating scholarship:', error);
            return { success: false, message: error.message };
        }
    },

    delete: async (id) => {
        try {
            const response = await api.delete(`/scholarships/${id}`);
            return { success: true, data: response.data || response };
        } catch (error) {
            console.error('Error deleting scholarship:', error);
            return { success: false, message: error.message };
        }
    },

    assignToStudent: async (studentId, scholarshipId) => {
        try {
            const response = await api.post(`/scholarships/assign/${studentId}`, { scholarshipId });
            return { success: true, data: response.data || response };
        } catch (error) {
            console.error('Error assigning scholarship:', error);
            return { success: false, message: error.message };
        }
    }
};

export default scholarshipService;
