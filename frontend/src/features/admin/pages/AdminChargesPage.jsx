import { useState, useEffect } from 'react';
import { jobTitleService } from '../../../services';
import { Briefcase, Plus, Search, Users, Edit2, Trash2, X, Save } from 'lucide-react';
import { toast } from '../../../utils/toast';

const AdminChargesPage = () => {
    // Real Data State
    const [cargos, setCargos] = useState([]);
    const [loading, setLoading] = useState(true);

    const [searchTerm, setSearchTerm] = useState('');
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingCargo, setEditingCargo] = useState(null);
    const [formData, setFormData] = useState({ name: '', department: '' });

    const departamentos = ['Dirección', 'Coordinación', 'Docencia', 'Administración', 'Finanzas', 'Mantenimiento', 'Tecnología'];

    useEffect(() => {
        loadCargos();
    }, []);

    const loadCargos = async () => {
        setLoading(true);
        try {
            const data = await jobTitleService.getAll();
            // Map API response to component structure if needed, or use directly
            // API Platform returns @id, name, department. Staff count might need extra logic or be included if defined in entity groups
            // For now, personal count will be 0 until backend provides it or we fetch it similarly
            setCargos(Array.isArray(data) ? data : []);
        } catch (error) {
            console.error("Error loading job titles:", error);
            // alert("Error al cargar cargos");
        } finally {
            setLoading(false);
        }
    };

    const filteredCargos = cargos.filter(c =>
        c.name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        c.department?.toLowerCase().includes(searchTerm.toLowerCase())
    );

    const handleOpenNew = () => {
        setEditingCargo(null);
        setFormData({ name: '', department: '' });
        setIsModalOpen(true);
    };

    const handleOpenEdit = (cargo) => {
        setEditingCargo(cargo);
        setFormData({ name: cargo.name, department: cargo.department });
        setIsModalOpen(true);
    };

    const handleDelete = async (id) => {
        if (confirm('¿Estás seguro de eliminar este cargo?')) {
            try {
                await jobTitleService.delete(id);
                loadCargos();
            } catch (error) {
                console.error("Error deleting:", error);
                toast.error("No se pudo eliminar el cargo. Verifique que no tenga personal asignado.");
            }
        }
    };

    const handleSave = async () => {
        if (!formData.name || !formData.department) {
            toast.warning('Por favor completa todos los campos');
            return;
        }

        try {
            if (editingCargo) {
                await jobTitleService.update(editingCargo.id, formData);
            } else {
                await jobTitleService.create(formData);
            }
            setIsModalOpen(false);
            loadCargos();
        } catch (error) {
            console.error("Error saving:", error);
            toast.error("Error al guardar el cargo");
        }
    };

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
                <button
                    onClick={handleOpenNew}
                    className="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
                >
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
                                    {cargo.name}
                                </td>
                                <td className="p-4 text-gray-600 dark:text-gray-300">
                                    <span className="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm">
                                        {cargo.department}
                                    </span>
                                </td>
                                <td className="p-4 text-center">
                                    <div className="flex items-center justify-center gap-1 text-gray-600 dark:text-gray-400">
                                        <Users size={16} />
                                        <span>{cargo.staffMembers?.length || 0}</span>
                                    </div>
                                </td>
                                <td className="p-4 text-right">
                                    <div className="flex justify-end gap-2">
                                        <button
                                            onClick={() => handleOpenEdit(cargo)}
                                            className="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors"
                                            title="Editar"
                                        >
                                            <Edit2 size={18} />
                                        </button>
                                        <button
                                            onClick={() => handleDelete(cargo.id)}
                                            className="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                            title="Eliminar"
                                        >
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

            {/* Modal */}
            {isModalOpen && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                    <div className="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                        <div className="p-4 border-b dark:border-gray-700 flex items-center justify-between">
                            <h2 className="text-lg font-bold text-gray-900 dark:text-white">
                                {editingCargo ? 'Editar Cargo' : 'Nuevo Cargo'}
                            </h2>
                            <button
                                onClick={() => setIsModalOpen(false)}
                                className="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                            >
                                <X size={20} className="text-gray-500" />
                            </button>
                        </div>
                        <div className="p-4 space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nombre del Cargo
                                </label>
                                <input
                                    type="text"
                                    value={formData.name}
                                    onChange={e => setFormData(prev => ({ ...prev, name: e.target.value }))}
                                    placeholder="Ej: Coordinador de Primaria"
                                    className="w-full px-3 py-2 border dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Departamento
                                </label>
                                <select
                                    value={formData.department}
                                    onChange={e => setFormData(prev => ({ ...prev, department: e.target.value }))}
                                    className="w-full px-3 py-2 border dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                >
                                    <option value="">Seleccionar...</option>
                                    {departamentos.map(d => (
                                        <option key={d} value={d}>{d}</option>
                                    ))}
                                </select>
                            </div>
                        </div>
                        <div className="p-4 border-t dark:border-gray-700 flex justify-end gap-2">
                            <button
                                onClick={() => setIsModalOpen(false)}
                                className="px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                            >
                                Cancelar
                            </button>
                            <button
                                onClick={handleSave}
                                className="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2"
                            >
                                <Save size={16} /> Guardar
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default AdminChargesPage;

