import React, { useState, useEffect } from 'react';
import { RefreshCw, AlertCircle, CheckCircle, Clock, FileText, ClipboardList } from 'lucide-react';
import { taskService } from '../services';
import { useTheme } from '../contexts/ThemeContext';

const TareasPage = () => {
    const { darkMode } = useTheme();
    const [tasks, setTasks] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchTasks();
    }, []);

    const fetchTasks = async () => {
        try {
            const data = await taskService.getMyTasks();
            setTasks(data || []);
        } catch (error) {
            console.error("Error loading tasks", error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <div className={`p-12 text-center ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                <RefreshCw className="animate-spin mx-auto mb-2" />
                Cargando mis tareas...
            </div>
        );
    }

    return (
        <div className={`p-6 rounded-2xl shadow-sm transition-colors ${darkMode ? 'bg-gray-800' : 'bg-white border border-gray-100'}`}>
            <h1 className={`text-2xl font-bold mb-6 flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                <ClipboardList className="text-blue-500" /> Mis Tareas
            </h1>

            {tasks.length === 0 ? (
                <div className={`text-center py-16 rounded-xl border-2 border-dashed ${darkMode ? 'border-gray-700 bg-gray-800/50' : 'border-gray-200 bg-gray-50'}`}>
                    <div className={`w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 ${darkMode ? 'bg-gray-700 text-gray-400' : 'bg-white text-gray-300 shadow-sm'}`}>
                        <FileText size={32} />
                    </div>
                    <h3 className={`text-lg font-medium mb-1 ${darkMode ? 'text-white' : 'text-gray-900'}`}>Estás al día</h3>
                    <p className={`${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>No tienes tareas pendientes por entregar.</p>
                </div>
            ) : (
                <div className="grid gap-4">
                    {tasks.map(task => {
                        const submissionStatus = task.mySubmission ? task.mySubmission.status : 'pending';

                        return (
                            <div
                                key={task.id}
                                onClick={() => window.location.href = `/mis-tareas/${task.id}`}
                                className={`p-4 border rounded-xl hover:shadow-md transition cursor-pointer ${darkMode ? 'bg-gray-700 border-gray-600 hover:bg-gray-600' : 'bg-gray-50 border-gray-200 hover:bg-white'
                                    }`}
                            >
                                <div className="flex justify-between items-center">
                                    <div>
                                        <h3 className={`font-semibold text-lg ${darkMode ? 'text-gray-100' : 'text-gray-800'}`}>{task.title}</h3>
                                        <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>{task.subject?.name} • {task.type}</p>
                                    </div>
                                    <div className="text-right">
                                        <span className={`px-2.5 py-1 rounded-full text-xs font-medium inline-flex items-center gap-1.5
                                            ${submissionStatus !== 'pending'
                                                ? (darkMode ? 'bg-green-900/30 text-green-400' : 'bg-green-100 text-green-700')
                                                : (darkMode ? 'bg-yellow-900/30 text-yellow-400' : 'bg-yellow-100 text-yellow-700')
                                            }`}>
                                            {submissionStatus !== 'pending' ? <CheckCircle size={14} /> : <Clock size={14} />}
                                            {submissionStatus === 'pending' ? 'Pendiente' : 'Entregada'}
                                        </span>
                                        <p className={`text-xs mt-1.5 ${darkMode ? 'text-gray-500' : 'text-gray-500'}`}>Vence: {task.dueDate}</p>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
};

export default TareasPage;
