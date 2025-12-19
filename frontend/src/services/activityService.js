import api from './api';

export const activityService = {
    /**
     * Get all school activities
     * @param {Object} filters - Optional filters (audience, limit)
     */
    getAll: async (filters = {}) => {
        const queryParams = new URLSearchParams();
        if (filters.audience) queryParams.append('audience', filters.audience);
        if (filters.limit) queryParams.append('limit', filters.limit);

        return await api.get(`/school-activities?${queryParams.toString()}`);
    },

    /**
     * Create a new activity (Admin only)
     */
    create: async (data) => {
        return await api.post('/school-activities', data);
    }
};

export default activityService;
