import React, { useState, useEffect } from 'react';
import { Package, Plus, ChevronDown, ChevronRight, Edit, X, DollarSign, RefreshCw } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { packageService } from '../services';

const PaquetesPage = () => {
    const { darkMode } = useTheme();
    const [expandedPackage, setExpandedPackage] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [selectedPackage, setSelectedPackage] = useState(null);
    const [loading, setLoading] = useState(true);
    const [packages, setPackages] = useState([]);
    const [selectedCycle, setSelectedCycle] = useState('2025');

    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    useEffect(() => {
        loadPackages();
    }, []);

    const loadPackages = async () => {
        setLoading(true);
        try {
            const response = await packageService.getAll();
            if (response.success) {
                setPackages(response.data);
            }
        } catch (error) {
            console.error('Error loading packages:', error);
            // Demo data
            setPackages([
                {
                    id: 1,
                    name: 'Paquete Normal 2025',
                    cycle: '2025',
                    totalPrice: 9500,
                    description: 'Paquete estándar para estudiantes regulares 2025',
                    isActive: true,
                    applicableGrades: ['1ro Básico', '2do Básico', '3ro Básico'],
                    details: [
                        { id: 1, productName: 'Inscripción', price: 1000, documentType: 'FACTURA_SAT', productType: 'Servicio' },
                        { id: 2, productName: 'Mensualidad (x10)', price: 750, documentType: 'RECIBO_SAT', productType: 'Servicio', quantity: 10 },
                        { id: 3, productName: 'Paquete de Libros', price: 1500, documentType: 'FACTURA_SAT', productType: 'Bien' },
                    ]
                },
                {
                    id: 2,
                    name: 'Paquete Becado 50% 2025',
                    cycle: '2025',
                    totalPrice: 4750,
                    description: 'Paquete con 50% de descuento para becados',
                    isActive: true,
                    applicableGrades: ['1ro Básico', '2do Básico'],
                    details: [
                        { id: 4, productName: 'Inscripción (50%)', price: 500, documentType: 'FACTURA_SAT', productType: 'Servicio' },
                        { id: 5, productName: 'Mensualidad (x10)', price: 375, documentType: 'RECIBO_SAT', productType: 'Servicio', quantity: 10 },
                        { id: 6, productName: 'Paquete de Libros', price: 1500, documentType: 'FACTURA_SAT', productType: 'Bien' },
                    ]
                },
                {
                    id: 3,
                    name: 'Paquete 2024 (Histórico)',
                    cycle: '2024',
                    totalPrice: 9000,
                    description: 'Paquete del año anterior',
                    isActive: false,
                    applicableGrades: ['General'],
                    details: []
                }
            ]);
        } finally {
            setLoading(false);
        }
    };

    const handleSave = async () => {
        try {
            if (selectedPackage?.id) {
                await packageService.update(selectedPackage.id, selectedPackage);
            } else {
                await packageService.create(selectedPackage);
            }
            loadPackages();
            setShowModal(false);
            setSelectedPackage(null);
        } catch (error) {
            console.error('Error saving package:', error);
            setShowModal(false);
        }
    };

    const filteredPackages = packages.filter(pkg => pkg.cycle === selectedCycle);

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
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
                        <option value="2025">Ciclo 2025</option>
                        <option value="2024">Ciclo 2024</option>
                    </select>

                    <button onClick={() => { setSelectedPackage({}); setShowModal(true); }} className="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg shadow-lg shadow-teal-900/20">
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
                                <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${pkg.isActive ? 'bg-teal-100 text-teal-600' : 'bg-gray-200 text-gray-500'
                                    }`}>
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
                            <div className={`border-t ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                                <div className={`p-4 ${darkMode ? 'bg-gray-700/50' : 'bg-gray-50'}`}>
                                    <div className="flex justify-between items-center mb-3">
                                        <h4 className={`font-medium ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Productos incluidos</h4>
                                        <button
                                            onClick={() => { setSelectedPackage(pkg); setShowModal(true); }}
                                            className="flex items-center gap-1 text-sm text-teal-500 hover:text-teal-600"
                                        >
                                            <Edit size={14} /> Editar
                                        </button>
                                    </div>
                                    <table className="w-full">
                                        <thead>
                                            <tr className={darkMode ? 'text-gray-400' : 'text-gray-500'}>
                                                <th className="text-left text-xs font-medium py-2">Producto</th>
                                                <th className="text-left text-xs font-medium py-2">Tipo</th>
                                                <th className="text-left text-xs font-medium py-2">Documento</th>
                                                <th className="text-right text-xs font-medium py-2">Precio</th>
                                            </tr>
                                        </thead>
                                        <tbody className={`divide-y ${darkMode ? 'divide-gray-600' : 'divide-gray-200'}`}>
                                            {pkg.details?.map(detail => (
                                                <tr key={detail.id}>
                                                    <td className={`py-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                                        {detail.productName}
                                                        {detail.quantity > 1 && <span className={`ml-1 text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>x{detail.quantity}</span>}
                                                    </td>
                                                    <td className={`py-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{detail.productType}</td>
                                                    <td className="py-2">
                                                        <span className={`px-2 py-0.5 rounded text-xs ${detail.documentType === 'FACTURA_SAT' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'
                                                            }`}>{detail.documentType?.replace('_', ' ')}</span>
                                                    </td>
                                                    <td className={`py-2 text-right font-medium ${darkMode ? 'text-green-400' : 'text-green-600'}`}>
                                                        Q {detail.price?.toLocaleString()}
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
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-lg p-6`}>
                        <div className="flex items-center justify-between mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {selectedPackage?.id ? 'Editar Paquete' : 'Nuevo Paquete'}
                            </h2>
                            <button onClick={() => setShowModal(false)}><X size={20} /></button>
                        </div>
                        <div className="space-y-4">
                            <div>
                                <label className={labelClass}>Nombre del Paquete</label>
                                <input type="text" className={inputClass} defaultValue={selectedPackage?.name} placeholder="Ej: Paquete Normal" />
                            </div>
                            <div>
                                <label className={labelClass}>Descripción</label>
                                <textarea rows={2} className={inputClass} defaultValue={selectedPackage?.description} placeholder="Descripción del paquete..." />
                            </div>
                            <div>
                                <label className={labelClass}>Grados Aplicables</label>
                                <select multiple className={`${inputClass} h-24`}>
                                    <option>1ro Básico</option>
                                    <option>2do Básico</option>
                                    <option>3ro Básico</option>
                                </select>
                            </div>
                        </div>
                        <div className="flex justify-end gap-3 mt-6">
                            <button onClick={() => setShowModal(false)} className={`px-4 py-2 rounded-lg ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}>Cancelar</button>
                            <button onClick={handleSave} className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">Guardar</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default PaquetesPage;
