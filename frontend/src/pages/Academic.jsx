import { toast } from '../utils/toast';
import { useState, useRef, useEffect } from 'react';
import axios from 'axios';
import {
    Sparkles,
    Send,
    Download,
    Clock,
    CheckCircle,
    Bot,
    FileText,
    Calendar,
    Settings,
    X,
    Save,
    Loader2
} from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';

const AI_API_URL = '/ai';

const Academic = () => {
    const { darkMode } = useTheme();
    const [config, setConfig] = useState({
        startTime: '07:30',
        endTime: '13:00',
        classDuration: 45,
        recessStart: '10:10',
        recessEnd: '10:50',
        hasRecess: true
    });
    const [showConfig, setShowConfig] = useState(false);

    const [messages, setMessages] = useState([
        {
            type: 'bot',
            text: '¡Hola! Soy tu asistente IA de horarios con procesamiento de lenguaje natural.\n\nPuedo entender muchos comandos naturales:\n• "Horario de 8 a 2, clases de 1 hora"\n• "Quita el receso"\n• "Miércoles solo física, arte y música"\n• "Pre-kinder y Kinder mismo horario"\n• "Generar horario"\n\n¡Dime qué necesitas!'
        }
    ]);
    const [input, setInput] = useState('');
    const [schedule, setSchedule] = useState(null);
    const [loading, setLoading] = useState(false);
    const [aiProcessing, setAiProcessing] = useState(false);
    const [selectedGrade, setSelectedGrade] = useState('Pre-Kinder');
    const [gradeGroups, setGradeGroups] = useState([]);
    const [dayRules, setDayRules] = useState({});
    const [teacherRestrictions, setTeacherRestrictions] = useState({});
    const chatEndRef = useRef(null);

    const days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
    const grades = [
        'Pre-Kinder', 'Kinder', 'Preparatoria',
        '1ro Primaria', '2do Primaria', '3ro Primaria', '4to Primaria', '5to Primaria', '6to Primaria',
        '1ro Básico', '2do Básico', '3ro Básico',
        '4to Bachillerato', '5to Bachillerato'
    ];

    const colors = {
        'Matemáticas': 'bg-blue-100 text-blue-800 border-blue-200',
        'Español': 'bg-green-100 text-green-800 border-green-200',
        'Ciencias': 'bg-purple-100 text-purple-800 border-purple-200',
        'Inglés': 'bg-red-100 text-red-800 border-red-200',
        'Historia': 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'Ed. Física': 'bg-orange-100 text-orange-800 border-orange-200',
        'Arte': 'bg-pink-100 text-pink-800 border-pink-200',
        'Música': 'bg-indigo-100 text-indigo-800 border-indigo-200',
        'Computación': 'bg-cyan-100 text-cyan-800 border-cyan-200',
        'Física': 'bg-teal-100 text-teal-800 border-teal-200',
        'Química': 'bg-lime-100 text-lime-800 border-lime-200',
        'Biología': 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'RECESO': 'bg-amber-200 text-amber-900 border-amber-300',
    };

    const generateTimeSlots = () => {
        const slots = [];
        const parseTime = (t) => {
            const [h, m] = t.split(':').map(Number);
            return h * 60 + (m || 0);
        };
        const formatTime = (mins) => {
            const h = Math.floor(mins / 60);
            const m = mins % 60;
            return `${h}:${m.toString().padStart(2, '0')}`;
        };

        let current = parseTime(config.startTime);
        const end = parseTime(config.endTime);
        const recessStart = parseTime(config.recessStart);
        const recessEnd = parseTime(config.recessEnd);

        while (current < end) {
            if (config.hasRecess && current >= recessStart && current < recessEnd) {
                slots.push({
                    label: `${formatTime(recessStart)} - ${formatTime(recessEnd)}`,
                    start: recessStart,
                    end: recessEnd,
                    isRecess: true
                });
                current = recessEnd;
                continue;
            }

            let slotEnd = current + config.classDuration;
            if (config.hasRecess && current < recessStart && slotEnd > recessStart) {
                slotEnd = recessStart;
            }
            if (slotEnd > end) slotEnd = end;

            slots.push({
                label: `${formatTime(current)} - ${formatTime(slotEnd)}`,
                start: current,
                end: slotEnd,
                isRecess: false
            });

            current = slotEnd;
        }

        return slots;
    };

    const timeSlots = generateTimeSlots();

    useEffect(() => {
        chatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    const handleSend = async () => {
        if (!input.trim() || aiProcessing) return;

        const userMessage = input.trim();
        setMessages(prev => [...prev, { type: 'user', text: userMessage }]);
        setInput('');
        setAiProcessing(true);

        try {
            // Call Python AI backend
            const response = await axios.post(`${AI_API_URL}/process-command`, {
                text: userMessage,
                current_config: config
            });

            const result = response.data;

            // Apply configuration changes
            if (result.config_changes) {
                const changes = result.config_changes;

                if (changes.hasRecess !== undefined) {
                    setConfig(prev => ({ ...prev, hasRecess: changes.hasRecess }));
                }
                if (changes.startTime) {
                    setConfig(prev => ({ ...prev, startTime: changes.startTime }));
                }
                if (changes.endTime) {
                    setConfig(prev => ({ ...prev, endTime: changes.endTime }));
                }
                if (changes.classDuration) {
                    setConfig(prev => ({ ...prev, classDuration: changes.classDuration }));
                }
                if (changes.gradeGroup) {
                    setGradeGroups(prev => [...prev, changes.gradeGroup]);
                }
                if (changes.dayRule) {
                    const { days, subjects } = changes.dayRule;
                    days.forEach(day => {
                        setDayRules(prev => ({ ...prev, [day]: subjects }));
                    });
                }
                if (changes.teacherRestriction) {
                    const { teacher, start, end } = changes.teacherRestriction;
                    setTeacherRestrictions(prev => ({
                        ...prev,
                        [teacher]: { start, end }
                    }));
                }
                if (changes.clear) {
                    setGradeGroups([]);
                    setDayRules({});
                    setTeacherRestrictions({});
                    setSchedule(null);
                    setConfig({
                        startTime: '07:30', endTime: '13:00', classDuration: 45,
                        recessStart: '10:10', recessEnd: '10:50', hasRecess: true
                    });
                }
            }

            // Add bot response
            setMessages(prev => [...prev, {
                type: 'bot',
                text: result.response_text,
                intent: result.intent,
                confidence: result.confidence
            }]);

            // Generate if needed
            if (result.should_generate) {
                setTimeout(() => generateSchedule(), 500);
            }

        } catch (error) {
            console.error('AI Error:', error);
            // Fallback to local processing if AI is unavailable
            setMessages(prev => [...prev, {
                type: 'bot',
                text: '⚠️ El servicio de IA no está disponible. Usando procesamiento local.\n\nEscribe "generar" para crear el horario.'
            }]);
        } finally {
            setAiProcessing(false);
        }
    };

    const generateSchedule = async () => {
        setLoading(true);

        try {
            const response = await axios.post(`${AI_API_URL}/generate-schedule`, {
                config: config,
                grade_groups: gradeGroups,
                day_rules: dayRules,
                teacher_restrictions: teacherRestrictions
            });

            if (response.data.success) {
                setSchedule(response.data.schedule);
                setMessages(prev => [...prev, {
                    type: 'bot',
                    text: `🎉 **¡Horario generado!**\n\n${response.data.message}\n\n⏰ ${config.startTime} - ${config.endTime}\n📚 ${timeSlots.filter(s => !s.isRecess).length} clases de ${config.classDuration} min\n${config.hasRecess ? `☕ Receso: ${config.recessStart} - ${config.recessEnd}` : '🚫 Sin receso'}`
                }]);
            }
        } catch (error) {
            console.error('Schedule generation error:', error);
            // Fallback local generation
            generateScheduleLocal();
        } finally {
            setLoading(false);
        }
    };

    const generateScheduleLocal = () => {
        const allSubjects = ['Matemáticas', 'Español', 'Ciencias', 'Inglés', 'Historia', 'Ed. Física', 'Arte', 'Música', 'Computación'];
        const teachers = ['Prof. García', 'Prof. López', 'Prof. Martínez', 'Prof. Smith', 'Prof. Hernández', 'Prof. Rodríguez', 'Prof. Flores'];
        const slots = generateTimeSlots();

        const copyFromMap = {};
        gradeGroups.forEach(group => {
            if (group.length >= 2) {
                const primary = group[0];
                group.slice(1).forEach(secondary => {
                    copyFromMap[secondary] = primary;
                });
            }
        });

        const newSchedule = {};

        grades.forEach(grade => {
            if (copyFromMap[grade]) return;

            newSchedule[grade] = {};
            days.forEach(day => {
                const daySubjects = dayRules[day] || allSubjects;

                newSchedule[grade][day] = slots.map(slot => {
                    if (slot.isRecess) {
                        return { subject: 'RECESO', teacher: '' };
                    }
                    const teacher = teachers[Math.floor(Math.random() * teachers.length)];
                    const subject = daySubjects[Math.floor(Math.random() * daySubjects.length)];
                    return { subject, teacher };
                });
            });
        });

        Object.entries(copyFromMap).forEach(([target, source]) => {
            if (newSchedule[source]) {
                newSchedule[target] = JSON.parse(JSON.stringify(newSchedule[source]));
            }
        });

        setSchedule(newSchedule);
        setLoading(false);

        const copiedGrades = Object.entries(copyFromMap).map(([t, s]) => `${t} = ${s}`);
        setMessages(prev => [...prev, {
            type: 'bot',
            text: `🎉 **¡Horario generado (local)!**\n\n⏰ ${config.startTime} - ${config.endTime}\n📚 ${slots.filter(s => !s.isRecess).length} clases de ${config.classDuration} min\n${config.hasRecess ? `☕ Receso: ${config.recessStart} - ${config.recessEnd}` : '🚫 Sin receso'}\n\n${copiedGrades.length > 0 ? `📌 Horarios iguales:\n${copiedGrades.map(g => `• ${g}`).join('\n')}` : ''}`
        }]);
    };

    const downloadPDF = (type) => {
        toast.info(`Descargando PDF: ${type === 'grade' ? selectedGrade : 'Todos'}`);
    };

    const ConfigModal = () => (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div className="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl">
                <div className="flex justify-between items-center mb-6">
                    <h3 className="text-xl font-bold flex items-center gap-2">
                        <Settings size={24} className="text-purple-600" />
                        Configurar Horario
                    </h3>
                    <button onClick={() => setShowConfig(false)} className="text-gray-400 hover:text-gray-600">
                        <X size={24} />
                    </button>
                </div>

                <div className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Hora Inicio</label>
                            <input type="time" value={config.startTime}
                                onChange={e => setConfig({ ...config, startTime: e.target.value })}
                                className="w-full px-3 py-2 border rounded-lg" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Hora Fin</label>
                            <input type="time" value={config.endTime}
                                onChange={e => setConfig({ ...config, endTime: e.target.value })}
                                className="w-full px-3 py-2 border rounded-lg" />
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Duración de Clase (min)</label>
                        <input type="number" value={config.classDuration}
                            onChange={e => setConfig({ ...config, classDuration: parseInt(e.target.value) || 45 })}
                            className="w-full px-3 py-2 border rounded-lg" min="15" max="120" />
                    </div>

                    <div className="flex items-center gap-2">
                        <input type="checkbox" id="hasRecess" checked={config.hasRecess}
                            onChange={e => setConfig({ ...config, hasRecess: e.target.checked })}
                            className="w-4 h-4" />
                        <label htmlFor="hasRecess" className="text-sm font-medium text-gray-700">Incluir Receso</label>
                    </div>

                    {config.hasRecess && (
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Receso Inicio</label>
                                <input type="time" value={config.recessStart}
                                    onChange={e => setConfig({ ...config, recessStart: e.target.value })}
                                    className="w-full px-3 py-2 border rounded-lg" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Receso Fin</label>
                                <input type="time" value={config.recessEnd}
                                    onChange={e => setConfig({ ...config, recessEnd: e.target.value })}
                                    className="w-full px-3 py-2 border rounded-lg" />
                            </div>
                        </div>
                    )}
                </div>

                <div className="mt-6 flex gap-3">
                    <button onClick={() => setShowConfig(false)}
                        className="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button onClick={() => { setShowConfig(false); setSchedule(null); }}
                        className="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center justify-center gap-2">
                        <Save size={18} /> Guardar
                    </button>
                </div>
            </div>
        </div>
    );

    return (
        <div className="space-y-6">
            {showConfig && <ConfigModal />}

            {/* Header */}
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                    <div className="p-3 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-lg">
                        <Sparkles size={24} className="text-white" />
                    </div>
                    <div>
                        <h1 className="text-2xl font-bold text-gray-800">Generador de Horarios IA</h1>
                        <p className="text-gray-500 text-sm">{config.startTime} - {config.endTime} • {config.classDuration} min • {config.hasRecess ? 'Con receso' : 'Sin receso'}</p>
                    </div>
                </div>
                <div className="flex gap-2">
                    <button onClick={() => setShowConfig(true)} className="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
                        <Settings size={16} /> Configurar
                    </button>
                    {schedule && (
                        <>
                            <button onClick={() => downloadPDF('grade')} className="flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium">
                                <Download size={16} /> {selectedGrade}
                            </button>
                            <button onClick={() => downloadPDF('all')} className="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                                <FileText size={16} /> Todos
                            </button>
                        </>
                    )}
                </div>
            </div>

            {/* Active Rules Display */}
            {(gradeGroups.length > 0 || Object.keys(dayRules).length > 0 || Object.keys(teacherRestrictions).length > 0 || !config.hasRecess) && (
                <div className={`${darkMode ? 'bg-purple-900/30 border-purple-700' : 'bg-purple-50 border-purple-200'} border rounded-xl p-4`}>
                    <div className="flex items-center justify-between mb-2">
                        <span className={`font-semibold ${darkMode ? 'text-purple-300' : 'text-purple-800'}`}>📌 Configuración IA activa:</span>
                        <button onClick={() => { setGradeGroups([]); setDayRules({}); setTeacherRestrictions({}); setConfig(prev => ({ ...prev, hasRecess: true })); }}
                            className="text-purple-600 text-sm hover:underline">Limpiar</button>
                    </div>
                    <div className="flex flex-wrap gap-2">
                        {!config.hasRecess && (
                            <span className="px-3 py-1 bg-white border border-red-300 rounded-full text-sm text-red-700">🚫 Sin receso</span>
                        )}
                        {gradeGroups.map((group, i) => (
                            <span key={i} className="px-3 py-1 bg-white border border-purple-300 rounded-full text-sm text-purple-700">
                                👥 {group.join(' = ')}
                            </span>
                        ))}
                        {Object.entries(dayRules).map(([day, subjects]) => (
                            <span key={day} className="px-3 py-1 bg-white border border-blue-300 rounded-full text-sm text-blue-700">
                                📅 {day}: {subjects.slice(0, 2).join(', ')}{subjects.length > 2 ? '...' : ''}
                            </span>
                        ))}
                        {Object.entries(teacherRestrictions).map(([teacher, times]) => (
                            <span key={teacher} className="px-3 py-1 bg-white border border-green-300 rounded-full text-sm text-green-700">
                                👨‍🏫 {teacher}: {times.start}-{times.end}
                            </span>
                        ))}
                    </div>
                </div>
            )}

            <div className="grid grid-cols-1 xl:grid-cols-4 gap-6">
                {/* Chat Panel */}
                <div className={`xl:col-span-1 ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} rounded-2xl shadow-sm border flex flex-col h-[500px]`}>
                    <div className={`p-3 border-b ${darkMode ? 'border-gray-700 bg-gradient-to-r from-purple-900/30 to-indigo-900/30' : 'border-gray-100 bg-gradient-to-r from-purple-50 to-indigo-50'} flex items-center gap-2`}>
                        <Bot size={18} className="text-purple-600" />
                        <span className={`font-semibold text-sm ${darkMode ? 'text-white' : 'text-gray-800'}`}>Asistente IA</span>
                        {aiProcessing && <Loader2 size={14} className="animate-spin text-purple-600 ml-auto" />}
                    </div>

                    <div className="flex-1 overflow-y-auto p-3 space-y-3">
                        {messages.map((msg, i) => (
                            <div key={i} className={`flex ${msg.type === 'user' ? 'justify-end' : 'justify-start'}`}>
                                <div className={`max-w-[90%] p-2.5 rounded-xl text-sm ${msg.type === 'user'
                                    ? 'bg-purple-600 text-white rounded-br-sm'
                                    : darkMode ? 'bg-gray-700 text-gray-100 rounded-bl-sm' : 'bg-gray-100 text-gray-800 rounded-bl-sm'
                                    }`}>
                                    <p className="whitespace-pre-line" dangerouslySetInnerHTML={{
                                        __html: msg.text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                                    }} />
                                    {msg.intent && msg.confidence && (
                                        <p className="text-[10px] opacity-50 mt-1">
                                            🧠 {msg.intent} ({Math.round(msg.confidence * 100)}%)
                                        </p>
                                    )}
                                </div>
                            </div>
                        ))}
                        {(loading || aiProcessing) && (
                            <div className="flex justify-start">
                                <div className="bg-gray-100 p-2.5 rounded-xl rounded-bl-sm flex items-center gap-2">
                                    <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-purple-600"></div>
                                    <span className="text-sm text-gray-600">{aiProcessing ? 'Procesando...' : 'Generando...'}</span>
                                </div>
                            </div>
                        )}
                        <div ref={chatEndRef} />
                    </div>

                    <div className="p-3 border-t border-gray-100">
                        <div className="flex gap-2">
                            <input
                                type="text"
                                value={input}
                                onChange={(e) => setInput(e.target.value)}
                                onKeyPress={(e) => e.key === 'Enter' && handleSend()}
                                placeholder="Ej: Quita el receso..."
                                className={`flex-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 outline-none text-sm ${darkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'border-gray-200'}`}
                                disabled={aiProcessing}
                            />
                            <button onClick={handleSend} disabled={aiProcessing}
                                className="p-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50">
                                <Send size={16} />
                            </button>
                        </div>
                        <button
                            onClick={() => { setInput('Generar horario'); setTimeout(handleSend, 100); }}
                            disabled={aiProcessing}
                            className="w-full mt-2 text-xs px-3 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg font-medium disabled:opacity-50"
                        >
                            ⚡ Generar Horario
                        </button>
                    </div>
                </div>

                {/* Schedule Panel */}
                <div className={`xl:col-span-3 ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} rounded-2xl shadow-sm border overflow-hidden`}>
                    {!schedule ? (
                        <div className="h-[500px] flex flex-col items-center justify-center text-center p-8">
                            <Calendar size={48} className="text-purple-300 mb-4" />
                            <h3 className="text-xl font-bold text-gray-800 mb-2">Sin horario generado</h3>
                            <p className="text-gray-500 text-sm max-w-sm">
                                Dile a la IA qué necesitas y luego escribe "generar".
                            </p>
                        </div>
                    ) : (
                        <>
                            <div className="flex border-b border-gray-100 overflow-x-auto bg-gray-50">
                                {grades.map(grade => (
                                    <button
                                        key={grade}
                                        onClick={() => setSelectedGrade(grade)}
                                        className={`px-3 py-2 text-xs font-medium whitespace-nowrap transition-all ${selectedGrade === grade
                                            ? 'bg-white text-purple-700 border-b-2 border-purple-600'
                                            : 'text-gray-500 hover:text-gray-700'
                                            }`}
                                    >
                                        {grade}
                                    </button>
                                ))}
                            </div>

                            <div className="px-4 py-2 bg-green-50 border-b border-green-100 flex items-center gap-2 text-green-700 text-sm">
                                <CheckCircle size={16} />
                                <span>Horario de <strong>{selectedGrade}</strong></span>
                            </div>

                            <div className="overflow-x-auto p-4">
                                <table className="w-full border-collapse">
                                    <thead>
                                        <tr>
                                            <th className="p-2 text-left text-xs font-semibold text-gray-500 uppercase w-28">
                                                <Clock size={14} className="inline mr-1" />Hora
                                            </th>
                                            {days.map(day => (
                                                <th key={day} className={`p-2 text-center text-xs font-semibold uppercase ${dayRules[day] ? 'text-blue-600' : 'text-gray-500'}`}>
                                                    {day}
                                                </th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {timeSlots.map((slot, slotIndex) => (
                                            <tr key={slotIndex} className={slot.isRecess ? 'bg-amber-50' : ''}>
                                                <td className="p-2 text-xs font-medium text-gray-600 border-r border-gray-100">
                                                    {slot.label}
                                                </td>
                                                {days.map(day => {
                                                    const cell = schedule[selectedGrade]?.[day]?.[slotIndex];
                                                    const colorClass = colors[cell?.subject] || 'bg-gray-100 text-gray-600';

                                                    return (
                                                        <td key={day} className="p-1">
                                                            <div className={`p-2 rounded-lg border ${colorClass}`}>
                                                                <p className="font-semibold text-xs">{cell?.subject}</p>
                                                                {!slot.isRecess && cell?.teacher && (
                                                                    <p className="text-[10px] opacity-70 mt-0.5">{cell.teacher}</p>
                                                                )}
                                                            </div>
                                                        </td>
                                                    );
                                                })}
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </>
                    )}
                </div>
            </div>
        </div>
    );
};

export default Academic;
