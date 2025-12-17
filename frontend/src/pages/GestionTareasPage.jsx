import React, { useState, useEffect } from 'react';
import { ClipboardList, Plus, Search, Filter, Calendar, BookOpen, GraduationCap, Edit, Trash2, Eye, Check, Clock, AlertCircle, X, Save, ChevronDown } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { useAuth } from '../contexts/AuthContext';

const GestionTareasPage = () => {
    const { darkMode } = useTheme();
    const { user } = useAuth();
    const [loading, setLoading] = useState(true);
    const [tasks, setTasks] = useState([]);
    const [filteredTasks, setFilteredTasks] = useState([]);
    const [showModal, setShowModal] = useState(false);
    const [editingTask, setEditingTask] = useState(null);
    const [selectedBimester, setSelectedBimester] = useState('');
    const [selectedCourse, setSelectedCourse] = useState('');
    const [selectedGrade, setSelectedGrade] = useState('');
    const [searchTerm, setSearchTerm] = useState('');

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none w-full ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;

    // Demo: Teacher's courses
    const teacherCourses = [
        { id: 1, name: 'Matemáticas' },
        { id: 2, name: 'Física' },
    ];

    // Demo: Available grades for each course
    const availableGrades = [
        { id: 1, name: '3ro Primaria A' },
        { id: 2, name: '3ro Primaria B' },
        { id: 3, name: '4to Primaria A' },
        { id: 4, name: '5to Primaria A' },
        { id: 5, name: '1ro Básico A' },
        { id: 6, name: '2do Básico A' },
    ];

    // Bimesters
    const bimesters = [
        { id: 1, name: '1er Bimestre', start: '2025-01-06', end: '2025-03-14' },
        { id: 2, name: '2do Bimestre', start: '2025-03-17', end: '2025-05-30' },
        { id: 3, name: '3er Bimestre', start: '2025-06-02', end: '2025-08-15' },
        { id: 4, name: '4to Bimestre', start: '2025-08-18', end: '2025-10-31' },
    ];

    // Demo tasks
    const demoTasks = [
        {
            id: 1,
            title: 'Ejercicios de Álgebra',
            description: 'Resolver ejercicios 1-20 del libro de texto, páginas 45-48',
            course: 'Matemáticas',
            bimester: 1,
            dueDate: '2025-01-20',
            points: 10,
            type: 'tarea',
            grades: ['3ro Primaria A', '3ro Primaria B'],
            status: 'active',
            submissions: 45,
            pending: 5,
        },
        {
            id: 2,
            title: 'Examen Parcial - Fracciones',
            description: 'Evaluación sobre suma, resta y multiplicación de fracciones',
            course: 'Matemáticas',
            bimester: 1,
            dueDate: '2025-01-25',
            points: 25,
            type: 'examen',
            grades: ['4to Primaria A', '5to Primaria A'],
            status: 'active',
            submissions: 30,
            pending: 2,
        },
        {
            id: 3,
            title: 'Proyecto: Movimiento Rectilíneo',
            description: 'Elaborar un video demostrando el MRU con materiales caseros',
            course: 'Física',
            bimester: 1,
            dueDate: '2025-02-01',
            points: 30,
            type: 'proyecto',
            grades: ['1ro Básico A', '2do Básico A'],
            status: 'active',
            submissions: 20,
            pending: 10,
        },
        {
            id: 4,
            title: 'Ejercicios de Geometría',
            description: 'Cálculo de áreas y perímetros',
            course: 'Matemáticas',
            bimester: 1,
            dueDate: '2025-01-15',
            points: 15,
            type: 'tarea',
            grades: ['3ro Primaria A'],
            status: 'completed',
            submissions: 25,
            pending: 0,
        },
    ];

    // Form state
    const [formData, setFormData] = useState({
        title: '',
        description: '',
        course: '',
        bimester: 1,
        dueDate: '',
        points: 10,
        type: 'tarea',
        grades: [],
    });

    useEffect(() => {
        setTimeout(() => {
            setTasks(demoTasks);
            setFilteredTasks(demoTasks);
            setLoading(false);
        }, 500);
    }, []);

    useEffect(() => {
        let filtered = [...tasks];
        if (searchTerm) {
            const term = searchTerm.toLowerCase();
            filtered = filtered.filter(t => t.title.toLowerCase().includes(term) || t.description.toLowerCase().includes(term));
        }
        if (selectedBimester) filtered = filtered.filter(t => t.bimester === parseInt(selectedBimester));
        if (selectedCourse) filtered = filtered.filter(t => t.course === selectedCourse);
        if (selectedGrade) filtered = filtered.filter(t => t.grades.includes(selectedGrade));
        setFilteredTasks(filtered);
    }, [searchTerm, selectedBimester, selectedCourse, selectedGrade, tasks]);

    const openNewTask = () => {
        setEditingTask(null);
        setFormData({
            title: '',
            description: '',
            course: teacherCourses[0]?.name || '',
            bimester: 1,
            dueDate: '',
            points: 10,
            type: 'tarea',
            grades: [],
        });
        setShowModal(true);
    };

    const openEditTask = (task) => {
        setEditingTask(task);
        setFormData({
            title: task.title,
            description: task.description,
            course: task.course,
            bimester: task.bimester,
            dueDate: task.dueDate,
            points: task.points,
            type: task.type,
            grades: task.grades,
        });
        setShowModal(true);
    };

    const handleSave = () => {
        if (!formData.title || !formData.dueDate || formData.grades.length === 0) {
            alert('Por favor complete todos los campos requeridos');
            return;
        }

        if (editingTask) {
            setTasks(prev => prev.map(t => t.id === editingTask.id ? { ...t, ...formData } : t));
        } else {
            const newTask = {
                id: Date.now(),
                ...formData,
                status: 'active',
                submissions: 0,
                pending: 50,
            };
            setTasks(prev => [...prev, newTask]);
        }
        setShowModal(false);
    };

    const handleDelete = (taskId) => {
        if (confirm('¿Está seguro de eliminar esta tarea?')) {
            setTasks(prev => prev.filter(t => t.id !== taskId));
        }
    };

    const toggleGrade = (grade) => {
        setFormData(prev => ({
            ...prev,
            grades: prev.grades.includes(grade) ? prev.grades.filter(g => g !== grade) : [...prev.grades, grade]
        }));
    };

    const getTypeColor = (type) => {
        switch (type) {
            case 'tarea': return darkMode ? 'bg-blue-900/50 text-blue-300' : 'bg-blue-100 text-blue-700';
            case 'examen': return darkMode ? 'bg-red-900/50 text-red-300' : 'bg-red-100 text-red-700';
            case 'proyecto': return darkMode ? 'bg-purple-900/50 text-purple-300' : 'bg-purple-100 text-purple-700';
            default: return darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600';
        }
    };

    const getStatusColor = (status) => {
        return status === 'completed'
            ? darkMode ? 'bg-green-900/50 text-green-300' : 'bg-green-100 text-green-700'
            : darkMode ? 'bg-yellow-900/50 text-yellow-300' : 'bg-yellow-100 text-yellow-700';
    };

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <ClipboardList className="animate-pulse mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando tareas...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Gestión de Tareas</h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>Cree y administre tareas, exámenes y proyectos</p>
                </div>
                <button onClick={openNewTask} className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg flex items-center gap-2">
                    <Plus size={18} /> Nueva Tarea
                </button>
            </div>

            {/* Stats */}
            <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-sm`}>
                    <div className="flex items-center gap-3">
                        <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${darkMode ? 'bg-blue-900/50' : 'bg-blue-100'}`}>
                            <ClipboardList className="text-blue-500" size={20} />
                        </div>
                        <div>
                            <p className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{tasks.length}</p>
                            <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Total Tareas</p>
                        </div>
                    </div>
                </div>
                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-sm`}>
                    <div className="flex items-center gap-3">
                        <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${darkMode ? 'bg-yellow-900/50' : 'bg-yellow-100'}`}>
                            <Clock className="text-yellow-500" size={20} />
                        </div>
                        <div>
                            <p className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{tasks.filter(t => t.status === 'active').length}</p>
                            <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Activas</p>
                        </div>
                    </div>
                </div>
                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-sm`}>
                    <div className="flex items-center gap-3">
                        <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${darkMode ? 'bg-green-900/50' : 'bg-green-100'}`}>
                            <Check className="text-green-500" size={20} />
                        </div>
                        <div>
                            <p className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{tasks.filter(t => t.status === 'completed').length}</p>
                            <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Completadas</p>
                        </div>
                    </div>
                </div>
                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-sm`}>
                    <div className="flex items-center gap-3">
                        <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${darkMode ? 'bg-red-900/50' : 'bg-red-100'}`}>
                            <AlertCircle className="text-red-500" size={20} />
                        </div>
                        <div>
                            <p className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{tasks.reduce((a, t) => a + t.pending, 0)}</p>
                            <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Pendientes</p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Filters */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm`}>
                <div className="flex flex-wrap gap-4 items-end">
                    <div className="flex-1 min-w-[200px]">
                        <label className={`block text-xs font-medium mb-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Buscar</label>
                        <div className="relative">
                            <Search className={`absolute left-3 top-1/2 -translate-y-1/2 ${darkMode ? 'text-gray-400' : 'text-gray-400'}`} size={16} />
                            <input type="text" placeholder="Título o descripción..." value={searchTerm} onChange={e => setSearchTerm(e.target.value)} className={`${inputClass} pl-9`} />
                        </div>
                    </div>
                    <div className="w-40">
                        <label className={`block text-xs font-medium mb-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Bimestre</label>
                        <select value={selectedBimester} onChange={e => setSelectedBimester(e.target.value)} className={inputClass}>
                            <option value="">Todos</option>
                            {bimesters.map(b => <option key={b.id} value={b.id}>{b.name}</option>)}
                        </select>
                    </div>
                    <div className="w-40">
                        <label className={`block text-xs font-medium mb-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Curso</label>
                        <select value={selectedCourse} onChange={e => setSelectedCourse(e.target.value)} className={inputClass}>
                            <option value="">Todos</option>
                            {teacherCourses.map(c => <option key={c.id} value={c.name}>{c.name}</option>)}
                        </select>
                    </div>
                    <div className="w-48">
                        <label className={`block text-xs font-medium mb-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Grado</label>
                        <select value={selectedGrade} onChange={e => setSelectedGrade(e.target.value)} className={inputClass}>
                            <option value="">Todos</option>
                            {availableGrades.map(g => <option key={g.id} value={g.name}>{g.name}</option>)}
                        </select>
                    </div>
                </div>
            </div>

            {/* Tasks List */}
            <div className="grid gap-4">
                {filteredTasks.map(task => (
                    <div key={task.id} className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm`}>
                        <div className="flex items-start justify-between">
                            <div className="flex-1">
                                <div className="flex items-center gap-3 mb-2">
                                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${getTypeColor(task.type)}`}>
                                        {task.type.charAt(0).toUpperCase() + task.type.slice(1)}
                                    </span>
                                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(task.status)}`}>
                                        {task.status === 'completed' ? 'Completada' : 'Activa'}
                                    </span>
                                    <span className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        {bimesters.find(b => b.id === task.bimester)?.name}
                                    </span>
                                </div>
                                <h3 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{task.title}</h3>
                                <p className={`mt-1 ${darkMode ? 'text-gray-400' : 'text-gray-600'} text-sm`}>{task.description}</p>
                                <div className="flex flex-wrap gap-4 mt-3">
                                    <div className="flex items-center gap-1 text-sm">
                                        <BookOpen size={14} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                                        <span className={darkMode ? 'text-gray-300' : 'text-gray-700'}>{task.course}</span>
                                    </div>
                                    <div className="flex items-center gap-1 text-sm">
                                        <Calendar size={14} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                                        <span className={darkMode ? 'text-gray-300' : 'text-gray-700'}>Entrega: {task.dueDate}</span>
                                    </div>
                                    <div className="flex items-center gap-1 text-sm">
                                        <GraduationCap size={14} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                                        <span className={darkMode ? 'text-gray-300' : 'text-gray-700'}>{task.grades.join(', ')}</span>
                                    </div>
                                </div>
                                <div className="flex items-center gap-4 mt-3">
                                    <span className={`px-3 py-1 rounded-lg text-sm font-medium ${darkMode ? 'bg-teal-900/50 text-teal-300' : 'bg-teal-100 text-teal-700'}`}>
                                        {task.points} pts
                                    </span>
                                    <span className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        {task.submissions} entregas • {task.pending} pendientes
                                    </span>
                                </div>
                            </div>
                            <div className="flex gap-2">
                                <button onClick={() => openEditTask(task)} className={`p-2 rounded-lg ${darkMode ? 'bg-gray-700 hover:bg-gray-600' : 'bg-gray-100 hover:bg-gray-200'}`}>
                                    <Edit size={16} className={darkMode ? 'text-gray-300' : 'text-gray-600'} />
                                </button>
                                <button onClick={() => handleDelete(task.id)} className={`p-2 rounded-lg ${darkMode ? 'bg-red-900/50 hover:bg-red-900' : 'bg-red-100 hover:bg-red-200'}`}>
                                    <Trash2 size={16} className="text-red-500" />
                                </button>
                            </div>
                        </div>
                    </div>
                ))}

                {filteredTasks.length === 0 && (
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center shadow-sm`}>
                        <ClipboardList className={`mx-auto ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} size={48} />
                        <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>No se encontraron tareas</p>
                        <button onClick={openNewTask} className="mt-4 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
                            Crear Primera Tarea
                        </button>
                    </div>
                )}
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto`}>
                        <div className={`p-6 border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'} flex items-center justify-between`}>
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {editingTask ? 'Editar Tarea' : 'Nueva Tarea'}
                            </h2>
                            <button onClick={() => setShowModal(false)} className={`p-2 rounded-lg ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100'}`}>
                                <X size={20} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                            </button>
                        </div>

                        <div className="p-6 space-y-4">
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Título *</label>
                                <input type="text" value={formData.title} onChange={e => setFormData(prev => ({ ...prev, title: e.target.value }))} className={inputClass} placeholder="Ej: Ejercicios de Álgebra" />
                            </div>

                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Descripción</label>
                                <textarea value={formData.description} onChange={e => setFormData(prev => ({ ...prev, description: e.target.value }))} className={`${inputClass} h-24 resize-none`} placeholder="Instrucciones detalladas..." />
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Tipo</label>
                                    <select value={formData.type} onChange={e => setFormData(prev => ({ ...prev, type: e.target.value }))} className={inputClass}>
                                        <option value="tarea">Tarea</option>
                                        <option value="examen">Examen</option>
                                        <option value="proyecto">Proyecto</option>
                                    </select>
                                </div>
                                <div>
                                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Curso</label>
                                    <select value={formData.course} onChange={e => setFormData(prev => ({ ...prev, course: e.target.value }))} className={inputClass}>
                                        {teacherCourses.map(c => <option key={c.id} value={c.name}>{c.name}</option>)}
                                    </select>
                                </div>
                            </div>

                            <div className="grid grid-cols-3 gap-4">
                                <div>
                                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Bimestre</label>
                                    <select value={formData.bimester} onChange={e => setFormData(prev => ({ ...prev, bimester: parseInt(e.target.value) }))} className={inputClass}>
                                        {bimesters.map(b => <option key={b.id} value={b.id}>{b.name}</option>)}
                                    </select>
                                </div>
                                <div>
                                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Fecha Entrega *</label>
                                    <input type="date" value={formData.dueDate} onChange={e => setFormData(prev => ({ ...prev, dueDate: e.target.value }))} className={inputClass} />
                                </div>
                                <div>
                                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Puntos</label>
                                    <input type="number" min="1" max="100" value={formData.points} onChange={e => setFormData(prev => ({ ...prev, points: parseInt(e.target.value) }))} className={inputClass} />
                                </div>
                            </div>

                            <div>
                                <label className={`block text-sm font-medium mb-2 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Grados Asignados *</label>
                                <div className="flex flex-wrap gap-2">
                                    {availableGrades.map(grade => (
                                        <button
                                            key={grade.id}
                                            onClick={() => toggleGrade(grade.name)}
                                            className={`px-3 py-2 rounded-lg text-sm transition-all ${formData.grades.includes(grade.name)
                                                    ? 'bg-teal-600 text-white'
                                                    : darkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                                }`}
                                        >
                                            {formData.grades.includes(grade.name) && <Check size={14} className="inline mr-1" />}
                                            {grade.name}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        </div>

                        <div className={`p-6 border-t ${darkMode ? 'border-gray-700' : 'border-gray-200'} flex justify-end gap-3`}>
                            <button onClick={() => setShowModal(false)} className={`px-4 py-2 rounded-lg ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'}`}>
                                Cancelar
                            </button>
                            <button onClick={handleSave} className="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg flex items-center gap-2">
                                <Save size={18} /> Guardar
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default GestionTareasPage;
