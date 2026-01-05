import api from './api';

const eventService = {
    getAll: () => api.get('/events'),

    // Admin only
    create: (data) => api.post('/events', data),

    // Helper to initialize dummy data
    init: () => api.post('/events/init', {}),
};

export default eventService;
