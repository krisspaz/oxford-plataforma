import React, { useState, useEffect } from 'react';
import { Calendar, Clock, BookOpen, RefreshCw, MapPin, Users } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { useAuth } from '../contexts/AuthContext';

const MiHorarioPage = () => {
    const { darkMode } = useTheme();
    const { user } = useAuth();
    const [loading, setLoading] = useState(true);
    const [schedule, setSchedule] = useState(null);
    const [selectedDay, setSelectedDay] = useState('Lunes');

    const days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

    // Demo schedule for teacher - this would come from backend
    const teacherSchedule = {
        teacher: 'Prof. Carlos Hernández',
        subject: 'Matemáticas',
        periods: [
            { id: 1, time: '07:30 - 08:15', label: '1er Período' },
            { id: 2, time: '08:15 - 09:00', label: '2do Período' },
            { id: 3, time: '09:00 - 09:45', label: '3er Período' },
            { id: 4, time: '09:45 - 10:15', label: 'Receso', isBreak: true },
            { id: 5, time: '10:15 - 11:00', label: '4to Período' },
            { id: 6, time: '11:00 - 11:45', label: '5to Período' },
            { id: 7, time: '11:45 - 12:30', label: '6to Período' },
        ],
        classes: {
            'Lunes': [
                { period: 1, grade: '3ro Primaria', section: 'A', room: 'Aula 5', subject: 'Matemáticas' },
                { period: 2, grade: '3ro Primaria', section: 'B', room: 'Aula 6', subject: 'Matemáticas' },
                { period: 3, grade: '4to Primaria', section: 'A', room: 'Aula 7', subject: 'Matemáticas' },
                { period: 5, grade: '5to Primaria', section: 'A', room: 'Aula 8', subject: 'Matemáticas' },
                { period: 6, grade: '5to Primaria', section: 'B', room: 'Aula 9', subject: 'Matemáticas' },
            ],
            'Martes': [
                { period: 1, grade: '2do Primaria', section: 'A', room: 'Aula 3', subject: 'Matemáticas' },
                { period: 2, grade: '2do Primaria', section: 'B', room: 'Aula 4', subject: 'Matemáticas' },
                { period: 3, grade: '3ro Primaria', section: 'A', room: 'Aula 5', subject: 'Matemáticas' },
                { period: 5, grade: '4to Primaria', section: 'B', room: 'Aula 7', subject: 'Matemáticas' },
                { period: 7, grade: '6to Primaria', section: 'A', room: 'Aula 10', subject: 'Matemáticas' },
            ],
            'Miércoles': [
                { period: 1, grade: '1ro Primaria', section: 'A', room: 'Aula 1', subject: 'Matemáticas' },
                { period: 2, grade: '1ro Primaria', section: 'B', room: 'Aula 2', subject: 'Matemáticas' },
                { period: 5, grade: '3ro Primaria', section: 'B', room: 'Aula 6', subject: 'Matemáticas' },
                { period: 6, grade: '4to Primaria', section: 'A', room: 'Aula 7', subject: 'Matemáticas' },
            ],
            'Jueves': [
                { period: 1, grade: '5to Primaria', section: 'A', room: 'Aula 8', subject: 'Matemáticas' },
                { period: 2, grade: '5to Primaria', section: 'B', room: 'Aula 9', subject: 'Matemáticas' },
                { period: 3, grade: '6to Primaria', section: 'A', room: 'Aula 10', subject: 'Matemáticas' },
                { period: 5, grade: '2do Primaria', section: 'A', room: 'Aula 3', subject: 'Matemáticas' },
                { period: 6, grade: '2do Primaria', section: 'B', room: 'Aula 4', subject: 'Matemáticas' },
            ],
            'Viernes': [
                { period: 1, grade: '4to Primaria', section: 'A', room: 'Aula 7', subject: 'Matemáticas' },
                { period: 2, grade: '4to Primaria', section: 'B', room: 'Aula 7', subject: 'Matemáticas' },
                { period: 3, grade: '1ro Primaria', section: 'A', room: 'Aula 1', subject: 'Matemáticas' },
                { period: 5, grade: '3ro Primaria', section: 'A', room: 'Aula 5', subject: 'Matemáticas' },
            ],
        }
    };

    useEffect(() => {
        // Simulate loading from API
        setTimeout(() => {
            setSchedule(teacherSchedule);
            setLoading(false);
        }, 500);
    }, []);

    const getClassForPeriod = (day, periodId) => {
        if (!schedule) return null;
        const dayClasses = schedule.classes[day] || [];
        return dayClasses.find(c => c.period === periodId);
    };

    const getTodayClasses = () => {
        const today = new Date().toLocaleDateString('es', { weekday: 'long' });
        const dayName = today.charAt(0).toUpperCase() + today.slice(1);
        const mappedDay = days.find(d => d.toLowerCase().includes(dayName.toLowerCase().substring(0, 3)));
        return schedule?.classes[mappedDay || 'Lunes'] || [];
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

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Mi Horario</h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>
                        {schedule?.teacher} - {schedule?.subject}
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
                            <Users className="text-purple-500" size={24} />
                        </div>
                        <div>
                            <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>12</p>
                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Grados asignados</p>
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
                                                    {classInfo.grade} "{classInfo.section}"
                                                </p>
                                                <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                                    {classInfo.subject}
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
                                <th className={`p-2 text-left border ${darkMode ? 'border-gray-700 bg-gray-700/50' : 'border-gray-200 bg-gray-50'}`}>Hora</th>
                                {days.map(day => (
                                    <th key={day} className={`p-2 text-center border ${darkMode ? 'border-gray-700 bg-gray-700/50' : 'border-gray-200 bg-gray-50'}`}>{day}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {schedule?.periods.map(period => (
                                <tr key={period.id}>
                                    <td className={`p-2 border text-xs ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
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
                                                        <div className="font-bold">{classInfo.grade}</div>
                                                        <div className="opacity-70">"{classInfo.section}"</div>
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
