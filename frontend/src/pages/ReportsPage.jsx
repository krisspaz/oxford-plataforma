import React from 'react';
import { Download, FileText } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';

const ReportsPage = () => {
    const { darkMode } = useTheme();

    const handleDownload = (type) => {
        alert(`Iniciando descarga de ${type}... (Simulado)`);
    };

    return (
        <div className="p-6">
            <h1 className={`text-2xl font-bold mb-6 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Reportes y Boletas</h1>

            <div className="grid md:grid-cols-2 gap-6">
                {/* Grades */}
                <div className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} p-6 rounded-xl shadow-sm border`}>
                    <div className="flex items-center gap-4 mb-4">
                        <div className={`p-3 ${darkMode ? 'bg-purple-900/50' : 'bg-purple-100'} text-purple-500 rounded-lg`}>
                            <FileText size={24} />
                        </div>
                        <div>
                            <h3 className={`font-bold text-lg ${darkMode ? 'text-white' : 'text-gray-900'}`}>Boletas de Calificaciones</h3>
                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Descarga por grado o estudiante</p>
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
                <div className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} p-6 rounded-xl shadow-sm border`}>
                    <div className="flex items-center gap-4 mb-4">
                        <div className={`p-3 ${darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} text-blue-500 rounded-lg`}>
                            <FileText size={24} />
                        </div>
                        <div>
                            <h3 className={`font-bold text-lg ${darkMode ? 'text-white' : 'text-gray-900'}`}>Cuadros Finales</h3>
                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Consolidado para MINEDUC</p>
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
