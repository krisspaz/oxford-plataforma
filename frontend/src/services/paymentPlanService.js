import { api } from './api';

export const paymentPlanService = {
    // Get payment plans by student
    getByStudent: (studentId) => api.get(`/payment-plans/student/${studentId}`),

    // Get pending quotas for a student
    getPendingQuotas: (studentId) => api.get(`/payment-plans/student/${studentId}/pending`),

    // Create new payment plan
    create: (data) => api.post('/payment-plans', data),

    // Pay a quota
    payQuota: (quotaId, amount) =>
        api.post(`/payment-plans/quotas/${quotaId}/pay`, { amount }),
};
export default paymentPlanService;
