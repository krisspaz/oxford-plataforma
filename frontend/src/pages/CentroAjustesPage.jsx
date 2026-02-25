import { toast } from '../utils/toast';
import React, { useState, useEffect } from 'react';
import { Settings, Users, DollarSign, GraduationCap, Search, Edit, History, Save, X, AlertTriangle } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import api from '../services/api';

const CentroAjustesPage = () => {
    const { darkMode } = useTheme();
    const [activeTab, setActiveTab] = useState('estudiantes');
    const [searchTerm, setSearchTerm] = useState('');
    const [loading, setLoading] = useState(false);
    const [showAdjustModal, setShowAdjustModal] = useState(false);
    const [selectedItem, setSelectedItem] = useState(null);
    const [adjustmentReason, setAdjustmentReason] = useState('');
    const [adjustmentData, setAdjustmentData] = useState({});
    const [recentAdjustments, setRecentAdjustments] = useState([]);

    const tabs = [
        { id: 'estudiantes', label: 'Estudiantes', icon: Users, color: 'blue' },
        { id: 'familias', label: 'Familias', icon: Users, color: 'teal' },
        { id: 'pagos', label: 'Pagos', icon: DollarSign, color: 'green' },
        { id: 'academico', label: 'Académico', icon: GraduationCap, color: 'purple' },
        { id: 'usuarios', label: 'Usuarios', icon: Users, color: 'orange' },
    ];

    // Load recent adjustments on mount
    useEffect(() => {
        loadRecentAdjustments();
    }, []);

    const loadRecentAdjustments = async () => {
        try {
            const response = await api.get('/admin/logs', {
                params: { limit: 10 }
            });
            const list = Array.isArray(response) ? response : (response?.data ?? response?.['hydra:member'] ?? []);
            setRecentAdjustments(Array.isArray(list) ? list : []);
        } catch (error) {
            console.error('Error loading adjustments:', error);
        }
    };

    const getTabContent = () => {
        switch (activeTab) {
            case 'estudiantes':
                return {
                    title: 'Ajustes de Estudiantes',
                    fields: [
                        { key: 'name', label: 'Nombre', type: 'text' },
                        { key: 'academicLevel', label: 'Nivel Académico', type: 'select' },
                        { key: 'grade', label: 'Grado', type: 'select' },
                        { key: 'section', label: 'Sección', type: 'select' },
                        { key: 'status', label: 'Estado', type: 'select' },
                    ],
                    description: 'Modificar datos de estudiantes. Requiere justificación.'
                };
            case 'familias':
                return {
                    title: 'Ajustes de Familias',
                    fields: [
                        { key: 'familyName', label: 'Nombre Familia', type: 'text' },
                        { key: 'email', label: 'Email', type: 'email' },
                        { key: 'phone', label: 'Teléfono', type: 'text' },
                        { key: 'address', label: 'Dirección', type: 'text' },
                    ],
                    description: 'Modificar datos de contacto de familias.'
                };
            case 'pagos':
                return {
                    title: 'Ajustes de Pagos',
                    fields: [
                        { key: 'amount', label: 'Monto', type: 'number' },
                        { key: 'dueDate', label: 'Fecha Vencimiento', type: 'date' },
                        { key: 'description', label: 'Descripción', type: 'text' },
                        { key: 'status', label: 'Estado', type: 'select' },
                    ],
                    description: 'Ajustes a cuotas y pagos. Registra historial de cambios.'
                };
            case 'academico':
                return {
                    title: 'Ajustes Académicos',
                    fields: [
                        { key: 'grade', label: 'Nota', type: 'number' },
                        { key: 'attendance', label: 'Asistencia', type: 'number' },
                        { key: 'observations', label: 'Observaciones', type: 'text' },
                    ],
                    description: 'Correcciones a notas o asistencias. Solo Admin.'
                };
            case 'usuarios':
                return {
                    title: 'Ajustes de Usuarios',
                    fields: [
                        { key: 'email', label: 'Email', type: 'email' },
                        { key: 'role', label: 'Rol', type: 'select' },
                        { key: 'status', label: 'Estado', type: 'select' },
                    ],
                    description: 'Modificar acceso y roles de usuarios.'
                };
            default:
                return { title: '', fields: [], description: '' };
        }
    };

    const content = getTabContent();

    const handleOpenAdjust = (item) => {
        setSelectedItem(item);
        setAdjustmentData({});
        setAdjustmentReason('');
        setShowAdjustModal(true);
    };

    const handleSaveAdjustment = async () => {
        if (!adjustmentReason.trim()) {
            toast.error('Debe ingresar una justificación para el ajuste');
            return;
        }

        setLoading(true);
        try {
            // This would call the appropriate API based on activeTab
            const endpoint = `/adjustments/${activeTab}`;
            await api.post(endpoint, {
                itemId: selectedItem?.id,
                changes: adjustmentData,
                reason: adjustmentReason,
            });

            toast.success('Ajuste guardado exitosamente');
            setShowAdjustModal(false);
            loadRecentAdjustments();
        } catch (error) {
            console.error('Error saving adjustment:', error);
            toast.error('Error al guardar el ajuste');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex justify-between items-center">
                <div>
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                        Centro de Ajustes
                    </h1>
                    <p className={`${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        Modificaciones con trazabilidad completa
                    </p>
                </div>
                <div className={`flex items-center gap-2 px-4 py-2 rounded-lg ${darkMode ? 'bg-amber-900/30 text-amber-400' : 'bg-amber-50 text-amber-700'}`}>
                    <AlertTriangle size={18} />
                    <span className="text-sm font-medium">Todos los cambios quedan registrados</span>
                </div>
            </div>

            {/* Tabs */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm p-2 flex gap-2 overflow-x-auto`}>
                {tabs.map(tab => (
                    <button
                        key={tab.id}
                        onClick={() => setActiveTab(tab.id)}
                        className={`flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap ${activeTab === tab.id
                            ? `bg-${tab.color}-600 text-white`
                            : darkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'
                            }`}
                    >
                        <tab.icon size={18} />
                        {tab.label}
                    </button>
                ))}
            </div>

            {/* Content Area */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Main Content */}
                <div className={`lg:col-span-2 ${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm p-6`}>
                    <div className="flex justify-between items-center mb-4">
                        <div>
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {content.title}
                            </h2>
                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                {content.description}
                            </p>
                        </div>
                    </div>

                    {/* Search */}
                    <div className="mb-4">
                        <div className="relative">
                            <Search className="absolute left-3 top-2.5 text-gray-400" size={18} />
                            <input
                                type="text"
                                placeholder="Buscar registro a ajustar..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                className={`w-full pl-10 pr-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none ${darkMode
                                    ? 'bg-gray-700 border-gray-600 text-white'
                                    : 'bg-gray-50 border-gray-300 text-gray-900'
                                    }`}
                            />
                        </div>
                    </div>

                    {/* Fields Preview */}
                    <div className={`p-4 rounded-lg border ${darkMode ? 'bg-gray-700/50 border-gray-600' : 'bg-gray-50 border-gray-200'}`}>
                        <h3 className={`font-medium mb-3 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                            Campos Editables:
                        </h3>
                        <div className="flex flex-wrap gap-2">
                            {content.fields.map(field => (
                                <span
                                    key={field.key}
                                    className={`px-3 py-1 rounded-full text-sm ${darkMode
                                        ? 'bg-gray-600 text-gray-300'
                                        : 'bg-white border border-gray-300 text-gray-600'
                                        }`}
                                >
                                    {field.label}
                                </span>
                            ))}
                        </div>
                    </div>

                    {/* Main Content Area - Search Results */}
                    <div className="mt-6">
                        {searchTerm ? (
                            <div className="text-center py-12">
                                <Search size={48} className="mx-auto text-gray-300 mb-4" />
                                <p className={`text-lg ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                    No se encontraron resultados para "{searchTerm}"
                                </p>
                            </div>
                        ) : (
                            <div className="text-center py-12">
                                <Settings size={48} className="mx-auto text-gray-300 mb-4" />
                                <p className={`text-lg ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                    Utilice el buscador para localizar un registro
                                </p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Recent Adjustments Sidebar */}
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm p-6`}>
                    <div className="flex items-center gap-2 mb-4">
                        <History size={20} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                        <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                            Ajustes Recientes
                        </h3>
                    </div>

                    <div className="space-y-3">
                        {recentAdjustments.length > 0 ? (
                            recentAdjustments.slice(0, 5).map((adj, index) => (
                                <div
                                    key={adj.id || index}
                                    className={`p-3 rounded-lg ${darkMode ? 'bg-gray-700' : 'bg-gray-50'}`}
                                >
                                    <p className={`font-medium text-sm ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                        {adj.description || adj.action}
                                    </p>
                                    <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        {adj.user} • {adj.createdAt}
                                    </p>
                                </div>
                            ))
                        ) : (
                            <div className="text-center py-8">
                                <History size={32} className="mx-auto text-gray-300 mb-2" />
                                <p className={`text-sm ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                                    No hay ajustes recientes
                                </p>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Adjustment Modal */}
            {showAdjustModal && selectedItem && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-lg p-6`}>
                        <div className="flex items-center justify-between mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                Realizar Ajuste
                            </h2>
                            <button onClick={() => setShowAdjustModal(false)}>
                                <X size={20} />
                            </button>
                        </div>

                        {/* Warning Banner */}
                        <div className={`p-3 rounded-lg mb-4 flex items-center gap-2 ${darkMode ? 'bg-amber-900/30 text-amber-400' : 'bg-amber-50 text-amber-700'}`}>
                            <AlertTriangle size={18} />
                            <span className="text-sm">Este cambio quedará registrado en el historial de auditoría</span>
                        </div>

                        {/* Before/After Fields */}
                        <div className="space-y-4">
                            {content.fields.slice(0, 3).map(field => (
                                <div key={field.key}>
                                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                        {field.label}
                                    </label>
                                    <div className="grid grid-cols-2 gap-2">
                                        <div>
                                            <span className="text-xs text-gray-500">Antes:</span>
                                            <input
                                                type={field.type}
                                                value={selectedItem[field.key] || ''}
                                                disabled
                                                className={`w-full px-3 py-2 rounded-lg border ${darkMode
                                                    ? 'bg-gray-700/50 border-gray-600 text-gray-400'
                                                    : 'bg-gray-100 border-gray-300 text-gray-500'
                                                    }`}
                                            />
                                        </div>
                                        <div>
                                            <span className="text-xs text-gray-500">Después:</span>
                                            <input
                                                type={field.type}
                                                value={adjustmentData[field.key] || ''}
                                                onChange={(e) => setAdjustmentData({ ...adjustmentData, [field.key]: e.target.value })}
                                                className={`w-full px-3 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none ${darkMode
                                                    ? 'bg-gray-700 border-gray-600 text-white'
                                                    : 'bg-white border-gray-300 text-gray-900'
                                                    }`}
                                            />
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>

                        {/* Reason (Required) */}
                        <div className="mt-4">
                            <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                Justificación del Ajuste *
                            </label>
                            <textarea
                                value={adjustmentReason}
                                onChange={(e) => setAdjustmentReason(e.target.value)}
                                rows={3}
                                placeholder="Ingrese el motivo del ajuste (obligatorio)..."
                                className={`w-full px-3 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none ${darkMode
                                    ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-500'
                                    : 'bg-white border-gray-300 text-gray-900'
                                    }`}
                            />
                        </div>

                        {/* Actions */}
                        <div className="flex gap-3 mt-6">
                            <button
                                onClick={() => setShowAdjustModal(false)}
                                className={`flex-1 px-4 py-2 rounded-lg ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200 text-gray-700'}`}
                            >
                                Cancelar
                            </button>
                            <button
                                onClick={handleSaveAdjustment}
                                disabled={loading || !adjustmentReason.trim()}
                                className="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg disabled:opacity-50 flex items-center justify-center gap-2"
                            >
                                <Save size={18} />
                                {loading ? 'Guardando...' : 'Guardar Ajuste'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default CentroAjustesPage;
