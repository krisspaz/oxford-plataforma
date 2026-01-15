import api from './api';

const helpTicketService = {
    getAll: async (params = {}) => {
        const queryString = new URLSearchParams(params).toString();
        const response = await api.get(`/help_tickets?${queryString}`);
        return response.member || response['hydra:member'] || response;
    },

    create: async (data) => {
        const response = await api.post('/help_tickets', data);
        return response;
    },

    update: async (id, data) => {
        const response = await api.put(`/help_tickets/${id}`, data);
        return response;
    },

    getMyTickets: async (studentId) => {
        // Assuming api platform filtering by student
        const response = await api.get(`/help_tickets?student=${studentId}`);
        return response.member || response['hydra:member'] || response;
    }
};

export default helpTicketService;
