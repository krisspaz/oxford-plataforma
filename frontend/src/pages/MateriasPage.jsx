import { toast } from '../utils/toast';
import React, { useState, useEffect } from 'react';
import { Book, Plus, Search, Edit, X, Users, RefreshCw } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { catalogService } from '../services';
import teacherService from '../services/teacherService';
import gradeService from '../services/gradeService';
import subjectService from '../services/subjectService';

const MateriasPage = () => {
    const { darkMode } = useTheme();
    const [activeTab, setActiveTab] = useState('materias');
    const [searchTerm, setSearchTerm] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [selectedItem, setSelectedItem] = useState(null);
    const [loading, setLoading] = useState(true);

    // Data States
    const [subjects, setSubjects] = useState([]);
    const [assignments] = useState([]);
    const [teachers, setTeachers] = useState([]);
    const [grades, setGrades] = useState([]);
    const [sections, setSections] = useState([]); // Filtered sections based on selected grade

    const [formData, setFormData] = useState({
        code: '',
        name: '',
        hoursWeek: '',
        active: true,
        // Assignment fields
        subjectId: '',
        teacherId: '',
        gradeId: '',
        sectionId: ''
    });

    const inputClass = darkMode
        ? 'px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none bg-gray-700 border-gray-600 text-white'
        : 'px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none bg-white border-gray-300 text-gray-900';
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    useEffect(() => {
        loadData();
    }, []);

    // Watch for grade changes to load sections
    useEffect(() => {
        if (formData.gradeId && activeTab === 'asignaciones') {
            loadSections(formData.gradeId);
        }
    }, [formData.gradeId, activeTab]);

    const loadSections = async (gradeId) => {
        try {
            const data = await gradeService.getSections(gradeId);
            setSections(Array.isArray(data) ? data : []);
        } catch (error) {
            console.error("Error loading sections", error);
            setSections([]);
        }
    };

    const handleSave = async () => {
        try {
            if (activeTab === 'materias') {
                const payload = {
                    code: formData.code,
                    name: formData.name,
                    hoursWeek: parseInt(formData.hoursWeek) || 0,
                    active: true
                };

                if (selectedItem) {
                    await catalogService.updateSubject(selectedItem.id, payload);
                } else {
                    await catalogService.createSubject(payload);
                }
            } else {
                // Assignment Logic
                if (!formData.subjectId || !formData.teacherId || !formData.gradeId) {
                    alert("Por favor complete todos los campos requeridos");
                    return;
                }
                const payload = {
                    subjectId: formData.subjectId,
                    teacherId: formData.teacherId,
                    gradeId: formData.gradeId,
                    sectionId: formData.sectionId || null,
                    hoursPerWeek: parseInt(formData.hoursWeek) || 0
                };

                // Assuming create/assign endpoint (adjust if update is different)
                await subjectService.assign(payload);
            }

            setShowModal(false);
            loadData();
        } catch (error) {
            console.error('Error saving:', error);
            toast.error('Error al guardar: ' + (error.message || 'Error desconocido'));
        }
    };

    const loadData = async () => {
        setLoading(true);
        try {
            // Parallel fetching
            const [subjectsData, teachersData, gradesData] = await Promise.all([
                catalogService.getSubjects(),
                teacherService.getAll(),
                gradeService.getAll()
            ]);

            // Handle Subjects
            let loadedSubjects = [];
            if (subjectsData && subjectsData.member) loadedSubjects = subjectsData.member;
            else if (Array.isArray(subjectsData)) loadedSubjects = subjectsData;
            else if (subjectsData.data) loadedSubjects = subjectsData.data;
            setSubjects(loadedSubjects);

            // Handle Teachers
            setTeachers(Array.isArray(teachersData) ? teachersData : (teachersData.member || []));

            // Handle Grades
            setGrades(Array.isArray(gradesData) ? gradesData : (gradesData.member || []));

            // For assignments, since we don't have a dedicated endpoint yet, we might need to 
            // rely on subjects having 'assignments' property or implement a fetch later.
            // For now, we leave it empty to avoid fake data, or maybe filter subjects?
            // setAssignments([...]); 

        } catch (error) {
            console.error('Error loading data:', error);
        } finally {
            setLoading(false);
        }
    };

    const filteredSubjects = subjects.filter(s =>
        s.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        s.code.toLowerCase().includes(searchTerm.toLowerCase())
    );

    // Mock assignments for now or empty until backend supports listing
    const filteredAssignments = assignments.filter(a =>
        (a.subject || '').toLowerCase().includes(searchTerm.toLowerCase())
    );

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando datos...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Materias y Asignaciones</h1>
                <button onClick={() => {
                    setSelectedItem(null);
                    setFormData({
                        code: '', name: '', hoursWeek: '', active: true,
                        subjectId: '', teacherId: '', gradeId: '', sectionId: ''
                    });
                    setShowModal(true);
                }} className="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
                    <Plus size={18} /> {activeTab === 'materias' ? 'Nueva Materia' : 'Nueva Asignación'}
                </button>
            </div>

            {/* Tabs */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-1 inline-flex shadow-sm`}>
                <button
                    onClick={() => setActiveTab('materias')}
                    className={`px-4 py-2 rounded-lg font-medium ${activeTab === 'materias'
                        ? 'bg-teal-600 text-white'
                        : darkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'
                        }`}
                >
                    <Book size={16} className="inline mr-2" />Catálogo de Materias
                </button>
                <button
                    onClick={() => setActiveTab('asignaciones')}
                    className={`px-4 py-2 rounded-lg font-medium ${activeTab === 'asignaciones'
                        ? 'bg-teal-600 text-white'
                        : darkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'
                        }`}
                >
                    <Users size={16} className="inline mr-2" />Asignaciones por Docente
                </button>
            </div>

            {/* Search */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm`}>
                <div className="relative max-w-md">
                    <Search className={`absolute left-3 top-2.5 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`} size={18} />
                    <input
                        type="text"
                        placeholder="Buscar..."
                        value={searchTerm}
                        onChange={e => setSearchTerm(e.target.value)}
                        className={`${inputClass} w-full pl-10`}
                    />
                </div>
            </div>

            {/* Content */}
            {activeTab === 'materias' ? (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                    <table className="w-full">
                        <thead className={darkMode ? 'bg-gray-700' : 'bg-gray-50'}>
                            <tr>
                                <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Código</th>
                                <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Nombre</th>
                                <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Horas/Semana</th>
                                <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Estado</th>
                                <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Acciones</th>
                            </tr>
                        </thead>
                        <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                            {filteredSubjects.map(subject => (
                                <tr key={subject.id} className={darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}>
                                    <td className={`px-4 py-3 font-mono ${darkMode ? 'text-teal-400' : 'text-teal-600'}`}>{subject.code}</td>
                                    <td className={`px-4 py-3 font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{subject.name}</td>
                                    <td className={`px-4 py-3 text-center ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{subject.hoursWeek}</td>
                                    <td className="px-4 py-3 text-center">
                                        <span className={`px-2 py-1 rounded-full text-xs ${subject.active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'
                                            }`}>{subject.active ? 'Activo' : 'Inactivo'}</span>
                                    </td>
                                    <td className="px-4 py-3 text-center">
                                        <button onClick={() => {
                                            setSelectedItem(subject);
                                            setFormData({
                                                code: subject.code,
                                                name: subject.name,
                                                hoursWeek: subject.hoursWeek,
                                                active: subject.active,
                                                subjectId: '', teacherId: '', gradeId: '', sectionId: ''
                                            });
                                            setShowModal(true);
                                        }} className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`}>
                                            <Edit size={16} className="text-blue-500" />
                                        </button>
                                    </td>
                                </tr>
                            ))}
                            {filteredSubjects.length === 0 && (
                                <tr>
                                    <td colSpan="5" className={`px-4 py-8 text-center ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                                        No hay materias registradas
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            ) : (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                    {filteredAssignments.length > 0 ? (
                        <table className="w-full">
                            <thead className={darkMode ? 'bg-gray-700' : 'bg-gray-50'}>
                                <tr>
                                    <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Materia</th>
                                    <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Docente</th>
                                    <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Grado</th>
                                    <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Sección</th>
                                    <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Horas/Semana</th>
                                    <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Acciones</th>
                                </tr>
                            </thead>
                            <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                                {filteredAssignments.map(assignment => (
                                    <tr key={assignment.id} className={darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}>
                                        <td className={`px-4 py-3 font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{assignment.subject}</td>
                                        <td className={`px-4 py-3 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{assignment.teacher}</td>
                                        <td className={`px-4 py-3 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{assignment.grade}</td>
                                        <td className="px-4 py-3 text-center">
                                            <span className="px-2 py-1 bg-teal-100 text-teal-700 rounded font-medium">{assignment.section}</span>
                                        </td>
                                        <td className={`px-4 py-3 text-center ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{assignment.hoursWeek}</td>
                                        <td className="px-4 py-3 text-center">
                                            <button onClick={() => { setSelectedItem(assignment); setShowModal(true); }} className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`}>
                                                <Edit size={16} className="text-blue-500" />
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    ) : (
                        <div className={`p-12 text-center ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                            <p>No hay asignaciones registradas o no se pudieron cargar.</p>
                            <p className="text-sm mt-2">Utiliza "Nueva Asignación" para crear una.</p>
                        </div>
                    )}
                </div>
            )}

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-md p-6`}>
                        <div className="flex items-center justify-between mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {selectedItem ? 'Editar' : 'Nuevo'} {activeTab === 'materias' ? 'Materia' : 'Asignación'}
                            </h2>
                            <button onClick={() => setShowModal(false)}><X size={20} /></button>
                        </div>
                        <div className="space-y-4">
                            {activeTab === 'materias' ? (
                                <>
                                    <div>
                                        <label className={labelClass}>Código</label>
                                        <input
                                            type="text"
                                            className={`${inputClass} w-full`}
                                            value={formData.code}
                                            onChange={e => setFormData({ ...formData, code: e.target.value })}
                                            placeholder="Ej: MAT01"
                                        />
                                    </div>
                                    <div>
                                        <label className={labelClass}>Nombre</label>
                                        <input
                                            type="text"
                                            className={`${inputClass} w-full`}
                                            value={formData.name}
                                            onChange={e => setFormData({ ...formData, name: e.target.value })}
                                            placeholder="Nombre de la materia"
                                        />
                                    </div>
                                    <div>
                                        <label className={labelClass}>Horas por Semana</label>
                                        <input
                                            type="number"
                                            className={`${inputClass} w-full`}
                                            value={formData.hoursWeek}
                                            onChange={e => setFormData({ ...formData, hoursWeek: parseInt(e.target.value) })}
                                        />
                                    </div>
                                </>
                            ) : (
                                <>
                                    <div>
                                        <label className={labelClass}>Materia</label>
                                        <select
                                            className={`${inputClass} w-full`}
                                            value={formData.subjectId}
                                            onChange={e => setFormData({ ...formData, subjectId: e.target.value })}
                                        >
                                            <option value="">Seleccione Materia</option>
                                            {subjects.map(s => <option key={s.id} value={s.id}>{s.name}</option>)}
                                        </select>
                                    </div>
                                    <div>
                                        <label className={labelClass}>Docente</label>
                                        <select
                                            className={`${inputClass} w-full`}
                                            value={formData.teacherId}
                                            onChange={e => setFormData({ ...formData, teacherId: e.target.value })}
                                        >
                                            <option value="">Seleccione Docente</option>
                                            {teachers.map(t => (
                                                <option key={t.id} value={t.id}>{t.firstName} {t.lastName} ({t.email})</option>
                                            ))}
                                        </select>
                                    </div>
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <label className={labelClass}>Grado</label>
                                            <select
                                                className={`${inputClass} w-full`}
                                                value={formData.gradeId}
                                                onChange={e => setFormData({ ...formData, gradeId: e.target.value })}
                                            >
                                                <option value="">Seleccione Grado</option>
                                                {grades.map(g => (
                                                    <option key={g.id} value={g.id}>{g.name}</option>
                                                ))}
                                            </select>
                                        </div>
                                        <div>
                                            <label className={labelClass}>Sección</label>
                                            <select
                                                className={`${inputClass} w-full`}
                                                value={formData.sectionId}
                                                onChange={e => setFormData({ ...formData, sectionId: e.target.value })}
                                                disabled={!formData.gradeId}
                                            >
                                                <option value="">Seleccione</option>
                                                {sections.map(s => (
                                                    <option key={s.id} value={s.id}>{s.name}</option>
                                                ))}
                                                {sections.length === 0 && <option value="" disabled>Sin secciones</option>}
                                            </select>
                                        </div>
                                    </div>
                                </>
                            )}
                        </div>
                        <div className="flex justify-end gap-3 mt-6">
                            <button onClick={() => setShowModal(false)} className={`px-4 py-2 rounded-lg ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}>Cancelar</button>
                            <button onClick={handleSave} className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">Guardar</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default MateriasPage;
