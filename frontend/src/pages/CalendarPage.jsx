import React, { useState, useEffect } from 'react';
import { taskService } from '../services';
import { Calendar, Clock, BookOpen, CheckCircle, AlertCircle } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';

const CalendarPage = () => {
    const { darkMode } = useTheme();
    const [tasks, setTasks] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadTasks();
    }, []);

    const loadTasks = async () => {
        setLoading(true);
        try {
            const data = await taskService.getMyTasks();
            setTasks(Array.isArray(data) ? data : []);
        } catch (error) {
            console.error("Error loading tasks:", error);
            setTasks([]);
        } finally {
            setLoading(false);
        }
    };

    const getStatusBadge = (status, dueDate) => {
        const isOverdue = new Date(dueDate) < new Date() && status !== 'COMPLETED';

        if (status === 'COMPLETED') {
            return (
                <span className="flex items-center gap-1 text-green-600 bg-green-50 px-3 py-1 rounded-full text-sm font-medium">
                    <CheckCircle size={14} /> Completada
                </span>
            );
        }
        if (isOverdue) {
            return (
                <span className="flex items-center gap-1 text-red-600 bg-red-50 px-3 py-1 rounded-full text-sm font-medium">
                    <AlertCircle size={14} /> Vencida
                </span>
            );
        }
        return (
            <span className="flex items-center gap-1 text-orange-600 bg-orange-50 px-3 py-1 rounded-full text-sm font-medium">
                <Clock size={14} /> Pendiente
            </span>
        );
    };

    if (loading) {
        return (
            <div className="flex items-center justify-center h-64">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-teal-600"></div>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                    <Calendar className="text-teal-500" size={28} />
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Cronograma de Tareas</h1>
                </div>
                <button className="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    + Nueva Tarea
                </button>
            </div>

            <div className="grid gap-4">
                {tasks.map(task => (
                    <div
                        key={task.id}
                        className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} p-5 rounded-2xl shadow-sm border hover:shadow-md transition-all`}
                    >
                        <div className="flex justify-between items-start mb-3">
                            <div className="flex-1">
                                <h3 className={`font-bold text-lg ${darkMode ? 'text-white' : 'text-gray-900'}`}>{task.title}</h3>
                                <p className={`mt-1 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>{task.description}</p>
                            </div>
                            {getStatusBadge(task.status, task.dueDate)}
                        </div>
                        <div className={`flex items-center justify-between pt-3 border-t ${darkMode ? 'border-gray-700' : 'border-gray-100'}`}>
                            <div className="flex items-center gap-2 text-sm">
                                <BookOpen size={16} className="text-teal-500" />
                                <span className={`font-medium ${darkMode ? 'text-teal-400' : 'text-teal-700'}`}>{task.course}</span>
                            </div>
                            <div className={`flex items-center gap-2 text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                <Clock size={16} />
                                <span>Vence: {new Date(task.dueDate).toLocaleDateString('es-GT', {
                                    weekday: 'short',
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                })}</span>
                            </div>
                        </div>
                    </div>
                ))}

                {tasks.length === 0 && (
                    <div className={`text-center py-12 ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-gray-50 border-gray-200'} rounded-2xl border-2 border-dashed`}>
                        <Calendar size={48} className={`mx-auto mb-3 ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                        <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>No hay tareas asignadas.</p>
                    </div>
                )}
            </div>
        </div>
    );
};

export default CalendarPage;
