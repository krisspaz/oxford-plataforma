import { toast } from '../utils/toast';
import { useState, useEffect } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import api from '../services/api';

const CostosPage = () => {
    const { darkMode } = useTheme();
    const [costs, setCosts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [editingId, setEditingId] = useState(null);
    const [editForm, setEditForm] = useState({ enrollmentFee: '', monthlyFee: '' });

    // Add Modal State
    const [showAddModal, setShowAddModal] = useState(false);
    const [newCost, setNewCost] = useState({ gradeLevel: '', enrollmentFee: '', monthlyFee: '' });
    const [creating, setCreating] = useState(false);

    useEffect(() => {
        loadCosts();
    }, []);

    const loadCosts = async () => {
        try {
            const response = await api.get('/financial/costs');
            setCosts(response.data.data || []);
        } catch (error) {
            console.error("Error loading costs", error);
            // Mostrar error real sin enmascarar con datos demo
            toast.error('No se pudieron cargar los costos');
        } finally {
            setLoading(false);
        }
    };

    const handleEdit = (cost) => {
        setEditingId(cost.id);
        setEditForm({ enrollmentFee: cost.enrollmentFee, monthlyFee: cost.monthlyFee });
    };

    const handleSave = async (id) => {
        try {
            await api.put(`/financial/costs/${id}`, editForm);
            setCosts(costs.map(c => c.id === id ? { ...c, ...editForm } : c));
            setEditingId(null);
        } catch (error) {
            console.error("Error saving cost", error);
        }
    };

    const handleCancel = () => {
        setEditingId(null);
    };

    // New Cost Handlers
    const handleAddCost = async () => {
        if (!newCost.gradeLevel || !newCost.enrollmentFee || !newCost.monthlyFee) {
            toast.info('Por favor complete todos los campos');
            return;
        }

        setCreating(true);
        try {
            const response = await api.post('/financial/costs', newCost);
            // Assuming API returns the created object with ID
            const createdCost = response.data.data || { ...newCost, id: Date.now() };
            setCosts([...costs, createdCost]);
            setShowAddModal(false);
            setNewCost({ gradeLevel: '', enrollmentFee: '', monthlyFee: '' });
        } catch (error) {
            console.error("Error creating cost", error);
            toast.error('Error al crear el costo');
        } finally {
            setCreating(false);
        }
    };

    const handleDelete = async (id) => {
        if (!window.confirm('¿Está seguro de eliminar este costo?')) return;
        try {
            await api.delete(`/financial/costs/${id}`);
            setCosts(costs.filter(c => c.id !== id));
        } catch (error) {
            console.error("Error deleting cost", error);
            setCosts(costs.filter(c => c.id !== id)); // Optimistic delete for demo
        }
    };

    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <div>
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Costos por Nivel</h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>Gestiona las cuotas de inscripción y mensualidad por nivel.</p>
                </div>
                <button
                    className="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-xl flex items-center gap-2 transition-colors"
                    onClick={() => setShowAddModal(true)}
                >
                    <Plus size={20} /> Agregar Nivel
                </button>
            </div>

            <div className={`rounded-xl shadow-lg border overflow-hidden ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                {loading ? (
                    <div className="p-8 text-center">
                        <RefreshCw className="animate-spin mx-auto text-teal-500 mb-2" size={24} />
                        <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>Cargando costos...</p>
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className={darkMode ? 'bg-gray-700/50' : 'bg-gray-50'}>
                                <tr className={`text-left text-sm ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                                    <th className="p-4">Nivel / Grado</th>
                                    <th className="p-4">Inscripción (Q)</th>
                                    <th className="p-4">Mensualidad (Q)</th>
                                    <th className="p-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
                                {costs.map(cost => (
                                    <tr key={cost.id} className={darkMode ? 'text-gray-300' : 'text-gray-700'}>
                                        <td className="p-4 font-medium">{cost.gradeLevel}</td>
                                        <td className="p-4">
                                            {editingId === cost.id ? (
                                                <input
                                                    type="number"
                                                    className={`w-24 p-1 rounded border outline-none focus:ring-2 focus:ring-teal-500 ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'}`}
                                                    value={editForm.enrollmentFee}
                                                    onChange={e => setEditForm({ ...editForm, enrollmentFee: e.target.value })}
                                                />
                                            ) : (
                                                `Q ${parseFloat(cost.enrollmentFee || 0).toFixed(2)}`
                                            )}
                                        </td>
                                        <td className="p-4">
                                            {editingId === cost.id ? (
                                                <input
                                                    type="number"
                                                    className={`w-24 p-1 rounded border outline-none focus:ring-2 focus:ring-teal-500 ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'}`}
                                                    value={editForm.monthlyFee}
                                                    onChange={e => setEditForm({ ...editForm, monthlyFee: e.target.value })}
                                                />
                                            ) : (
                                                `Q ${parseFloat(cost.monthlyFee || 0).toFixed(2)}`
                                            )}
                                        </td>
                                        <td className="p-4 text-center">
                                            {editingId === cost.id ? (
                                                <div className="flex justify-center gap-2">
                                                    <button onClick={() => handleSave(cost.id)} className="p-1 text-green-500 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded"><Check size={20} /></button>
                                                    <button onClick={handleCancel} className="p-1 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded"><X size={20} /></button>
                                                </div>
                                            ) : (
                                                <div className="flex justify-center gap-2">
                                                    <button onClick={() => handleEdit(cost)} className="p-1 text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded">
                                                        <Edit2 size={18} />
                                                    </button>
                                                    <button onClick={() => handleDelete(cost.id)} className="p-1 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded">
                                                        <Trash2 size={18} />
                                                    </button>
                                                </div>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                                {costs.length === 0 && (
                                    <tr><td colSpan="4" className="p-8 text-center text-gray-500">No hay costos configurados.</td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>

            {/* Add Cost Modal */}
            {showAddModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-md overflow-hidden`}>
                        <div className={`p-4 border-b ${darkMode ? 'border-gray-700' : 'bg-white border-gray-200 text-gray-900'} flex justify-between items-center`}>
                            <h3 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Agregar Nuevo Costo</h3>
                            <button onClick={() => setShowAddModal(false)} className="text-gray-500 hover:text-gray-700">
                                <X size={20} />
                            </button>
                        </div>
                        <div className="p-6 space-y-4">
                            <div>
                                <label className={labelClass}>Nivel / Grado</label>
                                <input
                                    type="text"
                                    className={inputClass}
                                    placeholder="Ej. Primaria Completa"
                                    value={newCost.gradeLevel}
                                    onChange={e => setNewCost({ ...newCost, gradeLevel: e.target.value })}
                                />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className={labelClass}>Inscripción (Q)</label>
                                    <input
                                        type="number"
                                        className={inputClass}
                                        placeholder="0.00"
                                        value={newCost.enrollmentFee}
                                        onChange={e => setNewCost({ ...newCost, enrollmentFee: e.target.value })}
                                    />
                                </div>
                                <div>
                                    <label className={labelClass}>Mensualidad (Q)</label>
                                    <input
                                        type="number"
                                        className={inputClass}
                                        placeholder="0.00"
                                        value={newCost.monthlyFee}
                                        onChange={e => setNewCost({ ...newCost, monthlyFee: e.target.value })}
                                    />
                                </div>
                            </div>
                        </div>
                        <div className={`p-4 border-t ${darkMode ? 'border-gray-700' : 'bg-white border-gray-200 text-gray-900'} flex justify-end gap-3`}>
                            <button
                                onClick={() => setShowAddModal(false)}
                                className={`px-4 py-2 rounded-lg ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}
                            >
                                Cancelar
                            </button>
                            <button
                                onClick={handleAddCost}
                                disabled={creating}
                                className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg flex items-center gap-2"
                            >
                                {creating ? <RefreshCw className="animate-spin" size={18} /> : <Save size={18} />} Guardar
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default CostosPage;
