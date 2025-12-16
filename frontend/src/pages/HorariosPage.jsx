import React, { useState, useEffect } from 'react';
import { Calendar, Clock, Users, BookOpen, RefreshCw, Sparkles, Check, AlertCircle, Plus, Trash2, Download } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';

const HorariosPage = () => {
    const { darkMode } = useTheme();
    const [loading, setLoading] = useState(false);
    const [generating, setGenerating] = useState(false);
    const [selectedGrade, setSelectedGrade] = useState('');
    const [selectedSection, setSelectedSection] = useState('');
    const [generatedSchedule, setGeneratedSchedule] = useState(null);
    const [aiProgress, setAiProgress] = useState(0);
    const [aiStatus, setAiStatus] = useState('');

    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    // Demo data
    const grades = [
        { id: 1, name: 'Kinder' },
        { id: 2, name: 'Preparatoria' },
        { id: 3, name: '1ro Primaria' },
        { id: 4, name: '2do Primaria' },
        { id: 5, name: '3ro Primaria' },
        { id: 6, name: '4to Primaria' },
        { id: 7, name: '5to Primaria' },
        { id: 8, name: '6to Primaria' },
        { id: 9, name: '1ro Básico' },
        { id: 10, name: '2do Básico' },
        { id: 11, name: '3ro Básico' },
    ];

    const sections = ['A', 'B'];
    const days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
    const periods = [
        { id: 1, time: '07:30 - 08:15', label: '1er Período' },
        { id: 2, time: '08:15 - 09:00', label: '2do Período' },
        { id: 3, time: '09:00 - 09:45', label: '3er Período' },
        { id: 4, time: '09:45 - 10:15', label: 'Receso', isBreak: true },
        { id: 5, time: '10:15 - 11:00', label: '4to Período' },
        { id: 6, time: '11:00 - 11:45', label: '5to Período' },
        { id: 7, time: '11:45 - 12:30', label: '6to Período' },
    ];

    const subjects = [
        { id: 1, name: 'Matemáticas', color: 'bg-blue-500', teacher: 'Prof. García' },
        { id: 2, name: 'Comunicación y Lenguaje', color: 'bg-green-500', teacher: 'Prof. López' },
        { id: 3, name: 'Ciencias Naturales', color: 'bg-purple-500', teacher: 'Prof. Martínez' },
        { id: 4, name: 'Estudios Sociales', color: 'bg-orange-500', teacher: 'Prof. Hernández' },
        { id: 5, name: 'Inglés', color: 'bg-red-500', teacher: 'Prof. Smith' },
        { id: 6, name: 'Educación Física', color: 'bg-teal-500', teacher: 'Prof. Pérez' },
        { id: 7, name: 'Arte y Música', color: 'bg-pink-500', teacher: 'Prof. Ramírez' },
        { id: 8, name: 'Computación', color: 'bg-indigo-500', teacher: 'Prof. Castillo' },
    ];

    // Simulate AI schedule generation
    const generateWithAI = async () => {
        if (!selectedGrade || !selectedSection) {
            alert('Por favor seleccione grado y sección');
            return;
        }

        setGenerating(true);
        setAiProgress(0);
        setAiStatus('Analizando carga docente...');

        // Simulate AI processing steps
        const steps = [
            { progress: 15, status: 'Verificando disponibilidad de docentes...' },
            { progress: 30, status: 'Analizando restricciones de horario...' },
            { progress: 45, status: 'Optimizando distribución de materias...' },
            { progress: 60, status: 'Evitando conflictos de aulas...' },
            { progress: 75, status: 'Balanceando carga semanal...' },
            { progress: 90, status: 'Validando horario generado...' },
            { progress: 100, status: '¡Horario generado exitosamente!' },
        ];

        for (const step of steps) {
            await new Promise(resolve => setTimeout(resolve, 600));
            setAiProgress(step.progress);
            setAiStatus(step.status);
        }

        // Generate random schedule
        const schedule = {};
        days.forEach(day => {
            schedule[day] = {};
            periods.forEach(period => {
                if (!period.isBreak) {
                    const randomSubject = subjects[Math.floor(Math.random() * subjects.length)];
                    schedule[day][period.id] = randomSubject;
                }
            });
        });

        setGeneratedSchedule(schedule);
        setGenerating(false);
    };

    const saveSchedule = () => {
        alert('✅ Horario guardado exitosamente');
    };

    const exportSchedule = () => {
        alert('📄 Exportando horario a PDF...');
    };

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Gestión de Horarios</h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>Generación inteligente de horarios con IA</p>
                </div>
            </div>

            {/* AI Generator Card */}
            <div className={`${darkMode ? 'bg-gradient-to-r from-purple-900/50 to-blue-900/50 border-purple-700' : 'bg-gradient-to-r from-purple-50 to-blue-50 border-purple-200'} border rounded-2xl p-6`}>
                <div className="flex items-center gap-3 mb-4">
                    <div className={`p-3 rounded-xl ${darkMode ? 'bg-purple-500/30' : 'bg-purple-100'}`}>
                        <Sparkles className="text-purple-500" size={24} />
                    </div>
                    <div>
                        <h2 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Generador de Horarios con IA</h2>
                        <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                            La IA analiza docentes, materias y restricciones para crear el horario óptimo
                        </p>
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label className={labelClass}>Grado *</label>
                        <select
                            className={inputClass}
                            value={selectedGrade}
                            onChange={e => setSelectedGrade(e.target.value)}
                        >
                            <option value="">Seleccionar grado...</option>
                            {grades.map(g => <option key={g.id} value={g.id}>{g.name}</option>)}
                        </select>
                    </div>
                    <div>
                        <label className={labelClass}>Sección *</label>
                        <select
                            className={inputClass}
                            value={selectedSection}
                            onChange={e => setSelectedSection(e.target.value)}
                        >
                            <option value="">Seleccionar sección...</option>
                            {sections.map(s => <option key={s} value={s}>Sección {s}</option>)}
                        </select>
                    </div>
                    <div className="flex items-end">
                        <button
                            onClick={generateWithAI}
                            disabled={generating || !selectedGrade || !selectedSection}
                            className={`w-full flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg font-medium transition-all ${generating
                                    ? 'bg-purple-600 text-white cursor-wait'
                                    : 'bg-purple-600 hover:bg-purple-700 text-white'
                                } disabled:opacity-50`}
                        >
                            {generating ? (
                                <>
                                    <RefreshCw size={18} className="animate-spin" />
                                    Generando...
                                </>
                            ) : (
                                <>
                                    <Sparkles size={18} />
                                    Generar con IA
                                </>
                            )}
                        </button>
                    </div>
                </div>

                {/* AI Progress */}
                {generating && (
                    <div className="mt-6">
                        <div className="flex items-center justify-between mb-2">
                            <span className={`text-sm font-medium ${darkMode ? 'text-purple-300' : 'text-purple-700'}`}>
                                {aiStatus}
                            </span>
                            <span className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                                {aiProgress}%
                            </span>
                        </div>
                        <div className={`w-full h-3 rounded-full ${darkMode ? 'bg-gray-700' : 'bg-gray-200'}`}>
                            <div
                                className="h-full bg-gradient-to-r from-purple-500 to-blue-500 rounded-full transition-all duration-500"
                                style={{ width: `${aiProgress}%` }}
                            />
                        </div>
                    </div>
                )}
            </div>

            {/* Generated Schedule */}
            {generatedSchedule && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-6 shadow-sm`}>
                    <div className="flex items-center justify-between mb-6">
                        <div>
                            <h2 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                Horario Generado: {grades.find(g => g.id === parseInt(selectedGrade))?.name} "{selectedSection}"
                            </h2>
                            <p className={`text-sm ${darkMode ? 'text-green-400' : 'text-green-600'}`}>
                                <Check size={14} className="inline mr-1" />
                                Horario optimizado por IA - Sin conflictos detectados
                            </p>
                        </div>
                        <div className="flex gap-2">
                            <button
                                onClick={exportSchedule}
                                className={`flex items-center gap-2 px-4 py-2 rounded-lg ${darkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'}`}
                            >
                                <Download size={18} />
                                Exportar PDF
                            </button>
                            <button
                                onClick={saveSchedule}
                                className="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg"
                            >
                                <Check size={18} />
                                Guardar Horario
                            </button>
                        </div>
                    </div>

                    {/* Schedule Grid */}
                    <div className="overflow-x-auto">
                        <table className="w-full border-collapse">
                            <thead>
                                <tr>
                                    <th className={`p-3 text-left border ${darkMode ? 'border-gray-700 bg-gray-700/50' : 'border-gray-200 bg-gray-50'}`}>
                                        Hora
                                    </th>
                                    {days.map(day => (
                                        <th key={day} className={`p-3 text-center border ${darkMode ? 'border-gray-700 bg-gray-700/50' : 'border-gray-200 bg-gray-50'}`}>
                                            {day}
                                        </th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody>
                                {periods.map(period => (
                                    <tr key={period.id}>
                                        <td className={`p-3 border ${darkMode ? 'border-gray-700' : 'border-gray-200'} ${period.isBreak ? (darkMode ? 'bg-yellow-900/20' : 'bg-yellow-50') : ''}`}>
                                            <div className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{period.time}</div>
                                            <div className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{period.label}</div>
                                        </td>
                                        {days.map(day => (
                                            <td key={`${day}-${period.id}`} className={`p-2 border ${darkMode ? 'border-gray-700' : 'border-gray-200'} ${period.isBreak ? (darkMode ? 'bg-yellow-900/20' : 'bg-yellow-50') : ''}`}>
                                                {period.isBreak ? (
                                                    <div className="text-center text-yellow-600 text-sm font-medium">🍽️ Receso</div>
                                                ) : generatedSchedule[day]?.[period.id] ? (
                                                    <div className={`p-2 rounded-lg ${generatedSchedule[day][period.id].color} text-white text-xs`}>
                                                        <div className="font-bold">{generatedSchedule[day][period.id].name}</div>
                                                        <div className="opacity-80">{generatedSchedule[day][period.id].teacher}</div>
                                                    </div>
                                                ) : null}
                                            </td>
                                        ))}
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Legend */}
                    <div className="mt-6 flex flex-wrap gap-3">
                        <span className={`text-sm font-medium ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>Materias:</span>
                        {subjects.map(subject => (
                            <div key={subject.id} className="flex items-center gap-2">
                                <div className={`w-3 h-3 rounded ${subject.color}`}></div>
                                <span className={`text-xs ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>{subject.name}</span>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Info Cards */}
            {!generatedSchedule && (
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div className={`p-6 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-sm`}>
                        <div className={`w-12 h-12 rounded-xl flex items-center justify-center mb-4 ${darkMode ? 'bg-blue-900/50' : 'bg-blue-100'}`}>
                            <Users className="text-blue-500" size={24} />
                        </div>
                        <h3 className={`font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Disponibilidad Docente</h3>
                        <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                            La IA considera la disponibilidad y carga horaria de cada docente.
                        </p>
                    </div>

                    <div className={`p-6 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-sm`}>
                        <div className={`w-12 h-12 rounded-xl flex items-center justify-center mb-4 ${darkMode ? 'bg-green-900/50' : 'bg-green-100'}`}>
                            <BookOpen className="text-green-500" size={24} />
                        </div>
                        <h3 className={`font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Distribución Óptima</h3>
                        <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                            Materias pesadas en la mañana, prácticas después del receso.
                        </p>
                    </div>

                    <div className={`p-6 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-sm`}>
                        <div className={`w-12 h-12 rounded-xl flex items-center justify-center mb-4 ${darkMode ? 'bg-purple-900/50' : 'bg-purple-100'}`}>
                            <AlertCircle className="text-purple-500" size={24} />
                        </div>
                        <h3 className={`font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Sin Conflictos</h3>
                        <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                            Validación automática de conflictos de aulas y docentes.
                        </p>
                    </div>
                </div>
            )}
        </div>
    );
};

export default HorariosPage;
