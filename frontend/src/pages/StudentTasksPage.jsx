import React, { useState, useEffect } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import { ClipboardList, Clock, CheckCircle, XCircle, Calendar, BookOpen, Filter, ChevronRight, AlertTriangle } from 'lucide-react';

const StudentTasksPage = () => {
    const { darkMode } = useTheme();
    const [activeTab, setActiveTab] = useState('pending');
    const [selectedSubject, setSelectedSubject] = useState('');
    const [selectedBimester, setSelectedBimester] = useState('');
    const [selectedTask, setSelectedTask] = useState(null);

    // Mock data - conectar con backend real
    const subjects = ['Todas', 'Matemáticas', 'Física', 'Química', 'Historia', 'Inglés'];
    const bimesters = ['Todos', '1er Bimestre', '2do Bimestre', '3er Bimestre', '4to Bimestre'];

    const [tasks] = useState([
        // Pending
        { id: 1, title: 'Ejercicios de Álgebra', subject: 'Matemáticas', dueDate: '2025-01-20', points: 15, status: 'pending', bimester: '1er Bimestre', description: 'Resolver ejercicios 1-20 del capítulo 3', type: 'tarea' },
        { id: 2, title: 'Ensayo sobre la Revolución', subject: 'Historia', dueDate: '2025-01-22', points: 20, status: 'pending', bimester: '1er Bimestre', description: 'Ensayo de 2 páginas sobre causas y consecuencias', type: 'proyecto' },
        { id: 3, title: 'Examen Parcial', subject: 'Física', dueDate: '2025-01-25', points: 30, status: 'pending', bimester: '1er Bimestre', description: 'Estudiar capítulos 1-4: Movimiento rectilíneo', type: 'examen' },
        // Submitted
        { id: 4, title: 'Práctica de Laboratorio', subject: 'Química', dueDate: '2025-01-15', points: 15, status: 'submitted', bimester: '1er Bimestre', submittedDate: '2025-01-14', grade: 14, description: 'Reporte del experimento de reacciones', type: 'tarea' },
        { id: 5, title: 'Reading Comprehension', subject: 'Inglés', dueDate: '2025-01-10', points: 10, status: 'submitted', bimester: '1er Bimestre', submittedDate: '2025-01-10', grade: 9, description: 'Leer texto y responder preguntas', type: 'tarea' },
        { id: 6, title: 'Línea del Tiempo', subject: 'Historia', dueDate: '2025-01-12', points: 20, status: 'submitted', bimester: '1er Bimestre', submittedDate: '2025-01-11', grade: null, description: 'Crear línea del tiempo ilustrada', type: 'proyecto' },
        // Overdue
        { id: 7, title: 'Ecuaciones Cuadráticas', subject: 'Matemáticas', dueDate: '2025-01-05', points: 15, status: 'overdue', bimester: '1er Bimestre', description: 'Resolver problemas de aplicación', type: 'tarea' },
        { id: 8, title: 'Vocabulary Quiz', subject: 'Inglés', dueDate: '2025-01-03', points: 10, status: 'overdue', bimester: '1er Bimestre', description: 'Estudiar vocabulario unidad 2', type: 'examen' },
    ]);

    const filterTasks = (status) => {
        return tasks.filter(task => {
            const matchStatus = task.status === status;
            const matchSubject = !selectedSubject || selectedSubject === 'Todas' || task.subject === selectedSubject;
            const matchBimester = !selectedBimester || selectedBimester === 'Todos' || task.bimester === selectedBimester;
            return matchStatus && matchSubject && matchBimester;
        });
    };

    const pendingTasks = filterTasks('pending');
    const submittedTasks = filterTasks('submitted');
    const overdueTasks = filterTasks('overdue');

    const getTypeColor = (type) => {
        switch (type) {
            case 'tarea': return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
            case 'examen': return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
            case 'proyecto': return 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400';
            default: return 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
        }
    };

    const getDaysRemaining = (dueDate) => {
        const today = new Date();
        const due = new Date(dueDate);
        const diff = Math.ceil((due - today) / (1000 * 60 * 60 * 24));
        return diff;
    };

    const tabs = [
        { id: 'pending', label: 'Pendientes', icon: Clock, count: pendingTasks.length, color: 'text-yellow-500' },
        { id: 'submitted', label: 'Entregadas', icon: CheckCircle, count: submittedTasks.length, color: 'text-green-500' },
        { id: 'overdue', label: 'Vencidas', icon: XCircle, count: overdueTasks.length, color: 'text-red-500' },
    ];

    const currentTasks = activeTab === 'pending' ? pendingTasks : activeTab === 'submitted' ? submittedTasks : overdueTasks;

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className={`text-2xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                        <ClipboardList className="text-obs-blue" /> Mis Tareas
                    </h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>
                        Gestiona tus tareas, exámenes y proyectos
                    </p>
                </div>
            </div>

            {/* Stats Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {tabs.map(tab => (
                    <div
                        key={tab.id}
                        onClick={() => setActiveTab(tab.id)}
                        className={`p-4 rounded-xl cursor-pointer transition-all ${activeTab === tab.id
                                ? 'ring-2 ring-obs-pink shadow-lg'
                                : ''
                            } ${darkMode ? 'bg-gray-800 hover:bg-gray-700' : 'bg-white hover:bg-gray-50'} shadow-sm`}
                    >
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-3">
                                <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${tab.id === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/30' :
                                        tab.id === 'submitted' ? 'bg-green-100 dark:bg-green-900/30' :
                                            'bg-red-100 dark:bg-red-900/30'
                                    }`}>
                                    <tab.icon size={20} className={tab.color} />
                                </div>
                                <div>
                                    <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{tab.count}</p>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{tab.label}</p>
                                </div>
                            </div>
                            {activeTab === tab.id && <ChevronRight className="text-obs-pink" />}
                        </div>
                    </div>
                ))}
            </div>

            {/* Filters */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm`}>
                <div className="flex flex-wrap gap-4 items-center">
                    <div className="flex items-center gap-2">
                        <Filter size={16} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                        <span className={`text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Filtrar:</span>
                    </div>
                    <select
                        value={selectedSubject}
                        onChange={e => setSelectedSubject(e.target.value)}
                        className={`px-3 py-2 rounded-lg border ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300'}`}
                    >
                        {subjects.map(s => <option key={s} value={s === 'Todas' ? '' : s}>{s}</option>)}
                    </select>
                    <select
                        value={selectedBimester}
                        onChange={e => setSelectedBimester(e.target.value)}
                        className={`px-3 py-2 rounded-lg border ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300'}`}
                    >
                        {bimesters.map(b => <option key={b} value={b === 'Todos' ? '' : b}>{b}</option>)}
                    </select>
                </div>
            </div>

            {/* Tasks List & Detail Panel */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Tasks List */}
                <div className={`lg:col-span-2 ${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                    {currentTasks.length === 0 ? (
                        <div className="p-12 text-center">
                            <ClipboardList size={48} className={`mx-auto ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                            <p className={`mt-4 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                No hay tareas {activeTab === 'pending' ? 'pendientes' : activeTab === 'submitted' ? 'entregadas' : 'vencidas'}
                            </p>
                        </div>
                    ) : (
                        <div className="divide-y divide-gray-200 dark:divide-gray-700">
                            {currentTasks.map(task => {
                                const daysLeft = getDaysRemaining(task.dueDate);
                                const isUrgent = task.status === 'pending' && daysLeft <= 2 && daysLeft > 0;

                                return (
                                    <div
                                        key={task.id}
                                        onClick={() => setSelectedTask(task)}
                                        className={`p-4 cursor-pointer transition-colors ${selectedTask?.id === task.id
                                                ? darkMode ? 'bg-obs-pink/10' : 'bg-obs-pink/5'
                                                : darkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'
                                            }`}
                                    >
                                        <div className="flex items-start gap-4">
                                            <div className={`w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 ${task.status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/30' :
                                                    task.status === 'submitted' ? 'bg-green-100 dark:bg-green-900/30' :
                                                        'bg-red-100 dark:bg-red-900/30'
                                                }`}>
                                                {task.status === 'pending' && <Clock className="text-yellow-500" size={20} />}
                                                {task.status === 'submitted' && <CheckCircle className="text-green-500" size={20} />}
                                                {task.status === 'overdue' && <XCircle className="text-red-500" size={20} />}
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <div className="flex items-center gap-2 mb-1">
                                                    <span className={`px-2 py-0.5 rounded text-xs font-medium ${getTypeColor(task.type)}`}>
                                                        {task.type.charAt(0).toUpperCase() + task.type.slice(1)}
                                                    </span>
                                                    {isUrgent && (
                                                        <span className="px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 flex items-center gap-1">
                                                            <AlertTriangle size={12} /> Urgente
                                                        </span>
                                                    )}
                                                </div>
                                                <h3 className={`font-semibold truncate ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                                    {task.title}
                                                </h3>
                                                <div className="flex flex-wrap items-center gap-3 mt-1">
                                                    <span className={`text-sm flex items-center gap-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                                        <BookOpen size={14} /> {task.subject}
                                                    </span>
                                                    <span className={`text-sm flex items-center gap-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                                        <Calendar size={14} /> {task.dueDate}
                                                    </span>
                                                    <span className={`text-sm font-medium ${darkMode ? 'text-obs-blue' : 'text-obs-blue'}`}>
                                                        {task.points} pts
                                                    </span>
                                                </div>
                                                {task.status === 'submitted' && task.grade !== null && (
                                                    <div className="mt-2">
                                                        <span className={`px-2 py-1 rounded text-sm font-medium ${task.grade >= task.points * 0.7
                                                                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                                                : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                                            }`}>
                                                            Nota: {task.grade}/{task.points}
                                                        </span>
                                                    </div>
                                                )}
                                                {task.status === 'submitted' && task.grade === null && (
                                                    <div className="mt-2">
                                                        <span className="px-2 py-1 rounded text-sm font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                            ⏳ Pendiente de calificación
                                                        </span>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </div>

                {/* Detail Panel */}
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                    {selectedTask ? (
                        <>
                            <div className={`p-4 ${selectedTask.status === 'pending' ? 'bg-yellow-500' :
                                    selectedTask.status === 'submitted' ? 'bg-green-500' : 'bg-red-500'
                                } text-white`}>
                                <span className="text-sm opacity-75">{selectedTask.bimester}</span>
                                <h3 className="font-bold text-lg">{selectedTask.title}</h3>
                                <p className="text-sm opacity-75">{selectedTask.subject}</p>
                            </div>
                            <div className="p-4 space-y-4">
                                <div>
                                    <p className={`text-xs uppercase tracking-wider ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Descripción</p>
                                    <p className={`mt-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>{selectedTask.description}</p>
                                </div>
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <p className={`text-xs uppercase tracking-wider ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Fecha Límite</p>
                                        <p className={`mt-1 font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{selectedTask.dueDate}</p>
                                    </div>
                                    <div>
                                        <p className={`text-xs uppercase tracking-wider ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Puntos</p>
                                        <p className={`mt-1 font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{selectedTask.points} pts</p>
                                    </div>
                                </div>
                                {selectedTask.status === 'submitted' && (
                                    <div>
                                        <p className={`text-xs uppercase tracking-wider ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Entregado el</p>
                                        <p className={`mt-1 font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{selectedTask.submittedDate}</p>
                                    </div>
                                )}
                                {selectedTask.status === 'pending' && (
                                    <button className="w-full py-3 bg-gradient-to-r from-obs-pink to-obs-purple text-white rounded-lg font-medium hover:opacity-90 transition-opacity">
                                        📤 Entregar Tarea
                                    </button>
                                )}
                                {selectedTask.status === 'overdue' && (
                                    <div className={`p-3 rounded-lg ${darkMode ? 'bg-red-900/20' : 'bg-red-50'}`}>
                                        <p className="text-red-500 text-sm font-medium">⚠️ Esta tarea venció el {selectedTask.dueDate}</p>
                                        <p className={`text-sm mt-1 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                                            Contacta a tu profesor para solicitar prórroga.
                                        </p>
                                    </div>
                                )}
                            </div>
                        </>
                    ) : (
                        <div className="p-12 text-center">
                            <ClipboardList size={48} className={`mx-auto ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                            <p className={`mt-4 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                Selecciona una tarea para ver los detalles
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default StudentTasksPage;
