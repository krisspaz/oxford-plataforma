import { toast } from 'sonner';
import React, { useState, useMemo } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Layout, Plus, Edit, Trash, Loader2, X, ChevronRight, AlertCircle, Save } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { gradeService, catalogService } from '../services';

const SeccionesPage = () => {
    const { darkMode } = useTheme();
    const queryClient = useQueryClient();
    const [selectedGradeId, setSelectedGradeId] = useState('');
    const [saving, setSaving] = useState(false);
    const [showModal, setShowModal] = useState(false);
    const [formData, setFormData] = useState({ name: 'A', capacity: 30, schedule: 'Matutina' });
    const [selectedSection, setSelectedSection] = useState(null);

    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'}`;

    // === QUERIES ===
    const { data: grades = [] } = useQuery({
        queryKey: ['grades'],
        queryFn: async () => {
            const response = await gradeService.getAll();
            const data = response.data || response || [];
            return Array.isArray(data) ? data : (data['hydra:member'] || []);
        },
    });

    const { data: sections = [], isLoading: loading } = useQuery({
        queryKey: ['sections', selectedGradeId],
        queryFn: async () => {
            if (!selectedGradeId) return [];
            const data = await gradeService.getSections(selectedGradeId);
            return Array.isArray(data) ? data : [];
        },
        enabled: !!selectedGradeId,
    });

    // === MUTATION ===
    const saveMutation = useMutation({
        mutationFn: async (data) => gradeService.createSection(selectedGradeId, data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['sections', selectedGradeId] });
            toast.success('Sección guardada');
            setShowModal(false);
        },
        onError: () => toast.error('Error al guardar sección'),
    });

    const handleOpenModal = (section = null) => {
        if (section) {
            setSelectedSection(section);
            setFormData({
                name: section.name,
                capacity: section.capacity || 30,
                schedule: section.schedule || 'Matutina'
            });
        } else {
            setSelectedSection(null);
            setFormData({ name: 'A', capacity: 30, schedule: 'Matutina' });
        }
        setShowModal(true);
    };

    const handleSave = async () => {
        if (!selectedGradeId) return;
        setSaving(true);
        try {
            await saveMutation.mutateAsync(formData);
        } finally {
            setSaving(false);
        }
    };

    const handleDelete = async (id) => {
        if (!window.confirm('¿Eliminar sección?')) return;
        // Need delete service
        toast.info('Eliminación no disponible');
    };

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Gestión de Secciones</h1>
                <button
                    onClick={() => handleOpenModal()}
                    disabled={!selectedGradeId}
                    className="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <Plus size={20} /> Nueva Sección
                </button>
            </div>

            {/* Grade Selector */}
            <div className={`p-4 rounded-xl shadow-sm ${darkMode ? 'bg-gray-800' : 'bg-white'}`}>
                <label className={`block text-sm font-medium mb-2 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                    Seleccione un Grado Académico
                </label>
                <select
                    value={selectedGradeId}
                    onChange={e => setSelectedGradeId(e.target.value)}
                    className={inputClass}
                >
                    <option value="">-- Seleccionar Grado --</option>
                    {grades.map(g => (
                        <option key={g.id} value={g.id}>{g.name} ({g.level?.name || 'Nivel'})</option>
                    ))}
                </select>
            </div>

            {/* Sections List */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden min-h-[200px]`}>
                {!selectedGradeId ? (
                    <div className="flex flex-col items-center justify-center p-12 text-gray-500">
                        <Layout size={48} className="mb-4 opacity-20" />
                        <p>Seleccione un grado para ver sus secciones</p>
                    </div>
                ) : loading ? (
                    <div className="flex justify-center p-12"><Loader2 className="animate-spin text-teal-500" /></div>
                ) : sections.length === 0 ? (
                    <div className="flex flex-col items-center justify-center p-12 text-gray-500">
                        <p>No hay secciones creadas para este grado</p>
                    </div>
                ) : (
                    <table className="w-full">
                        <thead className={darkMode ? 'bg-gray-700' : 'bg-gray-50'}>
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jornada</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacidad</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-200'}`}>
                            {sections.map((section) => (
                                <tr key={section.id}>
                                    <td className={`px-6 py-4 whitespace-nowrap text-sm font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{section.name}</td>
                                    <td className={`px-6 py-4 whitespace-nowrap text-sm ${darkMode ? 'text-gray-300' : 'text-gray-900'}`}>{section.schedule}</td>
                                    <td className={`px-6 py-4 whitespace-nowrap text-sm ${darkMode ? 'text-gray-300' : 'text-gray-900'}`}>{section.capacity}</td>
                                    <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button className="text-blue-600 hover:text-blue-900 mr-4 disabled:opacity-50" disabled title="Edición no disponible"><Edit size={18} /></button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-sm p-6`}>
                        <div className="flex items-center justify-between mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {selectedSection ? 'Editar Sección' : 'Nueva Sección'}
                            </h2>
                            <button onClick={() => setShowModal(false)}><X size={20} /></button>
                        </div>
                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium mb-1">Nombre</label>
                                <select
                                    className={inputClass}
                                    value={formData.name}
                                    onChange={e => setFormData({ ...formData, name: e.target.value })}
                                >
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                    <option value="E">E</option>
                                    <option value="U">Unica</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-1">Jornada</label>
                                <select
                                    className={inputClass}
                                    value={formData.schedule}
                                    onChange={e => setFormData({ ...formData, schedule: e.target.value })}
                                >
                                    <option value="Matutina">Matutina</option>
                                    <option value="Vespertina">Vespertina</option>
                                    <option value="Fin de Semana">Fin de Semana</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-1">Capacidad Máxima</label>
                                <input
                                    type="number"
                                    className={inputClass}
                                    value={formData.capacity}
                                    onChange={e => setFormData({ ...formData, capacity: parseInt(e.target.value) })}
                                />
                            </div>
                        </div>
                        <div className="flex justify-end gap-3 mt-6">
                            <button onClick={() => setShowModal(false)} className={`px-4 py-2 rounded-lg ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}>Cancelar</button>
                            <button onClick={handleSave} disabled={saving} className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg flex items-center gap-2">
                                {saving ? <Loader2 size={14} className="animate-spin" /> : <Save size={18} />}
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default SeccionesPage;
