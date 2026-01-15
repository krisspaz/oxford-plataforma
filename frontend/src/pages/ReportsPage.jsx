import React, { useState } from 'react';
import { FileText, Download, Filter, Search, FileSpreadsheet, File, Calendar, Users, DollarSign, BookOpen, TrendingUp, Clock } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';

const ReportsPage = () => {
    const { darkMode } = useTheme();
    const [activeCategory, setActiveCategory] = useState('all');
    const [searchTerm, setSearchTerm] = useState('');

    const categories = [
        { id: 'all', label: 'Todos', icon: FileText },
        { id: 'academic', label: 'Académicos', icon: BookOpen },
        { id: 'financial', label: 'Financieros', icon: DollarSign },
        { id: 'attendance', label: 'Asistencia', icon: Calendar },
    ];

    const reports = [
        { id: 1, name: 'Reporte de Asistencia Mensual', description: 'Control de asistencia por grado y sección', type: 'PDF', category: 'attendance', icon: Calendar, color: 'blue' },
        { id: 2, name: 'Rendimiento Académico por Grado', description: 'Estadísticas de notas y promedios', type: 'Excel', category: 'academic', icon: TrendingUp, color: 'green' },
        { id: 3, name: 'Listado de Solvencia Financiera', description: 'Estado de cuenta por estudiante', type: 'PDF', category: 'financial', icon: DollarSign, color: 'orange' },
        { id: 4, name: 'Boletas de Calificaciones', description: 'Boletas individuales por bimestre', type: 'PDF', category: 'academic', icon: FileText, color: 'purple' },
        { id: 5, name: 'Cuadro de Notas Final', description: 'Resumen anual de calificaciones', type: 'Excel', category: 'academic', icon: FileSpreadsheet, color: 'teal' },
        { id: 6, name: 'Listado de Estudiantes', description: 'Directorio completo por nivel', type: 'Excel', category: 'attendance', icon: Users, color: 'indigo' },
        { id: 7, name: 'Reporte de Inasistencias', description: 'Estudiantes con faltas acumuladas', type: 'PDF', category: 'attendance', icon: Clock, color: 'red' },
        { id: 8, name: 'Estado de Pagos Pendientes', description: 'Detalle de saldos por familia', type: 'Excel', category: 'financial', icon: DollarSign, color: 'amber' },
    ];

    const colorMap = {
        blue: 'bg-blue-500',
        green: 'bg-green-500',
        orange: 'bg-orange-500',
        purple: 'bg-purple-500',
        teal: 'bg-teal-500',
        indigo: 'bg-indigo-500',
        red: 'bg-red-500',
        amber: 'bg-amber-500',
    };

    const filteredReports = reports.filter(r =>
        (activeCategory === 'all' || r.category === activeCategory) &&
        r.name.toLowerCase().includes(searchTerm.toLowerCase())
    );

    const handleDownload = (report) => {
        import('jspdf').then(({ default: jsPDF }) => {
            import('jspdf-autotable').then(() => {
                const doc = new jsPDF();

                // Header
                doc.setFontSize(20);
                doc.setTextColor(40, 40, 40);
                doc.text('CORPORACIÓN EDUCACIONAL OXFORD', 105, 20, { align: 'center' });

                doc.setFontSize(14);
                doc.setTextColor(100, 100, 100);
                doc.text(report.name, 105, 30, { align: 'center' });

                doc.setDrawColor(200, 200, 200);
                doc.line(14, 35, 196, 35);

                // Body content based on report type
                doc.setFontSize(10);
                doc.setTextColor(0, 0, 0);
                doc.text(`Categoría: ${report.category}`, 14, 45);
                doc.text(`Fecha de generación: ${new Date().toLocaleDateString()}`, 14, 50);
                doc.text(`Descripción: ${report.description}`, 14, 60);

                // Mock Table Data
                const headers = [['#', 'Nombre Estudiante', 'Grado', 'Detalle', 'Estado']];
                const data = [];

                doc.autoTable({
                    startY: 70,
                    head: headers,
                    body: data,
                    theme: 'grid',
                    styles: { fontSize: 8 },
                    headStyles: { fillColor: [41, 128, 185], textColor: 255 }
                });

                // Footer
                const pageCount = doc.internal.getNumberOfPages();
                doc.setFontSize(8);
                for (let i = 1; i <= pageCount; i++) {
                    doc.setPage(i);
                    doc.text('Sistema Oxford - Reporte Generado Automáticamente', 14, 285);
                    doc.text(`Página ${i} de ${pageCount}`, 196, 285, { align: 'right' });
                }

                doc.save(`${report.name.replace(/ /g, '_')}.pdf`);
            }).catch(err => {
                console.error("Error loading jspdf-autotable:", err);
                alert('Error al generar PDF: No se pudo cargar el plugin de tablas.');
            });
        }).catch(err => {
            console.error("Error loading jsPDF:", err);
            alert('Error al generar PDF: No se pudo cargar la librería.');
        });
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
            <div className={`flex gap-2 p-1 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-gray-100'}`}>
                {categories.map(cat => {
                    const Icon = cat.icon;
                    return (
                        <button
                            key={cat.id}
                            onClick={() => setActiveCategory(cat.id)}
                            className={`flex-1 px-4 py-2.5 rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2 ${activeCategory === cat.id
                                ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg'
                                : darkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-600 hover:text-gray-900 hover:bg-white'
                                }`}
                        >
                            <Icon size={16} /> {cat.label}
                        </button>
                    );
                })}
            </div>

            {/* Search */}
            <div className="relative">
                <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                <input
                    type="text"
                    placeholder="Buscar reporte..."
                    value={searchTerm}
                    onChange={e => setSearchTerm(e.target.value)}
                    className={`w-full pl-12 pr-4 py-3 rounded-xl border ${darkMode
                        ? 'bg-gray-800 border-gray-700 text-white placeholder-gray-500'
                        : 'bg-white border-gray-200 placeholder-gray-400'
                        } focus:ring-2 focus:ring-indigo-500 outline-none`}
                />
            </div>

            {/* Reports Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {filteredReports.map((report) => {
                    const Icon = report.icon;
                    return (
                        <div
                            key={report.id}
                            className={`group relative overflow-hidden rounded-2xl ${darkMode ? 'bg-gray-800 hover:bg-gray-750' : 'bg-white hover:shadow-xl'
                                } shadow-lg transition-all duration-300 hover:-translate-y-1`}
                        >
                            {/* Color accent */}
                            <div className={`absolute top-0 left-0 w-full h-1 ${colorMap[report.color]}`}></div>

                            <div className="p-5">
                                <div className="flex items-start gap-4">
                                    <div className={`p-3 rounded-xl ${colorMap[report.color]}/20 ${colorMap[report.color].replace('bg-', 'text-')}`}>
                                        <Icon size={24} />
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <h3 className={`font-bold text-lg mb-1 ${darkMode ? 'text-white' : 'text-gray-900'}`}>{report.name}</h3>
                                        <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'} line-clamp-2`}>{report.description}</p>
                                    </div>
                                </div>

                                <div className="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <span className={`px-3 py-1 rounded-full text-xs font-bold ${report.type === 'PDF'
                                        ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                        : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        }`}>
                                        {report.type === 'PDF' ? <File size={12} className="inline mr-1" /> : <FileSpreadsheet size={12} className="inline mr-1" />}
                                        {report.type}
                                    </span>
                                    <button
                                        onClick={() => handleDownload(report)}
                                        className={`flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-all ${darkMode
                                            ? 'bg-gray-700 hover:bg-indigo-600 text-gray-300 hover:text-white'
                                            : 'bg-gray-100 hover:bg-indigo-600 text-gray-700 hover:text-white'
                                            } group-hover:bg-indigo-600 group-hover:text-white`}
                                    >
                                        <Download size={16} /> Descargar
                                    </button>
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

export default ReportsPage;

