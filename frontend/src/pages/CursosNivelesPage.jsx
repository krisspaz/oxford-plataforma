import { toast } from '../utils/toast';
import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Layers, Plus, Edit, Trash, Check, X, BookOpen, ChevronRight, Users, GraduationCap, RefreshCw } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { catalogService } from '../services';

const CursosNivelesPage = () => {
    const { darkMode } = useTheme();
    const navigate = useNavigate();
    const [levels, setLevels] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [formData, setFormData] = useState({ name: '', code: '', sortOrder: 0, isActive: true });
    const [selectedLevel, setSelectedLevel] = useState(null);

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        setLoading(true);
        try {
            const response = await catalogService.getAcademicLevels();
            // The controller returns a direct array, or an array inside data? 
            // Controller returns: $this->json(array_map(...)) -> which is an array directly.
            // But api.js wrapper returns 'data'. 
            // If the symfony controller returns json array, axios sees it as data.
            // Let's assume response is the array of levels.
            // Wait, api.js: if (contentType.includes('application/json')) data = await response.json(); return data;
            // So if controller returns [..], then response is [..].
            // If controller returns {success:true, data:[]}, then response is {success...}
            // My AcademicLevelController returns simple array for index(), OR objects.
            // Let's protect against both structure.
            const data = Array.isArray(response) ? response : (response.data || []);
            setLevels(data);
        } catch (error) {
            console.error(error);
        } finally {
            setLoading(false);
        }
    };

    const handleOpenModal = (level = null) => {
        if (level) {
            setSelectedLevel(level);
            setFormData({
                name: level.name,
                code: level.code || '',
                sortOrder: level.sortOrder || 0,
                isActive: level.isActive
            });
        } else {
            setSelectedLevel(null);
            setFormData({ name: '', code: '', sortOrder: 0, isActive: true });
        }
        setShowModal(true);
    };

    const handleSave = async () => {
        try {
            if (!formData.name) return toast.info('El nombre es obligatorio');
            if (!formData.code) return toast.info('El código es obligatorio');

            console.log('Sending data:', formData); // Debug

            let response;
            if (selectedLevel) {
                response = await catalogService.updateAcademicLevel(selectedLevel.id, formData);
            } else {
                response = await catalogService.createAcademicLevel(formData);
            }

            console.log('Server response:', response);

            // Check response status if accessible, or assume success if no throw
            toast.success(selectedLevel ? 'Nivel actualizado' : 'Nivel creado correctamente');

            setShowModal(false);
            await loadData(); // Ensure await
        } catch (error) {
            console.error('Error saving level:', error);
            // Show detailed error if available
            const message = error.response?.data?.message || error.response?.data?.detail || error.message || 'Error desconocido';
            toast.error('Error al guardar: ' + message);
        }
    };

    const handleDelete = async (id) => {
        if (!window.confirm('¿Está seguro de eliminar este nivel? Esta acción no se puede deshacer.')) return;
        try {
            await catalogService.deleteAcademicLevel(id);
            loadData();
        } catch (error) {
            console.error(error);
            toast.info('No se puede eliminar el nivel (posiblemente tiene grados asociados)');
        }
    };

    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'}`;

    return (
        <div className="space-y-8">
            <div className="flex justify-between items-center">
                <div>
                    <h1 className={`text-3xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Niveles Educativos</h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>Gestiona la estructura académica y niveles.</p>
                </div>
                <button
                    onClick={() => handleOpenModal()}
                    className="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl shadow-lg shadow-indigo-500/30 flex items-center gap-2 font-medium transition-all hover:-translate-y-1"
                >
                    <Plus size={20} /> Nuevo Nivel
                </button>
            </div>

            {loading ? (
                <div className="flex justify-center p-12"><RefreshCw className="animate-spin text-indigo-500" /></div>
            ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    {levels.map((level) => (
                        <div key={level.id} className={`group relative overflow-hidden rounded-2xl ${darkMode ? 'bg-gray-800/80 border-gray-700' : 'bg-white border-gray-100'} border shadow-lg hover:shadow-2xl transition-all duration-300`}>
                            <div className={`absolute top-0 left-0 w-2 h-full ${level.isActive ? 'bg-indigo-500' : 'bg-gray-400'}`}></div>
                            <div className="p-6">
                                <div className="flex justify-between items-start mb-4">
                                    <div className={`p-3 rounded-xl bg-indigo-100 text-indigo-600`}>
                                        <Layers size={28} />
                                    </div>
                                    <div className="flex gap-1">
                                        <button
                                            onClick={() => handleOpenModal(level)}
                                            className={`p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}
                                        >
                                            <Edit size={16} />
                                        </button>
                                        <button
                                            onClick={() => handleDelete(level.id)}
                                            className={`p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-red-500`}
                                        >
                                            <Trash size={16} />
                                        </button>
                                    </div>
                                </div>

                                <h3 className={`text-2xl font-bold mb-1 ${darkMode ? 'text-white' : 'text-gray-900'}`}>{level.name}</h3>
                                <p className="text-sm text-gray-400 font-mono mb-6">Código: {level.code || 'N/A'}</p>

                                <div className="space-y-3">
                                    <div className="flex items-center justify-between text-sm">
                                        <span className="flex items-center gap-2 text-gray-500">
                                            <div className={`w-2 h-2 rounded-full ${level.isActive ? 'bg-green-500' : 'bg-red-500'}`}></div>
                                            Estado
                                        </span>
                                        <span className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{level.isActive ? 'Activo' : 'Inactivo'}</span>
                                    </div>
                                </div>

                                <button
                                    onClick={() => navigate('/academico/grados')}
                                    className={`w-full mt-6 py-2 rounded-lg border ${darkMode ? 'border-gray-600 hover:bg-gray-700 text-gray-300' : 'border-gray-200 hover:bg-gray-50 text-gray-600'} text-sm font-medium transition-colors flex items-center justify-center gap-2 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/20 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 group-hover:border-indigo-200 dark:group-hover:border-indigo-800`}
                                >
                                    Ver Grados <ChevronRight size={16} />
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}

            <div className={`mt-8 p-6 rounded-2xl ${darkMode ? 'bg-indigo-900/20 border-indigo-800' : 'bg-indigo-50 border-indigo-100'} border`}>
                <div className="flex items-start gap-4">
                    <div className="p-3 bg-indigo-100 text-indigo-600 rounded-lg">
                        <GraduationCap size={24} />
                    </div>
                    <div>
                        <h4 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>Resumen Académico</h4>
                        <p className={`text-sm mt-1 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                            Actualmente cuenta con {levels.length} niveles educativos.
                            Para crear un nuevo grado, seleccione primero el nivel correspondiente en la sección de Grados.
                        </p>
                    </div>
                </div>
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-md p-6`}>
                        <div className="flex items-center justify-between mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {selectedLevel ? 'Editar Nivel' : 'Nuevo Nivel'}
                            </h2>
                            <button onClick={() => setShowModal(false)}><X size={20} /></button>
                        </div>
                        <div className="space-y-4">
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Nombre</label>
                                <input value={formData.name} onChange={e => setFormData({ ...formData, name: e.target.value })} className={inputClass} placeholder="Ej: Primaria" />
                            </div>
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Código</label>
                                <input value={formData.code} onChange={e => setFormData({ ...formData, code: e.target.value })} className={inputClass} placeholder="Ej: PRI" />
                            </div>
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Orden</label>
                                <input type="number" value={formData.sortOrder} onChange={e => setFormData({ ...formData, sortOrder: parseInt(e.target.value) })} className={inputClass} />
                            </div>
                            <div className="flex items-center gap-2">
                                <input type="checkbox" checked={formData.isActive} onChange={e => setFormData({ ...formData, isActive: e.target.checked })} />
                                <span className={darkMode ? 'text-gray-300' : 'text-gray-700'}>Activo</span>
                            </div>
                        </div>
                        <div className="flex justify-end gap-3 mt-6">
                            <button onClick={() => setShowModal(false)} className={`px-4 py-2 rounded-lg ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}>Cancelar</button>
                            <button onClick={handleSave} className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Guardar</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default CursosNivelesPage;
