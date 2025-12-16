import api from './api';

export const invoiceService = {
    // Get all invoices with filters
    getAll: (params = {}) => {
        const queryString = new URLSearchParams(params).toString();
        return api.get(`/invoices${queryString ? `?${queryString}` : ''}`);
    },

    // Get single invoice
    getById: (id) => api.get(`/invoices/${id}`),

    // Create new invoice/receipt
    create: (data) => api.post('/invoices', data),

    // Annul invoice
    annul: (id, reason) => api.post(`/invoices/${id}/annul`, { reason }),

    // Get daily cut report
    getCorteDia: (date) => api.get(`/invoices/corte-dia?date=${date}`),

    // Get invoices by date range
    getByDateRange: (from, to) => api.get(`/invoices?dateFrom=${from}&dateTo=${to}`),

    // Get invoices by type
    getByType: (type) => api.get(`/invoices?type=${type}`),

    // Download PDF
    downloadPdf: (id) => {
        const token = localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')).token : '';
        window.open(`/api/invoices/${id}/pdf?token=${token}`, '_blank');
    },
};

export default invoiceService;
