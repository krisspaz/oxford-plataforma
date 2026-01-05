import { useState, useEffect } from 'react';
import { Clock, CheckCircle, XCircle, AlertCircle, FileText, Calendar, Filter } from 'lucide-react';
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
        return <span className="text-red-500 font-bold text-xs">¡Vencida!</span>;
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
    const [filter, setFilter] = useState('pending'); // pending, overdue, submitted

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

    const getFilteredTasks = () => {
        const now = new Date();
        return tasks.filter(task => {
            const isSubmitted = !!task.mySubmission;
            const dueDate = new Date(task.dueDate);
            const isOverdue = dueDate < now && !isSubmitted;

            if (filter === 'submitted') return isSubmitted;
            if (filter === 'overdue') return isOverdue;
            if (filter === 'pending') return !isSubmitted && !isOverdue;
            return true;
        });
    };

    if (loading) {
        return <div className="p-10 text-center">Cargando tareas...</div>;
    }

    const filteredTasks = getFilteredTasks();

    const tabs = [
        { id: 'pending', label: 'Pendientes', icon: Clock, count: tasks.filter(t => !t.mySubmission && new Date(t.dueDate) >= new Date()).length },
        { id: 'overdue', label: 'Vencidas', icon: AlertCircle, count: tasks.filter(t => !t.mySubmission && new Date(t.dueDate) < new Date()).length },
        { id: 'submitted', label: 'Entregadas', icon: CheckCircle, count: tasks.filter(t => t.mySubmission).length },
    ];

    return (
        <div className={`space-y-6 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h1 className="text-2xl font-bold flex items-center gap-2">
                    <FileText className="text-blue-500" />
                    Mis Tareas
                </h1>

                {/* Tabs */}
                <div className={`flex p-1 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-gray-100'}`}>
                    {tabs.map(tab => (
                        <button
                            key={tab.id}
                            onClick={() => setFilter(tab.id)}
                            className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all
                                ${filter === tab.id
                                    ? (darkMode ? 'bg-gray-700 text-white shadow-sm' : 'bg-white text-gray-900 shadow-sm')
                                    : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'
                                }
                            `}
                        >
                            <tab.icon size={16} className={
                                filter === tab.id
                                    ? (tab.id === 'overdue' ? 'text-red-500' : tab.id === 'submitted' ? 'text-green-500' : 'text-blue-500')
                                    : ''
                            } />
                            {tab.label}
                            {tab.count > 0 && (
                                <span className={`text-[10px] px-1.5 py-0.5 rounded-full 
                                    ${filter === tab.id
                                        ? (darkMode ? 'bg-gray-600' : 'bg-gray-200')
                                        : (darkMode ? 'bg-gray-800' : 'bg-gray-200')}
                                `}>
                                    {tab.count}
                                </span>
                            )}
                        </button>
                    ))}
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {filteredTasks.map(task => {
                    const isSubmitted = !!task.mySubmission;
                    const isOverdue = new Date(task.dueDate) < new Date() && !isSubmitted;

                    return (
                        <div key={task.id} className={`rounded-xl shadow-md overflow-hidden border relative transition-all hover:-translate-y-1
                            ${isSubmitted
                                ? (darkMode ? 'bg-green-900/5 border-green-900/30' : 'bg-green-50 border-green-200')
                                : isOverdue
                                    ? (darkMode ? 'bg-red-900/5 border-red-900/30' : 'bg-red-50 border-red-200')
                                    : (darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200')
                            }`}>

                            <div className="p-5">
                                <div className="flex justify-between items-start mb-3">
                                    <span className={`px-2 py-1 rounded text-xs font-semibold uppercase ${task.type === 'examen' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'
                                        }`}>
                                        {task.type}
                                    </span>
                                    {isSubmitted ? (
                                        <span className="flex items-center text-green-600 text-sm font-medium">
                                            <CheckCircle size={16} className="mr-1" /> Entregada
                                        </span>
                                    ) : isOverdue ? (
                                        <span className="flex items-center text-red-500 text-sm font-bold">
                                            <XCircle size={16} className="mr-1" /> Vencida
                                        </span>
                                    ) : (
                                        <span className="flex items-center text-blue-500 text-sm font-medium">
                                            <Clock size={16} className="mr-1" /> Pendiente
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
                                        <p className="font-medium truncate">{task.subject?.name}</p>
                                    </div>
                                    <div>
                                        <p className="text-gray-500 text-xs">Punteo</p>
                                        <p className="font-medium">{task.points} pts</p>
                                    </div>
                                </div>

                                <div className="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div className="flex items-center gap-2">
                                        <Calendar size={14} className="text-gray-400" />
                                        <span className={`text-sm font-medium ${isOverdue ? 'text-red-500' : ''}`}>
                                            {task.dueDate}
                                        </span>
                                    </div>
                                    {!isSubmitted && !isOverdue && <TimeRemaining dueDate={task.dueDate} />}
                                </div>
                            </div>

                            <button
                                disabled={isSubmitted}
                                className={`w-full py-3 text-sm font-semibold transition-colors 
                                ${isSubmitted
                                        ? 'bg-transparent text-green-600 cursor-default border-t border-green-200 dark:border-green-900/30'
                                        : isOverdue
                                            ? 'bg-red-600 hover:bg-red-700 text-white'
                                            : 'bg-blue-600 hover:bg-blue-700 text-white'
                                    }`}>
                                {isSubmitted ? 'Ver Calificación' : isOverdue ? 'Entregar con Retraso' : 'Entregar Tarea'}
                            </button>
                        </div>
                    );
                })}

                {filteredTasks.length === 0 && (
                    <div className="col-span-full p-12 text-center text-gray-500 bg-gray-50 dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                        <Filter size={48} className="mx-auto mb-4 text-gray-300" />
                        <p>No hay tareas en esta sección.</p>
                        {filter === 'pending' && <p className="text-sm mt-2">¡Estás al día! 🎉</p>}
                    </div>
                )}
            </div>
        </div>
    );
};

export default TareasPage;
