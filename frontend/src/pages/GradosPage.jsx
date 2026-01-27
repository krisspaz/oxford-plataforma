import { toast } from '../utils/toast';
import React, { useState, useEffect } from 'react';
import { GraduationCap, Plus, Edit, ChevronDown, ChevronRight, X, RefreshCw, Trash } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { catalogService, gradeService } from '../services';

const GradosPage = () => {
    const { darkMode } = useTheme();
    const [expandedLevel, setExpandedLevel] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [modalType, setModalType] = useState('grade'); // 'grade' or 'section'
    const [selectedItem, setSelectedItem] = useState(null); // The grade being edited or section parent
    const [loading, setLoading] = useState(true);
    const [levels, setLevels] = useState([]);

    // Form States
    const [gradeForm, setGradeForm] = useState({ name: '', code: '', levelId: null, capacity: 30 });
    const [sectionForm, setSectionForm] = useState({ name: '', capacity: 30 });

    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        setLoading(true);
        try {
            const [levelsResponse, gradesResponse] = await Promise.all([
                catalogService.getAcademicLevels(),
                gradeService.getAll()
            ]);

            // Extract data from axios responses
            // catalogService returns axios response: {data: [...]}
            // gradeService.getAll returns response.data directly: [...] or {member/hydra:member}
            const levelsData = levelsResponse?.data || levelsResponse || [];

            // gradeService.getAll() already returns response.data
            let gradesData = gradesResponse;
            if (gradesResponse?.['hydra:member']) gradesData = gradesResponse['hydra:member'];
            else if (gradesResponse?.member) gradesData = gradesResponse.member;
            else if (!Array.isArray(gradesResponse)) gradesData = [];

            console.log('=== DEBUG ===');
            console.log('levelsData:', levelsData);
            console.log('gradesData:', gradesData);

            if (Array.isArray(levelsData) && levelsData.length > 0) {
                const organizedLevels = levelsData.map(level => {
                    const levelGrades = gradesData.filter(g => {
                        if (!g.level) return false;
                        if (typeof g.level === 'object' && g.level.id) return g.level.id === level.id;
                        if (typeof g.level === 'string') {
                            const match = g.level.match(/\/(\d+)$/);
                            return match && parseInt(match[1]) === level.id;
                        }
                        if (typeof g.level === 'number') return g.level === level.id;
                        return false;
                    });
                    return { ...level, grades: levelGrades };
                });
                setLevels(organizedLevels);
            } else {
                console.error('No levels data received:', levelsData);
                setLevels([]);
            }
        } catch (error) {
            console.error(error);
            toast.error('Error al cargar datos');
        } finally {
            setLoading(false)
        }
    };

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
            capacity: 30 // Grade entity doesn't have capacity, Section does. But user might want to set default? Not critical now.
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

    const handleSaveGrade = async () => {
        try {
            if (selectedItem) {
                // Edit
                await gradeService.update(selectedItem.id, gradeForm);
            } else {
                // Create
                await gradeService.create(gradeForm);
            }
            setShowModal(false);
            loadData();
        } catch (error) {
            console.error(error);
            toast.error('Error al guardar grado: ' + (error.response?.data?.error || error.message));
        }
    };

    const handleSaveSection = async () => {
        try {
            // selectedItem is the Grade
            await gradeService.createSection(selectedItem.id, sectionForm);
            setShowModal(false);
            loadData();
        } catch (error) {
            console.error(error);
            toast.error('Error al guardar sección: ' + (error.response?.data?.error || error.message));
        }
    };

    const handleDeleteGrade = async (id) => {
        if (!window.confirm('¿Eliminar grado? Esto borrará secciones y desvinculará alumnos.')) return;
        try {
            await gradeService.delete(id);
            loadData();
        } catch (error) {
            toast.info('No se pudo eliminar el grado');
        }
    };

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando grados...</p>
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
                {levels.length === 0 && <div className="text-gray-500 text-center py-10">No hay niveles definidos. Vaya a "Gestión de Niveles" primero.</div>}

                {levels.map(level => (
                    <div key={level.id} className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                        {/* Level Header */}
                        <div
                            onClick={() => setExpandedLevel(expandedLevel === level.id ? null : level.id)}
                            className={`p-4 flex items-center justify-between cursor-pointer ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}`}
                        >
                            <div className="flex items-center gap-4">
                                <div className="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-700 rounded-xl flex items-center justify-center text-white font-bold">
                                    {level.code}
                                </div>
                                <div>
                                    <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{level.name}</h3>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        {level.grades?.length || 0} grados
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                <button
                                    onClick={(e) => { e.stopPropagation(); openAddGradeModal(level); }}
                                    className="p-2 text-teal-500 hover:bg-teal-50 dark:hover:bg-teal-900/30 rounded-lg"
                                    title="Agregar Grado"
                                >
                                    <Plus size={18} />
                                </button>
                                {expandedLevel === level.id ? <ChevronDown size={20} /> : <ChevronRight size={20} />}
                            </div>
                        </div>

                        {/* Grades */}
                        {expandedLevel === level.id && level.grades && (
                            <div className={`border-t ${darkMode ? 'border-gray-700' : 'bg-white border-gray-200 text-gray-900'}`}>
                                <table className="w-full">
                                    <thead className={darkMode ? 'bg-gray-700' : 'bg-gray-50'}>
                                        <tr>
                                            <th className={`px-4 py-2 text-left text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Grado</th>
                                            <th className={`px-4 py-2 text-center text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Secciones</th>
                                            <th className={`px-4 py-2 text-center text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                                        {level.grades.map(grade => (
                                            <tr key={grade.id} className={darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}>
                                                <td className={`px-4 py-2 font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{grade.name}</td>
                                                <td className="px-4 py-2 text-center">
                                                    <div className="flex justify-center gap-1 flex-wrap">
                                                        {grade.sections?.map((section) => (
                                                            <span key={section.id} className="px-2 py-0.5 bg-teal-100 text-teal-700 rounded text-sm font-medium">
                                                                {section.name}
                                                            </span>
                                                        ))}
                                                        <button
                                                            onClick={() => openAddSectionModal(grade)}
                                                            className="px-2 py-0.5 border border-dashed border-gray-300 text-gray-400 rounded hover:text-teal-500 hover:border-teal-500 text-xs"
                                                        >
                                                            +
                                                        </button>
                                                    </div>
                                                </td>
                                                <td className="px-4 py-2 text-center">
                                                    <div className="flex justify-center gap-1">
                                                        <button
                                                            onClick={() => openEditGradeModal(grade)}
                                                            className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`}
                                                        >
                                                            <Edit size={16} className="text-blue-500" />
                                                        </button>
                                                        <button
                                                            onClick={() => handleDeleteGrade(grade.id)}
                                                            className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`}
                                                        >
                                                            <Trash size={16} className="text-red-500" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                        {level.grades.length === 0 && (
                                            <tr>
                                                <td colSpan="3" className="text-center py-4 text-gray-500 text-sm">No hay grados en este nivel.</td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        )}
                    </div>
                ))}
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-md p-6`}>
                        <div className="flex items-center justify-between mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {modalType === 'grade'
                                    ? (selectedItem ? 'Editar Grado' : 'Nuevo Grado')
                                    : 'Nueva Sección para ' + selectedItem?.name
                                }
                            </h2>
                            <button onClick={() => setShowModal(false)}><X size={20} /></button>
                        </div>

                        <div className="space-y-4">
                            {modalType === 'grade' ? (
                                <>
                                    <div>
                                        <label className={labelClass}>Nivel Académico</label>
                                        <select
                                            className={inputClass}
                                            value={gradeForm.levelId}
                                            onChange={e => setGradeForm({ ...gradeForm, levelId: parseInt(e.target.value) })}
                                            disabled={!!selectedItem} // Disable level change on edit
                                        >
                                            {levels.map(l => <option key={l.id} value={l.id}>{l.name}</option>)}
                                        </select>
                                    </div>
                                    <div>
                                        <label className={labelClass}>Nombre del Grado</label>
                                        <input
                                            type="text"
                                            className={inputClass}
                                            value={gradeForm.name}
                                            onChange={e => setGradeForm({ ...gradeForm, name: e.target.value })}
                                            placeholder="Ej: 1ro Básico"
                                        />
                                    </div>
                                    <div>
                                        <label className={labelClass}>Código</label>
                                        <input
                                            type="text"
                                            className={inputClass}
                                            value={gradeForm.code}
                                            onChange={e => setGradeForm({ ...gradeForm, code: e.target.value })}
                                            placeholder="Ej: 1BAS"
                                        />
                                    </div>
                                </>
                            ) : (
                                <>
                                    <div>
                                        <label className={labelClass}>Nombre de Sección</label>
                                        <input
                                            type="text"
                                            className={inputClass}
                                            value={sectionForm.name}
                                            onChange={e => setSectionForm({ ...sectionForm, name: e.target.value })}
                                            placeholder="Ej: A"
                                        />
                                    </div>
                                    <div>
                                        <label className={labelClass}>Capacidad</label>
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

                        <div className="flex justify-end gap-3 mt-6">
                            <button onClick={() => setShowModal(false)} className={`px-4 py-2 rounded-lg ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}>Cancelar</button>
                            <button
                                onClick={modalType === 'grade' ? handleSaveGrade : handleSaveSection}
                                className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg"
                            >
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
