import React, { useState, useEffect } from 'react';
import { Users, Plus, Search, Edit, Eye, Book, RefreshCw, X } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { catalogService } from '../services';

const DocentesPage = () => {
    const { darkMode } = useTheme();
    const [searchTerm, setSearchTerm] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [selectedTeacher, setSelectedTeacher] = useState(null);
    const [loading, setLoading] = useState(true);
    const [teachers, setTeachers] = useState([]);

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    useEffect(() => {
        loadTeachers();
    }, []);

    const loadTeachers = async () => {
        setLoading(true);
        try {
            const response = await catalogService.getTeachers();
            if (response.success) {
                setTeachers(response.data);
            }
        } catch (error) {
            console.error('Error loading teachers:', error);
            // Demo data
            setTeachers([
                {
                    id: 1,
                    code: 'DOC-001',
                    name: 'Prof. Roberto García',
                    email: 'rgarcia@oxford.edu',
                    phone: '5512-3456',
                    specialization: 'Matemáticas',
                    hireDate: '2020-01-15',
                    status: 'ACTIVO',
                    subjectsCount: 4,
                    subjects: ['Matemáticas 1ro A', 'Matemáticas 1ro B', 'Matemáticas 2do A', 'Matemáticas 2do B']
                },
                {
                    id: 2,
                    code: 'DOC-002',
                    name: 'Profa. María López',
                    email: 'mlopez@oxford.edu',
                    phone: '5534-5678',
                    specialization: 'Comunicación y Lenguaje',
                    hireDate: '2019-03-10',
                    status: 'ACTIVO',
                    subjectsCount: 3,
                    subjects: ['Comunicación 1ro A', 'Comunicación 1ro B', 'Comunicación 2do A']
                },
                {
                    id: 3,
                    code: 'DOC-003',
                    name: 'Prof. Carlos Hernández',
                    email: 'chernandez@oxford.edu',
                    phone: '5556-7890',
                    specialization: 'Ciencias Naturales',
                    hireDate: '2021-06-01',
                    status: 'ACTIVO',
                    subjectsCount: 2,
                    subjects: ['Ciencias 2do A', 'Ciencias 2do B']
                },
                {
                    id: 4,
                    code: 'DOC-004',
                    name: 'Profa. Ana Martínez',
                    email: 'amartinez@oxford.edu',
                    phone: '5578-9012',
                    specialization: 'Inglés',
                    hireDate: '2022-01-10',
                    status: 'ACTIVO',
                    subjectsCount: 5,
                    subjects: ['Inglés 1ro A', 'Inglés 1ro B', 'Inglés 2do A', 'Inglés 2do B', 'Inglés 3ro A']
                },
            ]);
        } finally {
            setLoading(false);
        }
    };

    const filteredTeachers = teachers.filter(t =>
        t.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        t.code.toLowerCase().includes(searchTerm.toLowerCase()) ||
        t.specialization.toLowerCase().includes(searchTerm.toLowerCase())
    );

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
                <button onClick={() => { setSelectedTeacher(null); setShowModal(true); }} className="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
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
            <div className="grid grid-cols-2 gap-4">
                {filteredTeachers.map(teacher => (
                    <div key={teacher.id} className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                        <div className="p-4">
                            <div className="flex items-start gap-4">
                                <div className="w-14 h-14 bg-gradient-to-br from-teal-500 to-teal-700 rounded-xl flex items-center justify-center text-white font-bold text-xl">
                                    {teacher.name.split(' ').slice(-1)[0].charAt(0)}
                                </div>
                                <div className="flex-1">
                                    <div className="flex items-center justify-between">
                                        <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{teacher.name}</h3>
                                        <span className={`px-2 py-0.5 rounded text-xs ${teacher.status === 'ACTIVO' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'
                                            }`}>{teacher.status}</span>
                                    </div>
                                    <p className={`text-sm ${darkMode ? 'text-teal-400' : 'text-teal-600'}`}>{teacher.code}</p>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{teacher.specialization}</p>
                                </div>
                            </div>

                            <div className={`mt-4 grid grid-cols-2 gap-2 text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                <div>📧 {teacher.email}</div>
                                <div>📱 {teacher.phone}</div>
                            </div>

                            <div className={`mt-4 p-3 rounded-lg ${darkMode ? 'bg-gray-700' : 'bg-gray-50'}`}>
                                <div className="flex items-center justify-between mb-2">
                                    <span className={`text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                        <Book size={14} className="inline mr-1" />Carga Académica
                                    </span>
                                    <span className="px-2 py-0.5 bg-teal-100 text-teal-700 rounded-full text-xs font-bold">
                                        {teacher.subjectsCount} materias
                                    </span>
                                </div>
                                <div className="flex flex-wrap gap-1">
                                    {teacher.subjects?.slice(0, 3).map((subj, i) => (
                                        <span key={i} className={`text-xs px-2 py-0.5 rounded ${darkMode ? 'bg-gray-600 text-gray-300' : 'bg-gray-200 text-gray-600'}`}>
                                            {subj}
                                        </span>
                                    ))}
                                    {teacher.subjects?.length > 3 && (
                                        <span className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>+{teacher.subjects.length - 3} más</span>
                                    )}
                                </div>
                            </div>

                            <div className="flex gap-2 mt-4">
                                <button
                                    onClick={() => { setSelectedTeacher(teacher); setShowModal(true); }}
                                    className={`flex-1 flex items-center justify-center gap-1 px-3 py-2 rounded-lg text-sm ${darkMode ? 'bg-gray-700 hover:bg-gray-600 text-gray-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'
                                        }`}
                                >
                                    <Edit size={14} /> Editar
                                </button>
                                <button className={`flex-1 flex items-center justify-center gap-1 px-3 py-2 rounded-lg text-sm ${darkMode ? 'bg-teal-900/30 hover:bg-teal-900/50 text-teal-400' : 'bg-teal-50 hover:bg-teal-100 text-teal-700'
                                    }`}>
                                    <Eye size={14} /> Ver Carga
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
                                    <input type="text" className={`${inputClass} w-full`} defaultValue={selectedTeacher?.code} placeholder="DOC-XXX" />
                                </div>
                                <div>
                                    <label className={labelClass}>Especialización</label>
                                    <select className={`${inputClass} w-full`} defaultValue={selectedTeacher?.specialization}>
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
                                <input type="text" className={`${inputClass} w-full`} defaultValue={selectedTeacher?.name} />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className={labelClass}>Email</label>
                                    <input type="email" className={`${inputClass} w-full`} defaultValue={selectedTeacher?.email} />
                                </div>
                                <div>
                                    <label className={labelClass}>Teléfono</label>
                                    <input type="text" className={`${inputClass} w-full`} defaultValue={selectedTeacher?.phone} />
                                </div>
                            </div>
                            <div>
                                <label className={labelClass}>Fecha de Contratación</label>
                                <input type="date" className={`${inputClass} w-full`} defaultValue={selectedTeacher?.hireDate} />
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

export default DocentesPage;
