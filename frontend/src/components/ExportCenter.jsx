import { useState } from 'react';
import { exportBoletaPDF, exportStudentListExcel, exportGradesExcel, exportAttendancePDF } from '../utils/exportUtils';

/**
 * Export Center Component
 * UI para exportar: Boletas, Listas, Calificaciones, Asistencias
 */
const ExportCenter = () => {
    const [loading, setLoading] = useState(false);
    const [exportType, setExportType] = useState(null);

    const exportOptions = [
        {
            id: 'boleta',
            title: 'Boleta de Calificaciones',
            description: 'PDF individual por estudiante',
            icon: '📑',
            format: 'PDF'
        },
        {
            id: 'grades',
            title: 'Calificaciones por Curso',
            description: 'Excel con todas las notas',
            icon: '📊',
            format: 'Excel'
        },
        {
            id: 'students',
            title: 'Lista de Estudiantes',
            description: 'Excel con datos de estudiantes',
            icon: '👥',
            format: 'Excel'
        },
        {
            id: 'attendance',
            title: 'Reporte de Asistencia',
            description: 'PDF con porcentajes',
            icon: '📋',
            format: 'PDF'
        }
    ];

    const handleExport = async (type) => {
        setLoading(true);
        setExportType(type);

        // Simular datos (conectar con backend real)
        setTimeout(() => {
            switch (type) {
                case 'boleta':
                    exportBoletaPDF(
                        { name: 'Juan Pérez', grade: '3ero Básico', section: 'A', code: 'EST-001' },
                        [
                            { subject: 'Matemáticas', grade: 85 },
                            { subject: 'Lenguaje', grade: 78 },
                            { subject: 'Ciencias', grade: 92 },
                            { subject: 'Sociales', grade: 88 },
                            { subject: 'Inglés', grade: 75 },
                        ],
                        'Primer Bimestre 2026'
                    );
                    break;
                case 'grades':
                    exportGradesExcel(
                        [
                            { code: 'EST-001', name: 'Juan Pérez', subject: 'Matemáticas', grade: 85 },
                            { code: 'EST-002', name: 'María García', subject: 'Matemáticas', grade: 92 },
                            { code: 'EST-003', name: 'Carlos López', subject: 'Matemáticas', grade: 78 },
                        ],
                        '3ero_A',
                        '1er_Bimestre'
                    );
                    break;
                case 'students':
                    exportStudentListExcel(
                        [
                            { code: 'EST-001', name: 'Juan Pérez', grade: '3ero', section: 'A', email: 'juan@email.com', phone: '5555-1234' },
                            { code: 'EST-002', name: 'María García', grade: '3ero', section: 'A', email: 'maria@email.com', phone: '5555-5678' },
                        ],
                        '3ero_A'
                    );
                    break;
                case 'attendance':
                    exportAttendancePDF(
                        [
                            { name: 'Juan Pérez', present: 18, absent: 2, late: 1, percentage: 90 },
                            { name: 'María García', present: 20, absent: 0, late: 1, percentage: 100 },
                        ],
                        'Enero 2026',
                        '3ero A'
                    );
                    break;
            }
            setLoading(false);
            setExportType(null);
        }, 1000);
    };

    return (
        <div className="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
            <div className="max-w-4xl mx-auto">
                {/* Header */}
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        📤 Centro de Exportación
                    </h1>
                    <p className="text-gray-600 dark:text-gray-400 mt-1">
                        Genera reportes en PDF y Excel
                    </p>
                </div>

                {/* Export Options */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {exportOptions.map((option) => (
                        <button
                            key={option.id}
                            onClick={() => handleExport(option.id)}
                            disabled={loading}
                            className={`p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all text-left border-2 ${exportType === option.id
                                    ? 'border-blue-500 ring-2 ring-blue-200'
                                    : 'border-transparent hover:border-gray-200'
                                }`}
                        >
                            <div className="flex items-start gap-4">
                                <div className="text-4xl">{option.icon}</div>
                                <div className="flex-1">
                                    <h3 className="font-semibold text-lg text-gray-900 dark:text-white">
                                        {option.title}
                                    </h3>
                                    <p className="text-gray-500 dark:text-gray-400 text-sm">
                                        {option.description}
                                    </p>
                                    <span className={`inline-block mt-2 px-3 py-1 rounded-full text-xs font-medium ${option.format === 'PDF'
                                            ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                            : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        }`}>
                                        {option.format}
                                    </span>
                                </div>
                                {loading && exportType === option.id ? (
                                    <div className="w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full animate-spin" />
                                ) : (
                                    <svg className="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                )}
                            </div>
                        </button>
                    ))}
                </div>

                {/* Info */}
                <div className="mt-8 p-6 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-200 dark:border-blue-800">
                    <h3 className="font-semibold text-blue-900 dark:text-blue-100 mb-2">
                        💡 Consejo
                    </h3>
                    <p className="text-blue-700 dark:text-blue-300 text-sm">
                        Los archivos se descargarán automáticamente. Para exportar datos específicos,
                        primero filtra los datos en la página correspondiente y luego usa el botón de exportar.
                    </p>
                </div>
            </div>
        </div>
    );
};

export default ExportCenter;
