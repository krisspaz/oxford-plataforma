import api from './api';

/**
 * Invoice Service
 * PERFORMANCE: Uses dynamic imports for jsPDF to avoid loading in initial bundle
 */

// Cached module reference
let jsPDFModule = null;

/**
 * Lazy-load jsPDF
 */
const loadJsPDF = async () => {
    if (!jsPDFModule) {
        const jspdf = await import('jspdf');
        jsPDFModule = jspdf.default;
    }
    return jsPDFModule;
};

const invoiceService = {
    getAll: async (params = {}) => {
        try {
            const queryString = new URLSearchParams(params).toString();
            const invoices = await api.get(`/invoices${queryString ? '?' + queryString : ''}`);
            return { success: true, data: invoices.data || invoices['hydra:member'] || invoices };
        } catch (error) {
            console.error('Error fetching invoices:', error);
            return { success: false, message: error.message };
        }
    },

    getDailyCut: async (from, to) => {
        try {
            const data = await api.get(`/financial/daily-cut?from=${from}&to=${to}`);
            return { success: true, ...data };
        } catch (error) {
            console.error('Error fetching daily cut:', error);
            return { success: false, error: true };
        }
    },

    create: async (invoiceData) => {
        try {
            const res = await api.post('/invoices', invoiceData);
            return { success: true, data: res };
        } catch (error) {
            return { success: false, message: error.message };
        }
    },

    annul: async (id, reason) => {
        try {
            const res = await api.post(`/invoices/${id}/annul`, { reason });
            return { success: true, data: res };
        } catch (error) {
            return { success: false, message: error.message };
        }
    },

    downloadPdf: async (id, invoiceData = null) => {
        try {
            // If invoice data is provided, use it directly; otherwise fetch from API
            let invoice = invoiceData;

            if (!invoice) {
                const response = await api.get(`/invoices/${id}/download`);
                if (response.success && response.data) {
                    invoice = response.data;
                } else {
                    // Use fallback data if API fails
                    invoice = {
                        id,
                        series: 'A',
                        number: String(id).padStart(3, '0'),
                        name: 'Cliente',
                        nit: 'CF',
                        total: 0,
                        type: 'RECIBO',
                        issuedAt: new Date().toISOString()
                    };
                }
            }

            // Lazy-load jsPDF only when generating PDF
            const jsPDF = await loadJsPDF();
            const doc = new jsPDF();

            // Header
            doc.setFontSize(18);
            doc.setTextColor(40, 40, 40);
            doc.text('CORPORACIÓN EDUCACIONAL OXFORD', 14, 22);

            doc.setFontSize(14);
            doc.setTextColor(100, 100, 100);
            doc.text(invoice.type === 'FACTURA_SAT' ? 'FACTURA ELECTRÓNICA' : 'COMPROBANTE DE PAGO', 14, 32);

            // Line separator
            doc.setDrawColor(200, 200, 200);
            doc.line(14, 38, 196, 38);

            // Document info
            doc.setFontSize(12);
            doc.setTextColor(0, 0, 0);
            doc.text(`Documento: ${invoice.series || 'A'}-${invoice.number || '001'}`, 14, 48);
            doc.text(`Fecha: ${invoice.issuedAt || new Date().toLocaleDateString()}`, 14, 56);

            if (invoice.uuid) {
                doc.setFontSize(8);
                doc.text(`UUID: ${invoice.uuid}`, 14, 64);
            }

            // Client info
            doc.setFontSize(12);
            doc.text('DATOS DEL CLIENTE:', 14, 78);
            doc.setFontSize(10);
            doc.text(`Nombre: ${invoice.name || 'Consumidor Final'}`, 20, 86);
            doc.text(`NIT: ${invoice.nit || 'CF'}`, 20, 94);

            // Amount
            doc.setFontSize(14);
            doc.setTextColor(25, 135, 84);
            doc.text(`Total: Q ${(invoice.total || 0).toLocaleString('es-GT', { minimumFractionDigits: 2 })}`, 14, 110);

            // Payment method
            doc.setFontSize(10);
            doc.setTextColor(100, 100, 100);
            doc.text(`Forma de Pago: ${invoice.paymentMethod || 'Efectivo'}`, 14, 120);

            // Status badge
            doc.setFontSize(10);
            if (invoice.status === 'EMITIDO') {
                doc.setTextColor(25, 135, 84);
                doc.text('Estado: EMITIDO', 14, 130);
            } else {
                doc.setTextColor(220, 53, 69);
                doc.text('Estado: ANULADO', 14, 130);
            }

            // Footer
            doc.setFontSize(8);
            doc.setTextColor(150, 150, 150);
            doc.text('Este documento es una copia digital del comprobante original.', 14, 280);

            doc.save(`comprobante_${invoice.series || 'A'}-${invoice.number || id}.pdf`);

            return { success: true };
        } catch (error) {
            console.error('Error downloading PDF:', error);
            return { success: false, message: error.message };
        }
    }
};

export default invoiceService;
