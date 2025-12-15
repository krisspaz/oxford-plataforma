import React from 'react';
import { Download, FileText } from 'lucide-react';
import axios from 'axios';

const ReportsPage = () => {
    const handleDownload = (type) => {
        alert(`Iniciando descarga de ${type}... (Simulado)`);
        // axios.get(...)
    };

    return (
        <div className="p-6">
            <h1 className="text-2xl font-bold mb-6 text-gray-800">Reportes y Boletas</h1>

            <div className="grid md:grid-cols-2 gap-6">
                {/* Grades */}
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div className="flex items-center gap-4 mb-4">
                        <div className="p-3 bg-purple-100 text-purple-600 rounded-lg">
                            <FileText size={24} />
                        </div>
                        <div>
                            <h3 className="font-bold text-lg">Boletas de Calificaciones</h3>
                            <p className="text-gray-500 text-sm">Descarga por grado o estudiante</p>
                        </div>
                    </div>
                    <button
                        onClick={() => handleDownload('boletas')}
                        className="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg transition-colors flex items-center justify-center gap-2"
                    >
                        <Download size={18} /> Descargar Boletas
                    </button>
                </div>

                {/* Certificates */}
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div className="flex items-center gap-4 mb-4">
                        <div className="p-3 bg-blue-100 text-blue-600 rounded-lg">
                            <FileText size={24} />
                        </div>
                        <div>
                            <h3 className="font-bold text-lg">Cuadros Finales</h3>
                            <p className="text-gray-500 text-sm">Consolidado para MINEDUC</p>
                        </div>
                    </div>
                    <button
                        onClick={() => handleDownload('cuadros')}
                        className="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition-colors flex items-center justify-center gap-2"
                    >
                        <Download size={18} /> Descargar Cuadros
                    </button>
                </div>
            </div>
        </div>
    );
};

export default ReportsPage;
