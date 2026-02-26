import { toast } from 'sonner';
import React, { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useTheme } from '../contexts/ThemeContext';
import { catalogService, gradeService } from '../services';

const GradosPage = () => {
    const { darkMode } = useTheme();
    const queryClient = useQueryClient();
    const [expandedLevel, setExpandedLevel] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [modalType, setModalType] = useState('grade'); // 'grade' or 'section'
    const [selectedItem, setSelectedItem] = useState(null); // The grade being edited or section parent

    // Form States
    const [gradeForm, setGradeForm] = useState({ name: '', code: '', levelId: null, capacity: 30 });
    const [sectionForm, setSectionForm] = useState({ name: '', capacity: 30 });

    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    // --- QUERIES ---
    const { data: levels = [], isLoading: loadingLevels } = useQuery({
        queryKey: ['academicLevels'],
        queryFn: async () => {
            const res = await catalogService.getAcademicLevels();
            return res.data || res || [];
        }
    });

    const { data: grades = [], isLoading: loadingGrades } = useQuery({
        queryKey: ['grades'],
        queryFn: async () => {
            const res = await gradeService.getAll();
            // Handle Hydra/Array variations
            if (res?.['hydra:member']) return res['hydra:member'];
            if (res?.member) return res.member;
            return Array.isArray(res) ? res : [];
        }
    });

    const isLoading = loadingLevels || loadingGrades;

    // Organize Data: Levels -> Grades -> Sections
    const organizedData = React.useMemo(() => {
        if (!levels.length) return [];
        return levels.map(level => {
            const levelGrades = grades.filter(g => {
                if (!g.level) return false;
                // Handle various formats of relation (object vs IRI vs ID)
                const gLevelId = typeof g.level === 'object' ? g.level.id : (typeof g.level === 'number' ? g.level : null);
                return gLevelId === level.id;
            });
            return { ...level, grades: levelGrades };
        });
    }, [levels, grades]);

    // --- MUTATIONS ---
    const createGradeMutation = useMutation({
        mutationFn: gradeService.create,
        onSuccess: () => {
            queryClient.invalidateQueries(['grades']);
            toast.success('Grado creado correctamente');
            setShowModal(false);
        },
        onError: (err) => toast.error('Error al crear grado: ' + (err.response?.data?.error || err.message))
    });

    const updateGradeMutation = useMutation({
        mutationFn: ({ id, data }) => gradeService.update(id, data),
        onSuccess: () => {
            queryClient.invalidateQueries(['grades']);
            toast.success('Grado actualizado');
            setShowModal(false);
        },
        onError: (err) => toast.error('Error al actualizar grado: ' + (err.response?.data?.error || err.message))
    });

    const deleteGradeMutation = useMutation({
        mutationFn: gradeService.delete,
        onSuccess: () => {
            queryClient.invalidateQueries(['grades']);
            toast.success('Grado eliminado');
        },
        // eslint-disable-next-line unused-imports/no-unused-vars
        onError: (err) => toast.error('Error al eliminar grado')
    });

    const createSectionMutation = useMutation({
        mutationFn: ({ gradeId, data }) => gradeService.createSection(gradeId, data),
        onSuccess: () => {
            queryClient.invalidateQueries(['grades']); // Re-fetch grades to see new section count/details if nested
            toast.success('Sección creada');
            setShowModal(false);
        },
        onError: (err) => toast.error('Error al crear sección: ' + (err.response?.data?.error || err.message))
    });


    // --- HANDLERS ---
    const openAddGradeModal = (level) => {
        setSelectedItem(null); // New Grade
        setGradeForm({ name: '', code: '', levelId: level.id, capacity: 30 });
        setModalType('grade');
        setShowModal(true);
    };

    const openEditGradeModal = (grade) => {
        setSelectedItem(grade);
        setGradeForm({
            name: grade.name,
            code: grade.code || '',
            levelId: grade.level?.id,
            capacity: 30
        });
        setModalType('grade');
        setShowModal(true);
    };

    const openAddSectionModal = (grade) => {
        setSelectedItem(grade); // Parent Grade
        setSectionForm({ name: 'A', capacity: 30 });
        setModalType('section');
        setShowModal(true);
    };

    const handleSaveGrade = () => {
        if (selectedItem) {
            updateGradeMutation.mutate({ id: selectedItem.id, data: gradeForm });
        } else {
            createGradeMutation.mutate(gradeForm);
        }
    };

    const handleSaveSection = () => {
        createSectionMutation.mutate({ gradeId: selectedItem.id, data: sectionForm });
    };

    const handleDeleteGrade = (id) => {
        if (window.confirm('¿Eliminar grado? Esto podría afectar datos relacionados.')) {
            deleteGradeMutation.mutate(id);
        }
    };

    if (isLoading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando estructura académica...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Niveles y Grados</h1>
            </div>

            {/* Levels List */}
            <div className="space-y-4">
                {organizedData.length === 0 && (
                    <div className="text-gray-500 text-center py-10 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                        <GraduationCap size={48} className="mx-auto mb-3 opacity-50" />
                        <p>No hay niveles definidos.</p>
                        <p className="text-sm mt-2">Configure los niveles académicos en el catálogo global.</p>
                    </div>
                )}

                {organizedData.map(level => (
                    <div key={level.id} className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden transition-all duration-200 border border-transparent ${expandedLevel === level.id ? 'ring-2 ring-teal-500/20' : ''}`}>
                        {/* Level Header */}
                        <div
                            onClick={() => setExpandedLevel(expandedLevel === level.id ? null : level.id)}
                            className={`p-4 flex items-center justify-between cursor-pointer ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}`}
                        >
                            <div className="flex items-center gap-4">
                                <div className="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-700 rounded-xl flex items-center justify-center text-white font-bold shadow-sm">
                                    {level.code?.substring(0, 2) || 'NV'}
                                </div>
                                <div>
                                    <h3 className={`font-bold text-lg ${darkMode ? 'text-white' : 'text-gray-800'}`}>{level.name}</h3>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        {level.grades?.length || 0} grados configurados
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <button
                                    onClick={(e) => { e.stopPropagation(); openAddGradeModal(level); }}
                                    className="px-3 py-1.5 text-sm bg-teal-50 text-teal-700 hover:bg-teal-100 dark:bg-teal-900/30 dark:text-teal-300 dark:hover:bg-teal-900/50 rounded-lg flex items-center gap-1 font-medium transition-colors"
                                    title="Agregar Grado"
                                >
                                    <Plus size={16} /> Agregar Grado
                                </button>
                                {expandedLevel === level.id ? <ChevronDown size={20} className="text-gray-400" /> : <ChevronRight size={20} className="text-gray-400" />}
                            </div>
                        </div>

                        {/* Grades */}
                        {expandedLevel === level.id && (
                            <div className={`border-t ${darkMode ? 'border-gray-700' : 'bg-gray-50/50 border-gray-100'}`}>
                                {level.grades && level.grades.length > 0 ? (
                                    <div className="overflow-x-auto">
                                        <table className="w-full">
                                            <thead>
                                                <tr>
                                                    <th className={`px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Grado</th>
                                                    <th className={`px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Secciones</th>
                                                    <th className={`px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-200'}`}>
                                                {level.grades.map(grade => (
                                                    <tr key={grade.id} className={`group ${darkMode ? 'bg-gray-800 hover:bg-gray-700/80' : 'bg-white hover:bg-white'}`}>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <div className={`text-sm font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{grade.name}</div>
                                                            <div className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-500'}`}>{grade.code}</div>
                                                        </td>
                                                        <td className="px-6 py-4">
                                                            <div className="flex items-center gap-2 flex-wrap">
                                                                {grade.sections?.map((section) => (
                                                                    <div key={section.id} className={`px-2.5 py-1 rounded-md text-xs font-semibold border ${darkMode ? 'bg-teal-900/30 text-teal-300 border-teal-800' : 'bg-teal-50 text-teal-700 border-teal-100'}`}>
                                                                        {section.name}
                                                                    </div>
                                                                ))}
                                                                <button
                                                                    onClick={() => openAddSectionModal(grade)}
                                                                    className={`px-2 py-1 rounded-md text-xs border border-dashed transition-colors ${darkMode ? 'border-gray-600 text-gray-400 hover:text-white hover:border-gray-400' : 'border-gray-300 text-gray-400 hover:text-gray-600 hover:border-gray-400'}`}
                                                                >
                                                                    + Sección
                                                                </button>
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <div className="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                                <button
                                                                    onClick={() => openEditGradeModal(grade)}
                                                                    className="p-1.5 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors"
                                                                >
                                                                    <Edit size={16} />
                                                                </button>
                                                                <button
                                                                    onClick={() => handleDeleteGrade(grade.id)}
                                                                    className="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors"
                                                                >
                                                                    <Trash size={16} />
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                ) : (
                                    <div className="p-8 text-center">
                                        <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>No hay grados registrados en {level.name}.</p>
                                        <button
                                            onClick={() => openAddGradeModal(level)}
                                            className="mt-2 text-sm text-teal-500 hover:underline font-medium"
                                        >
                                            Crear primer grado
                                        </button>
                                    </div>
                                )}
                            </div>
                        )}
                    </div>
                ))}
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-all">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all scale-100`}>
                        <div className={`px-6 py-4 border-b ${darkMode ? 'border-gray-700' : 'border-gray-100'} flex items-center justify-between`}>
                            <h2 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {modalType === 'grade'
                                    ? (selectedItem ? 'Editar Grado' : 'Nuevo Grado')
                                    : `Nueva Sección - ${selectedItem?.name}`
                                }
                            </h2>
                            <button onClick={() => setShowModal(false)} className={`p-1 rounded-full ${darkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-500'}`}><X size={20} /></button>
                        </div>

                        <div className="p-6 space-y-5">
                            {modalType === 'grade' ? (
                                <>
                                    <div>
                                        <label className={labelClass}>Nivel Académico</label>
                                        <div className={`px-3 py-2 border rounded-lg bg-opacity-50 ${darkMode ? 'bg-gray-900 border-gray-700 text-gray-400' : 'bg-gray-50 border-gray-200 text-gray-500'}`}>
                                            {levels.find(l => l.id === gradeForm.levelId)?.name || 'Seleccione nivel'}
                                        </div>
                                    </div>
                                    <div>
                                        <label className={labelClass}>Nombre del Grado <span className="text-red-500">*</span></label>
                                        <input
                                            type="text"
                                            className={inputClass}
                                            value={gradeForm.name}
                                            onChange={e => setGradeForm({ ...gradeForm, name: e.target.value })}
                                            placeholder="Ej: Primero Primaria"
                                            autoFocus
                                        />
                                    </div>
                                    <div>
                                        <label className={labelClass}>Código Corto</label>
                                        <input
                                            type="text"
                                            className={inputClass}
                                            value={gradeForm.code}
                                            onChange={e => setGradeForm({ ...gradeForm, code: e.target.value })}
                                            placeholder="Ej: 1PRI"
                                        />
                                    </div>
                                </>
                            ) : (
                                <>
                                    <div>
                                        <label className={labelClass}>Identificador de Sección <span className="text-red-500">*</span></label>
                                        <input
                                            type="text"
                                            className={inputClass}
                                            value={sectionForm.name}
                                            onChange={e => setSectionForm({ ...sectionForm, name: e.target.value })}
                                            placeholder="Ej: A, B, Matutina..."
                                            autoFocus
                                        />
                                        <p className="text-xs text-gray-500 mt-1">Generalmente una letra (A, B, C) o nombre identificador.</p>
                                    </div>
                                    <div>
                                        <label className={labelClass}>Capacidad Máxima</label>
                                        <input
                                            type="number"
                                            className={inputClass}
                                            value={sectionForm.capacity}
                                            onChange={e => setSectionForm({ ...sectionForm, capacity: parseInt(e.target.value) })}
                                        />
                                    </div>
                                </>
                            )}
                        </div>

                        <div className={`px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex justify-end gap-3`}>
                            <button
                                onClick={() => setShowModal(false)}
                                className={`px-4 py-2 rounded-lg font-medium text-sm ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-200'}`}
                            >
                                Cancelar
                            </button>
                            <button
                                onClick={modalType === 'grade' ? handleSaveGrade : handleSaveSection}
                                disabled={createGradeMutation.isPending || updateGradeMutation.isPending || createSectionMutation.isPending}
                                className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium text-sm shadow-lg shadow-teal-500/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                            >
                                {(createGradeMutation.isPending || updateGradeMutation.isPending || createSectionMutation.isPending) && <RefreshCw size={14} className="animate-spin" />}
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default GradosPage;
