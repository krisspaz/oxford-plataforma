import { toast } from '../utils/toast';
import { useState, useEffect, useMemo } from 'react';
import { AlertCircle, Search, DollarSign, Calendar, User, FileText, Loader, Filter, CheckCircle, PartyPopper } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';
import { useTheme } from '../contexts/ThemeContext';

import { usePdfExport } from '../hooks/usePdfExport';

const InsolventesPage = () => {
    const { darkMode } = useTheme();
    const navigate = useNavigate();
    const { exportTable } = usePdfExport(); // Hook
    const [students, setStudents] = useState([]);
    const [searchTerm, setSearchTerm] = useState('');
    const [showOnlyOverdue, setShowOnlyOverdue] = useState(true); // Default: only overdue
    const [loading, setLoading] = useState(true);

    // Load Real Data
    useEffect(() => {
        const loadData = async () => {
            setLoading(true);
            try {
                const response = await api.get('/payment-plans/insolvents');
                // Handle different response structures gracefully
                const data = response.data || response;
                if (Array.isArray(data)) {
                    setStudents(data);
                } else if (data.data && Array.isArray(data.data)) {
                    setStudents(data.data);
                }
            } catch (err) {
                console.error("Error loading insolvents:", err);
            } finally {
                setLoading(false);
            }
        };
        loadData();
    }, []);

    // Calculate days overdue for each student
    const studentsWithDaysOverdue = useMemo(() => {
        const today = new Date();
        return students.map(student => {
            const dueDate = new Date(student.dueDate);
            const diffTime = today - dueDate;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return {
                ...student,
                daysOverdue: diffDays,
                isOverdue: diffDays > 0
            };
        });
    }, [students]);

    // Filter students based on search and overdue toggle
    const filteredStudents = useMemo(() => {
        let result = studentsWithDaysOverdue;

        // Apply overdue filter
        if (showOnlyOverdue) {
            result = result.filter(student => student.isOverdue);
        }

        // Apply search filter
        if (searchTerm) {
            result = result.filter(student =>
                student.studentName?.toLowerCase().includes(searchTerm.toLowerCase())
            );
        }

        // Sort by days overdue (most overdue first)
        return result.sort((a, b) => b.daysOverdue - a.daysOverdue);
    }, [studentsWithDaysOverdue, showOnlyOverdue, searchTerm]);

    const totalDeuda = filteredStudents.reduce((acc, curr) => acc + (parseFloat(curr.amount) || 0), 0);
    const overdueCount = studentsWithDaysOverdue.filter(s => s.isOverdue).length;
    const allCount = studentsWithDaysOverdue.length;

    // Export Handler
    const handleExportPDF = () => {
        if (filteredStudents.length === 0) {
            toast.info('No hay datos para exportar');
            return;
        }

        const columns = ["Estudiante", "Descripción", "Vencimiento", "Monto"];
        const data = filteredStudents.map(student => [
            student.studentName,
            student.description,
            student.dueDate,
            `Q ${parseFloat(student.amount).toFixed(2)}`
        ]);

        exportTable({
            title: 'Reporte de Estudiantes Insolventes',
            subtitle: `Generado: ${new Date().toLocaleDateString()}`,
            columns: columns,
            data: data,
            filename: 'insolventes.pdf',
            autoTableOptions: {
                didParseCell: function (data) {
                    if (data.section === 'body' && data.column.index === 3) {
                        data.cell.styles.textColor = [220, 53, 69]; // Red for amount
                        data.cell.styles.fontStyle = 'bold';
                    }
                }
            }
        });
    };

    return (
        <div className={`p-6 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
            <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 className="text-3xl font-bold flex items-center gap-2">
                        <AlertCircle className="text-red-500" />
                        Estudiantes Insolventes
                    </h1>
                    <p className={`mt-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        Listado de estudiantes con cuotas vencidas
                    </p>
                </div>

                <div className="flex gap-4 items-center">
                    <button onClick={handleExportPDF} className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <FileText size={18} /> Exportar Reporte
                    </button>

                    <div className={`${darkMode ? 'bg-red-900/30 border-red-800' : 'bg-red-50 border-red-200'} border rounded-xl p-4 flex items-center gap-4`}>
                        <div className={`p-3 rounded-full ${darkMode ? 'bg-red-800/50' : 'bg-red-100'}`}>
                            <DollarSign className="text-red-500" size={24} />
                        </div>
                        <div>
                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>Total Vencido</p>
                            <p className="text-2xl font-bold text-red-500">Q {totalDeuda.toFixed(2)}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div className={`rounded-xl shadow-sm border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'} overflow-hidden`}>
                <div className="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap gap-4 items-center">
                    {/* Filter Toggle */}
                    <div className="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                        <button
                            onClick={() => setShowOnlyOverdue(true)}
                            className={`px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2 ${showOnlyOverdue
                                    ? 'bg-red-500 text-white shadow-sm'
                                    : darkMode ? 'text-gray-300 hover:bg-gray-600' : 'text-gray-600 hover:bg-gray-200'
                                }`}
                        >
                            <Filter size={16} />
                            Solo Vencidos ({overdueCount})
                        </button>
                        <button
                            onClick={() => setShowOnlyOverdue(false)}
                            className={`px-4 py-2 rounded-md text-sm font-medium transition-colors ${!showOnlyOverdue
                                    ? 'bg-blue-500 text-white shadow-sm'
                                    : darkMode ? 'text-gray-300 hover:bg-gray-600' : 'text-gray-600 hover:bg-gray-200'
                                }`}
                        >
                            Todos ({allCount})
                        </button>
                    </div>

                    {/* Search */}
                    <div className="relative flex-1 min-w-[200px]">
                        <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
                        <input
                            type="text"
                            placeholder="Buscar estudiante..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className={`w-full pl-10 pr-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none ${darkMode
                                ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400'
                                : 'bg-gray-50 border-gray-300 text-gray-900'
                                }`}
                        />
                    </div>
                </div>

                <div className="overflow-x-auto">
                    <table className="w-full text-left">
                        <thead className={`text-xs uppercase font-semibold ${darkMode ? 'bg-gray-900/50 text-gray-400' : 'bg-gray-50 text-gray-500'}`}>
                            <tr>
                                <th className="px-6 py-4">Estudiante</th>
                                <th className="px-6 py-4">Descripción</th>
                                <th className="px-6 py-4">Fecha Vencimiento</th>
                                <th className="px-6 py-4 text-center">Días Mora</th>
                                <th className="px-6 py-4 text-right">Monto</th>
                                <th className="px-6 py-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-200'}`}>
                            {filteredStudents.map((item) => (
                                <tr key={item.id} className={`hover:bg-gray-50/5 dark:hover:bg-gray-700/50 transition-colors`}>
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-3">
                                            <div className={`p-2 rounded-full ${darkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>
                                                <User size={18} className="text-blue-500" />
                                            </div>
                                            <div>
                                                <p className="font-semibold">{item.studentName}</p>
                                                <p className="text-xs text-gray-500">{item.studentEmail}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-2">
                                            <FileText size={16} className="text-gray-400" />
                                            <span>{item.description}</span>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-2 text-red-500 font-medium">
                                            <Calendar size={16} />
                                            <span>{item.dueDate}</span>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 text-center">
                                        {item.isOverdue ? (
                                            <span className={`px-3 py-1 rounded-full text-xs font-bold ${item.daysOverdue > 30
                                                    ? 'bg-red-600 text-white'
                                                    : item.daysOverdue > 15
                                                        ? 'bg-orange-500 text-white'
                                                        : 'bg-yellow-500 text-white'
                                                }`}>
                                                {item.daysOverdue} días
                                            </span>
                                        ) : (
                                            <span className="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                                Al día
                                            </span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                                        Q {parseFloat(item.amount).toFixed(2)}
                                    </td>
                                    <td className="px-6 py-4 text-center">
                                        <button
                                            onClick={() => navigate('/finanzas/pagos', { state: { studentId: item.studentId, preselectPayment: item.id } })}
                                            className="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors"
                                        >
                                            Cobrar
                                        </button>
                                    </td>
                                </tr>
                            ))}
                            {filteredStudents.length === 0 && !loading && (
                                <tr>
                                    <td colSpan="6" className="px-6 py-16 text-center">
                                        {showOnlyOverdue && overdueCount === 0 ? (
                                            <div className="flex flex-col items-center gap-4">
                                                <div className="p-4 bg-green-100 dark:bg-green-900/30 rounded-full">
                                                    <PartyPopper className="text-green-500" size={48} />
                                                </div>
                                                <div>
                                                    <h3 className="text-xl font-bold text-green-600 dark:text-green-400 mb-2">
                                                        🎉 ¡Excelente! Todos los estudiantes están al día
                                                    </h3>
                                                    <p className="text-gray-500 dark:text-gray-400">
                                                        No hay cuotas vencidas pendientes de cobro.
                                                    </p>
                                                </div>
                                                <button
                                                    onClick={() => setShowOnlyOverdue(false)}
                                                    className="mt-2 px-4 py-2 text-blue-500 hover:text-blue-600 font-medium flex items-center gap-2"
                                                >
                                                    Ver estado de cuenta general
                                                </button>
                                            </div>
                                        ) : (
                                            <div className="text-gray-500">
                                                No se encontraron registros con los filtros aplicados.
                                            </div>
                                        )}
                                    </td>
                                </tr>
                            )}
                            {loading && (
                                <tr>
                                    <td colSpan="6" className="px-6 py-12 text-center">
                                        <Loader className="animate-spin mx-auto text-blue-500" size={32} />
                                        <p className="mt-2 text-gray-500">Cargando datos...</p>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
};

export default InsolventesPage;
