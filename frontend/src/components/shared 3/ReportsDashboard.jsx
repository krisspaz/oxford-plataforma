import React, { useState } from 'react';
import { FileText, Download, BarChart3, Users, DollarSign, Calendar, TrendingUp, Clock, FileSpreadsheet, Filter, Search, Printer, Mail } from 'lucide-react';
import { useTheme } from '../../contexts/ThemeContext';

/**
 * ReportsDashboard - Unified Reports Hub
 * ========================================
 * Consolidates: ReportsPage, NotasFinalesPage, MonitoreoPage, BoletasPage
 * 
 * Features:
 * - Category-based report organization
 * - Search and filter
 * - Export to PDF/Excel
 * - Print and email options
 */
const ReportsDashboard = () => {
    const { darkMode } = useTheme();
    const [activeCategory, setActiveCategory] = useState('all');
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedReport, setSelectedReport] = useState(null);
    const [generating, setGenerating] = useState(false);

    // Report categories
    const categories = [
        { id: 'all', label: 'Todos', icon: FileText, count: 12 },
        { id: 'academic', label: 'Académicos', icon: BarChart3, count: 5 },
        { id: 'financial', label: 'Financieros', icon: DollarSign, count: 4 },
        { id: 'attendance', label: 'Asistencia', icon: Calendar, count: 3 },
    ];

    // Reports data
    const reports = [
        // Academic
        { id: 1, name: 'Boletas de Calificaciones', description: 'Boletas individuales por bimestre', category: 'academic', icon: FileText, format: 'PDF', color: 'purple' },
        { id: 2, name: 'Cuadro de Notas Final', description: 'Resumen anual de calificaciones', category: 'academic', icon: FileSpreadsheet, format: 'Excel', color: 'green' },
        { id: 3, name: 'Rendimiento por Grado', description: 'Estadísticas comparativas por grado', category: 'academic', icon: TrendingUp, format: 'PDF', color: 'blue' },
        { id: 4, name: 'Estudiantes en Riesgo', description: 'Alumnos con bajo rendimiento', category: 'academic', icon: Users, format: 'Excel', color: 'red' },
        { id: 5, name: 'Ranking Académico', description: 'Top estudiantes por promedio', category: 'academic', icon: BarChart3, format: 'PDF', color: 'amber' },
        // Financial
        { id: 6, name: 'Estado de Cuenta General', description: 'Saldos por familia', category: 'financial', icon: DollarSign, format: 'PDF', color: 'emerald' },
        { id: 7, name: 'Pagos del Mes', description: 'Ingresos recibidos este mes', category: 'financial', icon: DollarSign, format: 'Excel', color: 'green' },
        { id: 8, name: 'Morosidad', description: 'Cuentas vencidas pendientes', category: 'financial', icon: Clock, format: 'PDF', color: 'red' },
        { id: 9, name: 'Proyección de Ingresos', description: 'Forecast mensual', category: 'financial', icon: TrendingUp, format: 'Excel', color: 'blue' },
        // Attendance
        { id: 10, name: 'Asistencia Mensual', description: 'Resumen por grado y sección', category: 'attendance', icon: Calendar, format: 'PDF', color: 'indigo' },
        { id: 11, name: 'Inasistencias Frecuentes', description: 'Estudiantes con más faltas', category: 'attendance', icon: Users, format: 'PDF', color: 'orange' },
        { id: 12, name: 'Puntualidad Docente', description: 'Registro de llegadas de maestros', category: 'attendance', icon: Clock, format: 'Excel', color: 'teal' },
    ];

    const filteredReports = reports.filter(r =>
        (activeCategory === 'all' || r.category === activeCategory) &&
        r.name.toLowerCase().includes(searchTerm.toLowerCase())
    );

    const colorMap = {
        purple: 'from-purple-500 to-purple-600',
        green: 'from-green-500 to-green-600',
        blue: 'from-blue-500 to-blue-600',
        red: 'from-red-500 to-red-600',
        amber: 'from-amber-500 to-amber-600',
        emerald: 'from-emerald-500 to-emerald-600',
        indigo: 'from-indigo-500 to-indigo-600',
        orange: 'from-orange-500 to-orange-600',
        teal: 'from-teal-500 to-teal-600',
    };

    const handleGenerate = async (report, action = 'download') => {
        setGenerating(true);
        setSelectedReport(report);

        // Simulate generation delay
        await new Promise(resolve => setTimeout(resolve, 1500));

        setGenerating(false);
        setSelectedReport(null);

        // In real implementation, call API and handle file
        console.log(`${action} report:`, report.name);
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className={`text-3xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Centro de Reportes</h1>
                    <p className={`${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Genera y descarga reportes del sistema</p>
                </div>
            </div>

            {/* Categories */}
            <div className="flex flex-wrap gap-2">
                {categories.map(cat => {
                    const Icon = cat.icon;
                    const isActive = activeCategory === cat.id;
                    return (
                        <button
                            key={cat.id}
                            onClick={() => setActiveCategory(cat.id)}
                            className={`px-4 py-2 rounded-xl text-sm font-medium transition-all flex items-center gap-2 ${isActive
                                    ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg'
                                    : darkMode
                                        ? 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700'
                                        : 'bg-white text-gray-600 hover:text-gray-900 hover:bg-gray-50 shadow'
                                }`}
                        >
                            <Icon size={16} />
                            {cat.label}
                            <span className={`px-1.5 py-0.5 rounded-full text-xs ${isActive ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-700'}`}>
                                {cat.count}
                            </span>
                        </button>
                    );
                })}
            </div>

            {/* Search */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm`}>
                <div className="relative">
                    <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                    <input
                        type="text"
                        placeholder="Buscar reporte..."
                        value={searchTerm}
                        onChange={e => setSearchTerm(e.target.value)}
                        className={`w-full pl-12 pr-4 py-3 rounded-lg border ${darkMode
                                ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500'
                                : 'bg-gray-50 border-gray-200 placeholder-gray-400'
                            } focus:ring-2 focus:ring-indigo-500 outline-none`}
                    />
                </div>
            </div>

            {/* Reports Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {filteredReports.map(report => {
                    const Icon = report.icon;
                    const isGenerating = generating && selectedReport?.id === report.id;

                    return (
                        <div
                            key={report.id}
                            className={`group relative overflow-hidden rounded-2xl ${darkMode ? 'bg-gray-800' : 'bg-white'
                                } shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1`}
                        >
                            {/* Color accent */}
                            <div className={`absolute top-0 left-0 w-full h-1 bg-gradient-to-r ${colorMap[report.color]}`} />

                            <div className="p-5">
                                <div className="flex items-start gap-4">
                                    <div className={`p-3 rounded-xl bg-gradient-to-r ${colorMap[report.color]} text-white shadow-lg`}>
                                        <Icon size={24} />
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <h3 className={`font-bold text-lg mb-1 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                                            {report.name}
                                        </h3>
                                        <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'} line-clamp-2`}>
                                            {report.description}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <span className={`px-3 py-1 rounded-full text-xs font-bold ${report.format === 'PDF'
                                            ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                            : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        }`}>
                                        {report.format}
                                    </span>

                                    <div className="flex items-center gap-1">
                                        <button
                                            onClick={() => handleGenerate(report, 'print')}
                                            disabled={isGenerating}
                                            className={`p-2 rounded-lg ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100'} transition-colors`}
                                            title="Imprimir"
                                        >
                                            <Printer size={16} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                                        </button>
                                        <button
                                            onClick={() => handleGenerate(report, 'email')}
                                            disabled={isGenerating}
                                            className={`p-2 rounded-lg ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100'} transition-colors`}
                                            title="Enviar por correo"
                                        >
                                            <Mail size={16} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                                        </button>
                                        <button
                                            onClick={() => handleGenerate(report, 'download')}
                                            disabled={isGenerating}
                                            className={`flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-all ${isGenerating
                                                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                                    : 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:shadow-lg'
                                                }`}
                                        >
                                            {isGenerating ? (
                                                <>
                                                    <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                                                    Generando...
                                                </>
                                            ) : (
                                                <>
                                                    <Download size={16} /> Descargar
                                                </>
                                            )}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    );
                })}
            </div>

            {filteredReports.length === 0 && (
                <div className={`text-center py-12 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                    <FileText size={48} className="mx-auto mb-4 opacity-50" />
                    <p>No se encontraron reportes</p>
                </div>
            )}
        </div>
    );
};

export default ReportsDashboard;
