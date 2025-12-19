import { useState, useEffect } from 'react';
import { Clock, CheckCircle, XCircle, AlertCircle, FileText, Calendar } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { taskService } from '../services';

const TimeRemaining = ({ dueDate }) => {
    const [timeLeft, setTimeLeft] = useState(calculateTimeLeft());

    function calculateTimeLeft() {
        const difference = +new Date(dueDate) - +new Date();
        let timeLeft = {};

        if (difference > 0) {
            timeLeft = {
                days: Math.floor(difference / (1000 * 60 * 60 * 24)),
                hours: Math.floor((difference / (1000 * 60 * 60)) % 24),
                minutes: Math.floor((difference / 1000 / 60) % 60),
                seconds: Math.floor((difference / 1000) % 60),
            };
        }
        return timeLeft;
    }

    useEffect(() => {
        const timer = setInterval(() => {
            setTimeLeft(calculateTimeLeft());
        }, 1000);
        return () => clearInterval(timer);
    }, [dueDate]);

    const timerComponents = [];
    Object.keys(timeLeft).forEach((interval) => {
        if (!timeLeft[interval] && timeLeft[interval] !== 0) return;
        timerComponents.push(
            <span key={interval} className="font-mono font-bold mx-0.5">
                {timeLeft[interval]}{interval[0]}
            </span>
        );
    });

    if (timerComponents.length === 0) {
        return <span className="text-red-500 font-bold">¡Tiempo Agotado!</span>;
    }

    return (
        <span className="flex items-center text-orange-500 bg-orange-100 dark:bg-orange-900/30 px-2 py-1 rounded-md text-xs">
            <Clock size={12} className="mr-1" />
            {timerComponents}
        </span>
    );
};

const TareasPage = () => {
    const { darkMode } = useTheme();
    const [tasks, setTasks] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadTasks();
    }, []);

    const loadTasks = async () => {
        try {
            const data = await taskService.getMyTasks();
            setTasks(data);
        } catch (error) {
            console.error('Error loading tasks:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return <div className="p-10 text-center">Cargando tareas...</div>;
    }

    return (
        <div className={`space-y-6 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
            <h1 className="text-2xl font-bold flex items-center gap-2">
                <FileText className="text-blue-500" />
                Mis Tareas y Actividades
            </h1>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {tasks.map(task => {
                    const isCompleted = task.mySubmission && task.mySubmission.status !== 'pending'; // Adjust based on actual status values
                    // Actually submission status might be 'submitted', 'graded', etc.
                    // Let's assume 'submitted' or 'graded' means completed.
                    const completed = !!task.mySubmission;

                    return (
                        <div key={task.id} className={`rounded-xl shadow-md overflow-hidden border ${completed
                                ? (darkMode ? 'bg-green-900/10 border-green-800' : 'bg-green-50 border-green-200')
                                : (darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200')
                            }`}>
                            <div className="p-5">
                                <div className="flex justify-between items-start mb-3">
                                    <span className={`px-2 py-1 rounded text-xs font-semibold uppercase ${task.type === 'examen' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'
                                        }`}>
                                        {task.type}
                                    </span>
                                    {completed ? (
                                        <span className="flex items-center text-green-600 text-sm font-medium">
                                            <CheckCircle size={16} className="mr-1" /> Entregada
                                        </span>
                                    ) : (
                                        <span className="flex items-center text-gray-500 text-sm">
                                            <AlertCircle size={16} className="mr-1" /> Pendiente
                                        </span>
                                    )}
                                </div>

                                <h3 className="font-bold text-lg mb-2 line-clamp-2">{task.title}</h3>
                                <p className={`text-sm mb-4 line-clamp-3 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                                    {task.description}
                                </p>

                                <div className="grid grid-cols-2 gap-4 text-sm mb-4">
                                    <div>
                                        <p className="text-gray-500 text-xs">Materia</p>
                                        <p className="font-medium">{task.subject?.name}</p>
                                    </div>
                                    <div>
                                        <p className="text-gray-500 text-xs">Punteo</p>
                                        <p className="font-medium">{task.points} pts</p>
                                    </div>
                                </div>

                                <div className="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div className="flex items-center gap-2">
                                        <Calendar size={14} className="text-gray-400" />
                                        <span className="text-sm font-medium">{task.dueDate}</span>
                                    </div>
                                    {!completed && <TimeRemaining dueDate={task.dueDate} />}
                                </div>
                            </div>

                            <button className={`w-full py-3 text-sm font-semibold transition-colors ${completed
                                    ? 'bg-transparent text-green-600 cursor-default'
                                    : 'bg-blue-600 hover:bg-blue-700 text-white'
                                }`}>
                                {completed ? 'Ver Calificación' : 'Entregar Tarea'}
                            </button>
                        </div>
                    );
                })}

                {tasks.length === 0 && (
                    <div className="col-span-full p-12 text-center text-gray-500 bg-gray-50 dark:bg-gray-800 rounded-xl">
                        <CheckCircle size={48} className="mx-auto mb-4 text-gray-300" />
                        <p>No tienes tareas pendientes. ¡Buen trabajo!</p>
                    </div>
                )}
            </div>
        </div>
    );
};

export default TareasPage;
