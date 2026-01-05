import React, { useState, useEffect } from 'react';
import { DollarSign, Save, Edit2, Plus, Check, X } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import api from '../services/api';

const CostosPage = () => {
    const { darkMode } = useTheme();
    const [costs, setCosts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [editingId, setEditingId] = useState(null);
    const [editForm, setEditForm] = useState({ enrollmentFee: '', monthlyFee: '' });

    useEffect(() => {
        loadCosts();
    }, []);

    const loadCosts = async () => {
        try {
            const response = await api.get('/financial/costs');
            setCosts(response.data.data || []);
        } catch (error) {
            console.error("Error loading costs", error);
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

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <div>
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Costos por Nivel</h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>Gestiona las cuotas de inscripción y mensualidad por nivel.</p>
                </div>
                <button className="bg-teal-600 text-white px-4 py-2 rounded-xl flex items-center gap-2" onClick={() => alert('Función de agregar nivel en desarrollo')}>
                    <Plus size={20} /> Agregar Nivel
                </button>
            </div>

            <div className={`rounded-xl shadow-lg border overflow-hidden ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
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
                                            className="w-24 p-1 rounded border dark:bg-gray-700 dark:border-gray-600"
                                            value={editForm.enrollmentFee}
                                            onChange={e => setEditForm({ ...editForm, enrollmentFee: e.target.value })}
                                        />
                                    ) : (
                                        `Q ${parseFloat(cost.enrollmentFee).toFixed(2)}`
                                    )}
                                </td>
                                <td className="p-4">
                                    {editingId === cost.id ? (
                                        <input
                                            type="number"
                                            className="w-24 p-1 rounded border dark:bg-gray-700 dark:border-gray-600"
                                            value={editForm.monthlyFee}
                                            onChange={e => setEditForm({ ...editForm, monthlyFee: e.target.value })}
                                        />
                                    ) : (
                                        `Q ${parseFloat(cost.monthlyFee).toFixed(2)}`
                                    )}
                                </td>
                                <td className="p-4 text-center">
                                    {editingId === cost.id ? (
                                        <div className="flex justify-center gap-2">
                                            <button onClick={() => handleSave(cost.id)} className="text-green-500 hover:text-green-600"><Check size={20} /></button>
                                            <button onClick={handleCancel} className="text-red-500 hover:text-red-600"><X size={20} /></button>
                                        </div>
                                    ) : (
                                        <button onClick={() => handleEdit(cost)} className="text-blue-500 hover:text-blue-600">
                                            <Edit2 size={18} />
                                        </button>
                                    )}
                                </td>
                            </tr>
                        ))}
                        {costs.length === 0 && !loading && (
                            <tr><td colSpan="4" className="p-8 text-center text-gray-500">No hay costos configurados.</td></tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default CostosPage;
