import api from './api';

const jobTitleService = {
    getAll: async () => {
        const response = await api.get('/job_titles');
        return response.member || response['hydra:member'] || response;
    },

    create: async (data) => {
        const response = await api.post('/job_titles', data);
        return response.data || response;
    },

    update: async (id, data) => {
        const response = await api.put(`/job_titles/${id}`, data);
        return response.data || response;
    },

    delete: async (id) => {
        await api.delete(`/job_titles/${id}`);
        return { success: true };
    }
};

export default jobTitleService;
