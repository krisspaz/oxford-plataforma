import React, { useState, useEffect } from 'react';
import { Book, Plus, Search, Edit, X, Users, RefreshCw } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { catalogService } from '../services';

const MateriasPage = () => {
    const { darkMode } = useTheme();
    const [activeTab, setActiveTab] = useState('materias');
    const [searchTerm, setSearchTerm] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [selectedItem, setSelectedItem] = useState(null);
    const [loading, setLoading] = useState(true);
    const [subjects, setSubjects] = useState([]);
    const [assignments, setAssignments] = useState([]);

    const [formData, setFormData] = useState({
        code: '',
        name: '',
        hoursWeek: '',
        active: true
    });

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    useEffect(() => {
        loadData();
    }, []);

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
                loadData();
                setShowModal(false);
            }
        } catch (error) {
            console.error('Error saving:', error);
            alert('Error al guardar: ' + (error.message || 'Error desconocido'));
        }
    };

    const loadData = async () => {
        setLoading(true);
        try {
            const response = await catalogService.getSubjects();
            if (response && response.member) {
                setSubjects(response.member);
            } else if (response && response['hydra:member']) {
                setSubjects(response['hydra:member']);
            } else if (Array.isArray(response)) {
                setSubjects(response);
            } else if (response.success && response.data) {
                // Fallback for legacy controller if still active
                setSubjects(response.data);
            }
        } catch (error) {
            console.error('Error loading subjects:', error);
        } finally {
            setLoading(false);
        }
    };

    const filteredSubjects = subjects.filter(s =>
        s.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        s.code.toLowerCase().includes(searchTerm.toLowerCase())
    );

    const filteredAssignments = assignments.filter(a =>
        a.subject.toLowerCase().includes(searchTerm.toLowerCase()) ||
        a.teacher.toLowerCase().includes(searchTerm.toLowerCase()) ||
        a.grade.toLowerCase().includes(searchTerm.toLowerCase())
    );

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando materias...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Materias y Asignaciones</h1>
                <button onClick={() => {
                    setSelectedItem(null);
                    setFormData({ code: '', name: '', hoursWeek: '', active: true });
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
                                                active: subject.active
                                            });
                                            setShowModal(true);
                                        }} className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`}>
                                            <Edit size={16} className="text-blue-500" />
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            ) : (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
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
                                        <select className={`${inputClass} w-full`}>
                                            {subjects.map(s => <option key={s.id} value={s.id}>{s.name}</option>)}
                                        </select>
                                    </div>
                                    <div>
                                        <label className={labelClass}>Docente</label>
                                        <select className={`${inputClass} w-full`}>
                                            <option>Prof. Roberto García</option>
                                            <option>Profa. María López</option>
                                            <option>Prof. Carlos Hernández</option>
                                        </select>
                                    </div>
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <label className={labelClass}>Grado</label>
                                            <select className={`${inputClass} w-full`}>
                                                <option>1ro Básico</option>
                                                <option>2do Básico</option>
                                                <option>3ro Básico</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label className={labelClass}>Sección</label>
                                            <select className={`${inputClass} w-full`}>
                                                <option>A</option>
                                                <option>B</option>
                                                <option>C</option>
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
