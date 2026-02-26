import { toast } from 'sonner';
import { useState, useMemo } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { api } from '../services/api';
import { useTheme } from '../contexts/ThemeContext';

const Students = () => {
    const { darkMode } = useTheme();
    const queryClient = useQueryClient();
    const [searchTerm, setSearchTerm] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [newStudent, setNewStudent] = useState({
        firstName: '',
        lastName: '',
        carnet: '',
        email: '',
        birthDate: '',
        grade: ''
    });

    // === QUERY ===
    const { data: students = [], isLoading: loading } = useQuery({
        queryKey: ['students'],
        queryFn: async () => {
            const res = await api.get('/students');
            return res['hydra:member'] || res.member || res || [];
        },
    });

    // === MUTATION ===
    const createMutation = useMutation({
        mutationFn: async (data) => api.post('/students', { ...data, isActive: true }, {
            headers: { 'Content-Type': 'application/ld+json' }
        }),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['students'] });
            toast.success('Estudiante creado exitosamente');
            setShowModal(false);
            setNewStudent({ firstName: '', lastName: '', carnet: '', email: '', birthDate: '', grade: '' });
        },
        onError: () => toast.error('Error al crear estudiante'),
    });

    const handleCreateStudent = (e) => {
        e.preventDefault();
        createMutation.mutate(newStudent);
    };

    const filtered = useMemo(() => students.filter((s) =>
        [s.firstName, s.lastName, s.carnet]
            .join(' ')
            .toLowerCase()
            .includes(searchTerm.toLowerCase())
    ), [students, searchTerm]);

    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    return (
        <div className="space-y-6">
            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-lg p-6`}>
                        <div className="flex items-center justify-between mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Nuevo Estudiante</h2>
                            <button onClick={() => setShowModal(false)} className={`p-1 rounded ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100'}`}>
                                <X size={20} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                            </button>
                        </div>
                        <form onSubmit={handleCreateStudent} className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className={labelClass}>Nombre</label>
                                    <input
                                        type="text"
                                        value={newStudent.firstName}
                                        onChange={e => setNewStudent({ ...newStudent, firstName: e.target.value })}
                                        className={inputClass}
                                        placeholder="Juan"
                                        required
                                    />
                                </div>
                                <div>
                                    <label className={labelClass}>Apellido</label>
                                    <input
                                        type="text"
                                        value={newStudent.lastName}
                                        onChange={e => setNewStudent({ ...newStudent, lastName: e.target.value })}
                                        className={inputClass}
                                        placeholder="Pérez"
                                        required
                                    />
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className={labelClass}>Carnet</label>
                                    <input
                                        type="text"
                                        value={newStudent.carnet}
                                        onChange={e => setNewStudent({ ...newStudent, carnet: e.target.value })}
                                        className={inputClass}
                                        placeholder="2026-001"
                                        required
                                    />
                                </div>
                                <div>
                                    <label className={labelClass}>Grado</label>
                                    <select
                                        value={newStudent.grade}
                                        onChange={e => setNewStudent({ ...newStudent, grade: e.target.value })}
                                        className={inputClass}
                                        required
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option value="Pre-Kinder">Pre-Kinder</option>
                                        <option value="Kinder">Kinder</option>
                                        <option value="Preparatoria">Preparatoria</option>
                                        <option value="1ro Primaria">1ro Primaria</option>
                                        <option value="2do Primaria">2do Primaria</option>
                                        <option value="3ro Primaria">3ro Primaria</option>
                                        <option value="4to Primaria">4to Primaria</option>
                                        <option value="5to Primaria">5to Primaria</option>
                                        <option value="6to Primaria">6to Primaria</option>
                                        <option value="1ro Básico">1ro Básico</option>
                                        <option value="2do Básico">2do Básico</option>
                                        <option value="3ro Básico">3ro Básico</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label className={labelClass}>Correo Electrónico</label>
                                <input
                                    type="email"
                                    value={newStudent.email}
                                    onChange={e => setNewStudent({ ...newStudent, email: e.target.value })}
                                    className={inputClass}
                                    placeholder="juan@ejemplo.com"
                                />
                            </div>
                            <div>
                                <label className={labelClass}>Fecha de Nacimiento</label>
                                <input
                                    type="date"
                                    value={newStudent.birthDate}
                                    onChange={e => setNewStudent({ ...newStudent, birthDate: e.target.value })}
                                    className={inputClass}
                                />
                            </div>
                            <div className="flex justify-end gap-3 pt-4">
                                <button
                                    type="button"
                                    onClick={() => setShowModal(false)}
                                    className={`px-4 py-2 rounded-lg ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    disabled={createMutation.isPending}
                                    className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg disabled:opacity-50 flex items-center gap-2"
                                >
                                    {createMutation.isPending && <Loader2 size={16} className="animate-spin" />}
                                    {createMutation.isPending ? 'Guardando...' : 'Guardar'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Header */}
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Gestión de Estudiantes</h1>
                <button
                    onClick={() => setShowModal(true)}
                    className="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-all"
                >
                    <Plus size={20} />
                    Nuevo Estudiante
                </button>
            </div>

            {/* Search */}
            <div className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white'} p-4 rounded-xl shadow-sm border`}>
                <div className="relative">
                    <Search className={`absolute left-3 top-3 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`} size={20} />
                    <input
                        type="text"
                        placeholder="Buscar por nombre, apellido o carnet..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        className={`w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'}`}
                    />
                </div>
            </div>

            {/* Table */}
            <div className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white'} rounded-xl shadow-sm overflow-hidden border`}>
                <div className="overflow-x-auto">
                    <table className="w-full text-left">
                        <thead className={`${darkMode ? 'bg-gray-700 border-gray-600' : 'bg-gray-50 border-gray-200'} border-b`}>
                            <tr>
                                {['Carnet', 'Nombre Completo', 'Correo', 'Estado', 'Acciones'].map((h) => (
                                    <th key={h} className={`px-6 py-4 font-semibold ${darkMode ? 'text-gray-200' : 'text-gray-700'}`}>
                                        {h}
                                    </th>
                                ))}
                            </tr>
                        </thead>
                        <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                            {loading ? (
                                <tr>
                                    <td colSpan="5" className={`px-6 py-8 text-center ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        Cargando estudiantes...
                                    </td>
                                </tr>
                            ) : filtered.length === 0 ? (
                                <tr>
                                    <td colSpan="5" className={`px-6 py-8 text-center ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        No se encontraron estudiantes.
                                    </td>
                                </tr>
                            ) : (
                                filtered.map((s) => (
                                    <tr key={s.id} className={`${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'} transition-colors`}>
                                        <td className={`px-6 py-4 font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{s.carnet}</td>
                                        <td className={`px-6 py-4 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                            {s.firstName} {s.lastName}
                                        </td>
                                        <td className={`px-6 py-4 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{s.email || 'N/A'}</td>
                                        <td className="px-6 py-4">
                                            <span
                                                className={`px-2 py-1 rounded-full text-xs font-semibold ${s.isActive
                                                    ? 'bg-green-100 text-green-700'
                                                    : 'bg-red-100 text-red-700'
                                                    }`}
                                            >
                                                {s.isActive ? 'Activo' : 'Inactivo'}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex gap-2">
                                                <button className={`p-2 ${darkMode ? 'hover:bg-blue-900/50' : 'hover:bg-blue-100'} rounded-lg text-blue-500 transition-all`}>
                                                    <Edit size={18} />
                                                </button>
                                                <button className={`p-2 ${darkMode ? 'hover:bg-red-900/50' : 'hover:bg-red-100'} rounded-lg text-red-500 transition-all`}>
                                                    <Trash2 size={18} />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
};

export default Students;
