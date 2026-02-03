import { toast } from 'sonner';
import React, { useState, useMemo } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ClipboardList, Plus, Search, Calendar, BookOpen, GraduationCap, Edit, Trash2, Check, Clock, AlertCircle, X, Save, Loader2 } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { useAuth } from '../contexts/AuthContext';
import { taskService, gradeService, bimesterService, teacherService } from '../services';
import { Select, EmptyState, ErrorState } from '../components/ui';

const GestionTareasPage = ({ viewMode = 'management' }) => {
    const { darkMode } = useTheme();
    const { user, hasRole } = useAuth(); // Assuming user has { id, teacherId } attached if role is teacher? 
    // If not, we might need to fetch teacher profile or rely on backend 'my-tasks'.
    // Actually, create task requires 'teacherId' in the body. If the backend doesn't infer it from the logged in user for creation, we need it.
    // The previous TaskController::create looked up Teacher by ID.
    // Ideally the controller should infer it. BUT, for now let's assume we need to find our own teacher ID or the controller is updated.
    // Wait, TaskController::create takes 'teacherId'.
    // Let's assume we can get it from a profile call or AuthContext.
    // For now, let's try to fetch "Me" from teacher service if needed.

    const queryClient = useQueryClient();

    // UI State
    const [showModal, setShowModal] = useState(false);
    const [editingTask, setEditingTask] = useState(null);
    const [selectedBimester, setSelectedBimester] = useState('');
    const [selectedSubject, setSelectedSubject] = useState('');
    const [selectedGrade, setSelectedGrade] = useState('');
    const [searchTerm, setSearchTerm] = useState('');

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-obs-blue outline-none w-full ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'}`;

    // Form state
    const [formData, setFormData] = useState({
        title: '',
        description: '',
        subjectId: '',
        bimesterId: '',
        dueDate: '',
        points: 10,
        type: 'tarea',
        cycleId: 1,
        grades: [],
    });

    // === QUERIES ===
    const { data: myAssignments = [], isLoading: loadingAssignments } = useQuery({
        queryKey: ['teacher', 'assignments'],
        queryFn: () => teacherService.getMyAssignments(),
        staleTime: 5 * 60 * 1000,
    });

    const { data: grades = [], isLoading: loadingGrades } = useQuery({
        queryKey: ['grades'],
        queryFn: () => gradeService.getAll(),
    });

    const { data: bimesters = [], isLoading: loadingBimesters } = useQuery({
        queryKey: ['bimesters', 'active'],
        queryFn: () => bimesterService.getAll({ active: true }),
    });

    const { data: tasks = [], isLoading: loadingTasks, error: tasksError, refetch: refetchTasks } = useQuery({
        queryKey: ['tasks', 'mine'],
        queryFn: () => taskService.getMyTasks(),
    });

    const { data: currentTeacher } = useQuery({
        queryKey: ['teacher', 'me'],
        queryFn: () => teacherService.getMe(),
        enabled: hasRole('ROLE_DOCENTE'),
    });

    // Derived subjects from assignments
    const subjects = useMemo(() => {
        const uniqueSubjects = [];
        const seenIds = new Set();
        myAssignments.forEach(a => {
            if (a.subject && !seenIds.has(a.subject.id)) {
                seenIds.add(a.subject.id);
                uniqueSubjects.push(a.subject);
            }
        });
        return uniqueSubjects;
    }, [myAssignments]);

    const isLoading = loadingAssignments || loadingGrades || loadingBimesters || loadingTasks;

    // Filtered tasks (derived)
    const filteredTasks = useMemo(() => {
        let filtered = [...tasks];
        if (searchTerm) {
            const term = searchTerm.toLowerCase();
            filtered = filtered.filter(t => t.title?.toLowerCase().includes(term));
        }
        if (selectedBimester) {
            filtered = filtered.filter(t => t.bimester?.id === parseInt(selectedBimester));
        }
        if (selectedSubject) {
            filtered = filtered.filter(t => t.subject?.id === parseInt(selectedSubject));
        }
        return filtered;
    }, [tasks, searchTerm, selectedBimester, selectedSubject]);

    // === MUTATIONS ===
    const createTaskMutation = useMutation({
        mutationFn: (data) => taskService.create(data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['tasks'] });
            toast.success('Tarea creada exitosamente');
            setShowModal(false);
        },
        onError: (err) => toast.error(err.message || 'Error al crear tarea'),
    });

    const updateTaskMutation = useMutation({
        mutationFn: ({ id, data }) => taskService.update(id, data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['tasks'] });
            toast.success('Tarea actualizada');
            setShowModal(false);
        },
        onError: (err) => toast.error(err.message || 'Error al actualizar tarea'),
    });

    const deleteTaskMutation = useMutation({
        mutationFn: (id) => taskService.delete(id),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['tasks'] });
            toast.success('Tarea eliminada');
        },
        onError: () => toast.error('Error al eliminar tarea'),
    });

    const openNewTask = () => {
        setEditingTask(null);
        setFormData({
            title: '',
            description: '',
            subjectId: subjects[0]?.id || '',
            bimesterId: bimesters[0]?.id || '',
            dueDate: '',
            points: 10,
            type: 'tarea',
            cycleId: 1, // hardcoded for now or fetch active
            grades: [],
        });
        setShowModal(true);
    };

    const openEditTask = (task) => {
        setEditingTask(task);
        setFormData({
            title: task.title,
            description: task.description,
            subjectId: task.subject?.id || '',
            bimesterId: task.bimester?.id || '',
            dueDate: task.dueDate,
            points: task.points,
            type: task.type,
            cycleId: 1,
            grades: task.grades || [],
        });
        setShowModal(true);
    };

    const handleSave = () => {
        if (!formData.title || !formData.dueDate || formData.grades.length === 0) {
            toast.warning('Por favor complete todos los campos requeridos (Título, Fecha, Grados)');
            return;
        }

        const cycleId = bimesters[0]?.schoolCycle?.id || 1;
        const teacherId = currentTeacher?.id;

        if (!teacherId) {
            toast.error('No se pudo identificar su perfil docente');
            return;
        }

        const payload = {
            ...formData,
            teacherId,
            cycleId,
            points: parseInt(formData.points),
            subjectId: parseInt(formData.subjectId),
            bimesterId: parseInt(formData.bimesterId),
            grades: formData.grades,
        };

        if (editingTask) {
            updateTaskMutation.mutate({ id: editingTask.id, data: payload });
        } else {
            createTaskMutation.mutate(payload);
        }
    };

    const handleDelete = (taskId) => {
        if (window.confirm('¿Está seguro de eliminar esta tarea?')) {
            deleteTaskMutation.mutate(taskId);
        }
    };

    const toggleGrade = (gradeId, sectionId = 1) => {
        setFormData(prev => {
            const exists = prev.grades.find(g => g.gradeId === gradeId && g.sectionId === sectionId);
            if (exists) {
                return { ...prev, grades: prev.grades.filter(g => g !== exists) };
            } else {
                return { ...prev, grades: [...prev.grades, { gradeId, sectionId }] };
            }
        });
    };

    // Helper to check if grade is selected
    const isGradeSelected = (gradeId, sectionId = 1) => {
        return formData.grades.some(g => g.gradeId === gradeId && g.sectionId === sectionId);
    };

    const getTypeColor = (type) => {
        switch (type) {
            case 'examen': return 'bg-red-100 text-red-700';
            case 'proyecto': return 'bg-purple-100 text-purple-700';
            default: return 'bg-blue-100 text-blue-700';
        }
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'completed': return 'bg-green-100 text-green-700';
            case 'active': return 'bg-yellow-100 text-yellow-700';
            default: return 'bg-gray-100 text-gray-700';
        }
    };


    if (isLoading) {
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
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                        {viewMode === 'grading' ? 'Tareas Calificadas / Por Calificar' : 'Gestión de Tareas'}
                    </h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>Cree y administre tareas, exámenes y proyectos</p>
                </div>
                <button onClick={openNewTask} className="px-4 py-2 bg-gradient-to-r from-obs-pink to-obs-purple hover:from-pink-600 hover:to-purple-700 text-white rounded-lg flex items-center gap-2 shadow-lg shadow-obs-pink/20 transition-all hover:scale-105">
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
                            {subjects.map(c => <option key={c.id} value={c.name}>{c.name}</option>)}
                        </select>
                    </div>
                    <div className="w-48">
                        <label className={`block text-xs font-medium mb-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Grado</label>
                        <select value={selectedGrade} onChange={e => setSelectedGrade(e.target.value)} className={inputClass}>
                            <option value="">Todos</option>
                            {grades.map(g => <option key={g.id} value={g.name}>{g.name}</option>)}
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
                                    <span className={`px-3 py-1 rounded-lg text-sm font-medium ${darkMode ? 'bg-obs-blue/20 text-obs-blue' : 'bg-obs-blue/10 text-obs-blue'}`}>
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
                        <button onClick={openNewTask} className="mt-4 px-4 py-2 bg-gradient-to-r from-obs-pink to-obs-purple hover:from-pink-600 hover:to-purple-700 text-white rounded-lg shadow-lg shadow-obs-pink/20">
                            Crear Primera Tarea
                        </button>
                    </div>
                )}
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto`}>
                        <div className={`p-6 border-b ${darkMode ? 'border-gray-700' : 'bg-white border-gray-200 text-gray-900'} flex items-center justify-between`}>
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
                                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Materia</label>
                                    <select value={formData.subjectId} onChange={e => setFormData(prev => ({ ...prev, subjectId: e.target.value }))} className={inputClass}>
                                        <option value="">-- Seleccionar --</option>
                                        {subjects.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                                    </select>
                                </div>
                            </div>

                            <div className="grid grid-cols-3 gap-4">
                                <div>
                                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Bimestre</label>
                                    <select value={formData.bimesterId} onChange={e => setFormData(prev => ({ ...prev, bimesterId: parseInt(e.target.value) }))} className={inputClass}>
                                        <option value="">-- Seleccionar --</option>
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
                                <div className="flex flex-wrap gap-2 max-h-40 overflow-y-auto">
                                    {grades.map(grade => (
                                        <button
                                            key={grade.id}
                                            onClick={() => toggleGrade(grade.id)}
                                            className={`px-3 py-2 rounded-lg text-sm transition-all border ${isGradeSelected(grade.id)
                                                ? 'bg-obs-green text-white shadow-md border-obs-green'
                                                : darkMode ? 'bg-gray-700 text-gray-300 border-gray-600 hover:bg-gray-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-white hover:shadow-sm'
                                                }`}
                                        >
                                            {isGradeSelected(grade.id) && <Check size={14} className="inline mr-1" />}
                                            {grade.name}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        </div>

                        <div className={`p-6 border-t ${darkMode ? 'border-gray-700' : 'bg-white border-gray-200 text-gray-900'} flex justify-end gap-3`}>
                            <button onClick={() => setShowModal(false)} className={`px-4 py-2 rounded-lg ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'}`}>
                                Cancelar
                            </button>
                            <button onClick={handleSave} className="px-6 py-2 bg-gradient-to-r from-obs-pink to-obs-purple hover:from-pink-600 hover:to-purple-700 text-white rounded-lg flex items-center gap-2 shadow-lg shadow-obs-pink/20">
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
