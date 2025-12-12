import { useState, useEffect } from 'react';
import axios from 'axios';
import { Plus, Search, Trash2, Edit } from 'lucide-react';

const Students = () => {
    const [students, setStudents] = useState([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');

    const fetchStudents = async () => {
        try {
            const token = localStorage.getItem('token');
            const res = await axios.get('http://localhost:8000/api/students', {
                headers: { Authorization: `Bearer ${token}` }
            });

            setStudents(res.data['hydra:member'] || []);
        } catch (err) {
            console.error('Error fetching students:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchStudents();
    }, []);

    const filtered = students.filter((s) =>
        [s.firstName, s.lastName, s.carnet]
            .join(' ')
            .toLowerCase()
            .includes(searchTerm.toLowerCase())
    );

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex justify-between items-center">
                <h1 className="text-2xl font-bold text-gray-800">Gestión de Estudiantes</h1>

                <button className="bg-oxford-primary hover:bg-blue-900 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-all">
                    <Plus size={20} />
                    Nuevo Estudiante
                </button>
            </div>

            {/* Search */}
            <div className="bg-white p-4 rounded-xl shadow-sm">
                <div className="relative">
                    <Search className="absolute left-3 top-3 text-gray-400" size={20} />
                    <input
                        type="text"
                        placeholder="Buscar por nombre, apellido o carnet..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg 
                                   focus:ring-2 focus:ring-oxford-primary outline-none"
                    />
                </div>
            </div>

            {/* Table */}
            <div className="bg-white rounded-xl shadow-sm overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left">
                        <thead className="bg-gray-50 border-b border-gray-200">
                            <tr>
                                {['Carnet', 'Nombre Completo', 'Correo', 'Estado', 'Acciones'].map((h) => (
                                    <th key={h} className="px-6 py-4 font-semibold text-gray-700">
                                        {h}
                                    </th>
                                ))}
                            </tr>
                        </thead>

                        <tbody className="divide-y divide-gray-100">
                            {loading ? (
                                <tr>
                                    <td colSpan="5" className="px-6 py-8 text-center text-gray-500">
                                        Cargando estudiantes...
                                    </td>
                                </tr>
                            ) : filtered.length === 0 ? (
                                <tr>
                                    <td colSpan="5" className="px-6 py-8 text-center text-gray-500">
                                        No se encontraron estudiantes.
                                    </td>
                                </tr>
                            ) : (
                                filtered.map((s) => (
                                    <tr key={s.id} className="hover:bg-gray-50 transition-colors">
                                        <td className="px-6 py-4 font-medium text-gray-900">{s.carnet}</td>
                                        <td className="px-6 py-4 text-gray-700">
                                            {s.firstName} {s.lastName}
                                        </td>
                                        <td className="px-6 py-4 text-gray-500">{s.email || 'N/A'}</td>

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
                                                <button className="p-2 hover:bg-blue-100 rounded-lg text-blue-600 transition-all">
                                                    <Edit size={18} />
                                                </button>
                                                <button className="p-2 hover:bg-red-100 rounded-lg text-red-600 transition-all">
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
