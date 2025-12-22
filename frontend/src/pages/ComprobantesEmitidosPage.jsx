import React, { useState, useEffect } from 'react';
import { FileText, Download, Search, RefreshCw, Filter, Calendar } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { invoiceService } from '../services';
import { usePdfExport } from '../hooks/usePdfExport';

const ComprobantesEmitidosPage = () => {
    const { darkMode } = useTheme();
    const { exportTable, createDoc } = usePdfExport();
    const [invoices, setInvoices] = useState([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const [filterStatus, setFilterStatus] = useState('');
    const [dateFrom, setDateFrom] = useState('');
    const [dateTo, setDateTo] = useState('');

    useEffect(() => {
        loadInvoices();
    }, []);

    const loadInvoices = async () => {
        setLoading(true);
        try {
            const response = await invoiceService.getAll();
            if (response.success && Array.isArray(response.data)) {
                setInvoices(response.data);
            } else {
                throw new Error("Failed to load");
            }
        } catch (error) {
            console.error("Error loading invoices:", error);
            // Mock Data Fallback
            setInvoices([
                { id: 1, series: 'A', number: '001', student: 'Juan Perez García', nit: '12345678-9', date: '2025-01-15', amount: 1450.00, status: 'EMITIDO', paymentMethod: 'Efectivo' },
                { id: 2, series: 'A', number: '002', student: 'Maria López Fernández', nit: 'CF', date: '2025-01-15', amount: 850.00, status: 'EMITIDO', paymentMethod: 'Tarjeta' },
                { id: 3, series: 'A', number: '003', student: 'Carlos Ruiz Martínez', nit: '98765432-1', date: '2025-01-16', amount: 2200.00, status: 'ANULADO', paymentMethod: 'Depósito' },
                { id: 4, series: 'A', number: '004', student: 'Ana García López', nit: 'CF', date: '2025-01-16', amount: 750.00, status: 'EMITIDO', paymentMethod: 'Efectivo' },
                { id: 5, series: 'A', number: '005', student: 'Pedro Hernández', nit: '45678912-3', date: '2025-01-17', amount: 1850.00, status: 'EMITIDO', paymentMethod: 'Tarjeta' },
            ]);
        } finally {
            setLoading(false);
        }
    };

    const handleDownloadInvoice = (invoice) => {
        const doc = createDoc('COMPROBANTE DE PAGO', `Documento ${invoice.series}-${invoice.number}`);

        doc.setFontSize(12);
        doc.setTextColor(60, 60, 60);

        // Invoice details
        doc.text(`Documento: ${invoice.series}-${invoice.number}`, 14, 55);
        doc.text(`Fecha de Emisión: ${invoice.date}`, 14, 63);
        doc.text(`Estado: ${invoice.status === 'EMITIDO' ? 'Emitido' : 'Anulado'}`, 14, 71);

        // Separator
        doc.setDrawColor(200, 200, 200);
        doc.line(14, 78, 196, 78);

        // Client info
        doc.setFontSize(11);
        doc.text('DATOS DEL CLIENTE', 14, 88);
        doc.setFontSize(10);
        doc.text(`Nombre: ${invoice.student || invoice.name}`, 20, 96);
        doc.text(`NIT: ${invoice.nit}`, 20, 104);

        // Payment info
        doc.setFontSize(11);
        doc.text('DETALLE DEL PAGO', 14, 118);
        doc.setFontSize(10);
        doc.text(`Forma de Pago: ${invoice.paymentMethod}`, 20, 126);

        // Total
        doc.setFontSize(14);
        doc.setTextColor(25, 135, 84);
        doc.text(`TOTAL: Q ${invoice.amount.toLocaleString('es-GT', { minimumFractionDigits: 2 })}`, 14, 145);

        // Footer
        doc.setFontSize(8);
        doc.setTextColor(150, 150, 150);
        doc.text('Este documento es una representación digital del comprobante oficial.', 14, 280);
        doc.text('CORPORACIÓN EDUCACIONAL OXFORD - Ciudad de Guatemala', 14, 286);

        doc.save(`comprobante_${invoice.series}-${invoice.number}.pdf`);
    };

    const handleExportAll = () => {
        const columns = ['Documento', 'Estudiante', 'NIT', 'Fecha', 'Monto', 'Estado', 'Método'];
        const data = filteredInvoices.map(inv => [
            `${inv.series}-${inv.number}`,
            inv.student || inv.name,
            inv.nit,
            inv.date,
            `Q ${inv.amount.toLocaleString('es-GT', { minimumFractionDigits: 2 })}`,
            inv.status === 'EMITIDO' ? 'Emitido' : 'Anulado',
            inv.paymentMethod
        ]);

        exportTable({
            title: 'Comprobantes Emitidos',
            subtitle: `Generado: ${new Date().toLocaleDateString()}`,
            columns,
            data,
            filename: 'comprobantes_emitidos.pdf'
        });
    };

    const filteredInvoices = invoices.filter(inv => {
        const matchesSearch = !searchTerm ||
            (inv.student || inv.name || '').toLowerCase().includes(searchTerm.toLowerCase()) ||
            inv.nit.includes(searchTerm) ||
            `${inv.series}-${inv.number}`.includes(searchTerm);
        const matchesStatus = !filterStatus || inv.status === filterStatus;
        return matchesSearch && matchesStatus;
    });

    const summaryStats = {
        total: filteredInvoices.length,
        emitidos: filteredInvoices.filter(i => i.status === 'EMITIDO').length,
        anulados: filteredInvoices.filter(i => i.status === 'ANULADO').length,
        totalAmount: filteredInvoices.filter(i => i.status === 'EMITIDO').reduce((sum, i) => sum + i.amount, 0)
    };

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;

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
                <div className="flex gap-3">
                    <button onClick={loadInvoices} className={`px-4 py-2 rounded-lg flex items-center gap-2 ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-700'}`}>
                        <RefreshCw size={18} /> Actualizar
                    </button>
                    <button onClick={handleExportAll} className="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg flex items-center gap-2">
                        <Download size={18} /> Exportar Todo
                    </button>
                </div>
            </div>

            {/* Summary Cards */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div className={`p-4 rounded-xl border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'}`}>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Total Documentos</p>
                    <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{summaryStats.total}</p>
                </div>
                <div className={`p-4 rounded-xl border ${darkMode ? 'bg-green-900/30 border-green-700' : 'bg-green-50 border-green-200'}`}>
                    <p className={`text-sm ${darkMode ? 'text-green-300' : 'text-green-600'}`}>Emitidos</p>
                    <p className={`text-2xl font-bold ${darkMode ? 'text-green-400' : 'text-green-700'}`}>{summaryStats.emitidos}</p>
                </div>
                <div className={`p-4 rounded-xl border ${darkMode ? 'bg-red-900/30 border-red-700' : 'bg-red-50 border-red-200'}`}>
                    <p className={`text-sm ${darkMode ? 'text-red-300' : 'text-red-600'}`}>Anulados</p>
                    <p className={`text-2xl font-bold ${darkMode ? 'text-red-400' : 'text-red-700'}`}>{summaryStats.anulados}</p>
                </div>
                <div className={`p-4 rounded-xl border ${darkMode ? 'bg-blue-900/30 border-blue-700' : 'bg-blue-50 border-blue-200'}`}>
                    <p className={`text-sm ${darkMode ? 'text-blue-300' : 'text-blue-600'}`}>Total Recaudado</p>
                    <p className={`text-xl font-bold ${darkMode ? 'text-blue-400' : 'text-blue-700'}`}>Q {summaryStats.totalAmount.toLocaleString()}</p>
                </div>
            </div>

            {/* Filters */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm flex gap-4 items-end flex-wrap`}>
                <div className="flex-1 min-w-[200px]">
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
                <div className="min-w-[150px]">
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
                                <th className={`px-6 py-3 text-left text-xs font-medium uppercase ${darkMode ? 'text-gray-300' : 'text-gray-500'}`}>Documento</th>
                                <th className={`px-6 py-3 text-left text-xs font-medium uppercase ${darkMode ? 'text-gray-300' : 'text-gray-500'}`}>Estudiante</th>
                                <th className={`px-6 py-3 text-left text-xs font-medium uppercase ${darkMode ? 'text-gray-300' : 'text-gray-500'}`}>NIT</th>
                                <th className={`px-6 py-3 text-left text-xs font-medium uppercase ${darkMode ? 'text-gray-300' : 'text-gray-500'}`}>Fecha</th>
                                <th className={`px-6 py-3 text-right text-xs font-medium uppercase ${darkMode ? 'text-gray-300' : 'text-gray-500'}`}>Monto</th>
                                <th className={`px-6 py-3 text-center text-xs font-medium uppercase ${darkMode ? 'text-gray-300' : 'text-gray-500'}`}>Estado</th>
                                <th className={`px-6 py-3 text-right text-xs font-medium uppercase ${darkMode ? 'text-gray-300' : 'text-gray-500'}`}>Acciones</th>
                            </tr>
                        </thead>
                        <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-200'}`}>
                            {filteredInvoices.map((inv) => (
                                <tr key={inv.id} className={`${inv.status === 'ANULADO' ? 'opacity-60' : ''} ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}`}>
                                    <td className={`px-6 py-4 whitespace-nowrap text-sm font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                                        <div className="flex items-center gap-2">
                                            <FileText size={16} className="text-blue-500" />
                                            {inv.series}-{inv.number}
                                        </div>
                                    </td>
                                    <td className={`px-6 py-4 whitespace-nowrap text-sm ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>{inv.student || inv.name}</td>
                                    <td className={`px-6 py-4 whitespace-nowrap text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{inv.nit}</td>
                                    <td className={`px-6 py-4 whitespace-nowrap text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{inv.date}</td>
                                    <td className={`px-6 py-4 whitespace-nowrap text-sm text-right font-bold ${darkMode ? 'text-green-400' : 'text-green-600'}`}>
                                        Q {inv.amount.toLocaleString('es-GT', { minimumFractionDigits: 2 })}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-center">
                                        <span className={`px-2 py-1 text-xs rounded-full font-semibold ${inv.status === 'EMITIDO'
                                                ? 'bg-green-100 text-green-700 border border-green-200'
                                                : 'bg-red-100 text-red-700 border border-red-200'
                                            }`}>
                                            {inv.status === 'EMITIDO' ? 'Emitido' : 'Anulado'}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-right">
                                        <button
                                            onClick={() => handleDownloadInvoice(inv)}
                                            className={`p-2 rounded-lg transition-colors ${darkMode
                                                    ? 'text-blue-400 hover:bg-gray-600'
                                                    : 'text-blue-600 hover:bg-blue-50'
                                                }`}
                                            title="Descargar PDF"
                                        >
                                            <Download size={18} />
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {filteredInvoices.length === 0 && (
                    <div className="p-12 text-center">
                        <FileText size={48} className={`mx-auto mb-4 ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                        <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>No se encontraron comprobantes.</p>
                    </div>
                )}
            </div>
        </div>
    );
};

export default ComprobantesEmitidosPage;
