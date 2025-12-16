import React, { useState, useEffect } from 'react';
import { FileText, Search, Download, X, Eye, AlertTriangle, RefreshCw } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { invoiceService } from '../services';

const ComprobantesPage = () => {
    const { darkMode } = useTheme();
    const [searchTerm, setSearchTerm] = useState('');
    const [filterType, setFilterType] = useState('');
    const [filterStatus, setFilterStatus] = useState('');
    const [showAnnulModal, setShowAnnulModal] = useState(false);
    const [selectedInvoice, setSelectedInvoice] = useState(null);
    const [loading, setLoading] = useState(true);
    const [actionLoading, setActionLoading] = useState(null);
    const [invoices, setInvoices] = useState([]);
    const [annulReason, setAnnulReason] = useState('');

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;

    useEffect(() => {
        loadInvoices();
    }, [filterType, filterStatus]);

    const loadInvoices = async () => {
        setLoading(true);
        try {
            const params = {};
            if (filterType) params.type = filterType;
            if (filterStatus) params.status = filterStatus;

            const response = await invoiceService.getAll(params);
            if (response.success) {
                setInvoices(response.data);
            }
        } catch (error) {
            console.error('Error loading invoices:', error);
            // Demo data
            setInvoices([
                { id: 1, type: 'FACTURA_SAT', series: 'A', number: '001', uuid: 'ABC123', name: 'Juan Pérez', nit: '12345678-9', total: 3500, status: 'EMITIDO', issuedAt: '2025-01-16 10:30' },
                { id: 2, type: 'RECIBO_SAT', series: 'B', number: '045', uuid: 'DEF456', name: 'María López', nit: 'CF', total: 750, status: 'EMITIDO', issuedAt: '2025-01-16 09:15' },
                { id: 3, type: 'FACTURA_SAT', series: 'A', number: '002', uuid: 'GHI789', name: 'Carlos García', nit: '98765432-1', total: 1000, status: 'ANULADO', issuedAt: '2025-01-15 16:00', annulledAt: '2025-01-15 17:00', annulmentReason: 'Error en datos del cliente' },
            ]);
        } finally {
            setLoading(false);
        }
    };

    const handleRequestAnnul = async () => {
        if (!annulReason.trim()) {
            alert('Debe ingresar un motivo para la anulación');
            return;
        }

        setActionLoading(selectedInvoice.id);
        try {
            const response = await invoiceService.annul(selectedInvoice.id, annulReason);
            if (response.success) {
                setInvoices(invoices.map(inv =>
                    inv.id === selectedInvoice.id
                        ? { ...inv, status: 'ANULADO', annulmentReason: annulReason }
                        : inv
                ));
            }
        } catch (error) {
            console.error('Error annulling invoice:', error);
            // Demo mode
            setInvoices(invoices.map(inv =>
                inv.id === selectedInvoice.id
                    ? { ...inv, status: 'ANULADO', annulmentReason: annulReason }
                    : inv
            ));
        } finally {
            setActionLoading(null);
            setShowAnnulModal(false);
            setAnnulReason('');
            setSelectedInvoice(null);
        }
    };

    const handleDownloadPdf = (invoice) => {
        invoiceService.downloadPdf(invoice.id);
    };

    const filteredInvoices = invoices.filter(inv => {
        if (!searchTerm) return true;
        const search = searchTerm.toLowerCase();
        return inv.name.toLowerCase().includes(search) ||
            inv.nit.includes(search) ||
            `${inv.series}-${inv.number}`.includes(search);
    });

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando comprobantes...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Comprobantes Emitidos</h1>
                <button onClick={loadInvoices} className="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
                    <RefreshCw size={18} /> Actualizar
                </button>
            </div>

            {/* Filters */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm flex gap-4 items-end`}>
                <div className="flex-1">
                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Buscar</label>
                    <div className="relative">
                        <Search className={`absolute left-3 top-2.5 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`} size={18} />
                        <input
                            type="text"
                            placeholder="Nombre, NIT o número..."
                            value={searchTerm}
                            onChange={e => setSearchTerm(e.target.value)}
                            className={`${inputClass} w-full pl-10`}
                        />
                    </div>
                </div>
                <div>
                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Tipo</label>
                    <select value={filterType} onChange={e => setFilterType(e.target.value)} className={inputClass}>
                        <option value="">Todos</option>
                        <option value="FACTURA_SAT">Factura SAT</option>
                        <option value="RECIBO_SAT">Recibo SAT</option>
                    </select>
                </div>
                <div>
                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Estado</label>
                    <select value={filterStatus} onChange={e => setFilterStatus(e.target.value)} className={inputClass}>
                        <option value="">Todos</option>
                        <option value="EMITIDO">Emitido</option>
                        <option value="ANULADO">Anulado</option>
                    </select>
                </div>
            </div>

            {/* Table */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead className={darkMode ? 'bg-gray-700' : 'bg-gray-50'}>
                            <tr>
                                <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Tipo</th>
                                <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Número</th>
                                <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Receptor</th>
                                <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>NIT</th>
                                <th className={`px-4 py-3 text-right font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Total</th>
                                <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Estado</th>
                                <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Fecha</th>
                                <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Acciones</th>
                            </tr>
                        </thead>
                        <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                            {filteredInvoices.map(invoice => (
                                <tr key={invoice.id} className={`${invoice.status === 'ANULADO' ? 'opacity-60' : ''} ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}`}>
                                    <td className="px-4 py-3">
                                        <span className={`px-2 py-1 rounded text-xs font-medium ${invoice.type === 'FACTURA_SAT' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'
                                            }`}>{invoice.type === 'FACTURA_SAT' ? 'FACTURA' : 'RECIBO'}</span>
                                    </td>
                                    <td className={`px-4 py-3 font-mono ${darkMode ? 'text-white' : 'text-gray-900'}`}>{invoice.series}-{invoice.number}</td>
                                    <td className={`px-4 py-3 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>{invoice.name}</td>
                                    <td className={`px-4 py-3 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{invoice.nit}</td>
                                    <td className={`px-4 py-3 text-right font-medium ${darkMode ? 'text-green-400' : 'text-green-600'}`}>Q {invoice.total?.toLocaleString()}</td>
                                    <td className="px-4 py-3 text-center">
                                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${invoice.status === 'EMITIDO' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                                            }`}>{invoice.status}</span>
                                    </td>
                                    <td className={`px-4 py-3 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{invoice.issuedAt}</td>
                                    <td className="px-4 py-3">
                                        <div className="flex justify-center gap-1">
                                            <button onClick={() => handleDownloadPdf(invoice)} className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`} title="Descargar PDF">
                                                <Download size={16} className="text-blue-500" />
                                            </button>
                                            {invoice.status === 'EMITIDO' && (
                                                <button
                                                    onClick={() => { setSelectedInvoice(invoice); setShowAnnulModal(true); }}
                                                    className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`}
                                                    title="Solicitar Anulación"
                                                >
                                                    <AlertTriangle size={16} className="text-red-500" />
                                                </button>
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Annul Modal */}
            {showAnnulModal && selectedInvoice && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-md p-6`}>
                        <div className="flex items-center justify-between mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Solicitar Anulación</h2>
                            <button onClick={() => setShowAnnulModal(false)}><X size={20} /></button>
                        </div>
                        <div className={`p-4 rounded-lg mb-4 ${darkMode ? 'bg-red-900/30' : 'bg-red-50'}`}>
                            <p className={`text-sm ${darkMode ? 'text-red-300' : 'text-red-700'}`}>
                                <strong>Documento:</strong> {selectedInvoice.series}-{selectedInvoice.number}
                            </p>
                            <p className={`text-sm ${darkMode ? 'text-red-300' : 'text-red-700'}`}>
                                <strong>Total:</strong> Q {selectedInvoice.total?.toLocaleString()}
                            </p>
                        </div>
                        <div>
                            <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Motivo de Anulación *</label>
                            <textarea
                                value={annulReason}
                                onChange={e => setAnnulReason(e.target.value)}
                                rows={3}
                                placeholder="Ingrese el motivo..."
                                className={`${inputClass} w-full`}
                            />
                        </div>
                        <div className="flex gap-3 mt-6">
                            <button onClick={() => setShowAnnulModal(false)} className={`flex-1 px-4 py-2 rounded-lg ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200 text-gray-700'}`}>
                                Cancelar
                            </button>
                            <button
                                onClick={handleRequestAnnul}
                                disabled={actionLoading === selectedInvoice.id}
                                className="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg disabled:opacity-50"
                            >
                                {actionLoading === selectedInvoice.id ? 'Procesando...' : 'Solicitar Anulación'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default ComprobantesPage;
