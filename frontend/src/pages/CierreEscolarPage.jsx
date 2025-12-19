import React from 'react';
import { Archive, AlertTriangle } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';

const CierreEscolarPage = () => {
    const { darkMode } = useTheme();

    return (
        <div className="p-6">
            <h1 className={`text-2xl font-bold mb-6 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Cierre Escolar</h1>

            <div className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white'} rounded-xl shadow-sm border p-8 text-center`}>
                <div className="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <Archive size={40} className="text-orange-500" />
                </div>

                <h2 className={`text-xl font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-900'}`}>Asistente de Cierre de Ciclo</h2>
                <p className={`max-w-md mx-auto mb-8 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                    Esta herramienta permite migrar los datos al siguiente ciclo escolar, archivar notas e historiales, y reiniciar estados de cuentas.
                </p>

                <div className={`p-4 rounded-lg bg-yellow-50 border border-yellow-200 text-left max-w-2xl mx-auto mb-8`}>
                    <div className="flex gap-3">
                        <AlertTriangle className="text-yellow-600 flex-shrink-0" />
                        <div>
                            <h3 className="font-bold text-yellow-800">Precaución</h3>
                            <p className="text-yellow-700 text-sm mt-1">
                                Este proceso es irreversible. Se recomienda realizar una copia de seguridad completa de la base de datos antes de proceder.
                            </p>
                        </div>
                    </div>
                </div>

                <button className="px-6 py-3 bg-gray-300 text-gray-600 font-bold rounded-lg cursor-not-allowed" disabled>
                    Iniciar Cierre (Próximamente)
                </button>
            </div>
        </div>
    );
};

export default CierreEscolarPage;
