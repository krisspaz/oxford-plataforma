import React, { useState, useEffect } from 'react';
import { Calendar, Clock, BookOpen, RefreshCw, MapPin, Users } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { useAuth } from '../contexts/AuthContext';
import { scheduleService } from '../services';

const MiHorarioPage = () => {
    const { darkMode } = useTheme();
    const { user, hasRole } = useAuth();
    const [loading, setLoading] = useState(true);
    const [schedule, setSchedule] = useState(null);
    const [selectedDay, setSelectedDay] = useState('Lunes');
    const [error, setError] = useState(null);

    const days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
    // Map integer day from backend (1=Monday) to string
    const dayMap = { 1: 'Lunes', 2: 'Martes', 3: 'Miércoles', 4: 'Jueves', 5: 'Viernes' };

    useEffect(() => {
        fetchSchedule();
    }, [user]);

    const fetchSchedule = async () => {
        try {
            setLoading(true);
            let response;

            if (hasRole('ROLE_DOCENTE')) {
                response = await scheduleService.getMySchedule();
            } else if (hasRole('ROLE_ESTUDIANTE') || hasRole('ROLE_PADRE')) {
                response = await scheduleService.getMyStudentSchedule();
            } else {
                // Admin or other: maybe show nothing or generic message?
                // For now, let's try to fetch as if they were a teacher or student just in case, or stop.
                setLoading(false);
                return;
            }

            // Backend returns flat array of schedule objects
            const rawData = response.data || response; // Handle axios vs standard response

            // Transform flat data to structured format expected by UI
            // Structure needed: { classes: { 'Lunes': [...], ... } }

            const structuredClasses = {
                'Lunes': [], 'Martes': [], 'Miércoles': [], 'Jueves': [], 'Viernes': []
            };

            rawData.forEach(item => {
                const dayName = dayMap[item.dayOfWeek];
                if (dayName && structuredClasses[dayName]) {
                    structuredClasses[dayName].push({
                        period: item.period,
                        subject: item.subject?.name || 'Materia',
                        teacher: item.teacher?.name || 'Docente',
                        room: item.classroom || 'Aula'
                    });
                }
            });

            // Sort periods
            Object.keys(structuredClasses).forEach(day => {
                structuredClasses[day].sort((a, b) => a.period - b.period);
            });

            setSchedule({
                grade: 'Ciclo 2026', // Placeholder or fetch from first item
                section: '',
                classes: structuredClasses,
                // Static periods for now, or could come from DB if variable
                periods: [
                    { id: 1, time: '07:30 - 08:15', label: '1er Período' },
                    { id: 2, time: '08:15 - 09:00', label: '2do Período' },
                    { id: 3, time: '09:00 - 09:45', label: '3er Período' },
                    { id: 4, time: '09:45 - 10:15', label: 'Receso', isBreak: true },
                    { id: 5, time: '10:15 - 11:00', label: '4to Período' },
                    { id: 6, time: '11:00 - 11:45', label: '5to Período' },
                    { id: 7, time: '11:45 - 12:30', label: '6to Período' },
                ]
            });

        } catch (err) {
            console.error("Error loading schedule", err);
            setError("No se pudo cargar el horario. " + (err.response?.data?.error || err.message));
        } finally {
            setLoading(false);
        }
    };

    const getClassForPeriod = (day, periodId) => {
        if (!schedule || !schedule.classes[day]) return null;
        return schedule.classes[day].find(c => c.period === periodId);
    };

    const getTodayClasses = () => {
        if (!schedule) return [];
        const dayIndex = new Date().getDay(); // 0-6
        if (dayIndex >= 1 && dayIndex <= 5) {
            return schedule.classes[days[dayIndex - 1]] || [];
        }
        return [];
    };

    const getTotalClasses = () => {
        if (!schedule) return 0;
        return Object.values(schedule.classes).flat().length;
    };

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando horario...</p>
            </div>
        );
    }

    if (error) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <div className="mx-auto bg-red-100 text-red-500 p-3 rounded-full w-12 h-12 flex items-center justify-center mb-3">
                    <Users size={24} />
                </div>
                <h3 className="text-lg font-bold text-red-500 mb-2">Error</h3>
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{error}</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Mi Horario</h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>
                        {schedule?.grade} "{schedule?.section}"
                    </p>
                </div>
            </div>

            {/* Stats Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-sm`}>
                    <div className="flex items-center gap-3">
                        <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${darkMode ? 'bg-blue-900/50' : 'bg-blue-100'}`}>
                            <Calendar className="text-blue-500" size={24} />
                        </div>
                        <div>
                            <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{getTotalClasses()}</p>
                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Clases por semana</p>
                        </div>
                    </div>
                </div>
                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-sm`}>
                    <div className="flex items-center gap-3">
                        <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${darkMode ? 'bg-green-900/50' : 'bg-green-100'}`}>
                            <Clock className="text-green-500" size={24} />
                        </div>
                        <div>
                            <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{getTodayClasses().length}</p>
                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Clases hoy</p>
                        </div>
                    </div>
                </div>
                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-sm`}>
                    <div className="flex items-center gap-3">
                        <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${darkMode ? 'bg-purple-900/50' : 'bg-purple-100'}`}>
                            <BookOpen className="text-purple-500" size={24} />
                        </div>
                        <div>
                            <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>8</p>
                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Materias</p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Day Tabs */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-2 shadow-sm`}>
                <div className="flex gap-1">
                    {days.map(day => (
                        <button
                            key={day}
                            onClick={() => setSelectedDay(day)}
                            className={`flex-1 py-3 px-4 rounded-lg font-medium transition-all ${selectedDay === day
                                ? 'bg-teal-600 text-white'
                                : darkMode
                                    ? 'text-gray-400 hover:bg-gray-700'
                                    : 'text-gray-600 hover:bg-gray-100'
                                }`}
                        >
                            {day}
                        </button>
                    ))}
                </div>
            </div>

            {/* Schedule Grid */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-6 shadow-sm`}>
                <h2 className={`text-lg font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                    Horario del {selectedDay}
                </h2>
                <div className="space-y-3">
                    {schedule?.periods.map(period => {
                        const classInfo = getClassForPeriod(selectedDay, period.id);

                        return (
                            <div
                                key={period.id}
                                className={`flex items-center gap-4 p-4 rounded-xl border ${period.isBreak
                                    ? darkMode ? 'bg-yellow-900/20 border-yellow-700/30' : 'bg-yellow-50 border-yellow-200'
                                    : classInfo
                                        ? darkMode ? 'bg-teal-900/30 border-teal-700/50' : 'bg-teal-50 border-teal-200'
                                        : darkMode ? 'bg-gray-700/50 border-gray-600' : 'bg-gray-50 border-gray-200'
                                    }`}
                            >
                                {/* Time */}
                                <div className="w-32 flex-shrink-0">
                                    <p className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{period.time}</p>
                                    <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{period.label}</p>
                                </div>

                                {/* Content */}
                                <div className="flex-1">
                                    {period.isBreak ? (
                                        <p className="text-yellow-600 font-medium">🍽️ Receso</p>
                                    ) : classInfo ? (
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <p className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                                    {classInfo.subject}
                                                </p>
                                                <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                                    {classInfo.teacher}
                                                </p>
                                            </div>
                                            <div className={`flex items-center gap-2 px-3 py-1 rounded-lg ${darkMode ? 'bg-gray-600' : 'bg-white'}`}>
                                                <MapPin size={14} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                                                <span className={`text-sm ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{classInfo.room}</span>
                                            </div>
                                        </div>
                                    ) : (
                                        <p className={`${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Sin clase asignada</p>
                                    )}
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>

            {/* Weekly Overview Table */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-6 shadow-sm`}>
                <h2 className={`text-lg font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Vista Semanal</h2>
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr>
                                <th className={`p-2 text-left border ${darkMode ? 'border-gray-700 bg-gray-700/50 text-gray-200' : 'border-gray-200 bg-gray-50 text-gray-700'}`}>Hora</th>
                                {days.map(day => (
                                    <th key={day} className={`p-2 text-center border ${darkMode ? 'border-gray-700 bg-gray-700/50 text-gray-200' : 'border-gray-200 bg-gray-50 text-gray-700'}`}>{day}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {schedule?.periods.map(period => (
                                <tr key={period.id}>
                                    <td className={`p-2 border text-xs ${darkMode ? 'border-gray-700 text-gray-300' : 'border-gray-200 text-gray-600'}`}>
                                        <div className="font-medium">{period.time}</div>
                                    </td>
                                    {days.map(day => {
                                        const classInfo = getClassForPeriod(day, period.id);
                                        return (
                                            <td key={`${day}-${period.id}`} className={`p-1 border text-center ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                                                {period.isBreak ? (
                                                    <span className="text-yellow-500 text-xs">Receso</span>
                                                ) : classInfo ? (
                                                    <div className={`p-1.5 rounded text-xs ${darkMode ? 'bg-teal-900/50 text-teal-300' : 'bg-teal-100 text-teal-800'}`}>
                                                        <div className="font-bold">{classInfo.subject}</div>
                                                        <div className="opacity-70">{classInfo.teacher.split(' ')[1]}</div>
                                                    </div>
                                                ) : (
                                                    <span className={`text-xs ${darkMode ? 'text-gray-600' : 'text-gray-300'}`}>-</span>
                                                )}
                                            </td>
                                        );
                                    })}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
};

export default MiHorarioPage;
