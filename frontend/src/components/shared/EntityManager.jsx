import { useState, useEffect, useMemo } from 'react';
import { useTheme } from '../../contexts/ThemeContext';
import { api } from '../../services';
import { toast } from '../../utils/toast';

/**
 * EntityManager - Generic CRUD Component
 * =======================================
 * Replaces: GradosPage, CursosNivelesPage, SeccionesPage, MateriasPage, 
 * UserListPage, ProductsPage, and any other simple CRUD pages.
 * 
 * @param {Object} config - Entity configuration
 * @param {string} config.title - Page title
 * @param {string} config.subtitle - Page subtitle
 * @param {string} config.endpoint - API endpoint (e.g., '/grades')
 * @param {string} config.entityName - Entity name for labels
 * @param {Array} config.columns - Column definitions [{key, label, type, width}]
 * @param {Array} config.formFields - Form field definitions [{key, label, type, required, options}]
 * @param {Object} config.defaultValues - Default values for new entities
 * @param {Function} config.onCustomAction - Optional custom action handler
 */
const EntityManager = ({
    title = 'Gestión de Entidades',
    subtitle = 'Administra los registros del sistema',
    endpoint = '/entities',
    entityName = 'elemento',
    columns = [],
    formFields = [],
    defaultValues = {},
    actions = ['create', 'edit', 'delete', 'view'],
    searchFields = [],
    customActions = [],
    // eslint-disable-next-line unused-imports/no-unused-vars
    Icon = null
}) => {
    const { darkMode } = useTheme();
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [modalMode, setModalMode] = useState('create'); // create | edit | view
    const [selectedItem, setSelectedItem] = useState(null);
    const [formData, setFormData] = useState(defaultValues);
    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 10;

    // Styles
    const cardClass = `${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} border rounded-xl`;
    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;
    const btnPrimary = 'px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-lg shadow-lg transition-all flex items-center gap-2';
    const btnSecondary = `px-4 py-2 rounded-lg ${darkMode ? 'bg-gray-700 hover:bg-gray-600 text-gray-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'}`;

    // Load data
    useEffect(() => {
        loadData();
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [endpoint]);

    const loadData = async () => {
        setLoading(true);
        try {
            const response = await api.get(endpoint);
            const items = response.data || response['hydra:member'] || response || [];
            setData(Array.isArray(items) ? items : []);
        } catch (error) {
            console.error('Error loading data:', error);
            setData([]);
        } finally {
            setLoading(false);
        }
    };

    // Filter data based on search
    const filteredData = useMemo(() => {
        if (!searchTerm) return data;
        const term = searchTerm.toLowerCase();
        return data.filter(item => {
            const fieldsToSearch = searchFields.length > 0 ? searchFields : columns.map(c => c.key);
            return fieldsToSearch.some(field => {
                const value = item[field];
                return value && String(value).toLowerCase().includes(term);
            });
        });
    }, [data, searchTerm, searchFields, columns]);

    // Pagination
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    const paginatedData = filteredData.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

    // CRUD handlers
    const openCreate = () => {
        setFormData(defaultValues);
        setSelectedItem(null);
        setModalMode('create');
        setShowModal(true);
    };

    const openEdit = (item) => {
        setFormData(item);
        setSelectedItem(item);
        setModalMode('edit');
        setShowModal(true);
    };

    const openView = (item) => {
        setSelectedItem(item);
        setModalMode('view');
        setShowModal(true);
    };

    const handleSave = async () => {
        try {
            if (modalMode === 'create') {
                await api.post(endpoint, formData);
            } else if (modalMode === 'edit' && selectedItem?.id) {
                await api.put(`${endpoint}/${selectedItem.id}`, formData);
            }
            setShowModal(false);
            loadData();
        } catch (error) {
            console.error('Error saving:', error);
            toast.error('Error al guardar: ' + error.message);
        }
    };

    const handleDelete = async (item) => {
        if (!confirm(`¿Eliminar este ${entityName}?`)) return;
        try {
            await api.delete(`${endpoint}/${item.id}`);
            loadData();
        } catch (error) {
            console.error('Error deleting:', error);
            toast.error('Error al eliminar: ' + error.message);
        }
    };

    const handleFieldChange = (key, value) => {
        setFormData(prev => ({ ...prev, [key]: value }));
    };

    // Render field value based on type
    const renderCellValue = (item, column) => {
        const value = item[column.key];
        switch (column.type) {
            case 'date':
                return value ? new Date(value).toLocaleDateString('es-GT') : '-';
            case 'datetime':
                return value ? new Date(value).toLocaleString('es-GT') : '-';
            case 'currency':
                return value ? `Q ${Number(value).toLocaleString()}` : 'Q 0';
            case 'boolean':
                return value ? '✓ Sí' : '✗ No';
            case 'badge':
                // eslint-disable-next-line no-case-declarations
                const badgeColors = column.badgeColors || {};
                // eslint-disable-next-line no-case-declarations
                const color = badgeColors[value] || 'gray';
                return (
                    <span className={`px-2 py-0.5 rounded-full text-xs font-bold bg-${color}-100 text-${color}-700 dark:bg-${color}-900/30 dark:text-${color}-400`}>
                        {value || '-'}
                    </span>
                );
            case 'array':
                return Array.isArray(value) ? value.join(', ') : value;
            default:
                return value ?? '-';
        }
    };

    // Render form field
    const renderFormField = (field) => {
        const value = formData[field.key] ?? '';
        const isReadonly = modalMode === 'view';

        switch (field.type) {
            case 'select':
                return (
                    <select
                        className={inputClass}
                        value={value}
                        onChange={e => handleFieldChange(field.key, e.target.value)}
                        disabled={isReadonly}
                    >
                        <option value="">Seleccionar...</option>
                        {(field.options || []).map(opt => (
                            <option key={opt.value} value={opt.value}>{opt.label}</option>
                        ))}
                    </select>
                );
            case 'textarea':
                return (
                    <textarea
                        className={inputClass}
                        rows={3}
                        value={value}
                        onChange={e => handleFieldChange(field.key, e.target.value)}
                        disabled={isReadonly}
                        placeholder={field.placeholder}
                    />
                );
            case 'number':
                return (
                    <input
                        type="number"
                        className={inputClass}
                        value={value}
                        onChange={e => handleFieldChange(field.key, parseFloat(e.target.value) || 0)}
                        disabled={isReadonly}
                        min={field.min}
                        max={field.max}
                        step={field.step}
                    />
                );
            case 'checkbox':
                return (
                    <label className="flex items-center gap-2">
                        <input
                            type="checkbox"
                            checked={!!value}
                            onChange={e => handleFieldChange(field.key, e.target.checked)}
                            disabled={isReadonly}
                            className="w-4 h-4 rounded"
                        />
                        <span className={darkMode ? 'text-gray-300' : 'text-gray-700'}>{field.checkboxLabel || 'Activo'}</span>
                    </label>
                );
            case 'date':
                return (
                    <input
                        type="date"
                        className={inputClass}
                        value={value ? value.substring(0, 10) : ''}
                        onChange={e => handleFieldChange(field.key, e.target.value)}
                        disabled={isReadonly}
                    />
                );
            default:
                return (
                    <input
                        type={field.type || 'text'}
                        className={inputClass}
                        value={value}
                        onChange={e => handleFieldChange(field.key, e.target.value)}
                        disabled={isReadonly}
                        placeholder={field.placeholder}
                    />
                );
        }
    };

    if (loading) {
        return (
            <div className={`${cardClass} p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-indigo-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className={`text-3xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{title}</h1>
                    <p className={`${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{subtitle}</p>
                </div>
                {actions.includes('create') && (
                    <button onClick={openCreate} className={btnPrimary}>
                        <Plus size={18} /> Nuevo {entityName}
                    </button>
                )}
            </div>

            {/* Search & Filters */}
            <div className={`${cardClass} p-4 flex flex-col md:flex-row gap-4`}>
                <div className="relative flex-1">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                    <input
                        type="text"
                        placeholder="Buscar..."
                        value={searchTerm}
                        onChange={e => { setSearchTerm(e.target.value); setCurrentPage(1); }}
                        className={`${inputClass} pl-10`}
                    />
                </div>
                <button className={btnSecondary + ' flex items-center gap-2'}>
                    <Filter size={18} /> Filtros
                </button>
                <button onClick={loadData} className={btnSecondary + ' flex items-center gap-2'}>
                    <RefreshCw size={18} /> Actualizar
                </button>
            </div>

            {/* Table */}
            <div className={`${cardClass} overflow-hidden`}>
                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead className={darkMode ? 'bg-gray-700' : 'bg-gray-50'}>
                            <tr>
                                {columns.map(col => (
                                    <th key={col.key} className={`px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider ${darkMode ? 'text-gray-300' : 'text-gray-600'}`} style={{ width: col.width }}>
                                        {col.label}
                                    </th>
                                ))}
                                <th className={`px-4 py-3 text-right text-xs font-semibold uppercase ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Acciones</th>
                            </tr>
                        </thead>
                        <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                            {paginatedData.length === 0 ? (
                                <tr>
                                    <td colSpan={columns.length + 1} className={`px-4 py-12 text-center ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                                        No se encontraron registros
                                    </td>
                                </tr>
                            ) : (
                                paginatedData.map((item, idx) => (
                                    <tr key={item.id || idx} className={darkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'}>
                                        {columns.map(col => (
                                            <td key={col.key} className={`px-4 py-3 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                                {renderCellValue(item, col)}
                                            </td>
                                        ))}
                                        <td className="px-4 py-3 text-right">
                                            <div className="flex items-center justify-end gap-1">
                                                {actions.includes('view') && (
                                                    <button onClick={() => openView(item)} className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`} title="Ver">
                                                        <Eye size={16} className="text-blue-500" />
                                                    </button>
                                                )}
                                                {actions.includes('edit') && (
                                                    <button onClick={() => openEdit(item)} className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`} title="Editar">
                                                        <Edit size={16} className="text-amber-500" />
                                                    </button>
                                                )}
                                                {actions.includes('delete') && (
                                                    <button onClick={() => handleDelete(item)} className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`} title="Eliminar">
                                                        <Trash2 size={16} className="text-red-500" />
                                                    </button>
                                                )}
                                                {customActions.map((action, i) => (
                                                    <button key={i} onClick={() => action.onClick(item)} className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`} title={action.label}>
                                                        {action.icon}
                                                    </button>
                                                ))}
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                {totalPages > 1 && (
                    <div className={`px-4 py-3 flex items-center justify-between border-t ${darkMode ? 'border-gray-700' : 'border-gray-100'}`}>
                        <span className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                            Mostrando {((currentPage - 1) * itemsPerPage) + 1} - {Math.min(currentPage * itemsPerPage, filteredData.length)} de {filteredData.length}
                        </span>
                        <div className="flex items-center gap-2">
                            <button
                                onClick={() => setCurrentPage(p => Math.max(1, p - 1))}
                                disabled={currentPage === 1}
                                className={`p-2 rounded ${darkMode ? 'hover:bg-gray-700 disabled:opacity-50' : 'hover:bg-gray-100 disabled:opacity-50'}`}
                            >
                                <ChevronLeft size={18} />
                            </button>
                            <span className={`text-sm ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                {currentPage} / {totalPages}
                            </span>
                            <button
                                onClick={() => setCurrentPage(p => Math.min(totalPages, p + 1))}
                                disabled={currentPage === totalPages}
                                className={`p-2 rounded ${darkMode ? 'hover:bg-gray-700 disabled:opacity-50' : 'hover:bg-gray-100 disabled:opacity-50'}`}
                            >
                                <ChevronRight size={18} />
                            </button>
                        </div>
                    </div>
                )}
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col`}>
                        <div className={`p-4 border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'} flex items-center justify-between`}>
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {modalMode === 'create' ? `Nuevo ${entityName}` : modalMode === 'edit' ? `Editar ${entityName}` : `Detalle de ${entityName}`}
                            </h2>
                            <button onClick={() => setShowModal(false)} className={`p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700`}>
                                <X size={20} />
                            </button>
                        </div>

                        <div className="flex-1 overflow-y-auto p-4 space-y-4">
                            {formFields.map(field => (
                                <div key={field.key}>
                                    <label className={labelClass}>
                                        {field.label} {field.required && <span className="text-red-500">*</span>}
                                    </label>
                                    {renderFormField(field)}
                                </div>
                            ))}
                        </div>

                        <div className={`p-4 border-t ${darkMode ? 'border-gray-700' : 'border-gray-200'} flex justify-end gap-3`}>
                            <button onClick={() => setShowModal(false)} className={btnSecondary}>
                                {modalMode === 'view' ? 'Cerrar' : 'Cancelar'}
                            </button>
                            {modalMode !== 'view' && (
                                <button onClick={handleSave} className={btnPrimary}>
                                    <Save size={16} /> Guardar
                                </button>
                            )}
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default EntityManager;
