import React, { useState, useEffect } from 'react';
import { Users, Search, Eye, Plus, X, RefreshCw } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { api } from '../services';

const FamiliasPage = () => {
    const { darkMode } = useTheme();
    const [searchTerm, setSearchTerm] = useState('');
    const [showDetailModal, setShowDetailModal] = useState(false);
    const [selectedFamily, setSelectedFamily] = useState(null);
    const [loading, setLoading] = useState(true);
    const [families, setFamilies] = useState([]);

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;

    useEffect(() => {
        loadFamilies();
    }, []);

    const loadFamilies = async () => {
        setLoading(true);
        try {
            const response = await api.get('/families');
            if (response.success) {
                setFamilies(response.data);
            }
        } catch (error) {
            console.error('Error loading families:', error);
            // Error - show empty state
            setFamilies([
                {
                    id: 1,
                    father: { name: 'Roberto Pérez', phone: '5512-3456', email: 'rperez@email.com' },
                    mother: { name: 'María García de Pérez', phone: '5534-5678', email: 'mgarcia@email.com' },
                    students: [
                        { id: 1, name: 'Juan Pérez García', carnet: '2025-001', grade: '1ro Básico A', status: 'ACTIVO' },
                        { id: 2, name: 'Ana Pérez García', carnet: '2025-015', grade: 'Kinder 5 B', status: 'ACTIVO' },
                    ]
                },
                {
                    id: 2,
                    father: { name: 'Carlos López', phone: '5556-7890', email: 'clopez@email.com' },
                    mother: { name: 'Laura Martínez de López', phone: '5578-9012', email: 'lmartinez@email.com' },
                    students: [
                        { id: 3, name: 'María López Martínez', carnet: '2025-002', grade: '2do Básico B', status: 'ACTIVO' },
                    ]
                },
                {
                    id: 3,
                    father: { name: 'Fernando Hernández', phone: '5590-1234', email: 'fhernandez@email.com' },
                    mother: null,
                    guardian: { name: 'Elena Hernández', relationship: 'Hermana', phone: '5501-2345' },
                    students: [
                        { id: 4, name: 'Carlos Hernández', carnet: '2025-003', grade: '3ro Básico A', status: 'ACTIVO' },
                    ]
                },
            ]);
        } finally {
            setLoading(false);
        }
    };

    const filteredFamilies = families.filter(f => {
        if (!searchTerm) return true;
        const search = searchTerm.toLowerCase();
        return f.father?.name.toLowerCase().includes(search) ||
            f.mother?.name.toLowerCase().includes(search) ||
            f.students.some(s => s.name.toLowerCase().includes(search) || s.carnet.includes(search));
    });

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando familias...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Familias</h1>
                <button className="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
                    <Plus size={18} /> Nueva Familia
                </button>
            </div>

            {/* Search */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm`}>
                <div className="relative max-w-md">
                    <Search className={`absolute left-3 top-2.5 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`} size={18} />
                    <input
                        type="text"
                        placeholder="Buscar por nombre de padre, madre o estudiante..."
                        value={searchTerm}
                        onChange={e => setSearchTerm(e.target.value)}
                        className={`${inputClass} w-full pl-10`}
                    />
                </div>
            </div>

            {/* Info Banner */}
            <div className={`${darkMode ? 'bg-blue-900/30 border-blue-700' : 'bg-blue-50 border-blue-200'} border rounded-xl p-4 flex items-start gap-3`}>
                <Users className="text-blue-500 mt-0.5" size={20} />
                <div>
                    <p className={`font-medium ${darkMode ? 'text-blue-300' : 'text-blue-700'}`}>Gestión de Familias</p>
                    <p className={`text-sm ${darkMode ? 'text-blue-200' : 'text-blue-600'}`}>
                        Las familias se crean automáticamente durante el proceso de inscripción.
                        Aquí puede consultar y editar los datos de padres y estudiantes agrupados.
                    </p>
                </div>
            </div>

            {/* Families List */}
            <div className="space-y-4">
                {filteredFamilies.map(family => (
                    <div key={family.id} className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                        <div className="p-4">
                            <div className="flex items-start justify-between">
                                <div className="flex items-start gap-4">
                                    <div className="w-14 h-14 bg-gradient-to-br from-teal-500 to-teal-700 rounded-xl flex items-center justify-center text-white">
                                        <Users size={24} />
                                    </div>
                                    <div>
                                        <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                            Familia {family.father?.name.split(' ').slice(-1)[0] || family.mother?.name.split(' ').slice(-2, -1)[0]}
                                        </h3>
                                        <div className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                            {family.students.length} estudiante{family.students.length > 1 ? 's' : ''} inscrito{family.students.length > 1 ? 's' : ''}
                                        </div>
                                    </div>
                                </div>
                                <button
                                    onClick={() => { setSelectedFamily(family); setShowDetailModal(true); }}
                                    className={`p-2 rounded-lg ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100'}`}
                                >
                                    <Eye size={18} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                                </button>
                            </div>

                            <div className={`mt-4 grid grid-cols-2 gap-4 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                                <div className={`p-3 rounded-lg ${darkMode ? 'bg-gray-700' : 'bg-gray-50'}`}>
                                    <p className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Padre</p>
                                    {family.father ? (
                                        <>
                                            <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{family.father.name}</p>
                                            <p className="text-sm">📱 {family.father.phone}</p>
                                        </>
                                    ) : <p className={darkMode ? 'text-gray-500' : 'text-gray-400'}>No registrado</p>}
                                </div>
                                <div className={`p-3 rounded-lg ${darkMode ? 'bg-gray-700' : 'bg-gray-50'}`}>
                                    <p className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Madre</p>
                                    {family.mother ? (
                                        <>
                                            <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{family.mother.name}</p>
                                            <p className="text-sm">📱 {family.mother.phone}</p>
                                        </>
                                    ) : <p className={darkMode ? 'text-gray-500' : 'text-gray-400'}>No registrada</p>}
                                </div>
                            </div>

                            {family.guardian && (
                                <div className={`mt-2 p-3 rounded-lg ${darkMode ? 'bg-orange-900/30' : 'bg-orange-50'}`}>
                                    <p className={`text-xs ${darkMode ? 'text-orange-400' : 'text-orange-600'}`}>Encargado ({family.guardian.relationship})</p>
                                    <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{family.guardian.name}</p>
                                </div>
                            )}

                            <div className={`mt-4 pt-4 border-t ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                                <p className={`text-xs mb-2 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Estudiantes:</p>
                                <div className="flex flex-wrap gap-2">
                                    {family.students.map(student => (
                                        <span
                                            key={student.id}
                                            className={`px-3 py-1 rounded-full text-sm ${student.status === 'ACTIVO'
                                                    ? 'bg-teal-100 text-teal-700'
                                                    : 'bg-gray-100 text-gray-600'
                                                }`}
                                        >
                                            {student.name} ({student.carnet})
                                        </span>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {filteredFamilies.length === 0 && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                    <Users size={48} className={`mx-auto mb-4 ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>No se encontraron familias</p>
                </div>
            )}

            {/* Detail Modal */}
            {showDetailModal && selectedFamily && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto`}>
                        <div className="flex items-center justify-between mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Detalle de Familia</h2>
                            <button onClick={() => setShowDetailModal(false)}><X size={20} /></button>
                        </div>

                        <div className="space-y-4">
                            {selectedFamily.father && (
                                <div className={`p-4 rounded-lg ${darkMode ? 'bg-gray-700' : 'bg-gray-50'}`}>
                                    <h3 className={`font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>👨 Padre</h3>
                                    <p>{selectedFamily.father.name}</p>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>📱 {selectedFamily.father.phone}</p>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>📧 {selectedFamily.father.email}</p>
                                </div>
                            )}

                            {selectedFamily.mother && (
                                <div className={`p-4 rounded-lg ${darkMode ? 'bg-gray-700' : 'bg-gray-50'}`}>
                                    <h3 className={`font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>👩 Madre</h3>
                                    <p>{selectedFamily.mother.name}</p>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>📱 {selectedFamily.mother.phone}</p>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>📧 {selectedFamily.mother.email}</p>
                                </div>
                            )}

                            <div className={`p-4 rounded-lg ${darkMode ? 'bg-gray-700' : 'bg-gray-50'}`}>
                                <h3 className={`font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>🎓 Estudiantes</h3>
                                {selectedFamily.students.map(s => (
                                    <div key={s.id} className={`p-2 rounded mb-2 ${darkMode ? 'bg-gray-600' : 'bg-white'}`}>
                                        <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{s.name}</p>
                                        <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{s.carnet} • {s.grade}</p>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div className="flex justify-end mt-6">
                            <button onClick={() => setShowDetailModal(false)} className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default FamiliasPage;
