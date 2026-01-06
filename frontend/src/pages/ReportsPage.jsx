import React from 'react';
import { FileText, Download } from 'lucide-react';

const ReportsPage = () => {
    const reports = [
        { id: 1, name: 'Reporte de Asistencia Mensual', type: 'PDF' },
        { id: 2, name: 'Rendimiento Académico por Grado', type: 'Excel' },
        { id: 3, name: 'Listado de Solvencia Financiera', type: 'PDF' },
    ];

    return (
        <div className="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
            <h1 className="text-2xl font-bold mb-6 text-gray-800 dark:text-white flex items-center gap-2">
                <FileText /> Centro de Reportes
            </h1>

            <div className="grid gap-4">
                {reports.map((report) => (
                    <div key={report.id} className="flex justify-between items-center p-4 border rounded hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div>
                            <h3 className="font-semibold">{report.name}</h3>
                            <span className="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded">{report.type}</span>
                        </div>
                        <button className="text-indigo-600 hover:text-indigo-800">
                            <Download size={20} />
                        </button>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default ReportsPage;
