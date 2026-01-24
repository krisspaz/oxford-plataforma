import React, { useState, useEffect } from 'react';
import { financialEnterpriseService } from '../services/financialEnterpriseService';
import { useTheme } from '../../../contexts/ThemeContext';
import { CreditCard, Plus, Calendar, User, DollarSign } from 'lucide-react';

const PlansManager = () => {
    const { darkMode } = useTheme();
    const [plans, setPlans] = useState([]);
    const [insolvents, setInsolvents] = useState([]);
    const [activeTab, setActiveTab] = useState('insolvents'); // insolvents | plans

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        const insolventsData = await financialEnterpriseService.getInsolventStudents();
        setInsolvents(insolventsData);
        // Mock loading plans for a "demo student" since we don't have context
        const plansData = await financialEnterpriseService.getPlans(1);
        setPlans(plansData);
    };

    return (
        <div className={`p-6 rounded-2xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h3 className={`text-xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                        <CreditCard className="text-blue-500" />
                        Gestión de Planes y Cobros
                    </h3>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        Control de cuotas, morosidad y convenios de pago.
                    </p>
                </div>
                <div className="flex gap-2">
                    <button
                        onClick={() => setActiveTab('insolvents')}
                        className={`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${activeTab === 'insolvents' ? 'bg-red-100 text-red-700' : 'text-gray-500 hover:bg-gray-100'}`}
                    >
                        Morosos Detectados
                    </button>
                    <button
                        onClick={() => setActiveTab('plans')}
                        className={`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${activeTab === 'plans' ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:bg-gray-100'}`}
                    >
                        Planes Activos
                    </button>
                </div>
            </div>

            {activeTab === 'insolvents' && (
                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead className={`border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                            <tr>
                                <th className="text-left py-3 px-4 text-sm font-semibold text-gray-500">Estudiante</th>
                                <th className="text-left py-3 px-4 text-sm font-semibold text-gray-500">Concepto</th>
                                <th className="text-left py-3 px-4 text-sm font-semibold text-gray-500">Vencimiento</th>
                                <th className="text-right py-3 px-4 text-sm font-semibold text-gray-500">Monto</th>
                                <th className="py-3 px-4"></th>
                            </tr>
                        </thead>
                        <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                            {insolvents.map((item) => (
                                <tr key={item.id} className="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td className={`py-3 px-4 font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{item.studentName}</td>
                                    <td className="py-3 px-4 text-gray-500">{item.description}</td>
                                    <td className="py-3 px-4 text-red-500 font-medium">{item.dueDate}</td>
                                    <td className="py-3 px-4 text-right font-mono">Q{item.amount.toFixed(2)}</td>
                                    <td className="py-3 px-4 text-right">
                                        <button className="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Cobrar</button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            {activeTab === 'plans' && (
                <div className="grid gap-4">
                    {plans.map(plan => (
                        <div key={plan.id} className={`p-4 rounded-xl border ${darkMode ? 'border-gray-700 bg-gray-750' : 'border-gray-200 bg-gray-50'}`}>
                            <div className="flex justify-between items-start mb-2">
                                <span className={`text-xs px-2 py-1 rounded-full font-bold ${plan.status === 'ACTIVE' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'}`}>
                                    {plan.status}
                                </span>
                                <span className="text-xs text-gray-500">Creado: {plan.createdAt}</span>
                            </div>
                            <div className="flex justify-between items-end">
                                <div>
                                    <p className="text-sm text-gray-500">Progreso de Pago</p>
                                    <div className="flex items-baseline gap-1">
                                        <span className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>Q{plan.paidAmount}</span>
                                        <span className="text-sm text-gray-400">/ Q{plan.totalAmount}</span>
                                    </div>
                                </div>
                                <div className="text-right">
                                    <p className="text-sm text-gray-500">Pendiente</p>
                                    <p className="text-xl font-bold text-red-500">Q{plan.pendingAmount}</p>
                                </div>
                            </div>
                            <div className="mt-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div
                                    className="h-full bg-blue-500 rounded-full"
                                    style={{ width: `${(plan.paidAmount / plan.totalAmount) * 100}%` }}
                                ></div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
};

export default PlansManager;
