import { toast } from 'sonner';
import React, { useState, useEffect, useMemo } from 'react';
import { CreditCard, DollarSign, Receipt, Check, User, Search, Loader2, X } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { studentService, paymentService } from '../services';

const RegistroPagosPage = () => {
    const { darkMode } = useTheme();
    const [selectedStudent, setSelectedStudent] = useState(null);
    const [searchTerm, setSearchTerm] = useState('');
    const [paymentMethod, setPaymentMethod] = useState('efectivo');
    const [selectedQuotas, setSelectedQuotas] = useState([]);
    const [showReceiptModal, setShowReceiptModal] = useState(false);
    const [loading, setLoading] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [students, setStudents] = useState([]);
    const [quotas, setQuotas] = useState([]);
    const [lastReceipt, setLastReceipt] = useState(null);
    const [nit, setNit] = useState('CF');

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    // Search students
    useEffect(() => {
        if (searchTerm.length >= 2) {
            const searchStudents = async () => {
                try {
                    const response = await studentService.search(searchTerm);
                    if (response.success) {
                        setStudents(response.data);
                    } else {
                        setStudents([]);
                    }
                } catch (error) {
                    console.error('Error searching students:', error);
                    setStudents([]);
                }
            };
            searchStudents();
        } else {
            setStudents([]);
        }
    }, [searchTerm]);

    // Load quotas when student selected
    useEffect(() => {
        if (selectedStudent) {
            loadQuotas();
            setNit(selectedStudent.nit || 'CF');
        }
    }, [selectedStudent]);

    const loadQuotas = async () => {
        setLoading(true);
        try {
            const response = await paymentService.getPendingQuotas(selectedStudent.id);
            if (response.success) {
                setQuotas(response.data);
            } else {
                setQuotas([]);
            }
        } catch (error) {
            console.error('Error loading quotas:', error);
            toast.error('Error al cargar cuotas: ' + error.message);
            setQuotas([]);
        } finally {
            setLoading(false);
        }
    };

    const totalSelected = selectedQuotas.reduce((sum, id) => {
        const quota = quotas.find(q => q.id === id);
        return sum + (quota?.pending || quota?.amount || 0);
    }, 0);

    const toggleQuota = (id) => {
        setSelectedQuotas(prev => prev.includes(id) ? prev.filter(q => q !== id) : [...prev, id]);
    };

    const handleProcessPayment = async () => {
        setProcessing(true);
        try {
            const response = await paymentService.applyToQuotas({
                studentId: selectedStudent.id,
                quotaIds: selectedQuotas,
                method: paymentMethod,
                nit: nit,
                total: totalSelected
            });

            if (response.success) {
                setLastReceipt(response.data);
            } else {
                toast.error('Error al procesar pago: ' + (response.message || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error processing payment:', error);
            toast.error('Error al procesar pago: ' + error.message);
        } finally {
            setProcessing(false);
            if (lastReceipt) setShowReceiptModal(true);
        }
    };

    const handleCloseReceipt = () => {
        setShowReceiptModal(false);
        setSelectedQuotas([]);
        loadQuotas();
    };

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Registro de Pagos</h1>
            </div>

            <div className="grid grid-cols-3 gap-6">
                {/* Student Search */}
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm p-4`}>
                    <h2 className={`font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                        <User size={18} className="inline mr-2" />Seleccionar Estudiante
                    </h2>
                    <div className="relative mb-4">
                        <Search className={`absolute left-3 top-2.5 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`} size={18} />
                        <input
                            type="text"
                            placeholder="Buscar por nombre o carnet..."
                            value={searchTerm}
                            onChange={e => setSearchTerm(e.target.value)}
                            className={`${inputClass} w-full pl-10`}
                        />
                    </div>
                    <div className="space-y-2 max-h-48 overflow-y-auto">
                        {students.map(s => (
                            <div
                                key={s.id}
                                onClick={() => { setSelectedStudent(s); setSelectedQuotas([]); }}
                                className={`p-3 rounded-lg cursor-pointer transition-all ${selectedStudent?.id === s.id
                                    ? 'bg-teal-600 text-white'
                                    : darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100'
                                    }`}
                            >
                                <p className="font-medium">{s.fullName}</p>
                                <p className={`text-sm ${selectedStudent?.id === s.id ? 'text-teal-100' : darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                    {s.carnet} - {s.grade}
                                </p>
                            </div>
                        ))}
                        {searchTerm.length >= 2 && students.length === 0 && (
                            <p className={`text-center py-4 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>No se encontraron estudiantes</p>
                        )}
                    </div>
                </div>

                {/* Quotas Selection */}
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm p-4`}>
                    <h2 className={`font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                        <Receipt size={18} className="inline mr-2" />Cuotas Pendientes
                    </h2>
                    {loading ? (
                        <div className="py-8 text-center">
                            <Loader2 className="animate-spin mx-auto text-teal-500" size={24} />
                        </div>
                    ) : selectedStudent ? (
                        <div className="space-y-2">
                            {quotas.map(quota => (
                                <div
                                    key={quota.id}
                                    onClick={() => toggleQuota(quota.id)}
                                    className={`p-3 rounded-lg cursor-pointer border-2 transition-all ${selectedQuotas.includes(quota.id)
                                        ? 'border-teal-500 bg-teal-50 dark:bg-teal-900/30'
                                        : darkMode ? 'border-gray-700 hover:border-gray-600' : 'border-gray-200 hover:border-gray-300'
                                        }`}
                                >
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{quota.concept}</p>
                                            <span className={`text-xs px-2 py-0.5 rounded ${quota.type === 'FACTURA_SAT' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'
                                                }`}>{quota.type?.replace('_', ' ')}</span>
                                        </div>
                                        <div className="text-right">
                                            <p className={`font-bold ${darkMode ? 'text-green-400' : 'text-green-600'}`}>
                                                Q {(quota.pending || quota.amount).toLocaleString()}
                                            </p>
                                            {selectedQuotas.includes(quota.id) && <Check size={16} className="text-teal-500 ml-auto" />}
                                        </div>
                                    </div>
                                </div>
                            ))}
                            {quotas.length === 0 && (
                                <div className="py-8 text-center">
                                    <Check size={32} className="mx-auto text-green-500 mb-2" />
                                    <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>Sin cuotas pendientes</p>
                                </div>
                            )}
                        </div>
                    ) : (
                        <p className={`text-center py-8 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                            Seleccione un estudiante
                        </p>
                    )}
                </div>

                {/* Payment Form */}
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm p-4`}>
                    <h2 className={`font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                        <CreditCard size={18} className="inline mr-2" />Procesar Pago
                    </h2>

                    {selectedQuotas.length > 0 ? (
                        <div className="space-y-4">
                            <div className={`p-4 rounded-lg ${darkMode ? 'bg-teal-900/30' : 'bg-teal-50'}`}>
                                <p className={`text-sm ${darkMode ? 'text-teal-300' : 'text-teal-700'}`}>Total a pagar:</p>
                                <p className={`text-3xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                    Q {totalSelected.toLocaleString()}
                                </p>
                            </div>

                            <div>
                                <label className={labelClass}>Método de Pago</label>
                                <select value={paymentMethod} onChange={e => setPaymentMethod(e.target.value)} className={`${inputClass} w-full`}>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                                    <option value="deposito">Depósito Bancario</option>
                                    <option value="transferencia">Transferencia</option>
                                </select>
                            </div>

                            {paymentMethod === 'deposito' && (
                                <div>
                                    <label className={labelClass}>No. Boleta</label>
                                    <input type="text" className={`${inputClass} w-full`} placeholder="Número de boleta" />
                                </div>
                            )}

                            {paymentMethod === 'tarjeta' && (
                                <div>
                                    <label className={labelClass}>Últimos 4 dígitos</label>
                                    <input type="text" maxLength={4} className={`${inputClass} w-full`} placeholder="XXXX" />
                                </div>
                            )}

                            <div>
                                <label className={labelClass}>Datos de Facturación</label>
                                <div className="flex gap-2 mb-2">
                                    <select
                                        className={`${inputClass} w-1/3`}
                                        value={nit === 'CF' ? 'CF' : (nit.length > 9 ? 'CUI' : 'NIT')}
                                        onChange={(e) => {
                                            if (e.target.value === 'CF') setNit('CF');
                                            else setNit('');
                                        }}
                                    >
                                        <option value="NIT">NIT</option>
                                        <option value="CUI">CUI</option>
                                        <option value="CF">CF</option>
                                    </select>
                                    <input
                                        type="text"
                                        value={nit}
                                        onChange={e => setNit(e.target.value)}
                                        className={`${inputClass} w-2/3`}
                                        placeholder="Ingrese NIT o CUI"
                                        disabled={nit === 'CF'}
                                    />
                                </div>
                            </div>

                            <button
                                onClick={handleProcessPayment}
                                disabled={processing}
                                className="w-full py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-bold flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                {processing ? (
                                    <><Loader2 size={20} className="animate-spin" /> Procesando...</>
                                ) : (
                                    <><DollarSign size={20} /> Procesar Pago</>
                                )}
                            </button>
                        </div>
                    ) : (
                        <p className={`text-center py-8 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                            Seleccione cuotas a pagar
                        </p>
                    )}
                </div>
            </div>

            {/* Receipt Modal */}
            {showReceiptModal && lastReceipt && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-md p-6 text-center`}>
                        <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <Check size={32} className="text-green-600" />
                        </div>
                        <h2 className={`text-xl font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>¡Pago Exitoso!</h2>
                        <p className={`mb-4 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                            El pago de Q {totalSelected.toLocaleString()} se ha procesado correctamente.
                        </p>
                        <div className={`p-4 rounded-lg mb-4 ${darkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>
                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Comprobante generado:</p>
                            <p className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {lastReceipt.series || 'RECI'} {lastReceipt.number}
                            </p>
                        </div>
                        <div className="flex gap-3">
                            <button onClick={handleCloseReceipt} className={`flex-1 px-4 py-2 rounded-lg ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200 text-gray-700'}`}>
                                Cerrar
                            </button>
                            <button className="flex-1 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
                                Imprimir
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default RegistroPagosPage;
