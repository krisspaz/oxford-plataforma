import React, { useState } from 'react';
import { Brain, Sparkles, Calendar, Check, AlertCircle, Zap, Rocket, Clock, Layers, Users } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';

const IAHorariosPage = () => {
    const { darkMode } = useTheme();
    const [generating, setGenerating] = useState(false);
    const [progress, setProgress] = useState(0);
    const [result, setResult] = useState(null);
    const [currentStep, setCurrentStep] = useState('Idle'); // Idle, Analyzing, Optimizing, Finalizing

    const handleGenerate = () => {
        setGenerating(true);
        setProgress(0);
        setResult(null);
        setCurrentStep('Analyzing');

        // Simulate AI generation process with steps
        const interval = setInterval(() => {
            setProgress(prev => {
                if (prev >= 100) {
                    clearInterval(interval);
                    setGenerating(false);
                    setResult(true);
                    return 100;
                }

                // Update Step Text based on progress
                if (prev > 30 && prev < 60) setCurrentStep('Optimizing');
                if (prev > 60) setCurrentStep('Finalizing');

                return prev + 2; // Increment progress
            });
        }, 100);
    };

    return (
        <div className="max-w-6xl mx-auto space-y-8">
            <div className={`relative overflow-hidden rounded-3xl p-10 ${darkMode ? 'bg-gradient-to-br from-indigo-900 via-purple-900 to-gray-900' : 'bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-500'} text-white shadow-2xl`}>
                <div className="absolute top-0 right-0 p-10 opacity-10">
                    <Brain size={300} />
                </div>

                <div className="relative z-10 flex flex-col md:flex-row items-center gap-8">
                    <div className="flex-1 space-y-4">
                        <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-sm font-medium">
                            <Sparkles size={16} /> powered by Oxford AI
                        </div>
                        <h1 className="text-4xl md:text-5xl font-extrabold tracking-tight">
                            Generador Inteligente de Horarios
                        </h1>
                        <p className="text-lg text-indigo-100 max-w-2xl">
                            Optimiza la asignación de recursos académicos en segundos. Nuestro algoritmo neuronal analiza millones de combinaciones para encontrar el equilibrio perfecto entre docentes, aulas y disponibilidad.
                        </p>
                    </div>
                    <div className="flex-shrink-0">
                        {!generating && !result && (
                            <button
                                onClick={handleGenerate}
                                className="group relative px-8 py-4 bg-white text-indigo-600 rounded-2xl font-bold text-lg shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300"
                            >
                                <span className="flex items-center gap-3">
                                    <Rocket size={24} className="group-hover:-translate-y-1 transition-transform" />
                                    Iniciar Generación
                                </span>
                            </button>
                        )}
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Configuration Panel */}
                <div className={`p-6 rounded-2xl border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} shadow-lg h-full`}>
                    <h3 className={`text-xl font-bold mb-6 flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                        <SettingsIcon darkMode={darkMode} /> Parámetros de Entrada
                    </h3>

                    <div className="space-y-4">
                        <MetricRow label="Docentes Activos" value="24" icon={Users} darkMode={darkMode} />
                        <MetricRow label="Grados/Secciones" value="15" icon={Layers} darkMode={darkMode} />
                        <MetricRow label="Aulas Disponibles" value="18" icon={GridIcon} darkMode={darkMode} />
                        <MetricRow label="Jornada" value="Matutina" icon={Clock} darkMode={darkMode} />
                    </div>

                    <div className={`mt-6 p-4 rounded-xl ${darkMode ? 'bg-gray-700/50' : 'bg-gray-50'} border ${darkMode ? 'border-gray-600' : 'border-gray-200'}`}>
                        <h4 className={`text-sm font-bold mb-2 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Restricciones Activas</h4>
                        <ul className={`space-y-2 text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                            <li className="flex items-center gap-2"><Check size={14} className="text-green-500" /> Sin horas libres intermedias</li>
                            <li className="flex items-center gap-2"><Check size={14} className="text-green-500" /> Prioridad materias prácticas</li>
                            <li className="flex items-center gap-2"><Check size={14} className="text-green-500" /> Balance de carga docente</li>
                        </ul>
                    </div>
                </div>

                {/* Main Action / Result Area */}
                <div className={`lg:col-span-2 p-8 rounded-2xl border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} shadow-lg flex flex-col items-center justify-center min-h-[400px]`}>

                    {!generating && !result && (
                        <div className="text-center space-y-4 opacity-50">
                            <Brain size={64} className="mx-auto text-gray-400" />
                            <p className="text-lg font-medium">Sistema listo para iniciar análisis.</p>
                        </div>
                    )}

                    {generating && (
                        <div className="w-full max-w-lg text-center space-y-8">
                            <div className="relative">
                                <div className="absolute inset-0 bg-indigo-500 blur-xl opacity-20 rounded-full animate-pulse"></div>
                                <Brain size={80} className="relative z-10 mx-auto text-indigo-500 animate-bounce" />
                            </div>

                            <div className="space-y-2">
                                <h2 className={`text-2xl font-bold animate-pulse ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                                    {currentStep === 'Analyzing' && 'Analizando disponibilidad...'}
                                    {currentStep === 'Optimizing' && 'Optimizando combinaciones...'}
                                    {currentStep === 'Finalizing' && 'Generando horarios finales...'}
                                </h2>
                                <div className="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700 overflow-hidden">
                                    <div
                                        className="bg-gradient-to-r from-indigo-500 to-purple-500 h-3 rounded-full transition-all duration-300 ease-out"
                                        style={{ width: `${progress}%` }}
                                    ></div>
                                </div>
                                <p className="text-sm text-gray-500 font-mono">{progress}% Completo</p>
                            </div>
                        </div>
                    )}

                    {result && (
                        <div className="w-full text-center space-y-8 animate-in fade-in zoom-in duration-500">
                            <div className="inline-flex p-6 rounded-full bg-green-100 text-green-600 mb-2 shadow-inner ring-8 ring-green-50 dark:ring-green-900/20 dark:bg-green-900/30 dark:text-green-400">
                                <Check size={64} />
                            </div>

                            <div className="space-y-2">
                                <h2 className={`text-3xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>¡Generación Completada!</h2>
                                <p className={`text-lg ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                                    Se ha generado el Ciclo Escolar 2025 con éxito.
                                </p>
                            </div>

                            <div className="grid grid-cols-3 gap-4 max-w-2xl mx-auto">
                                <ResultStat label="Horarios" value="65" darkMode={darkMode} />
                                <ResultStat label="Conflictos" value="0" darkMode={darkMode} highlight />
                                <ResultStat label="Eficiencia" value="98%" darkMode={darkMode} />
                            </div>

                            <div className="flex justify-center gap-4 pt-4">
                                <button className={`px-6 py-3 rounded-xl border font-bold transition-all ${darkMode ? 'border-gray-600 hover:bg-gray-700 text-gray-300' : 'border-gray-200 hover:bg-gray-50 text-gray-700'}`}>
                                    Descargar Reporte PDF
                                </button>
                                <button className="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition-transform hover:-translate-y-1">
                                    Ver Horarios Generados
                                </button>
                            </div>
                        </div>
                    )}

                </div>
            </div>
        </div>
    );
};

// Helper Components
const MetricRow = ({ label, value, icon: Icon, darkMode }) => (
    <div className={`flex items-center justify-between p-3 rounded-xl ${darkMode ? 'bg-gray-700/50' : 'bg-gray-50'}`}>
        <div className="flex items-center gap-3">
            <div className={`p-2 rounded-lg ${darkMode ? 'bg-gray-600 text-gray-300' : 'bg-white text-gray-500'} shadow-sm`}>
                <Icon size={18} />
            </div>
            <span className={`text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>{label}</span>
        </div>
        <span className={`font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{value}</span>
    </div>
);

const ResultStat = ({ label, value, darkMode, highlight }) => (
    <div className={`p-4 rounded-2xl border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} shadow-sm`}>
        <p className={`text-3xl font-bold mb-1 ${highlight ? 'text-green-500' : (darkMode ? 'text-white' : 'text-gray-900')}`}>{value}</p>
        <p className="text-xs text-gray-500 uppercase tracking-wider">{label}</p>
    </div>
);

const SettingsIcon = ({ darkMode }) => (
    <div className={`p-1.5 rounded-md ${darkMode ? 'bg-indigo-900/50 text-indigo-400' : 'bg-indigo-100 text-indigo-600'}`}>
        <Zap size={20} />
    </div>
);

const GridIcon = ({ size }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <rect x="3" y="3" width="7" height="7"></rect>
        <rect x="14" y="3" width="7" height="7"></rect>
        <rect x="14" y="14" width="7" height="7"></rect>
        <rect x="3" y="14" width="7" height="7"></rect>
    </svg>
);

export default IAHorariosPage;
