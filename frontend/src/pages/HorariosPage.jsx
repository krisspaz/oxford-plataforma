import React, { useState, useEffect, useRef } from 'react';
import { Calendar, Clock, Users, BookOpen, RefreshCw, Sparkles, Check, AlertCircle, Plus, Trash2, Download, Send, Bot, User, MessageCircle } from 'lucide-react';
// Removed jsPDF direct imports
import { usePdfExport } from '../hooks/usePdfExport';
import { useTheme } from '../contexts/ThemeContext';
import axios from 'axios';

const HorariosPage = () => {
    const { darkMode } = useTheme();
    const [loading, setLoading] = useState(false);
    const [generating, setGenerating] = useState(false);

    // Real data state
    const [courses, setCourses] = useState([]);
    const [selectedCourseId, setSelectedCourseId] = useState('');
    const [scheduleData, setScheduleData] = useState({}); // { Day: { PeriodID: ScheduleEntry } }

    const [aiProgress, setAiProgress] = useState(0);
    const [aiStatus, setAiStatus] = useState('');

    // Chat state
    const [chatOpen, setChatOpen] = useState(true);
    const [messages, setMessages] = useState([
        {
            type: 'ai',
            text: '👋 ¡Hola! Soy tu asistente de horarios. Selecciona un curso para ver su horario real descargado de la base de datos.',
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

    const days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
    const daysMap = { 1: 'Lunes', 2: 'Martes', 3: 'Miércoles', 4: 'Jueves', 5: 'Viernes' };

    const periods = [
        { id: 1, time: '07:30 - 08:15', label: '1er Período' },
        { id: 2, time: '08:15 - 09:00', label: '2do Período' },
        { id: 3, time: '09:00 - 09:45', label: '3er Período' },
        { id: 4, time: '09:45 - 10:15', label: 'Receso', isBreak: true },
        { id: 5, time: '10:15 - 11:00', label: '4to Período' },
        { id: 6, time: '11:00 - 11:45', label: '5to Período' },
        { id: 7, time: '11:45 - 12:30', label: '6to Período' },
    ];

    // Fetch Initial Data
    useEffect(() => {
        const fetchData = async () => {
            try {
                // Fetch Courses
                const coursesIdsResponse = await axios.get('/api/courses');
                // API Platform returns 'hydra:member' or array depending on config. Assuming array for now based on previous work.
                // Or check usage in other files. Standard is usually just array if simple controller, or hydra if API Platform.
                // Let's assume standard json array for "Courses" if using default API Platform, it might be inside 'hydra:member'.
                // If it fails we'll debug.
                const coursesData = coursesIdsResponse.data['hydra:member'] || coursesIdsResponse.data;
                setCourses(coursesData);
            } catch (error) {
                console.error("Error fetching courses:", error);
            }
        };
        fetchData();
    }, []);

    // Fetch Schedule when Course is selected
    useEffect(() => {
        if (!selectedCourseId) {
            setScheduleData({});
            return;
        }

        const fetchSchedule = async () => {
            setLoading(true);
            try {
                // Use our new controller endpoint
                const response = await axios.get(`/api/schedule/course/${selectedCourseId}`);
                const rawSchedules = response.data;

                // Transform to frontend format: { DayName: { PeriodId: Entry } }
                const formatted = {};
                days.forEach(d => formatted[d] = {});

                rawSchedules.forEach(s => {
                    const dayName = daysMap[s.dayOfWeek];
                    if (dayName) {
                        formatted[dayName][s.period] = {
                            name: s.subject ? s.subject.name : 'Sin materia',
                            color: 'bg-blue-500', // Default color for now
                            teacher: s.teacher ? s.teacher.name : 'Sin profesor'
                        };
                    }
                });

                setScheduleData(formatted);
            } catch (error) {
                console.error("Error fetching schedule:", error);
                setScheduleData({});
            } finally {
                setLoading(false);
            }
        };

        fetchSchedule();
    }, [selectedCourseId]);

    // ... chat logic ... (keep existing chat logic mostly, but adapt generateWithAI to maybe just alert it's a demo for now or update it later)

    // Scroll chat to bottom
    useEffect(() => {
        chatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    // Send message to AI
    const sendMessage = async () => {
        // ... (keep logic, but update processLocalCommand or generateWithAI as needed)
        // For now, let's keep the existing structure but maybe disable "Generate" if it relies on mocks.
        // Actually, user wants "Real Data", so "Generate with AI" might be misleading if it just generates client-side mocks.
        // I will keep it as "Simulation" feedback.

        if (!inputMessage.trim() || isSending) return;
        const userMessage = inputMessage.trim();
        setInputMessage('');
        setIsSending(true);

        // ... basic echo for now ...
        setMessages(prev => [...prev, { type: 'user', text: userMessage, time: new Date() }]);

        setTimeout(() => {
            setMessages(prev => [...prev, { type: 'ai', text: "La funcionalidad de IA de texto está en modo demostración. Por favor usa los controles manuales.", time: new Date() }]);
            setIsSending(false);
        }, 1000);
    };

    // ... (Keep existing simple functions or simplified versions) ...
    // Removing mock generation logic for brevity and clarity as we focus on REAL data viewing.

    const [generatingAll, setGeneratingAll] = useState(false);
    const [totalProgress, setTotalProgress] = useState(0);

    const handleGenerateAll = () => {
        alert("Esta función simularía la generación masiva. Conectada a datos reales, esto requeriría un endpoint de backend más complejo.");
    };

    // ... inside component ...
    const { exportTable, createDoc } = usePdfExport();

    // Export functions
    const exportSchedulePDF = () => {
        if (!selectedCourseId) {
            alert("No hay curso seleccionado.");
            return;
        }

        const selectedCourse = courses.find(c => c.id === parseInt(selectedCourseId));
        const courseName = selectedCourse ? `${selectedCourse.name}` : 'Curso';

        // Flatten data for table
        const tableColumn = ["Hora", ...days];
        const tableRows = periods.map(period => {
            const rowData = [period.time];
            days.forEach(day => {
                const cellData = scheduleData[day]?.[period.id];
                if (cellData) {
                    rowData.push(`${cellData.name}\n${cellData.teacher || ''}`);
                } else {
                    rowData.push('-');
                }
            });
            return rowData;
        });

        exportTable({
            title: 'Horario de Clases 2025',
            subtitle: `Curso: ${courseName}`,
            columns: tableColumn,
            data: tableRows,
            filename: `horario_${courseName.replace(/\s+/g, '_')}.pdf`,
            autoTableOptions: {
                didParseCell: function (data) {
                    if (data.row.raw[0].includes('Receso')) {
                        data.cell.styles.fillColor = [255, 255, 200];
                    }
                }
            }
        });
    };

    // Quick command buttons
    const quickCommands = [];

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Gestión de Horarios (Datos Reales)</h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>Visualiza y exporta los horarios oficiales del sistema.</p>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Left Column - Config & Schedule */}
                <div className={`lg:col-span-3 space-y-6`}>
                    {/* Config Card */}
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-6 shadow-sm`}>
                        <div className="flex justify-between items-center mb-4">
                            <h2 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Selección de Curso</h2>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className={labelClass}>Curso / Grado</label>
                                <select className={inputClass} value={selectedCourseId} onChange={e => setSelectedCourseId(e.target.value)}>
                                    <option value="">-- Seleccionar Curso --</option>
                                    {courses.map(c => (
                                        <option key={c.id} value={c.id}>{c.name} ({c.gradeLevel} {c.section})</option>
                                    ))}
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Loading State */}
                    {loading && (
                        <div className="text-center py-10">
                            <RefreshCw className="animate-spin h-8 w-8 text-purple-600 mx-auto" />
                            <p className="mt-2 text-gray-500">Cargando horario...</p>
                        </div>
                    )}

                    {/* Generated Schedule */}
                    {!loading && selectedCourseId && (
                        <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-6 shadow-sm`}>
                            <div className="flex items-center justify-between mb-4">
                                <h2 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                    Horario: {courses.find(c => c.id === parseInt(selectedCourseId))?.name}
                                </h2>
                                <div className="flex gap-2">
                                    <button onClick={exportSchedulePDF} className={`px-3 py-1.5 rounded-lg text-sm ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-700'}`}>
                                        <Download size={16} className="inline mr-1" />Exportar PDF
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
                                                        {scheduleData[day]?.[period.id] && (
                                                            <div className={`p-1.5 rounded text-white text-xs ${scheduleData[day][period.id].color}`}>
                                                                <div className="font-bold truncate">{scheduleData[day][period.id].name}</div>
                                                                {scheduleData[day][period.id].teacher && (
                                                                    <div className="opacity-80 truncate">{scheduleData[day][period.id].teacher}</div>
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
            </div>
        </div>
    );
};
export default HorariosPage;
