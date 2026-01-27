import { toast } from '../utils/toast';
import React, { useState, useEffect } from 'react';
import { Users, Plus, Search, Edit, Eye, Book, RefreshCw, X } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { catalogService, teacherService } from '../services';

const DocentesPage = () => {
    const { darkMode } = useTheme();
    const [searchTerm, setSearchTerm] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [selectedTeacher, setSelectedTeacher] = useState(null);
    const [loading, setLoading] = useState(true);
    const [teachers, setTeachers] = useState([]);
    const [formData, setFormData] = useState({
        code: '', name: '', email: '', phone: '', hireDate: '', specialization: 'Matemáticas'
    });
    const [saving, setSaving] = useState(false);

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    useEffect(() => {
        loadTeachers();
    }, []);

    const loadTeachers = async () => {
        setLoading(true);
        try {
            const response = await teacherService.getAll();
            if (response.data) {
                // Ensure array
                const list = Array.isArray(response.data) ? response.data : (response.data['hydra:member'] || []);
                setTeachers(list);
            } else {
                setTeachers([]);
            }
        } catch (error) {
            console.error('Error loading teachers:', error);
            // Fallback to catalog service if teacherService implementation differs
            try {
                const res = await catalogService.getTeachers();
                setTeachers(res && res.data ? res.data : []);
            } catch (e) {
                setTeachers([]);
            }
        } finally {
            setLoading(false);
        }
    };

    const handleOpenModal = (teacher = null) => {
        if (teacher) {
            setSelectedTeacher(teacher);
            setFormData({
                code: teacher.code || '',
                name: teacher.name || '',
                email: teacher.email || '',
                phone: teacher.phone || '',
                hireDate: teacher.hireDate ? teacher.hireDate.split('T')[0] : '',
                specialization: teacher.specialization || 'Matemáticas'
            });
        } else {
            setSelectedTeacher(null);
            setFormData({
                code: '', name: '', email: '', phone: '', hireDate: '', specialization: 'Matemáticas'
            });
        }
        setShowModal(true);
    };

    const handleSave = async () => {
        if (!formData.name || !formData.email) {
            toast.error('Nombre y Email son requeridos');
            return;
        }

        setSaving(true);
        try {
            if (selectedTeacher) {
                await teacherService.update(selectedTeacher.id, formData);
                toast.success('Docente actualizado');
            } else {
                await teacherService.create(formData);
                toast.success('Docente creado');
            }
            setShowModal(false);
            loadTeachers();
        } catch (error) {
            console.error('Error saving teacher:', error);
            toast.error('Error al guardar: ' + error.message);
        } finally {
            setSaving(false);
        }
    };

    const filteredTeachers = Array.isArray(teachers) ? teachers.filter(t =>
        (t.name || '').toLowerCase().includes(searchTerm.toLowerCase()) ||
        (t.code || '').toLowerCase().includes(searchTerm.toLowerCase()) ||
        (t.specialization || '').toLowerCase().includes(searchTerm.toLowerCase())
    ) : [];

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando docentes...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Docentes</h1>
                <button onClick={() => handleOpenModal(null)} className="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
                    <Plus size={18} /> Nuevo Docente
                </button>
            </div>

            {/* Search */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm`}>
                <div className="relative max-w-md">
                    <Search className={`absolute left-3 top-2.5 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`} size={18} />
                    <input
                        type="text"
                        placeholder="Buscar por nombre, código o especialidad..."
                        value={searchTerm}
                        onChange={e => setSearchTerm(e.target.value)}
                        className={`${inputClass} w-full pl-10`}
                    />
                </div>
            </div>

            {/* Teachers Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {filteredTeachers.map(teacher => (
                    <div key={teacher.id} className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                        <div className="p-4">
                            <div className="flex items-start gap-4">
                                <div className="w-14 h-14 bg-gradient-to-br from-teal-500 to-teal-700 rounded-xl flex items-center justify-center text-white font-bold text-xl">
                                    {(teacher.name || '?').charAt(0)}
                                </div>
                                <div className="flex-1">
                                    <div className="flex items-center justify-between">
                                        <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{teacher.name}</h3>
                                        <span className={`px-2 py-0.5 rounded text-xs ${teacher.isActive ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'
                                            }`}>{teacher.isActive ? 'ACTIVO' : 'INACTIVO'}</span>
                                    </div>
                                    <p className={`text-sm ${darkMode ? 'text-teal-400' : 'text-teal-600'}`}>{teacher.code}</p>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{teacher.specialization}</p>
                                </div>
                            </div>

                            <div className={`mt-4 grid grid-cols-2 gap-2 text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                <div>📧 {teacher.email}</div>
                                <div>📱 {teacher.phone}</div>
                            </div>

                            <div className="flex gap-2 mt-4">
                                <button
                                    onClick={() => handleOpenModal(teacher)}
                                    className={`flex-1 flex items-center justify-center gap-1 px-3 py-2 rounded-lg text-sm ${darkMode ? 'bg-gray-700 hover:bg-gray-600 text-gray-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'
                                        }`}
                                >
                                    <Edit size={14} /> Editar
                                </button>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {filteredTeachers.length === 0 && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                    <Users size={48} className={`mx-auto mb-4 ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>No se encontraron docentes</p>
                </div>
            )}

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-lg p-6`}>
                        <div className="flex items-center justify-between mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {selectedTeacher ? 'Editar Docente' : 'Nuevo Docente'}
                            </h2>
                            <button onClick={() => setShowModal(false)}><X size={20} /></button>
                        </div>
                        <div className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className={labelClass}>Código</label>
                                    <input
                                        type="text"
                                        className={`${inputClass} w-full`}
                                        value={formData.code}
                                        onChange={e => setFormData({ ...formData, code: e.target.value })}
                                        placeholder="DOC-XXX"
                                    />
                                </div>
                                <div>
                                    <label className={labelClass}>Especialización</label>
                                    <select
                                        className={`${inputClass} w-full`}
                                        value={formData.specialization}
                                        onChange={e => setFormData({ ...formData, specialization: e.target.value })}
                                    >
                                        <option>Matemáticas</option>
                                        <option>Comunicación y Lenguaje</option>
                                        <option>Ciencias Naturales</option>
                                        <option>Ciencias Sociales</option>
                                        <option>Inglés</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label className={labelClass}>Nombre Completo</label>
                                <input
                                    type="text"
                                    className={`${inputClass} w-full`}
                                    value={formData.name}
                                    onChange={e => setFormData({ ...formData, name: e.target.value })}
                                />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className={labelClass}>Email</label>
                                    <input
                                        type="email"
                                        className={`${inputClass} w-full`}
                                        value={formData.email}
                                        onChange={e => setFormData({ ...formData, email: e.target.value })}
                                    />
                                </div>
                                <div>
                                    <label className={labelClass}>Teléfono</label>
                                    <input
                                        type="text"
                                        className={`${inputClass} w-full`}
                                        value={formData.phone}
                                        onChange={e => setFormData({ ...formData, phone: e.target.value })}
                                    />
                                </div>
                            </div>
                            <div>
                                <label className={labelClass}>Fecha de Contratación</label>
                                <input
                                    type="date"
                                    className={`${inputClass} w-full`}
                                    value={formData.hireDate}
                                    onChange={e => setFormData({ ...formData, hireDate: e.target.value })}
                                />
                            </div>
                        </div>
                        <div className="flex justify-end gap-3 mt-6">
                            <button onClick={() => setShowModal(false)} className={`px-4 py-2 rounded-lg ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}>Cancelar</button>
                            <button
                                onClick={handleSave}
                                disabled={saving}
                                className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg flex items-center gap-2"
                            >
                                {saving && <RefreshCw size={14} className="animate-spin" />}
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default DocentesPage;
