import React, { useState, useEffect } from 'react';
import { FileSpreadsheet, Download, Calendar, DollarSign, CreditCard, Printer, RefreshCw } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { invoiceService } from '../services';

import { usePdfExport } from '../hooks/usePdfExport';

const CorteDiaPage = () => {
    const { darkMode } = useTheme();
    const { exportTable } = usePdfExport(); // Hook
    const [dateFrom, setDateFrom] = useState(new Date().toISOString().split('T')[0]);
    // ...

    const handleExportPDF = () => {
        if (payments.length === 0) {
            alert('No hay datos para exportar');
            return;
        }

        const columns = ["Nombre", "Productos", "Método", "Documento", "Monto"];
        const data = payments.map(p => [
            p.name,
            p.products,
            p.method,
            `${p.series}-${p.number}`,
            `Q ${p.total?.toLocaleString()}`
        ]);

        // Add Total Row
        data.push(['', '', '', 'TOTAL', `Q ${totals.total.toLocaleString()}`]);

        exportTable({
            title: 'Reporte de Corte del Día',
            subtitle: `Del: ${dateFrom} Al: ${dateTo}`,
            columns: columns,
            data: data,
            filename: `corte_${dateFrom}_${dateTo}.pdf`,
            autoTableOptions: {
                didParseCell: function (data) {
                    // Highlight Total Row
                    if (data.row.index === payments.length) {
                        data.cell.styles.fontStyle = 'bold';
                        data.cell.styles.fillColor = [200, 200, 200];
                    }
                }
            }
        });
    };

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Corte del Día</h1>
                <div className="flex gap-2 no-print">
                    <button onClick={handleExportPDF} className="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                        <Download size={18} /> Exportar PDF
                    </button>
                    <button onClick={() => window.print()} className="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        <Printer size={18} /> Imprimir
                    </button>
                    {/* Estilos para impresión Horizontal */}
                    <style>{`
                        @media print {
                            @page { size: landscape; margin: 1cm; }
                            .no-print { display: none !important; }
                            body { background: white; color: black; }
                            .print-container { width: 100%; }
                            table { width: 100%; font-size: 12px; border-collapse: collapse; }
                            th, td { border: 1px solid #ddd; padding: 4px; }
                            thead { background-color: #f3f4f6 !important; -webkit-print-color-adjust: exact; }
                        }
                    `}</style>
                </div>
            </div>

            {/* Filters */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm flex gap-4 items-end`}>
                <div>
                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Desde</label>
                    <input type="date" value={dateFrom} onChange={e => setDateFrom(e.target.value)} className={inputClass} />
                </div>
                <div>
                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Hasta</label>
                    <input type="date" value={dateTo} onChange={e => setDateTo(e.target.value)} className={inputClass} />
                </div>
                <button onClick={fetchData} disabled={loading} className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg flex items-center gap-2">
                    <RefreshCw size={16} className={loading ? 'animate-spin' : ''} /> Consultar
                </button>
            </div>

            {/* Summary Cards */}
            <div className="grid grid-cols-4 gap-4">
                <div className={`${darkMode ? 'bg-green-900/30 border-green-700' : 'bg-green-50 border-green-200'} border rounded-xl p-4`}>
                    <div className="flex items-center gap-2 mb-2">
                        <DollarSign className="text-green-500" size={20} />
                        <span className={`font-medium ${darkMode ? 'text-green-300' : 'text-green-700'}`}>Efectivo</span>
                    </div>
                    <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Q {totals.efectivo.toLocaleString()}</p>
                </div>
                <div className={`${darkMode ? 'bg-blue-900/30 border-blue-700' : 'bg-blue-50 border-blue-200'} border rounded-xl p-4`}>
                    <div className="flex items-center gap-2 mb-2">
                        <CreditCard className="text-blue-500" size={20} />
                        <span className={`font-medium ${darkMode ? 'text-blue-300' : 'text-blue-700'}`}>Tarjeta</span>
                    </div>
                    <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Q {totals.tarjeta.toLocaleString()}</p>
                </div>
                <div className={`${darkMode ? 'bg-purple-900/30 border-purple-700' : 'bg-purple-50 border-purple-200'} border rounded-xl p-4`}>
                    <div className="flex items-center gap-2 mb-2">
                        <FileSpreadsheet className="text-purple-500" size={20} />
                        <span className={`font-medium ${darkMode ? 'text-purple-300' : 'text-purple-700'}`}>Depósito</span>
                    </div>
                    <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Q {totals.deposito.toLocaleString()}</p>
                </div>
                <div className={`${darkMode ? 'bg-teal-900/30 border-teal-700' : 'bg-teal-50 border-teal-200'} border rounded-xl p-4`}>
                    <div className="flex items-center gap-2 mb-2">
                        <Calendar className="text-teal-500" size={20} />
                        <span className={`font-medium ${darkMode ? 'text-teal-300' : 'text-teal-700'}`}>Total del Día</span>
                    </div>
                    <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Q {totals.total.toLocaleString()}</p>
                </div>
            </div>

            {/* Detailed Table */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                <div className={`p-4 border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                    <h2 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Detalle de Pagos</h2>
                </div>
                {loading ? (
                    <div className="p-8 text-center">
                        <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                        <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando...</p>
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className={darkMode ? 'bg-gray-700' : 'bg-gray-50'}>
                                <tr>
                                    <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Nombre</th>
                                    <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Productos</th>
                                    <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Método</th>
                                    <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Documento</th>
                                    <th className={`px-4 py-3 text-right font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Monto</th>
                                </tr>
                            </thead>
                            <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                                {payments.map(p => (
                                    <tr key={p.id} className={darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}>
                                        <td className={`px-4 py-3 font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{p.name}</td>
                                        <td className={`px-4 py-3 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{p.products}</td>
                                        <td className="px-4 py-3">
                                            <span className={`px-2 py-1 rounded-full text-xs font-medium ${p.method === 'Efectivo' ? 'bg-green-100 text-green-700' :
                                                p.method === 'Tarjeta' ? 'bg-blue-100 text-blue-700' :
                                                    'bg-purple-100 text-purple-700'
                                                }`}>{p.method}</span>
                                        </td>
                                        <td className={`px-4 py-3 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{p.series}-{p.number}</td>
                                        <td className={`px-4 py-3 text-right font-bold ${darkMode ? 'text-green-400' : 'text-green-600'}`}>Q {p.total?.toLocaleString()}</td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot className={darkMode ? 'bg-gray-700' : 'bg-gray-100'}>
                                <tr>
                                    <td colSpan="4" className={`px-4 py-3 font-bold text-right ${darkMode ? 'text-white' : 'text-gray-800'}`}>TOTAL:</td>
                                    <td className={`px-4 py-3 text-right font-bold text-lg ${darkMode ? 'text-teal-400' : 'text-teal-600'}`}>Q {totals.total.toLocaleString()}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                )}
            </div>
        </div>
    );
};

export default CorteDiaPage;
