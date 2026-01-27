import React from 'react';
import { Construction, ArrowLeft, Clock, Sparkles } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { useTheme } from '../contexts/ThemeContext';

const ComingSoonPage = ({ title = "Módulo en Desarrollo" }) => {
    const { darkMode } = useTheme();
    const navigate = useNavigate();

    return (
        <div className={`min-h-[70vh] flex flex-col items-center justify-center p-8 text-center rounded-2xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-lg border ${darkMode ? 'border-gray-700' : 'bg-white border-gray-200 text-gray-900'}`}>
            {/* Animated Background Pattern */}
            <div className="relative">
                <div className={`absolute -inset-4 rounded-full blur-2xl opacity-30 ${darkMode ? 'bg-gradient-to-r from-teal-600 to-blue-600' : 'bg-gradient-to-r from-teal-400 to-blue-400'}`}></div>
                <div className={`relative p-6 rounded-full ${darkMode ? 'bg-gradient-to-br from-gray-700 to-gray-800' : 'bg-gradient-to-br from-gray-50 to-gray-100'} shadow-inner`}>
                    <div className={`p-4 rounded-full ${darkMode ? 'bg-gradient-to-r from-teal-600 to-blue-600' : 'bg-gradient-to-r from-teal-500 to-blue-500'}`}>
                        <Construction size={48} className="text-white" />
                    </div>
                </div>
            </div>

            <div className="mt-8 space-y-4 max-w-lg">
                <h1 className={`text-3xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                    {title}
                </h1>

                <div className="flex items-center justify-center gap-2 text-teal-500">
                    <Sparkles size={18} />
                    <span className="text-sm font-medium uppercase tracking-wider">En Desarrollo</span>
                    <Sparkles size={18} />
                </div>

                <p className={`text-lg ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                    Nuestro equipo está trabajando para traerte esta funcionalidad muy pronto.
                    ¡Gracias por tu paciencia!
                </p>

                {/* Progress indicator */}
                <div className={`mt-6 p-4 rounded-xl ${darkMode ? 'bg-gray-700/50' : 'bg-gray-50'} border ${darkMode ? 'border-gray-600' : 'bg-white border-gray-200 text-gray-900'}`}>
                    <div className="flex items-center justify-between mb-2">
                        <span className={`text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Progreso estimado</span>
                        <span className="text-sm font-bold text-teal-500">75%</span>
                    </div>
                    <div className={`h-2 rounded-full ${darkMode ? 'bg-gray-600' : 'bg-gray-200'} overflow-hidden`}>
                        <div className="h-full w-3/4 bg-gradient-to-r from-teal-500 to-blue-500 rounded-full transition-all duration-500"></div>
                    </div>
                </div>

                {/* Expected date */}
                <div className={`flex items-center justify-center gap-2 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                    <Clock size={16} />
                    <span className="text-sm">Disponible próximamente</span>
                </div>
            </div>

            <button
                onClick={() => navigate(-1)}
                className={`mt-8 flex items-center gap-2 px-6 py-3 rounded-xl font-medium transition-all hover:-translate-y-1 shadow-lg ${darkMode
                        ? 'bg-gradient-to-r from-teal-600 to-blue-600 hover:from-teal-500 hover:to-blue-500 text-white'
                        : 'bg-gradient-to-r from-teal-500 to-blue-500 hover:from-teal-600 hover:to-blue-600 text-white'
                    }`}
            >
                <ArrowLeft size={20} />
                Regresar
            </button>
        </div>
    );
};

export default ComingSoonPage;
