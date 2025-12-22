import api from './api';

export const paymentService = {
    // Get all payments
    getAll: (params = {}) => {
        const queryString = new URLSearchParams(params).toString();
        return api.get(`/payments${queryString ? `?${queryString}` : ''}`);
    },

    // Get single payment
    getById: (id) => api.get(`/payments/${id}`),

    // Process new payment
    create: (data) => api.post('/payments', data),

    // Get payments by student
    getByStudent: (studentId) => api.get(`/payments?student=${studentId}`),

    // Get pending quotas for student
    // Get pending quotas for student
    getPendingQuotas: (studentId) => api.get(`/payments/pending/${studentId}`),

    // Apply payment to quotas
    applyToQuotas: (paymentData) => api.post('/payments/apply', paymentData),

    // Get daily totals
    getDailyTotals: (date) => api.get(`/payments/totals?date=${date}`),

    // Get overdue payments (Insolventes)
    getOverduePayments: () => api.get('/payments/overdue'),
};

export default paymentService;
