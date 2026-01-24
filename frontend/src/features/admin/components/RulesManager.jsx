import React, { useState, useEffect } from 'react';
import { securityService } from '../services/securityService';
import { useTheme } from '../../../contexts/ThemeContext';
import { BookOpen, Plus, Edit2, Trash2, X, Save, AlertCircle } from 'lucide-react';
import { toast } from 'sonner';

const RulesManager = () => {
    const { darkMode } = useTheme();
    const [rules, setRules] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [editingRule, setEditingRule] = useState(null);

    // Form State
    const [formData, setFormData] = useState({
        code: '',
        name: '',
        type: 'schedule',
        priority: 'medium',
        description: '',
        active: true
    });

    useEffect(() => {
        loadRules();
    }, []);

    const loadRules = async () => {
        try {
            const data = await securityService.getRules();
            setRules(data);
        } catch (error) {
            toast.error('Error al cargar reglas');
        } finally {
            setLoading(false);
        }
    };

    const handleEdit = (rule) => {
        setEditingRule(rule);
        setFormData(rule);
        setShowModal(true);
    };

    const handleCreate = () => {
        setEditingRule(null);
        setFormData({ code: '', name: '', type: 'schedule', priority: 'medium', description: '', active: true });
        setShowModal(true);
    };

    const handleSave = async (e) => {
        e.preventDefault();
        try {
            if (editingRule) {
                // await securityService.updateRule(editingRule.id, formData); // Add when backend ready
                setRules(prev => prev.map(r => r.id === editingRule.id ? { ...formData, id: r.id } : r));
                toast.success('Regla actualizada');
            } else {
                // await securityService.createRule(formData); // Add when backend ready
                setRules(prev => [...prev, { ...formData, id: Date.now() }]);
                toast.success('Regla creada');
            }
            setShowModal(false);
        } catch (error) {
            toast.error('Error al guardar regla');
        }
    };

    const handleDelete = async (id) => {
        if (!window.confirm('¿Eliminar esta regla?')) return;
        try {
            // await securityService.deleteRule(id);
            setRules(prev => prev.filter(r => r.id !== id));
            toast.success('Regla eliminada');
        } catch (error) {
            toast.error('Error al eliminar');
        }
    };

    const inputClass = `w-full px-3 py-2 rounded-lg border focus:ring-2 focus:ring-purple-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-200 text-gray-900'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    return (
        <div className={`rounded-2xl shadow-lg border p-6 ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h3 className={`text-xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                        <BookOpen className="text-pink-500" />
                        Reglas Institucionales
                    </h3>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        Gestión de normativas y restricciones automáticas.
                    </p>
                </div>
                <button
                    onClick={handleCreate}
                    className="flex items-center gap-2 px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors"
                >
                    <Plus size={18} /> Nueva Regla
                </button>
            </div>

            <div className="grid grid-cols-1 gap-4">
                {loading ? (
                    <p className="text-center py-8">Cargando reglas...</p>
                ) : rules.length === 0 ? (
                    <p className="text-center py-8">No hay reglas definidas.</p>
                ) : (
                    rules.map((rule) => (
                        <div key={rule.id} className={`p-4 rounded-xl border flex justify-between items-start ${darkMode ? 'bg-gray-700/30 border-gray-700' : 'bg-gray-50 border-gray-200'}`}>
                            <div>
                                <div className="flex items-center gap-3 mb-1">
                                    <span className={`text-xs font-mono px-2 py-0.5 rounded ${darkMode ? 'bg-gray-700' : 'bg-gray-200'}`}>{rule.code}</span>
                                    <h4 className={`font-semibold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{rule.name}</h4>
                                    <span className={`text-xs px-2 py-0.5 rounded-full ${rule.priority === 'critical' ? 'bg-red-100 text-red-700' :
                                            rule.priority === 'high' ? 'bg-orange-100 text-orange-700' :
                                                'bg-blue-100 text-blue-700'
                                        }`}>
                                        {rule.priority.toUpperCase()}
                                    </span>
                                </div>
                                <p className={`text-sm mb-2 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>{rule.description}</p>
                                <div className="flex gap-2 text-xs opacity-70">
                                    <span className="capitalize">{rule.type}</span>
                                </div>
                            </div>
                            <div className="flex gap-2">
                                <button onClick={() => handleEdit(rule)} className="p-2 text-blue-500 hover:bg-blue-50 rounded-lg">
                                    <Edit2 size={16} />
                                </button>
                                <button onClick={() => handleDelete(rule.id)} className="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                                    <Trash2 size={16} />
                                </button>
                            </div>
                        </div>
                    ))
                )}
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
                    <div className={`w-full max-w-lg rounded-2xl shadow-2xl p-6 ${darkMode ? 'bg-gray-800' : 'bg-white'}`}>
                        <div className="flex justify-between items-center mb-6">
                            <h3 className="text-xl font-bold">
                                {editingRule ? 'Editar Regla' : 'Nueva Regla'}
                            </h3>
                            <button onClick={() => setShowModal(false)}><X size={24} /></button>
                        </div>

                        <form onSubmit={handleSave} className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className={labelClass}>Código</label>
                                    <input type="text" className={inputClass} value={formData.code} onChange={e => setFormData({ ...formData, code: e.target.value })} requiredplaceholder="Ej: REG-001" />
                                </div>
                                <div>
                                    <label className={labelClass}>Nombre</label>
                                    <input type="text" className={inputClass} value={formData.name} onChange={e => setFormData({ ...formData, name: e.target.value })} required placeholder="Nombre de la regla" />
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className={labelClass}>Tipo</label>
                                    <select className={inputClass} value={formData.type} onChange={e => setFormData({ ...formData, type: e.target.value })}>
                                        <option value="schedule">Horario</option>
                                        <option value="financial">Financiero</option>
                                        <option value="security">Seguridad</option>
                                        <option value="academic">Académico</option>
                                    </select>
                                </div>
                                <div>
                                    <label className={labelClass}>Prioridad</label>
                                    <select className={inputClass} value={formData.priority} onChange={e => setFormData({ ...formData, priority: e.target.value })}>
                                        <option value="low">Baja</option>
                                        <option value="medium">Media</option>
                                        <option value="high">Alta</option>
                                        <option value="critical">Crítica</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label className={labelClass}>Descripción</label>
                                <textarea className={inputClass} rows="3" value={formData.description} onChange={e => setFormData({ ...formData, description: e.target.value })} />
                            </div>

                            <div className="flex items-center gap-2">
                                <input type="checkbox" id="active" checked={formData.active} onChange={e => setFormData({ ...formData, active: e.target.checked })} className="w-4 h-4 text-purple-600 rounded" />
                                <label htmlFor="active" className={darkMode ? 'text-gray-300' : 'text-gray-700'}>Regla Activa</label>
                            </div>

                            <div className="flex justify-end gap-3 mt-6">
                                <button type="button" onClick={() => setShowModal(false)} className="px-4 py-2 rounded-lg text-gray-500 hover:bg-gray-100">Cancelar</button>
                                <button type="submit" className="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium">Guardar Regla</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
};

export default RulesManager;
