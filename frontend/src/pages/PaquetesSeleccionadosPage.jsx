import React, { useState, useEffect } from 'react';
import { Package, Check, X, RefreshCw, Search, Filter, Download } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { usePdfExport } from '../hooks/usePdfExport';
import { packageService } from '../services/packageService';

const PaquetesSeleccionadosPage = () => {
    const { darkMode } = useTheme();
    const { exportTable } = usePdfExport();
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const [filterStatus, setFilterStatus] = useState('all');
    const [selectedPackages, setSelectedPackages] = useState([]);
    const [actionLoading, setActionLoading] = useState(null);

    useEffect(() => {
        loadSelectedPackages();
    }, []);

    const loadSelectedPackages = async () => {
        setLoading(true);
        try {
            // Using real service
            const response = await packageService.getAllSelections();
            if (response.data && response.data['hydra:member']) {
                setSelectedPackages(response.data['hydra:member']);
            } else if (Array.isArray(response.data)) {
                setSelectedPackages(response.data);
            } else {
                setSelectedPackages([]);
            }
        } catch (error) {
            console.error("Error loading packages:", error);
            setSelectedPackages([]);
        } finally {
            setLoading(false);
        }
    };

    const handleApprove = async (id) => {
        setActionLoading(id);
        try {
            await new Promise(resolve => setTimeout(resolve, 500));
            setSelectedPackages(prev =>
                prev.map(p => p.id === id ? { ...p, status: 'APPROVED' } : p)
            );
        } finally {
            setActionLoading(null);
        }
    };

    const handleReject = async (id) => {
        const reason = prompt('Ingrese el motivo del rechazo:');
        if (!reason) return;

        setActionLoading(id);
        try {
            await new Promise(resolve => setTimeout(resolve, 500));
            setSelectedPackages(prev =>
                prev.map(p => p.id === id ? { ...p, status: 'REJECTED', rejectionReason: reason } : p)
            );
        } finally {
            setActionLoading(null);
        }
    };

    const handleExportPDF = () => {
        const columns = ['Estudiante', 'Carnet', 'Grado', 'Paquete', 'Total', 'Fecha', 'Estado'];
        const data = filteredPackages.map(p => [
            p.student,
            p.carnet,
            p.grade,
            p.package,
            `Q ${p.total.toLocaleString()}`,
            p.selectedAt,
            p.status === 'APPROVED' ? 'Aprobado' : p.status === 'REJECTED' ? 'Rechazado' : 'Pendiente'
        ]);

        exportTable({
            title: 'Paquetes Seleccionados',
            subtitle: `Generado: ${new Date().toLocaleDateString()}`,
            columns,
            data,
            filename: 'paquetes_seleccionados.pdf'
        });
    };

    const getStatusBadge = (status) => {
        const styles = {
            'PENDING': 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'APPROVED': 'bg-green-100 text-green-800 border-green-300',
            'REJECTED': 'bg-red-100 text-red-800 border-red-300'
        };
        const labels = {
            'PENDING': 'Pendiente',
            'APPROVED': 'Aprobado',
            'REJECTED': 'Rechazado'
        };
        return (
            <span className={`px-3 py-1 rounded-full text-xs font-bold border ${styles[status]}`}>
                {labels[status]}
            </span>
        );
    };

    const filteredPackages = selectedPackages.filter(p => {
        const matchesSearch = p.student.toLowerCase().includes(searchTerm.toLowerCase()) ||
            p.carnet.includes(searchTerm);
        const matchesStatus = filterStatus === 'all' || p.status === filterStatus;
        return matchesSearch && matchesStatus;
    });

    const summaryStats = {
        total: selectedPackages.length,
        pending: selectedPackages.filter(p => p.status === 'PENDING').length,
        approved: selectedPackages.filter(p => p.status === 'APPROVED').length,
        rejected: selectedPackages.filter(p => p.status === 'REJECTED').length,
        totalAmount: selectedPackages.filter(p => p.status === 'APPROVED').reduce((sum, p) => sum + p.total, 0)
    };

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando paquetes...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Paquetes Seleccionados</h1>
                <div className="flex gap-3">
                    <button onClick={loadSelectedPackages} className={`px-4 py-2 rounded-lg flex items-center gap-2 ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-700'}`}>
                        <RefreshCw size={18} /> Actualizar
                    </button>
                    <button onClick={handleExportPDF} className="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg flex items-center gap-2">
                        <Download size={18} /> Exportar PDF
                    </button>
                </div>
            </div>

            {/* Summary Cards */}
            <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div className={`p-4 rounded-xl border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'}`}>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Total</p>
                    <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{summaryStats.total}</p>
                </div>
                <div className={`p-4 rounded-xl border ${darkMode ? 'bg-yellow-900/30 border-yellow-700' : 'bg-yellow-50 border-yellow-200'}`}>
                    <p className={`text-sm ${darkMode ? 'text-yellow-300' : 'text-yellow-600'}`}>Pendientes</p>
                    <p className={`text-2xl font-bold ${darkMode ? 'text-yellow-400' : 'text-yellow-700'}`}>{summaryStats.pending}</p>
                </div>
                <div className={`p-4 rounded-xl border ${darkMode ? 'bg-green-900/30 border-green-700' : 'bg-green-50 border-green-200'}`}>
                    <p className={`text-sm ${darkMode ? 'text-green-300' : 'text-green-600'}`}>Aprobados</p>
                    <p className={`text-2xl font-bold ${darkMode ? 'text-green-400' : 'text-green-700'}`}>{summaryStats.approved}</p>
                </div>
                <div className={`p-4 rounded-xl border ${darkMode ? 'bg-red-900/30 border-red-700' : 'bg-red-50 border-red-200'}`}>
                    <p className={`text-sm ${darkMode ? 'text-red-300' : 'text-red-600'}`}>Rechazados</p>
                    <p className={`text-2xl font-bold ${darkMode ? 'text-red-400' : 'text-red-700'}`}>{summaryStats.rejected}</p>
                </div>
                <div className={`p-4 rounded-xl border ${darkMode ? 'bg-blue-900/30 border-blue-700' : 'bg-blue-50 border-blue-200'}`}>
                    <p className={`text-sm ${darkMode ? 'text-blue-300' : 'text-blue-600'}`}>Total Aprobado</p>
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
                            placeholder="Buscar estudiante..."
                            value={searchTerm}
                            onChange={e => setSearchTerm(e.target.value)}
                            className={`${inputClass} w-full pl-10`}
                        />
                    </div>
                </div>
                <div className="min-w-[150px]">
                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Estado</label>
                    <select
                        value={filterStatus}
                        onChange={e => setFilterStatus(e.target.value)}
                        className={inputClass}
                    >
                        <option value="all">Todos</option>
                        <option value="PENDING">Pendientes</option>
                        <option value="APPROVED">Aprobados</option>
                        <option value="REJECTED">Rechazados</option>
                    </select>
                </div>
            </div>

            {/* Table */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead className={darkMode ? 'bg-gray-700' : 'bg-gray-50'}>
                            <tr>
                                <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Estudiante</th>
                                <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Paquete</th>
                                <th className={`px-4 py-3 text-right font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Total</th>
                                <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Fecha</th>
                                <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Estado</th>
                                <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Acciones</th>
                            </tr>
                        </thead>
                        <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                            {filteredPackages.map(pkg => (
                                <tr key={pkg.id} className={darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}>
                                    <td className="px-4 py-3">
                                        <div>
                                            <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{pkg.student}</p>
                                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{pkg.carnet} • {pkg.grade}</p>
                                        </div>
                                    </td>
                                    <td className={`px-4 py-3 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                        <div className="flex items-center gap-2">
                                            <Package size={16} className="text-teal-500" />
                                            {pkg.package}
                                        </div>
                                    </td>
                                    <td className={`px-4 py-3 text-right font-bold ${darkMode ? 'text-green-400' : 'text-green-600'}`}>
                                        Q {pkg.total.toLocaleString()}
                                    </td>
                                    <td className={`px-4 py-3 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{pkg.selectedAt}</td>
                                    <td className="px-4 py-3 text-center">{getStatusBadge(pkg.status)}</td>
                                    <td className="px-4 py-3">
                                        {pkg.status === 'PENDING' && (
                                            <div className="flex justify-center gap-2">
                                                <button
                                                    onClick={() => handleApprove(pkg.id)}
                                                    disabled={actionLoading === pkg.id}
                                                    className="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 disabled:opacity-50"
                                                    title="Aprobar"
                                                >
                                                    {actionLoading === pkg.id ? <RefreshCw size={16} className="animate-spin" /> : <Check size={16} />}
                                                </button>
                                                <button
                                                    onClick={() => handleReject(pkg.id)}
                                                    disabled={actionLoading === pkg.id}
                                                    className="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 disabled:opacity-50"
                                                    title="Rechazar"
                                                >
                                                    <X size={16} />
                                                </button>
                                            </div>
                                        )}
                                        {pkg.status !== 'PENDING' && (
                                            <span className={`text-sm ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>--</span>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {filteredPackages.length === 0 && (
                    <div className="p-12 text-center">
                        <Package size={48} className={`mx-auto mb-4 ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                        <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>No hay paquetes seleccionados que coincidan con los filtros.</p>
                    </div>
                )}
            </div>
        </div>
    );
};

export default PaquetesSeleccionadosPage;
