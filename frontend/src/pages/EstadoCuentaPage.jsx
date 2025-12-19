import React, { useState, useEffect } from 'react';
import { User, Search, DollarSign, Check, Clock, AlertCircle, RefreshCw } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { studentService } from '../services';

import { usePdfExport } from '../hooks/usePdfExport';

const EstadoCuentaPage = () => {
    const { darkMode } = useTheme();
    const { exportTable } = usePdfExport(); // Hook

    // ... (state)

    // Export Handler
    const handleExportPDF = () => {
        if (!selectedStudent || !accountData) {
            alert('Selecciona un estudiante primero');
            return;
        }

        const columns = ["Concepto", "Monto", "Pagado", "Pendiente", "Vencimiento", "Estado", "Documento"];
        const data = accountData.quotas.map(q => [
            q.concept,
            `Q ${q.amount.toLocaleString()}`,
            `Q ${q.paid.toLocaleString()}`,
            `Q ${q.pending.toLocaleString()}`,
            q.dueDate,
            q.status,
            q.document || '-'
        ]);

        // Add Summary Row
        data.push(['RESUMEN', `Asignado: Q ${accountData.summary.totalAssigned}`, `Pagado: Q ${accountData.summary.totalPaid}`, `Pendiente: Q ${accountData.summary.totalPending}`, '', '', '']);

        exportTable({
            title: 'Estado de Cuenta',
            subtitle: `Estudiante: ${selectedStudent.fullName} - Carnet: ${selectedStudent.carnet}`,
            columns: columns,
            data: data,
            filename: `estado_cuenta_${selectedStudent.carnet}.pdf`,
            autoTableOptions: {
                didParseCell: function (data) {
                    // Colorize Status
                    if (data.section === 'body' && data.column.index === 5) {
                        if (data.cell.raw === 'PAGADO') data.cell.styles.textColor = [25, 135, 84];
                        else if (data.cell.raw === 'VENCIDO') data.cell.styles.textColor = [220, 53, 69];
                        else data.cell.styles.textColor = [255, 193, 7];
                    }
                    // Highlight Summary Row
                    if (data.row.index === accountData.quotas.length) {
                        data.cell.styles.fontStyle = 'bold';
                        data.cell.styles.fillColor = [220, 220, 220];
                    }
                }
            }
        });
    };

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Estado de Cuenta</h1>
                {selectedStudent && (
                    <button onClick={handleExportPDF} className="flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                        <Check size={18} /> Descargar PDF
                    </button>
                )}
            </div>

            {/* Search Code ... */}

            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm`}>
                <label className={`block text-sm font-medium mb-2 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                    <User size={16} className="inline mr-2" />Buscar Estudiante
                </label>
                <div className="relative max-w-md">
                    <Search className={`absolute left-3 top-2.5 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`} size={18} />
                    <input
                        type="text"
                        placeholder="Buscar por nombre o carnet..."
                        value={searchTerm}
                        onChange={e => setSearchTerm(e.target.value)}
                        className={`${inputClass} w-full pl-10`}
                    />
                </div>
                {students.length > 0 && (
                    <div className="mt-2 max-h-40 overflow-y-auto space-y-1">
                        {students.map(s => (
                            <div
                                key={s.id}
                                onClick={() => { setSelectedStudent(s); setStudents([]); setSearchTerm(''); }}
                                className={`p-2 rounded cursor-pointer ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100'}`}
                            >
                                <span className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{s.fullName}</span>
                                <span className={`text-sm ml-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{s.carnet} - {s.grade}</span>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* Loading */}
            {loading && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                    <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                    <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando estado de cuenta...</p>
                </div>
            )}

            {/* Account Data */}
            {selectedStudent && accountData && !loading && (
                <>
                    {/* Student Info */}
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm flex items-center gap-4`}>
                        <div className="w-14 h-14 bg-gradient-to-br from-teal-500 to-teal-700 rounded-xl flex items-center justify-center text-white font-bold text-xl">
                            {selectedStudent.fullName.charAt(0)}
                        </div>
                        <div>
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{selectedStudent.fullName}</h2>
                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{selectedStudent.carnet} • {selectedStudent.grade}</p>
                        </div>
                    </div>

                    {/* Summary Cards */}
                    <div className="grid grid-cols-3 gap-4">
                        <div className={`${darkMode ? 'bg-blue-900/30 border-blue-700' : 'bg-blue-50 border-blue-200'} border rounded-xl p-4`}>
                            <p className={`text-sm ${darkMode ? 'text-blue-300' : 'text-blue-600'}`}>Total Asignado</p>
                            <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Q {accountData.summary.totalAssigned.toLocaleString()}</p>
                        </div>
                        <div className={`${darkMode ? 'bg-green-900/30 border-green-700' : 'bg-green-50 border-green-200'} border rounded-xl p-4`}>
                            <p className={`text-sm ${darkMode ? 'text-green-300' : 'text-green-600'}`}>Total Pagado</p>
                            <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Q {accountData.summary.totalPaid.toLocaleString()}</p>
                        </div>
                        <div className={`${darkMode ? 'bg-red-900/30 border-red-700' : 'bg-red-50 border-red-200'} border rounded-xl p-4`}>
                            <p className={`text-sm ${darkMode ? 'text-red-300' : 'text-red-600'}`}>Saldo Pendiente</p>
                            <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Q {accountData.summary.totalPending.toLocaleString()}</p>
                        </div>
                    </div>

                    {/* Quotas Table */}
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                        <div className={`p-4 border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                            <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Detalle de Cuotas</h3>
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead className={darkMode ? 'bg-gray-700' : 'bg-gray-50'}>
                                    <tr>
                                        <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Concepto</th>
                                        <th className={`px-4 py-3 text-right font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Monto</th>
                                        <th className={`px-4 py-3 text-right font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Pagado</th>
                                        <th className={`px-4 py-3 text-right font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Pendiente</th>
                                        <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Vencimiento</th>
                                        <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Estado</th>
                                        <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Documento</th>
                                    </tr>
                                </thead>
                                <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                                    {accountData.quotas.map(quota => (
                                        <tr key={quota.id} className={darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}>
                                            <td className={`px-4 py-3 font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{quota.concept}</td>
                                            <td className={`px-4 py-3 text-right ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Q {quota.amount.toLocaleString()}</td>
                                            <td className={`px-4 py-3 text-right ${darkMode ? 'text-green-400' : 'text-green-600'}`}>Q {quota.paid.toLocaleString()}</td>
                                            <td className={`px-4 py-3 text-right font-medium ${quota.pending > 0 ? 'text-red-500' : darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                                Q {quota.pending.toLocaleString()}
                                            </td>
                                            <td className={`px-4 py-3 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{quota.dueDate}</td>
                                            <td className="px-4 py-3 text-center">{getStatusBadge(quota.status)}</td>
                                            <td className={`px-4 py-3 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{quota.document || '-'}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </>
            )}

            {!selectedStudent && !loading && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                    <DollarSign size={48} className={`mx-auto mb-4 ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>Busque un estudiante para ver su estado de cuenta</p>
                </div>
            )}
        </div>
    );
};

export default EstadoCuentaPage;
