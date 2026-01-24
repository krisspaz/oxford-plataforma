import React, { useState, useEffect } from 'react';
import { financialEnterpriseService } from '../services/financialEnterpriseService';
import { useTheme } from '../../../contexts/ThemeContext';
import { FileText, Search, XCircle, Download } from 'lucide-react';
import { toast } from 'sonner';

const InvoiceAuditor = () => {
    const { darkMode } = useTheme();
    const [invoices, setInvoices] = useState([]);

    useEffect(() => {
        financialEnterpriseService.getInvoices().then(setInvoices);
    }, []);

    const handleAnnul = async (invoice) => {
        const reason = window.prompt(`Anular documento ${invoice.series}-${invoice.number}. Ingrese motivo:`);
        if (!reason) return;

        try {
            await financialEnterpriseService.annulInvoice(invoice.id, reason);
            toast.success('Documento anulado correctamente (Simulado)');
            // Update local state mock
            setInvoices(prev => prev.map(inv => inv.id === invoice.id ? { ...inv, status: 'ANULADO' } : inv));
        } catch (error) {
            toast.error('Error al anular');
        }
    };

    return (
        <div className={`p-6 rounded-2xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h3 className={`text-xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                        <FileText className="text-orange-500" />
                        Auditoría de Facturación
                    </h3>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        Historial de documentos SAT y anulaciones.
                    </p>
                </div>
            </div>

            <div className="overflow-x-auto">
                <table className="w-full">
                    <thead className={`border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                        <tr>
                            <th className="text-left py-3 px-4 text-sm font-semibold text-gray-500">Documento</th>
                            <th className="text-left py-3 px-4 text-sm font-semibold text-gray-500">Cliente</th>
                            <th className="text-left py-3 px-4 text-sm font-semibold text-gray-500">Fecha</th>
                            <th className="text-right py-3 px-4 text-sm font-semibold text-gray-500">Total</th>
                            <th className="text-center py-3 px-4 text-sm font-semibold text-gray-500">Estado</th>
                            <th className="py-3 px-4"></th>
                        </tr>
                    </thead>
                    <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                        {invoices.map((inv) => (
                            <tr key={inv.id} className="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td className={`py-3 px-4 font-mono text-sm ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                    {inv.series}-{inv.number}
                                </td>
                                <td className={`py-3 px-4 ${darkMode ? 'text-white' : 'text-gray-900'}`}>{inv.recipient}</td>
                                <td className="py-3 px-4 text-gray-500 text-sm">{inv.date}</td>
                                <td className="py-3 px-4 text-right font-bold">Q{inv.total.toFixed(2)}</td>
                                <td className="py-3 px-4 text-center">
                                    <span className={`px-2 py-1 rounded text-xs font-bold ${inv.status === 'EMITIDO' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                                        }`}>
                                        {inv.status}
                                    </span>
                                </td>
                                <td className="py-3 px-4 flex justify-end gap-2">
                                    <button className="p-1 text-gray-400 hover:text-blue-500" title="Descargar PDF">
                                        <Download size={18} />
                                    </button>
                                    {inv.status === 'EMITIDO' && (
                                        <button
                                            onClick={() => handleAnnul(inv)}
                                            className="p-1 text-gray-400 hover:text-red-500"
                                            title="Anular Documento"
                                        >
                                            <XCircle size={18} />
                                        </button>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default InvoiceAuditor;
