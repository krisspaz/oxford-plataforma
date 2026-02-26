import { toast } from 'sonner';
import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useTheme } from '../contexts/ThemeContext';
import { packageService, catalogService } from '../services';

const PaquetesPage = () => {
    const { darkMode } = useTheme();
    const queryClient = useQueryClient();
    const [expandedPackage, setExpandedPackage] = useState(null);
    const [showModal, setShowModal] = useState(false);

    const [cycleOptions] = useState(() => {
        const currentYear = new Date().getFullYear();
        return [currentYear, currentYear + 1, currentYear + 2];
    });
    const [selectedCycle, setSelectedCycle] = useState(new Date().getFullYear().toString());

    const [formData, setFormData] = useState({
        id: null, name: '', description: '', cycle: new Date().getFullYear().toString(),
        isActive: true, applicableGrades: [], details: []
    });

    const documentTypes = ['FACTURA_SAT', 'RECIBO_SAT', 'NOTA_DEBITO'];
    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    // === QUERIES ===
    const { data: packages = [], isLoading: loadingPackages } = useQuery({
        queryKey: ['packages'],
        queryFn: async () => {
            const res = await packageService.getAll();
            return res.success || Array.isArray(res.data) ? (res.data || []) : [];
        },
    });

    const { data: grades = [], isLoading: loadingGrades } = useQuery({
        queryKey: ['grades', 'names'],
        queryFn: async () => {
            try {
                const res = await catalogService.getGrades();
                const list = res.success || Array.isArray(res.data) ? (res.data || []) : [];
                return list.map(g => g.name);
            } catch {
                return ['Prekinder', 'Kinder', 'Preparatoria', '1ro Primaria', '2do Primaria', '3ro Primaria'];
            }
        },
    });

    const loading = loadingPackages || loadingGrades;

    // === MUTATION ===
    const saveMutation = useMutation({
        mutationFn: async (pkg) => pkg.id ? packageService.update(pkg.id, pkg) : packageService.create(pkg),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['packages'] });
            toast.success('Paquete guardado');
            setShowModal(false);
        },
        onError: () => toast.error('Error al guardar paquete'),
    });

    const openNewPackage = () => {
        setFormData({
            id: null,
            name: '',
            description: '',
            cycle: selectedCycle,
            isActive: true,
            applicableGrades: [],
            details: [{ id: Date.now(), productName: '', price: 0, documentType: 'FACTURA_SAT', productType: 'Servicio', quantity: 1 }]
        });
        setShowModal(true);
    };

    const openEditPackage = (pkg) => {
        setFormData({
            ...pkg,
            // eslint-disable-next-line react-hooks/purity
            details: pkg.details?.length > 0 ? pkg.details : [{ id: Date.now(), productName: '', price: 0, documentType: 'FACTURA_SAT', productType: 'Servicio', quantity: 1 }]
        });
        setShowModal(true);
    };

    const handleSave = () => {
        if (!formData.name) {
            toast.warning('Por favor ingresa un nombre para el paquete');
            return;
        }
        if (formData.details.some(d => !d.productName || d.price <= 0)) {
            toast.warning('Por favor completa todos los productos con nombre y precio válido');
            return;
        }
        const totalPrice = formData.details.reduce((sum, d) => sum + (d.price * (d.quantity || 1)), 0);
        saveMutation.mutate({ ...formData, totalPrice });
    };

    const addProduct = () => {
        setFormData(prev => ({
            ...prev,
            details: [...prev.details, { id: Date.now(), productName: '', price: 0, documentType: 'FACTURA_SAT', productType: 'Servicio', quantity: 1 }]
        }));
    };

    const removeProduct = (id) => {
        setFormData(prev => ({
            ...prev,
            details: prev.details.filter(d => d.id !== id)
        }));
    };

    const updateProduct = (id, field, value) => {
        setFormData(prev => ({
            ...prev,
            details: prev.details.map(d => d.id === id ? { ...d, [field]: value } : d)
        }));
    };

    const toggleGrade = (grade) => {
        setFormData(prev => ({
            ...prev,
            applicableGrades: prev.applicableGrades.includes(grade)
                ? prev.applicableGrades.filter(g => g !== grade)
                : [...prev.applicableGrades, grade]
        }));
    };

    const calculateTotal = () => formData.details.reduce((sum, d) => sum + ((d.price || 0) * (d.quantity || 1)), 0);

    const filteredPackages = packages.filter(pkg => pkg.cycle === selectedCycle);

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <Loader2 className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando paquetes...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Paquetes Escolares</h1>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Gestión de costos y productos por ciclo</p>
                </div>

                <div className="flex items-center gap-4">
                    <select
                        value={selectedCycle}
                        onChange={(e) => setSelectedCycle(e.target.value)}
                        className={`px-4 py-2 rounded-lg border outline-none focus:ring-2 focus:ring-teal-500 ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300'}`}
                    >
                        {cycleOptions.map(year => (
                            <option key={year} value={year}>Ciclo {year}</option>
                        ))}
                    </select>

                    <button onClick={openNewPackage} className="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg shadow-lg shadow-teal-900/20">
                        <Plus size={18} /> Nuevo Paquete
                    </button>
                </div>
            </div>

            {/* Packages List */}
            <div className="space-y-4">
                {filteredPackages.length === 0 && (
                    <div className="text-center py-12 text-gray-500">
                        No hay paquetes registrados para el ciclo {selectedCycle}.
                    </div>
                )}
                {filteredPackages.map(pkg => (
                    <div key={pkg.id} className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                        {/* Header */}
                        <div
                            onClick={() => setExpandedPackage(expandedPackage === pkg.id ? null : pkg.id)}
                            className={`p-4 flex items-center justify-between cursor-pointer ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}`}
                        >
                            <div className="flex items-center gap-4">
                                <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${pkg.isActive ? 'bg-teal-100 text-teal-600' : 'bg-gray-200 text-gray-500'}`}>
                                    <Package size={24} />
                                </div>
                                <div>
                                    <div className="flex items-center gap-2">
                                        <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{pkg.name}</h3>
                                        {!pkg.isActive && <span className="px-2 py-0.5 bg-gray-200 text-gray-600 rounded text-xs">Inactivo</span>}
                                    </div>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{pkg.description}</p>
                                </div>
                            </div>
                            <div className="flex items-center gap-4">
                                <div className="text-right">
                                    <p className={`text-2xl font-bold ${darkMode ? 'text-green-400' : 'text-green-600'}`}>Q {pkg.totalPrice?.toLocaleString()}</p>
                                    <p className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>{pkg.details?.length || 0} productos</p>
                                </div>
                                {expandedPackage === pkg.id ? <ChevronDown size={20} /> : <ChevronRight size={20} />}
                            </div>
                        </div>

                        {/* Expanded Details */}
                        {expandedPackage === pkg.id && (
                            <div className={`border-t ${darkMode ? 'border-gray-700' : 'bg-white border-gray-200 text-gray-900'}`}>
                                <div className={`p-4 ${darkMode ? 'bg-gray-700/50' : 'bg-gray-50'}`}>
                                    <div className="flex justify-between items-center mb-3">
                                        <h4 className={`font-medium ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Productos incluidos</h4>
                                        <button
                                            onClick={() => openEditPackage(pkg)}
                                            className="flex items-center gap-1 text-sm text-teal-500 hover:text-teal-600"
                                        >
                                            <Edit size={14} /> Editar
                                        </button>
                                    </div>
                                    <table className="w-full">
                                        <thead>
                                            <tr className={darkMode ? 'text-gray-400' : 'text-gray-500'}>
                                                <th className="text-left text-xs font-medium py-2">Producto</th>
                                                <th className="text-center text-xs font-medium py-2">Cantidad</th>
                                                <th className="text-left text-xs font-medium py-2">Documento</th>
                                                <th className="text-right text-xs font-medium py-2">Precio Unit.</th>
                                                <th className="text-right text-xs font-medium py-2">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody className={`divide-y ${darkMode ? 'divide-gray-600' : 'divide-gray-200'}`}>
                                            {pkg.details?.map(detail => (
                                                <tr key={detail.id}>
                                                    <td className={`py-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>{detail.productName}</td>
                                                    <td className={`py-2 text-center ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{detail.quantity || 1}</td>
                                                    <td className="py-2">
                                                        <span className={`px-2 py-0.5 rounded text-xs ${detail.documentType === 'FACTURA_SAT' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'}`}>
                                                            {detail.documentType?.replace('_', ' ')}
                                                        </span>
                                                    </td>
                                                    <td className={`py-2 text-right ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Q {detail.price?.toLocaleString()}</td>
                                                    <td className={`py-2 text-right font-medium ${darkMode ? 'text-green-400' : 'text-green-600'}`}>
                                                        Q {((detail.price || 0) * (detail.quantity || 1)).toLocaleString()}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                    <div className="mt-4 flex flex-wrap gap-2">
                                        <span className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Grados aplicables:</span>
                                        {pkg.applicableGrades?.map((grade, i) => (
                                            <span key={i} className={`px-2 py-0.5 rounded text-xs ${darkMode ? 'bg-gray-600 text-gray-300' : 'bg-gray-200 text-gray-700'}`}>{grade}</span>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                ))}
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col`}>
                        <div className={`p-4 border-b ${darkMode ? 'border-gray-700' : 'bg-white border-gray-200 text-gray-900'} flex items-center justify-between`}>
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {formData.id ? 'Editar Paquete' : 'Nuevo Paquete'}
                            </h2>
                            <button onClick={() => setShowModal(false)} className={`p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700`}>
                                <X size={20} />
                            </button>
                        </div>

                        <div className="flex-1 overflow-y-auto p-4 space-y-6">
                            {/* Basic Info */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className={labelClass}>Nombre del Paquete *</label>
                                    <input
                                        type="text"
                                        className={inputClass}
                                        value={formData.name}
                                        onChange={e => setFormData(prev => ({ ...prev, name: e.target.value }))}
                                        placeholder="Ej: Paquete Normal 2025"
                                    />
                                </div>
                                <div>
                                    <label className={labelClass}>Ciclo Escolar</label>
                                    <select
                                        className={inputClass}
                                        value={formData.cycle}
                                        onChange={e => setFormData(prev => ({ ...prev, cycle: e.target.value }))}
                                    >
                                        {cycleOptions.map(year => (
                                            <option key={year} value={year}>{year}</option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label className={labelClass}>Descripción</label>
                                <textarea
                                    rows={2}
                                    className={inputClass}
                                    value={formData.description}
                                    onChange={e => setFormData(prev => ({ ...prev, description: e.target.value }))}
                                    placeholder="Descripción del paquete..."
                                />
                            </div>

                            {/* Grades */}
                            <div>
                                <label className={labelClass}>Grados Aplicables</label>
                                <div className="flex flex-wrap gap-2 mt-1">
                                    {grades.map(grade => (
                                        <button
                                            key={grade}
                                            type="button"
                                            onClick={() => toggleGrade(grade)}
                                            className={`px-3 py-1 rounded-full text-sm transition-colors ${formData.applicableGrades.includes(grade)
                                                ? 'bg-teal-500 text-white'
                                                : darkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                                }`}
                                        >
                                            {grade}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Products */}
                            <div>
                                <div className="flex items-center justify-between mb-2">
                                    <label className={labelClass}>Productos / Servicios *</label>
                                    <button
                                        type="button"
                                        onClick={addProduct}
                                        className="text-sm text-teal-500 hover:text-teal-600 flex items-center gap-1"
                                    >
                                        <Plus size={14} /> Agregar producto
                                    </button>
                                </div>
                                <div className="space-y-3">
                                    {formData.details.map((detail, idx) => (
                                        <div key={detail.id} className={`p-3 rounded-lg border ${darkMode ? 'border-gray-700 bg-gray-700/50' : 'border-gray-200 bg-gray-50'}`}>
                                            <div className="flex items-center gap-2 mb-2">
                                                <span className={`text-sm font-medium ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Producto {idx + 1}</span>
                                                {formData.details.length > 1 && (
                                                    <button
                                                        type="button"
                                                        onClick={() => removeProduct(detail.id)}
                                                        className="ml-auto text-red-500 hover:text-red-600"
                                                    >
                                                        <Trash2 size={14} />
                                                    </button>
                                                )}
                                            </div>
                                            <div className="grid grid-cols-2 md:grid-cols-5 gap-2">
                                                <div className="col-span-2">
                                                    <input
                                                        type="text"
                                                        placeholder="Nombre del producto"
                                                        className={inputClass}
                                                        value={detail.productName}
                                                        onChange={e => updateProduct(detail.id, 'productName', e.target.value)}
                                                    />
                                                </div>
                                                <div>
                                                    <div className="relative">
                                                        <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">Q</span>
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            step="0.01"
                                                            placeholder="Precio"
                                                            className={`${inputClass} pl-8`}
                                                            value={detail.price || ''}
                                                            onChange={e => updateProduct(detail.id, 'price', parseFloat(e.target.value) || 0)}
                                                        />
                                                    </div>
                                                </div>
                                                <div>
                                                    <input
                                                        type="number"
                                                        min="1"
                                                        placeholder="Cant."
                                                        className={inputClass}
                                                        value={detail.quantity || 1}
                                                        onChange={e => updateProduct(detail.id, 'quantity', parseInt(e.target.value) || 1)}
                                                    />
                                                </div>
                                                <div>
                                                    <select
                                                        className={inputClass}
                                                        value={detail.documentType}
                                                        onChange={e => updateProduct(detail.id, 'documentType', e.target.value)}
                                                    >
                                                        {documentTypes.map(dt => (
                                                            <option key={dt} value={dt}>{dt.replace('_', ' ')}</option>
                                                        ))}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Total */}
                            <div className={`p-4 rounded-lg ${darkMode ? 'bg-teal-900/30' : 'bg-teal-50'} flex items-center justify-between`}>
                                <span className={`font-medium ${darkMode ? 'text-teal-300' : 'text-teal-700'}`}>Total del Paquete</span>
                                <span className={`text-2xl font-bold ${darkMode ? 'text-teal-400' : 'text-teal-600'}`}>Q {calculateTotal().toLocaleString()}</span>
                            </div>
                        </div>

                        <div className={`p-4 border-t ${darkMode ? 'border-gray-700' : 'bg-white border-gray-200 text-gray-900'} flex justify-end gap-3`}>
                            <button
                                onClick={() => setShowModal(false)}
                                className={`px-4 py-2 rounded-lg ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}
                            >
                                Cancelar
                            </button>
                            <button
                                onClick={handleSave}
                                className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg flex items-center gap-2"
                            >
                                <Save size={16} /> Guardar Paquete
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default PaquetesPage;
