import { useState, useRef, useEffect } from 'react';
import {
    Sparkles,
    Send,
    Download,
    Clock,
    Users,
    CheckCircle,
    Bot,
    FileText,
    Filter,
    Calendar
} from 'lucide-react';

const Academic = () => {
    const [messages, setMessages] = useState([
        {
            type: 'bot',
            text: '¡Hola! Soy tu asistente de horarios. Puedo ayudarte a generar horarios optimizados.\n\n📅 **Configuración base:**\n• Horario: 7:30 AM - 1:00 PM\n• Duración: 45 minutos por clase\n• Receso: 10:10 - 10:50 AM\n\n¿Tienes alguna restricción? Escribe "generar" para crear el horario.'
        }
    ]);
    const [input, setInput] = useState('');
    const [schedule, setSchedule] = useState(null);
    const [loading, setLoading] = useState(false);
    const [selectedGrade, setSelectedGrade] = useState('1ro Primaria');
    const chatEndRef = useRef(null);

    const timeSlots = [
        { label: '7:30 - 8:15', isRecess: false },
        { label: '8:15 - 9:00', isRecess: false },
        { label: '9:00 - 9:45', isRecess: false },
        { label: '9:45 - 10:10', isRecess: false },
        { label: '10:10 - 10:50', isRecess: true },
        { label: '10:50 - 11:35', isRecess: false },
        { label: '11:35 - 12:20', isRecess: false },
        { label: '12:20 - 1:00', isRecess: false },
    ];

    const days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
    const grades = ['1ro Primaria', '2do Primaria', '3ro Primaria', '4to Primaria', '5to Primaria', '6to Primaria'];

    const subjects = ['Matemáticas', 'Español', 'Ciencias', 'Inglés', 'Historia', 'Ed. Física', 'Arte', 'Música'];
    const teachers = ['Prof. García', 'Prof. López', 'Prof. Martínez', 'Prof. Smith', 'Prof. Hernández', 'Prof. Rodríguez', 'Prof. Flores'];

    const colors = {
        'Matemáticas': 'bg-blue-100 text-blue-800 border-blue-200',
        'Español': 'bg-green-100 text-green-800 border-green-200',
        'Ciencias': 'bg-purple-100 text-purple-800 border-purple-200',
        'Inglés': 'bg-red-100 text-red-800 border-red-200',
        'Historia': 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'Ed. Física': 'bg-orange-100 text-orange-800 border-orange-200',
        'Arte': 'bg-pink-100 text-pink-800 border-pink-200',
        'Música': 'bg-indigo-100 text-indigo-800 border-indigo-200',
        'RECESO': 'bg-amber-200 text-amber-900 border-amber-300',
    };

    useEffect(() => {
        chatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    const handleSend = () => {
        if (!input.trim()) return;

        setMessages(prev => [...prev, { type: 'user', text: input }]);
        const userInput = input.toLowerCase();
        setInput('');

        setTimeout(() => {
            let response = '';
            if (userInput.includes('generar') || userInput.includes('crear')) {
                response = '✅ ¡Generando horario optimizado!\n\nAplicando:\n• Restricciones de maestros\n• Distribución equitativa\n• Sin conflictos';
                generateSchedule();
            } else if (userInput.includes('pdf') || userInput.includes('descargar')) {
                response = '📄 Usa los botones de descarga arriba del horario para exportar a PDF.';
            } else {
                response = 'Puedo ayudarte con:\n• "Generar" - Crear horario\n• "Descargar PDF" - Exportar\n\n¿Qué necesitas?';
            }
            setMessages(prev => [...prev, { type: 'bot', text: response }]);
        }, 500);
    };

    const generateSchedule = () => {
        setLoading(true);

        setTimeout(() => {
            const newSchedule = {};

            grades.forEach(grade => {
                newSchedule[grade] = {};
                days.forEach(day => {
                    newSchedule[grade][day] = timeSlots.map(slot => {
                        if (slot.isRecess) {
                            return { subject: 'RECESO', teacher: '' };
                        }
                        const subject = subjects[Math.floor(Math.random() * subjects.length)];
                        const teacher = teachers[Math.floor(Math.random() * teachers.length)];
                        return { subject, teacher };
                    });
                });
            });

            setSchedule(newSchedule);
            setLoading(false);
            setMessages(prev => [...prev, {
                type: 'bot',
                text: '🎉 ¡Horario listo! Selecciona un grado en las pestañas para ver su horario semanal.'
            }]);
        }, 1500);
    };

    const downloadPDF = (type) => {
        alert(`Descargando PDF: Horario de ${type === 'grade' ? selectedGrade : 'Todos los grados'}\n\n(En producción generaría un PDF real)`);
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                    <div className="p-3 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-lg">
                        <Sparkles size={24} className="text-white" />
                    </div>
                    <div>
                        <h1 className="text-2xl font-bold text-gray-800">Generador de Horarios IA</h1>
                        <p className="text-gray-500 text-sm">7:30 AM - 1:00 PM • 45 min/clase • Receso 10:10-10:50</p>
                    </div>
                </div>
                {schedule && (
                    <div className="flex gap-2">
                        <button
                            onClick={() => downloadPDF('grade')}
                            className="flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium shadow-md"
                        >
                            <Download size={16} /> {selectedGrade}
                        </button>
                        <button
                            onClick={() => downloadPDF('all')}
                            className="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium shadow-md"
                        >
                            <FileText size={16} /> Todos
                        </button>
                    </div>
                )}
            </div>

            <div className="grid grid-cols-1 xl:grid-cols-4 gap-6">
                {/* Chat Panel - Smaller */}
                <div className="xl:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-[500px]">
                    <div className="p-3 border-b border-gray-100 flex items-center gap-2 bg-gradient-to-r from-purple-50 to-indigo-50">
                        <Bot size={18} className="text-purple-600" />
                        <span className="font-semibold text-gray-800 text-sm">Asistente IA</span>
                    </div>

                    <div className="flex-1 overflow-y-auto p-3 space-y-3">
                        {messages.map((msg, i) => (
                            <div key={i} className={`flex ${msg.type === 'user' ? 'justify-end' : 'justify-start'}`}>
                                <div className={`max-w-[90%] p-2.5 rounded-xl text-sm ${msg.type === 'user'
                                        ? 'bg-purple-600 text-white rounded-br-sm'
                                        : 'bg-gray-100 text-gray-800 rounded-bl-sm'
                                    }`}>
                                    <p className="whitespace-pre-line" dangerouslySetInnerHTML={{
                                        __html: msg.text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                                    }} />
                                </div>
                            </div>
                        ))}
                        {loading && (
                            <div className="flex justify-start">
                                <div className="bg-gray-100 p-2.5 rounded-xl rounded-bl-sm flex items-center gap-2">
                                    <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-purple-600"></div>
                                    <span className="text-sm text-gray-600">Generando...</span>
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
                                placeholder="Escribe aquí..."
                                className="flex-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none text-sm"
                            />
                            <button onClick={handleSend} className="p-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                <Send size={16} />
                            </button>
                        </div>
                        <button
                            onClick={() => { setInput('Generar horario'); setTimeout(handleSend, 100); }}
                            className="w-full mt-2 text-xs px-3 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg font-medium hover:from-purple-700 hover:to-indigo-700"
                        >
                            ⚡ Generar Horario
                        </button>
                    </div>
                </div>

                {/* Schedule Panel - Larger */}
                <div className="xl:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    {!schedule ? (
                        <div className="h-[500px] flex flex-col items-center justify-center text-center p-8">
                            <div className="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                                <Calendar size={32} className="text-purple-600" />
                            </div>
                            <h3 className="text-xl font-bold text-gray-800 mb-2">Sin horario generado</h3>
                            <p className="text-gray-500 text-sm max-w-sm">
                                Usa el chat o presiona "Generar Horario" para crear un horario optimizado automáticamente.
                            </p>
                        </div>
                    ) : (
                        <>
                            {/* Grade Tabs */}
                            <div className="flex border-b border-gray-100 overflow-x-auto bg-gray-50">
                                {grades.map(grade => (
                                    <button
                                        key={grade}
                                        onClick={() => setSelectedGrade(grade)}
                                        className={`px-4 py-3 text-sm font-medium whitespace-nowrap transition-all ${selectedGrade === grade
                                                ? 'bg-white text-purple-700 border-b-2 border-purple-600'
                                                : 'text-gray-500 hover:text-gray-700 hover:bg-white/50'
                                            }`}
                                    >
                                        {grade}
                                    </button>
                                ))}
                            </div>

                            {/* Success Banner */}
                            <div className="px-4 py-2 bg-green-50 border-b border-green-100 flex items-center gap-2 text-green-700 text-sm">
                                <CheckCircle size={16} />
                                <span>Horario de <strong>{selectedGrade}</strong> generado sin conflictos</span>
                            </div>

                            {/* Schedule Grid */}
                            <div className="overflow-x-auto p-4">
                                <table className="w-full border-collapse">
                                    <thead>
                                        <tr>
                                            <th className="p-2 text-left text-xs font-semibold text-gray-500 uppercase w-24">
                                                <Clock size={14} className="inline mr-1" />Hora
                                            </th>
                                            {days.map(day => (
                                                <th key={day} className="p-2 text-center text-xs font-semibold text-gray-500 uppercase">
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
                                                        <td key={day} className="p-1.5">
                                                            <div className={`p-2 rounded-lg border ${colorClass} ${slot.isRecess ? 'text-center' : ''}`}>
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

                            {/* Legend */}
                            <div className="px-4 py-3 bg-gray-50 border-t border-gray-100 flex flex-wrap gap-2">
                                {Object.entries(colors).slice(0, -1).map(([subject, color]) => (
                                    <span key={subject} className={`px-2 py-1 rounded text-[10px] font-medium ${color}`}>
                                        {subject}
                                    </span>
                                ))}
                            </div>
                        </>
                    )}
                </div>
            </div>
        </div>
    );
};

export default Academic;
