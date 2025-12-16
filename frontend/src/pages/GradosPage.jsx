import React, { useState, useEffect } from 'react';
import { GraduationCap, Plus, Edit, ChevronDown, ChevronRight, X, RefreshCw } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { catalogService } from '../services';

const GradosPage = () => {
    const { darkMode } = useTheme();
    const [expandedLevel, setExpandedLevel] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [modalType, setModalType] = useState('grade'); // 'grade' or 'section'
    const [selectedItem, setSelectedItem] = useState(null);
    const [loading, setLoading] = useState(true);
    const [levels, setLevels] = useState([]);

    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        setLoading(true);
        try {
            const [levelsRes, gradesRes] = await Promise.all([
                catalogService.getAcademicLevels(),
                catalogService.getGrades()
            ]);

            if (levelsRes.success) {
                // Organize grades by level
                const gradesData = gradesRes.success ? gradesRes.data : [];
                const organizedLevels = levelsRes.data.map(level => ({
                    ...level,
                    grades: gradesData.filter(g => g.level === level.name) || []
                }));
                setLevels(organizedLevels);
            }
        } catch (error) {
            console.error('Error loading data:', error);
            // Demo data
            setLevels([
                {
                    id: 1,
                    code: 'PRE',
                    name: 'Preprimaria',
                    grades: [
                        { id: 1, name: 'Kinder 4', sections: ['A', 'B'], capacity: 25 },
                        { id: 2, name: 'Kinder 5', sections: ['A', 'B'], capacity: 25 },
                        { id: 3, name: 'Preparatoria', sections: ['A', 'B'], capacity: 25 },
                    ]
                },
                {
                    id: 2,
                    code: 'PRI',
                    name: 'Primaria',
                    grades: [
                        { id: 4, name: '1ro Primaria', sections: ['A', 'B'], capacity: 30 },
                        { id: 5, name: '2do Primaria', sections: ['A', 'B'], capacity: 30 },
                        { id: 6, name: '3ro Primaria', sections: ['A', 'B'], capacity: 30 },
                        { id: 7, name: '4to Primaria', sections: ['A', 'B'], capacity: 30 },
                        { id: 8, name: '5to Primaria', sections: ['A', 'B'], capacity: 30 },
                        { id: 9, name: '6to Primaria', sections: ['A', 'B'], capacity: 30 },
                    ]
                },
                {
                    id: 3,
                    code: 'BAS',
                    name: 'Básico',
                    grades: [
                        { id: 10, name: '1ro Básico', sections: ['A', 'B', 'C'], capacity: 35 },
                        { id: 11, name: '2do Básico', sections: ['A', 'B'], capacity: 35 },
                        { id: 12, name: '3ro Básico', sections: ['A'], capacity: 35 },
                    ]
                },
                {
                    id: 4,
                    code: 'BAC',
                    name: 'Bachillerato',
                    grades: [
                        { id: 13, name: '4to Bachillerato', sections: ['A'], capacity: 30 },
                        { id: 14, name: '5to Bachillerato', sections: ['A'], capacity: 30 },
                    ]
                },
            ]);
        } finally {
            setLoading(false);
        }
    };

    const openAddGradeModal = (level) => {
        setSelectedItem({ level });
        setModalType('grade');
        setShowModal(true);
    };

    const openEditGradeModal = (grade) => {
        setSelectedItem(grade);
        setModalType('grade');
        setShowModal(true);
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
                                >
                                    <Plus size={18} />
                                </button>
                                {expandedLevel === level.id ? <ChevronDown size={20} /> : <ChevronRight size={20} />}
                            </div>
                        </div>

                        {/* Grades */}
                        {expandedLevel === level.id && level.grades && (
                            <div className={`border-t ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                                <table className="w-full">
                                    <thead className={darkMode ? 'bg-gray-700' : 'bg-gray-50'}>
                                        <tr>
                                            <th className={`px-4 py-2 text-left text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Grado</th>
                                            <th className={`px-4 py-2 text-center text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Secciones</th>
                                            <th className={`px-4 py-2 text-center text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Capacidad</th>
                                            <th className={`px-4 py-2 text-center text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                                        {level.grades.map(grade => (
                                            <tr key={grade.id} className={darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}>
                                                <td className={`px-4 py-2 font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{grade.name}</td>
                                                <td className="px-4 py-2 text-center">
                                                    <div className="flex justify-center gap-1">
                                                        {grade.sections?.map((section, i) => (
                                                            <span key={i} className="px-2 py-0.5 bg-teal-100 text-teal-700 rounded text-sm font-medium">
                                                                {section}
                                                            </span>
                                                        ))}
                                                    </div>
                                                </td>
                                                <td className={`px-4 py-2 text-center ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                                                    {grade.capacity} alumnos
                                                </td>
                                                <td className="px-4 py-2 text-center">
                                                    <button
                                                        onClick={() => openEditGradeModal(grade)}
                                                        className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`}
                                                    >
                                                        <Edit size={16} className="text-blue-500" />
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
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
                                {selectedItem?.id ? 'Editar Grado' : 'Nuevo Grado'}
                            </h2>
                            <button onClick={() => setShowModal(false)}><X size={20} /></button>
                        </div>
                        <div className="space-y-4">
                            <div>
                                <label className={labelClass}>Nivel Académico</label>
                                <select className={inputClass} defaultValue={selectedItem?.level?.id}>
                                    {levels.map(l => <option key={l.id} value={l.id}>{l.name}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className={labelClass}>Nombre del Grado</label>
                                <input type="text" className={inputClass} defaultValue={selectedItem?.name} placeholder="Ej: 1ro Básico" />
                            </div>
                            <div>
                                <label className={labelClass}>Capacidad por Sección</label>
                                <input type="number" className={inputClass} defaultValue={selectedItem?.capacity || 30} />
                            </div>
                            <div>
                                <label className={labelClass}>Secciones</label>
                                <div className="flex gap-2 flex-wrap mt-1">
                                    {['A', 'B', 'C', 'D'].map(section => (
                                        <label key={section} className="flex items-center gap-1">
                                            <input type="checkbox" defaultChecked={selectedItem?.sections?.includes(section)} />
                                            <span className={darkMode ? 'text-gray-300' : 'text-gray-700'}>{section}</span>
                                        </label>
                                    ))}
                                </div>
                            </div>
                        </div>
                        <div className="flex justify-end gap-3 mt-6">
                            <button onClick={() => setShowModal(false)} className={`px-4 py-2 rounded-lg ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}>Cancelar</button>
                            <button onClick={() => setShowModal(false)} className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">Guardar</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default GradosPage;
