import React, { useState, useEffect, useRef } from 'react';
import { Calendar, Clock, Users, BookOpen, RefreshCw, Sparkles, Check, AlertCircle, Plus, Trash2, Download, Send, Bot, User, MessageCircle } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import axios from 'axios';

const HorariosPage = () => {
    const { darkMode } = useTheme();
    const [loading, setLoading] = useState(false);
    const [generating, setGenerating] = useState(false);
    const [selectedGrade, setSelectedGrade] = useState('');
    const [selectedSection, setSelectedSection] = useState('');
    const [generatedSchedule, setGeneratedSchedule] = useState(null);
    const [aiProgress, setAiProgress] = useState(0);
    const [aiStatus, setAiStatus] = useState('');

    // Chat state
    const [chatOpen, setChatOpen] = useState(true);
    const [messages, setMessages] = useState([
        {
            type: 'ai',
            text: '👋 ¡Hola! Soy tu asistente de horarios. Puedo ayudarte a:\n\n• Generar horarios automáticamente\n• Configurar restricciones de profesores\n• Ajustar horarios y recesos\n• Asignar materias por día\n\nEscribe "ayuda" para ver todos los comandos.',
            time: new Date()
        }
    ]);
    const [inputMessage, setInputMessage] = useState('');
    const [isSending, setIsSending] = useState(false);
    const chatEndRef = useRef(null);
    const [scheduleConfig, setScheduleConfig] = useState({
        startTime: '07:30',
        endTime: '13:00',
        classDuration: 45,
        hasRecess: true,
        recessStart: '10:10',
        recessEnd: '10:50'
    });

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

    // Scroll chat to bottom
    useEffect(() => {
        chatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    // Send message to AI
    const sendMessage = async () => {
        if (!inputMessage.trim() || isSending) return;

        const userMessage = inputMessage.trim();
        setInputMessage('');
        setIsSending(true);

        // Add user message
        setMessages(prev => [...prev, {
            type: 'user',
            text: userMessage,
            time: new Date()
        }]);

        try {
            // Call AI service
            const response = await axios.post('/api/ai/process-command', {
                text: userMessage,
                current_config: scheduleConfig
            });

            const aiResponse = response.data;

            // Apply config changes if any
            if (aiResponse.config_changes) {
                if (aiResponse.config_changes.clear) {
                    setScheduleConfig({
                        startTime: '07:30',
                        endTime: '13:00',
                        classDuration: 45,
                        hasRecess: true,
                        recessStart: '10:10',
                        recessEnd: '10:50'
                    });
                } else {
                    setScheduleConfig(prev => ({ ...prev, ...aiResponse.config_changes }));
                }
            }

            // Generate schedule if requested
            if (aiResponse.should_generate) {
                await generateWithAI();
            }

            // Add AI response
            setMessages(prev => [...prev, {
                type: 'ai',
                text: aiResponse.response_text,
                intent: aiResponse.intent,
                confidence: aiResponse.confidence,
                time: new Date()
            }]);

        } catch (error) {
            console.error('AI Error:', error);

            // Fallback - local processing
            let responseText = processLocalCommand(userMessage);

            setMessages(prev => [...prev, {
                type: 'ai',
                text: responseText,
                time: new Date()
            }]);
        } finally {
            setIsSending(false);
        }
    };

    // Local command processing (fallback)
    const processLocalCommand = (text) => {
        const lower = text.toLowerCase();

        if (lower.includes('generar') || lower.includes('genera') || lower.includes('crear')) {
            generateWithAI();
            return '🔄 **Generando horario...**';
        }
        if (lower.includes('quita') && lower.includes('receso')) {
            setScheduleConfig(prev => ({ ...prev, hasRecess: false }));
            return '✅ **Receso eliminado.** El horario será continuo.\n\nEscribe "generar" para crear el horario.';
        }
        if (lower.includes('agrega') && lower.includes('receso')) {
            setScheduleConfig(prev => ({ ...prev, hasRecess: true }));
            return '✅ **Receso agregado.**\n\nEscribe "generar" para crear el horario.';
        }
        if (lower.includes('ayuda') || lower.includes('help')) {
            return `🤖 **Comandos disponibles:**

⏰ **Horarios:** "De 8 a 2 de la tarde, clases de 45 min"
🚫 **Receso:** "Quita el receso" / "Agrega receso"
📅 **Días:** "Miércoles solo física, arte y música"
👨‍🏫 **Profes:** "Prof. García no puede los lunes"
⚡ **Generar:** "Genera el horario"
🗑️ **Limpiar:** "Reiniciar todo"`;
        }
        if (lower.includes('hola') || lower.includes('hi')) {
            return '👋 ¡Hola! ¿En qué te puedo ayudar con los horarios?';
        }
        if (lower.includes('limpiar') || lower.includes('reiniciar')) {
            setScheduleConfig({
                startTime: '07:30',
                endTime: '13:00',
                classDuration: 45,
                hasRecess: true,
                recessStart: '10:10',
                recessEnd: '10:50'
            });
            setGeneratedSchedule(null);
            return '🗑️ **Todo limpio.** Configuración reiniciada.';
        }

        return '❓ No entendí. Escribe "ayuda" para ver comandos disponibles.';
    };

    // Simulate AI schedule generation
    const generateWithAI = async () => {
        if (!selectedGrade || !selectedSection) {
            setMessages(prev => [...prev, {
                type: 'ai',
                text: '⚠️ Por favor selecciona un grado y sección primero.',
                time: new Date()
            }]);
            return;
        }

        setGenerating(true);
        setAiProgress(0);
        setAiStatus('Analizando carga docente...');

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
            await new Promise(resolve => setTimeout(resolve, 500));
            setAiProgress(step.progress);
            setAiStatus(step.status);
        }

        // Generate schedule
        const schedule = {};
        days.forEach(day => {
            schedule[day] = {};
            periods.forEach(period => {
                if (!period.isBreak || scheduleConfig.hasRecess) {
                    if (period.isBreak) {
                        schedule[day][period.id] = { name: 'RECESO', color: 'bg-yellow-500', teacher: '' };
                    } else {
                        const randomSubject = subjects[Math.floor(Math.random() * subjects.length)];
                        schedule[day][period.id] = randomSubject;
                    }
                }
            });
        });

        setGeneratedSchedule(schedule);
        setGenerating(false);

        setMessages(prev => [...prev, {
            type: 'ai',
            text: `✅ **¡Horario generado!**\n\nGrado: ${grades.find(g => g.id === parseInt(selectedGrade))?.name} "${selectedSection}"\nPeriodos: ${periods.filter(p => !p.isBreak).length}\nReceso: ${scheduleConfig.hasRecess ? 'Sí' : 'No'}`,
            time: new Date()
        }]);
    };

    const saveSchedule = () => {
        alert('✅ Horario guardado exitosamente');
    };

    const exportSchedule = () => {
        alert('📄 Exportando horario a PDF...');
    };

    // Quick command buttons
    const quickCommands = [
        { label: 'Generar horario', command: 'Genera el horario' },
        { label: 'Sin receso', command: 'Quita el receso' },
        { label: 'Con receso', command: 'Agrega receso' },
        { label: 'Ayuda', command: 'ayuda' },
    ];

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Gestión de Horarios con IA</h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>Habla con la IA para crear y modificar horarios</p>
                </div>
                <button
                    onClick={() => setChatOpen(!chatOpen)}
                    className={`flex items-center gap-2 px-4 py-2 rounded-lg ${chatOpen ? 'bg-purple-600 text-white' : darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-700'}`}
                >
                    <MessageCircle size={18} />
                    {chatOpen ? 'Ocultar Chat' : 'Mostrar Chat IA'}
                </button>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Left Column - Config & Schedule */}
                <div className={`lg:col-span-2 space-y-6`}>
                    {/* Config Card */}
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-6 shadow-sm`}>
                        <h2 className={`text-lg font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Configuración</h2>
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label className={labelClass}>Grado</label>
                                <select className={inputClass} value={selectedGrade} onChange={e => setSelectedGrade(e.target.value)}>
                                    <option value="">Seleccionar...</option>
                                    {grades.map(g => <option key={g.id} value={g.id}>{g.name}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className={labelClass}>Sección</label>
                                <select className={inputClass} value={selectedSection} onChange={e => setSelectedSection(e.target.value)}>
                                    <option value="">Seleccionar...</option>
                                    {sections.map(s => <option key={s} value={s}>Sección {s}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className={labelClass}>Hora Inicio</label>
                                <input type="time" className={inputClass} value={scheduleConfig.startTime} onChange={e => setScheduleConfig(prev => ({ ...prev, startTime: e.target.value }))} />
                            </div>
                            <div>
                                <label className={labelClass}>Hora Fin</label>
                                <input type="time" className={inputClass} value={scheduleConfig.endTime} onChange={e => setScheduleConfig(prev => ({ ...prev, endTime: e.target.value }))} />
                            </div>
                        </div>
                        <div className="flex items-center gap-4 mt-4">
                            <label className="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    checked={scheduleConfig.hasRecess}
                                    onChange={e => setScheduleConfig(prev => ({ ...prev, hasRecess: e.target.checked }))}
                                    className="w-4 h-4 rounded text-purple-600"
                                />
                                <span className={darkMode ? 'text-gray-300' : 'text-gray-700'}>Incluir receso</span>
                            </label>
                            <span className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                Duración clase: {scheduleConfig.classDuration} min
                            </span>
                        </div>
                    </div>

                    {/* Progress */}
                    {generating && (
                        <div className={`${darkMode ? 'bg-purple-900/30 border-purple-700' : 'bg-purple-50 border-purple-200'} border rounded-xl p-6`}>
                            <div className="flex items-center gap-3 mb-4">
                                <RefreshCw className="animate-spin text-purple-500" size={24} />
                                <span className={`font-medium ${darkMode ? 'text-purple-300' : 'text-purple-700'}`}>{aiStatus}</span>
                            </div>
                            <div className={`w-full h-3 rounded-full ${darkMode ? 'bg-gray-700' : 'bg-gray-200'}`}>
                                <div className="h-full bg-gradient-to-r from-purple-500 to-blue-500 rounded-full transition-all duration-500" style={{ width: `${aiProgress}%` }} />
                            </div>
                        </div>
                    )}

                    {/* Generated Schedule */}
                    {generatedSchedule && (
                        <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-6 shadow-sm`}>
                            <div className="flex items-center justify-between mb-4">
                                <h2 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                    Horario: {grades.find(g => g.id === parseInt(selectedGrade))?.name} "{selectedSection}"
                                </h2>
                                <div className="flex gap-2">
                                    <button onClick={exportSchedule} className={`px-3 py-1.5 rounded-lg text-sm ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-700'}`}>
                                        <Download size={16} className="inline mr-1" />PDF
                                    </button>
                                    <button onClick={saveSchedule} className="px-3 py-1.5 rounded-lg text-sm bg-green-600 text-white">
                                        <Check size={16} className="inline mr-1" />Guardar
                                    </button>
                                </div>
                            </div>
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr>
                                            <th className={`p-2 text-left border ${darkMode ? 'border-gray-700 bg-gray-700/50' : 'border-gray-200 bg-gray-50'}`}>Hora</th>
                                            {days.map(day => (
                                                <th key={day} className={`p-2 text-center border ${darkMode ? 'border-gray-700 bg-gray-700/50' : 'border-gray-200 bg-gray-50'}`}>{day}</th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {periods.map(period => (
                                            <tr key={period.id}>
                                                <td className={`p-2 border text-xs ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                                                    <div className="font-medium">{period.time}</div>
                                                </td>
                                                {days.map(day => (
                                                    <td key={`${day}-${period.id}`} className={`p-1 border ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                                                        {generatedSchedule[day]?.[period.id] && (
                                                            <div className={`p-1.5 rounded text-white text-xs ${generatedSchedule[day][period.id].color}`}>
                                                                <div className="font-bold truncate">{generatedSchedule[day][period.id].name}</div>
                                                                {generatedSchedule[day][period.id].teacher && (
                                                                    <div className="opacity-80 truncate">{generatedSchedule[day][period.id].teacher}</div>
                                                                )}
                                                            </div>
                                                        )}
                                                    </td>
                                                ))}
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    )}
                </div>

                {/* Right Column - AI Chat */}
                {chatOpen && (
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm flex flex-col h-[600px]`}>
                        {/* Chat Header */}
                        <div className={`p-4 border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'} flex items-center gap-3`}>
                            <div className="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 flex items-center justify-center">
                                <Bot className="text-white" size={20} />
                            </div>
                            <div>
                                <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Asistente IA</h3>
                                <p className={`text-xs ${darkMode ? 'text-green-400' : 'text-green-600'}`}>● En línea</p>
                            </div>
                        </div>

                        {/* Chat Messages */}
                        <div className="flex-1 overflow-y-auto p-4 space-y-4">
                            {messages.map((msg, idx) => (
                                <div key={idx} className={`flex ${msg.type === 'user' ? 'justify-end' : 'justify-start'}`}>
                                    <div className={`max-w-[85%] p-3 rounded-2xl ${msg.type === 'user'
                                            ? 'bg-purple-600 text-white rounded-br-md'
                                            : darkMode
                                                ? 'bg-gray-700 text-gray-200 rounded-bl-md'
                                                : 'bg-gray-100 text-gray-800 rounded-bl-md'
                                        }`}>
                                        <div className="whitespace-pre-wrap text-sm">{msg.text}</div>
                                        <div className={`text-xs mt-1 ${msg.type === 'user' ? 'text-purple-200' : darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                            {msg.time.toLocaleTimeString('es', { hour: '2-digit', minute: '2-digit' })}
                                        </div>
                                    </div>
                                </div>
                            ))}
                            {isSending && (
                                <div className="flex justify-start">
                                    <div className={`p-3 rounded-2xl rounded-bl-md ${darkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>
                                        <div className="flex gap-1">
                                            <span className="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style={{ animationDelay: '0ms' }}></span>
                                            <span className="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style={{ animationDelay: '150ms' }}></span>
                                            <span className="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style={{ animationDelay: '300ms' }}></span>
                                        </div>
                                    </div>
                                </div>
                            )}
                            <div ref={chatEndRef} />
                        </div>

                        {/* Quick Commands */}
                        <div className={`px-4 py-2 border-t ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                            <div className="flex gap-2 overflow-x-auto pb-2">
                                {quickCommands.map((cmd, idx) => (
                                    <button
                                        key={idx}
                                        onClick={() => { setInputMessage(cmd.command); }}
                                        className={`px-3 py-1 rounded-full text-xs whitespace-nowrap ${darkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`}
                                    >
                                        {cmd.label}
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* Chat Input */}
                        <div className={`p-4 border-t ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                            <div className="flex gap-2">
                                <input
                                    type="text"
                                    value={inputMessage}
                                    onChange={e => setInputMessage(e.target.value)}
                                    onKeyDown={e => e.key === 'Enter' && sendMessage()}
                                    placeholder="Escribe un mensaje..."
                                    className={`flex-1 px-4 py-2 rounded-full border ${darkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'border-gray-300 placeholder-gray-400'} focus:ring-2 focus:ring-purple-500 outline-none`}
                                />
                                <button
                                    onClick={sendMessage}
                                    disabled={isSending || !inputMessage.trim()}
                                    className="w-10 h-10 rounded-full bg-purple-600 hover:bg-purple-700 text-white flex items-center justify-center disabled:opacity-50"
                                >
                                    <Send size={18} />
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default HorariosPage;
