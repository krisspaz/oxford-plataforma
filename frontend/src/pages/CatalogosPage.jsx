import { toast } from '../utils/toast';
import React, { useState, useEffect } from 'react';
import { Settings, Plus, Edit, X, RefreshCw, Trash2 } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { catalogService } from '../services';

const CatalogosPage = () => {
    const { darkMode } = useTheme();
    const [activeTab, setActiveTab] = useState('statuses');
    const [showModal, setShowModal] = useState(false);
    const [selectedItem, setSelectedItem] = useState(null);
    const [loading, setLoading] = useState(true);
    const [catalogs, setCatalogs] = useState({
        statuses: [],
        relationships: [],
        documentTypes: [],
        paymentMethods: []
    });

    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    const tabs = [
        { key: 'statuses', label: 'Estados', loadFn: 'getStatuses' },
        { key: 'relationships', label: 'Parentescos', loadFn: 'getRelationshipTypes' },
        { key: 'documentTypes', label: 'Tipos Documento', loadFn: 'getDocumentTypes' },
        { key: 'paymentMethods', label: 'Métodos de Pago', loadFn: 'getPaymentMethods' },
    ];

    useEffect(() => {
        loadAllCatalogs();
    }, []);

    const loadAllCatalogs = async () => {
        setLoading(true);
        try {
            const [statuses, relationships, documentTypes, paymentMethods] = await Promise.all([
                catalogService.getStatuses(),
                catalogService.getRelationshipTypes(),
                catalogService.getDocumentTypes(),
                catalogService.getPaymentMethods()
            ]);

            setCatalogs({
                statuses: statuses.success ? statuses.data : [],
                relationships: relationships.success ? relationships.data : [],
                documentTypes: documentTypes.success ? documentTypes.data : [],
                paymentMethods: paymentMethods.success ? paymentMethods.data : []
            });
        } catch (error) {
            console.error('Error loading catalogs:', error);
            toast.error('Error al cargar catálogos: ' + error.message);
            setCatalogs({ statuses: [], relationships: [], documentTypes: [], paymentMethods: [] });
        } finally {
            setLoading(false);
        }
    };

    const currentItems = catalogs[activeTab] || [];
    const currentTabLabel = tabs.find(t => t.key === activeTab)?.label || '';

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando catálogos...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Catálogos del Sistema</h1>
                <button onClick={() => { setSelectedItem(null); setShowModal(true); }} className="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
                    <Plus size={18} /> Nuevo {currentTabLabel.slice(0, -1)}
                </button>
            </div>

            {/* Tabs */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-1 inline-flex shadow-sm flex-wrap gap-1`}>
                {tabs.map(tab => (
                    <button
                        key={tab.key}
                        onClick={() => setActiveTab(tab.key)}
                        className={`px-4 py-2 rounded-lg font-medium text-sm ${activeTab === tab.key
                            ? 'bg-teal-600 text-white'
                            : darkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'
                            }`}
                    >
                        {tab.label}
                    </button>
                ))}
            </div>

            {/* Table */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                <div className={`p-4 border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                    <h2 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                        <Settings size={18} className="inline mr-2" />{currentTabLabel}
                    </h2>
                </div>
                <table className="w-full">
                    <thead className={darkMode ? 'bg-gray-700' : 'bg-gray-50'}>
                        <tr>
                            <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Código</th>
                            <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Nombre</th>
                            <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Acciones</th>
                        </tr>
                    </thead>
                    <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                        {currentItems.map(item => (
                            <tr key={item.id} className={darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}>
                                <td className={`px-4 py-3 font-mono ${darkMode ? 'text-teal-400' : 'text-teal-600'}`}>{item.code}</td>
                                <td className={`px-4 py-3 ${darkMode ? 'text-white' : 'text-gray-800'}`}>{item.name}</td>
                                <td className="px-4 py-3 text-center">
                                    <button
                                        onClick={() => { setSelectedItem(item); setShowModal(true); }}
                                        className={`p-1.5 rounded mx-1 ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`}
                                    >
                                        <Edit size={16} className="text-blue-500" />
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {currentItems.length === 0 && (
                    <div className={`p-8 text-center ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                        No hay elementos en este catálogo
                    </div>
                )}
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-md p-6`}>
                        <div className="flex items-center justify-between mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {selectedItem ? 'Editar' : 'Nuevo'} {currentTabLabel.slice(0, -1)}
                            </h2>
                            <button onClick={() => setShowModal(false)}><X size={20} /></button>
                        </div>
                        <div className="space-y-4">
                            <div>
                                <label className={labelClass}>Código</label>
                                <input
                                    type="text"
                                    className={inputClass}
                                    defaultValue={selectedItem?.code}
                                    placeholder="Código único (mayúsculas)"
                                />
                            </div>
                            <div>
                                <label className={labelClass}>Nombre</label>
                                <input
                                    type="text"
                                    className={inputClass}
                                    defaultValue={selectedItem?.name}
                                    placeholder="Nombre descriptivo"
                                />
                            </div>
                        </div>
                        <div className="flex justify-end gap-3 mt-6">
                            <button onClick={() => setShowModal(false)} className={`px-4 py-2 rounded-lg ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}>Cancelar</button>
                            <button onClick={() => setShowModal(false)} className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">Guardar</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default CatalogosPage;
