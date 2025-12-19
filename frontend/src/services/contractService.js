import api from './api';

const contractService = {
    getAll: async () => {
        try {
            const response = await api.get('/contracts'); // Uses API Platform default or custom list
            return { success: true, data: response.data['hydra:member'] || response.data };
        } catch (error) {
            console.error('Error fetching contracts:', error);
            return { success: false, message: error.message };
        }
    },

    generate: async (data) => {
        try {
            const response = await api.post('/contracts/generate', data);
            return { success: true, data: response.data };
        } catch (error) {
            console.error('Error generating contract:', error);
            return { success: false, message: error.message };
        }
    },

    // For parents to see their children's contracts
    getMyContracts: async () => {
        try {
            const response = await api.get('/contracts/my'); // Needs endpoint or filter
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error };
        }
    }
};

export default contractService;
