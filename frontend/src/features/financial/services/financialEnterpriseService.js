import { api } from '../../../services/api';

/**
 * Financial Enterprise Service
 * Connects frontend to advanced financial backend modules
 */
export const financialEnterpriseService = {
    // Payment Plans (Complex Installments)
    getPlans: async (studentId) => {
        try {
            const response = await api.get(`/payment-plans/student/${studentId}`);
            return response.data || [];
        } catch (error) {
            console.warn('Backend PaymentPlans offline, using mock.');
            return MOCK_PLANS;
        }
    },

    createPlan: async (planData) => {
        // planData: { studentId, totalAmount, numberOfQuotas, downPayment, startDate, notes }
        return api.post('/payment-plans', planData);
    },

    getInsolventStudents: async () => {
        try {
            const response = await api.get('/payment-plans/insolvents');
            return response.data || [];
        } catch (error) {
            return MOCK_INSOLVENTS;
        }
    },

    // Advanced Invoicing (SAT Integration)
    getInvoices: async (filters = {}) => {
        try {
            // filters: { type, status, dateFrom, dateTo }
            const response = await api.get('/invoices', { params: filters });
            return response.data || [];
        } catch (error) {
            return MOCK_INVOICES;
        }
    },

    annulInvoice: async (id, reason) => {
        return api.post(`/invoices/${id}/annul`, { reason });
    },

    // Banking Security (Anti-Fraud)
    getBankingAlerts: async () => {
        try {
            // This endpoint would come from BankingSecurityService
            const response = await api.get('/security/banking-alerts');
            return response.data || [];
        } catch (error) {
            return MOCK_ALERTS;
        }
    }
};

// --- MOCK DATA ---
const MOCK_PLANS = [
    { id: 1, totalAmount: 5000, paidAmount: 2000, pendingAmount: 3000, numberOfQuotas: 10, status: 'ACTIVE', createdAt: '2024-01-15' },
    { id: 2, totalAmount: 1200, paidAmount: 1200, pendingAmount: 0, numberOfQuotas: 3, status: 'COMPLETED', createdAt: '2023-11-20' },
];

const MOCK_INSOLVENTS = [
    { id: 101, studentName: 'Juan Pérez', amount: 450.00, dueDate: '2024-02-01', description: 'Cuota 2/10' },
    { id: 102, studentName: 'Maria Garcia', amount: 1200.00, dueDate: '2024-01-15', description: 'Matrícula 2024' },
];

const MOCK_INVOICES = [
    { id: 9001, series: 'FACE-63', number: '10293', date: '2024-02-14', total: 450.00, status: 'EMITIDO', recipient: 'Juan Pérez' },
    { id: 9002, series: 'FACE-63', number: '10294', date: '2024-02-14', total: 150.00, status: 'ANULADO', recipient: 'Error Prueba' },
    { id: 9003, series: 'FACE-63', number: '10295', date: '2024-02-15', total: 2800.00, status: 'EMITIDO', recipient: 'Colegio Example' },
];

const MOCK_ALERTS = [
    { id: 1, type: 'unusual_hours', severity: 'medium', description: 'Cobro registrado a las 3:00 AM', timestamp: '2024-02-14T03:00:00' },
    { id: 2, type: 'sensitive_action', severity: 'high', description: 'Intento de anulación masiva detectado', timestamp: '2024-02-12T14:30:00' },
];
