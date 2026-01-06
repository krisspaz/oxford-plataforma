import React, { useState } from 'react';
import { Briefcase, Plus, Edit2, Trash2, Search, Users } from 'lucide-react';

const AdminChargesPage = () => {
    // Mock Data
    const [cargos, setCargos] = useState([
        { id: 1, nombre: 'Director General', departamento: 'Dirección', personal: 1 },
        { id: 2, nombre: 'Coordinador Académico', departamento: 'Coordinación', personal: 2 },
        { id: 3, nombre: 'Docente Titular', departamento: 'Docencia', personal: 45 },
        { id: 4, nombre: 'Secretaria Ejecutiva', departamento: 'Administración', personal: 3 },
        { id: 5, nombre: 'Contador', departamento: 'Finanzas', personal: 1 },
        { id: 6, nombre: 'Auxiliar de Limpieza', departamento: 'Mantenimiento', personal: 5 },
    ]);

    const [searchTerm, setSearchTerm] = useState('');

    const filteredCargos = cargos.filter(c =>
        c.nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
        c.departamento.toLowerCase().includes(searchTerm.toLowerCase())
    );

    return (
        <div className="p-6 max-w-7xl mx-auto">
            <header className="mb-8 flex justify-between items-center">
                <div>
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <Briefcase className="text-indigo-600" />
                        Gestión de Cargos
                    </h1>
                    <p className="text-gray-500 mt-2">Administración de puestos y jerarquías institucionales</p>
                </div>
                <button className="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <Plus size={18} />
                    Nuevo Cargo
                </button>
            </header>

            {/* Búsqueda y Filtros */}
            <div className="mb-6">
                <div className="relative max-w-md">
                    <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
                    <input
                        type="text"
                        placeholder="Buscar cargo o departamento..."
                        className="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white"
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                    />
                </div>
            </div>

            {/* Tabla */}
            <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
                        <tr>
                            <th className="p-4 font-semibold">Nombre del Cargo</th>
                            <th className="p-4 font-semibold">Departamento</th>
                            <th className="p-4 font-semibold text-center">Personal Asignado</th>
                            <th className="p-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                        {filteredCargos.map((cargo) => (
                            <tr key={cargo.id} className="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td className="p-4 font-medium text-gray-900 dark:text-white flex items-center gap-3">
                                    <div className="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                        <Briefcase size={16} className="text-gray-500" />
                                    </div>
                                    {cargo.nombre}
                                </td>
                                <td className="p-4 text-gray-600 dark:text-gray-300">
                                    <span className="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm">
                                        {cargo.departamento}
                                    </span>
                                </td>
                                <td className="p-4 text-center">
                                    <div className="flex items-center justify-center gap-1 text-gray-600 dark:text-gray-400">
                                        <Users size={16} />
                                        <span>{cargo.personal}</span>
                                    </div>
                                </td>
                                <td className="p-4 text-right">
                                    <div className="flex justify-end gap-2">
                                        <button className="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors">
                                            <Edit2 size={18} />
                                        </button>
                                        <button className="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                            <Trash2 size={18} />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {filteredCargos.length === 0 && (
                    <div className="p-8 text-center text-gray-500">
                        No se encontraron cargos que coincidan con la búsqueda.
                    </div>
                )}
            </div>
        </div>
    );
};

export default AdminChargesPage;
