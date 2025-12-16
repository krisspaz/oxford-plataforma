import { useState, useEffect } from 'react';
import axios from 'axios';
import { DollarSign, FileText, Plus } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';

const Financial = () => {
    const { darkMode } = useTheme();
    const [payments, setPayments] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [newPayment, setNewPayment] = useState({ student: '', amount: '', concept: '', date: new Date().toISOString().split('T')[0] });
    const [students, setStudents] = useState([]);

    const token = localStorage.getItem('token');

    const fetchPayments = async () => {
        try {
            const response = await axios.get('http://localhost:8000/api/payments', {
                headers: { Authorization: `Bearer ${token}`, Accept: 'application/ld+json' }
            });
            setPayments(response.data['hydra:member']);
        } catch (error) {
            console.error('Error fetching payments', error);
        }
        setLoading(false);
    };

    const fetchStudents = async () => {
        try {
            const response = await axios.get('http://localhost:8000/api/students', {
                headers: { Authorization: `Bearer ${token}` }
            });
            setStudents(response.data['hydra:member']);
        } catch (error) {
            console.error('Error fetching students', error);
        }
    };

    useEffect(() => {
        fetchPayments();
        fetchStudents();
    }, []);

    const handleCreatePayment = async (e) => {
        e.preventDefault();
        try {
            await axios.post('http://localhost:8000/api/payments', {
                ...newPayment,
                student: `/api/students/${newPayment.student}`,
                amount: parseFloat(newPayment.amount),
                status: 'PAID',
                paymentDate: newPayment.date
            }, {
                headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/ld+json' }
            });
            setShowModal(false);
            fetchPayments();
        } catch (error) {
            alert('Error registrando pago');
            console.error(error);
        }
    };

    const downloadReceipt = async (paymentId) => {
        window.open(`http://localhost:8000/api/payments/${paymentId}/receipt`, '_blank');
    };

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Módulo Financiero</h1>
                <button
                    onClick={() => setShowModal(true)}
                    className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
                >
                    <Plus size={20} />
                    Registrar Pago
                </button>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className={`lg:col-span-2 ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white'} rounded-xl shadow-sm overflow-hidden border`}>
                    <div className={`p-4 border-b ${darkMode ? 'border-gray-700' : 'border-gray-100'} flex justify-between items-center`}>
                        <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-700'}`}>Historial de Pagos</h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead className={`${darkMode ? 'bg-gray-700' : 'bg-gray-50'}`}>
                                <tr>
                                    <th className={`px-6 py-3 text-xs font-medium ${darkMode ? 'text-gray-300' : 'text-gray-500'} uppercase`}>ID</th>
                                    <th className={`px-6 py-3 text-xs font-medium ${darkMode ? 'text-gray-300' : 'text-gray-500'} uppercase`}>Fecha</th>
                                    <th className={`px-6 py-3 text-xs font-medium ${darkMode ? 'text-gray-300' : 'text-gray-500'} uppercase`}>Alumno</th>
                                    <th className={`px-6 py-3 text-xs font-medium ${darkMode ? 'text-gray-300' : 'text-gray-500'} uppercase`}>Concepto</th>
                                    <th className={`px-6 py-3 text-xs font-medium ${darkMode ? 'text-gray-300' : 'text-gray-500'} uppercase`}>Monto</th>
                                    <th className={`px-6 py-3 text-xs font-medium ${darkMode ? 'text-gray-300' : 'text-gray-500'} uppercase`}>Recibo</th>
                                </tr>
                            </thead>
                            <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                                {loading ? <tr><td colSpan="6" className={`text-center p-4 ${darkMode ? 'text-gray-400' : ''}`}>Cargando...</td></tr> :
                                    payments.length === 0 ? <tr><td colSpan="6" className={`text-center p-4 ${darkMode ? 'text-gray-400' : ''}`}>No hay pagos registrados.</td></tr> :
                                        payments.map(payment => (
                                            <tr key={payment.id} className={`${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}`}>
                                                <td className={`px-6 py-4 text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>#{payment.id}</td>
                                                <td className={`px-6 py-4 text-sm ${darkMode ? 'text-white' : 'text-gray-900'}`}>{payment.paymentDate ? payment.paymentDate.split('T')[0] : ''}</td>
                                                <td className={`px-6 py-4 text-sm ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                                                    {payment.student ? payment.student.firstName + ' ' + payment.student.lastName : 'N/A'}
                                                </td>
                                                <td className={`px-6 py-4 text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{payment.concept}</td>
                                                <td className="px-6 py-4 text-sm font-bold text-green-500">Q {payment.amount}</td>
                                                <td className="px-6 py-4 text-sm">
                                                    <button
                                                        onClick={() => downloadReceipt(payment.id)}
                                                        className="text-blue-500 hover:text-blue-400 flex items-center gap-1"
                                                    >
                                                        <FileText size={16} /> PDF
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div className="space-y-6">
                    <div className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white'} p-6 rounded-xl shadow-sm border`}>
                        <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-700'} mb-4`}>Resumen del Mes</h3>
                        <div className="flex items-center gap-4 mb-6">
                            <div className={`p-3 ${darkMode ? 'bg-green-900/50' : 'bg-green-100'} text-green-500 rounded-full`}>
                                <DollarSign size={24} />
                            </div>
                            <div>
                                <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Ingresos Totales</p>
                                <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                                    Q {payments.reduce((acc, curr) => acc + parseFloat(curr.amount), 0).toFixed(2)}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-md p-6`}>
                        <h2 className={`text-xl font-bold mb-4 ${darkMode ? 'text-white' : ''}`}>Registrar Nuevo Pago</h2>
                        <form onSubmit={handleCreatePayment} className="space-y-4">
                            <div>
                                <label className={`block text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Estudiante</label>
                                <select
                                    className={`w-full border rounded-lg p-2 ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`}
                                    value={newPayment.student}
                                    onChange={e => setNewPayment({ ...newPayment, student: e.target.value })}
                                    required
                                >
                                    <option value="">Seleccione un estudiante</option>
                                    {students.map(s => (
                                        <option key={s.id} value={s.id}>{s.firstName} {s.lastName} ({s.carnet})</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className={`block text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Concepto</label>
                                <input
                                    type="text"
                                    className={`w-full border rounded-lg p-2 ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`}
                                    value={newPayment.concept}
                                    onChange={e => setNewPayment({ ...newPayment, concept: e.target.value })}
                                    placeholder="Ej: Mensualidad Marzo"
                                    required
                                />
                            </div>
                            <div>
                                <label className={`block text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Monto (Q)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    className={`w-full border rounded-lg p-2 ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`}
                                    value={newPayment.amount}
                                    onChange={e => setNewPayment({ ...newPayment, amount: e.target.value })}
                                    required
                                />
                            </div>
                            <div>
                                <label className={`block text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Fecha</label>
                                <input
                                    type="date"
                                    className={`w-full border rounded-lg p-2 ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`}
                                    value={newPayment.date}
                                    onChange={e => setNewPayment({ ...newPayment, date: e.target.value })}
                                    required
                                />
                            </div>
                            <div className="flex justify-end gap-2 pt-4">
                                <button
                                    type="button"
                                    onClick={() => setShowModal(false)}
                                    className={`px-4 py-2 ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'} rounded-lg`}
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    className="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg"
                                >
                                    Guardar Pago
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
};

export default Financial;
