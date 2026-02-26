import { useState, useEffect } from 'react';

/**
 * FASE 19: Economic Impact Dashboard
 * Visualiza costos ocultos, ahorros y ROI del sistema
 */
const EconomicDashboard = () => {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [period, setPeriod] = useState('monthly');

    useEffect(() => {
        // Simulate fetch from backend
        setTimeout(() => {
            setData({
                hiddenCosts: {
                    monthly: 12500,
                    yearly: 150000,
                    breakdown: [
                        { name: 'Horas extra por replanificación', amount: 3750, icon: '⏰' },
                        { name: 'Sustitutos por burnout', amount: 3200, icon: '🔄' },
                        { name: 'Reuniones de coordinación', amount: 2400, icon: '📋' },
                        { name: 'Errores manuales', amount: 1500, icon: '❌' },
                        { name: 'Rotación de personal', amount: 1650, icon: '👋' },
                    ]
                },
                savings: {
                    monthly: 9800,
                    yearly: 117600,
                    breakdown: [
                        { name: 'Ahorro en planificación', amount: 4000, icon: '📅' },
                        { name: 'Reducción de conflictos', amount: 2800, icon: '✅' },
                        { name: 'Prevención de burnout', amount: 1920, icon: '💚' },
                        { name: 'Errores evitados', amount: 1080, icon: '🛡️' },
                    ]
                },
                roi: {
                    percentage: 156,
                    systemCost: 3500,
                    netSavings: 6300,
                },
                timeSaved: {
                    weekly: 12,
                    monthly: 52,
                    yearly: 624,
                }
            });
            setLoading(false);
        }, 1000);
    }, []);

    if (loading) {
        return (
            <div className="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                <div className="text-center">
                    <div className="text-4xl animate-spin mb-4">💰</div>
                    <p className="text-gray-600 dark:text-gray-400">Calculando impacto económico...</p>
                </div>
            </div>
        );
    }

    const formatCurrency = (amount) => `Q${amount.toLocaleString('es-GT')}`;

    return (
        <div className="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
            <div className="max-w-7xl mx-auto">
                {/* Header */}
                <div className="flex items-center justify-between mb-8">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            💰 Impacto Económico
                        </h1>
                        <p className="text-gray-600 dark:text-gray-400 mt-1">
                            Visualización de costos ocultos y ahorros generados por el sistema
                        </p>
                    </div>

                    {/* Period selector */}
                    <div className="flex bg-gray-200 dark:bg-gray-700 rounded-xl p-1">
                        <button
                            onClick={() => setPeriod('monthly')}
                            className={`px-4 py-2 rounded-lg transition-colors ${period === 'monthly'
                                    ? 'bg-white dark:bg-gray-600 shadow-sm'
                                    : 'text-gray-600 dark:text-gray-400'
                                }`}
                        >
                            Mensual
                        </button>
                        <button
                            onClick={() => setPeriod('yearly')}
                            className={`px-4 py-2 rounded-lg transition-colors ${period === 'yearly'
                                    ? 'bg-white dark:bg-gray-600 shadow-sm'
                                    : 'text-gray-600 dark:text-gray-400'
                                }`}
                        >
                            Anual
                        </button>
                    </div>
                </div>

                {/* Main Stats */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    {/* Hidden Costs */}
                    <div className="bg-gradient-to-br from-red-500 to-orange-500 rounded-2xl p-6 text-white">
                        <div className="text-3xl mb-2">📉</div>
                        <h3 className="text-sm opacity-80 mb-1">Costos Ocultos Identificados</h3>
                        <p className="text-3xl font-bold">
                            {formatCurrency(period === 'monthly' ? data.hiddenCosts.monthly : data.hiddenCosts.yearly)}
                        </p>
                        <p className="text-sm opacity-70 mt-2">
                            {period === 'monthly' ? 'Este mes' : 'Este año'}
                        </p>
                    </div>

                    {/* Savings */}
                    <div className="bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl p-6 text-white">
                        <div className="text-3xl mb-2">💚</div>
                        <h3 className="text-sm opacity-80 mb-1">Ahorros Generados</h3>
                        <p className="text-3xl font-bold">
                            {formatCurrency(period === 'monthly' ? data.savings.monthly : data.savings.yearly)}
                        </p>
                        <p className="text-sm opacity-70 mt-2">
                            Por el sistema de IA
                        </p>
                    </div>

                    {/* ROI */}
                    <div className="bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl p-6 text-white">
                        <div className="text-3xl mb-2">📈</div>
                        <h3 className="text-sm opacity-80 mb-1">ROI del Sistema</h3>
                        <p className="text-3xl font-bold">{data.roi.percentage}%</p>
                        <p className="text-sm opacity-70 mt-2">
                            Retorno sobre inversión
                        </p>
                    </div>

                    {/* Time Saved */}
                    <div className="bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl p-6 text-white">
                        <div className="text-3xl mb-2">⏰</div>
                        <h3 className="text-sm opacity-80 mb-1">Tiempo Ahorrado</h3>
                        <p className="text-3xl font-bold">
                            {period === 'monthly' ? data.timeSaved.monthly : data.timeSaved.yearly}h
                        </p>
                        <p className="text-sm opacity-70 mt-2">
                            En tareas administrativas
                        </p>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Hidden Costs Breakdown */}
                    <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            📊 Desglose de Costos Ocultos
                        </h2>
                        <div className="space-y-4">
                            {data.hiddenCosts.breakdown.map((item, idx) => (
                                <div key={idx} className="flex items-center gap-4">
                                    <div className="text-2xl">{item.icon}</div>
                                    <div className="flex-1">
                                        <p className="text-gray-900 dark:text-white font-medium">{item.name}</p>
                                        <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                            <div
                                                className="bg-red-500 h-2 rounded-full"
                                                style={{ width: `${(item.amount / data.hiddenCosts.monthly) * 100}%` }}
                                            />
                                        </div>
                                    </div>
                                    <div className="text-red-600 font-bold">
                                        {formatCurrency(item.amount)}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Savings Breakdown */}
                    <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            💰 Desglose de Ahorros
                        </h2>
                        <div className="space-y-4">
                            {data.savings.breakdown.map((item, idx) => (
                                <div key={idx} className="flex items-center gap-4">
                                    <div className="text-2xl">{item.icon}</div>
                                    <div className="flex-1">
                                        <p className="text-gray-900 dark:text-white font-medium">{item.name}</p>
                                        <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                            <div
                                                className="bg-green-500 h-2 rounded-full"
                                                style={{ width: `${(item.amount / data.savings.monthly) * 100}%` }}
                                            />
                                        </div>
                                    </div>
                                    <div className="text-green-600 font-bold">
                                        {formatCurrency(item.amount)}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Executive Summary */}
                <div className="mt-6 bg-gradient-to-r from-gray-800 to-gray-900 rounded-2xl p-8 text-white">
                    <h2 className="text-xl font-bold mb-4">📋 Resumen Ejecutivo</h2>
                    <div className="grid md:grid-cols-3 gap-6">
                        <div>
                            <h3 className="text-gray-400 text-sm mb-1">Costo del Sistema</h3>
                            <p className="text-2xl font-bold">{formatCurrency(data.roi.systemCost)}/mes</p>
                        </div>
                        <div>
                            <h3 className="text-gray-400 text-sm mb-1">Ahorro Neto</h3>
                            <p className="text-2xl font-bold text-green-400">{formatCurrency(data.roi.netSavings)}/mes</p>
                        </div>
                        <div>
                            <h3 className="text-gray-400 text-sm mb-1">Proyección Anual</h3>
                            <p className="text-2xl font-bold text-purple-400">{formatCurrency(data.roi.netSavings * 12)}</p>
                        </div>
                    </div>
                    <div className="mt-6 p-4 bg-green-600/20 rounded-xl border border-green-500/30">
                        <p className="text-green-300">
                            ✅ El sistema genera <strong>{formatCurrency(data.roi.netSavings)}</strong> de ahorro neto mensual,
                            con un ROI del <strong>{data.roi.percentage}%</strong>.
                            La inversión se recupera en menos de 1 mes.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default EconomicDashboard;
