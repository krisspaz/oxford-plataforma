import React, { useState, useEffect } from 'react';
import { DollarSign, CreditCard, FileText, Receipt, TrendingUp, TrendingDown, Users, AlertCircle, CheckCircle, Clock, ArrowUpRight, ArrowDownRight, Filter, Search, Download, Plus, ChevronRight } from 'lucide-react';
import { useTheme } from '../../contexts/ThemeContext';
import { api } from '../../services';

/**
 * FinancialHub - Unified Financial Operations Dashboard
 * ======================================================
 * Consolidates: ComprobantesPage, ComprobantesEmitidosPage, RegistroPagosPage,
 * FinancialDashboard, CobranzaPage, EstadoCuentaPage
 */
const FinancialHub = () => {
    const { darkMode } = useTheme();
    const [activeTab, setActiveTab] = useState('overview');
    const [loading, setLoading] = useState(true);
    const [data, setData] = useState({
        summary: { income: 0, pending: 0, overdue: 0, families: 0 },
        recentPayments: [],
        pendingInvoices: [],
    });

    const tabs = [
        { id: 'overview', label: 'Resumen', icon: TrendingUp },
        { id: 'payments', label: 'Pagos', icon: DollarSign },
        { id: 'invoices', label: 'Facturas', icon: FileText },
        { id: 'receipts', label: 'Recibos', icon: Receipt },
        { id: 'collections', label: 'Cobranza', icon: AlertCircle },
    ];

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        setLoading(false);
        // Mock data - replace with actual API calls
        setData({
            summary: {
                income: 125000,
                pending: 45000,
                overdue: 12500,
                families: 280,
                monthlyGrowth: 12.5,
                collectionRate: 85,
            },
            recentPayments: [
                { id: 1, family: 'García López', amount: 1500, date: '2026-01-07', method: 'Transferencia', status: 'completed' },
                { id: 2, family: 'Martínez Ruiz', amount: 2200, date: '2026-01-06', method: 'Efectivo', status: 'completed' },
                { id: 3, family: 'Hernández Paz', amount: 1800, date: '2026-01-06', method: 'Tarjeta', status: 'completed' },
                { id: 4, family: 'Morales Chen', amount: 3000, date: '2026-01-05', method: 'Transferencia', status: 'pending' },
            ],
            pendingInvoices: [
                { id: 1, family: 'López Mejía', amount: 2500, dueDate: '2026-01-10', daysOverdue: 0 },
                { id: 2, family: 'Ramírez Sol', amount: 1800, dueDate: '2025-12-28', daysOverdue: 10 },
                { id: 3, family: 'Castro Vega', amount: 3200, dueDate: '2025-12-20', daysOverdue: 18 },
            ],
        });
    };

    const formatCurrency = (amount) => `Q ${amount.toLocaleString()}`;

    const cardClass = `${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg`;

    const renderOverview = () => (
        <div className="space-y-6">
            {/* Stats Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {[
                    { label: 'Ingresos del Mes', value: data.summary.income, icon: TrendingUp, color: 'emerald', trend: '+12.5%' },
                    { label: 'Por Cobrar', value: data.summary.pending, icon: Clock, color: 'amber', trend: null },
                    { label: 'Vencido', value: data.summary.overdue, icon: AlertCircle, color: 'red', trend: '-3.2%' },
                    { label: 'Familias Activas', value: data.summary.families, icon: Users, color: 'blue', isCount: true },
                ].map((stat, i) => {
                    const Icon = stat.icon;
                    return (
                        <div key={i} className={`${cardClass} p-5 relative overflow-hidden`}>
                            <div className={`absolute top-0 right-0 w-20 h-20 bg-${stat.color}-500/10 rounded-full -translate-y-1/2 translate-x-1/2`} />
                            <div className="flex items-start justify-between relative">
                                <div>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{stat.label}</p>
                                    <p className={`text-2xl font-bold mt-1 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                                        {stat.isCount ? stat.value : formatCurrency(stat.value)}
                                    </p>
                                    {stat.trend && (
                                        <p className={`text-xs mt-1 flex items-center gap-1 ${stat.trend.startsWith('+') ? 'text-emerald-500' : 'text-red-500'}`}>
                                            {stat.trend.startsWith('+') ? <ArrowUpRight size={12} /> : <ArrowDownRight size={12} />}
                                            {stat.trend} vs mes anterior
                                        </p>
                                    )}
                                </div>
                                <div className={`p-3 rounded-xl bg-${stat.color}-100 dark:bg-${stat.color}-900/30`}>
                                    <Icon className={`text-${stat.color}-600 dark:text-${stat.color}-400`} size={24} />
                                </div>
                            </div>
                        </div>
                    );
                })}
            </div>

            {/* Recent Payments & Pending */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Recent Payments */}
                <div className={cardClass}>
                    <div className="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>Pagos Recientes</h3>
                        <button className="text-indigo-500 text-sm flex items-center gap-1 hover:underline">
                            Ver todos <ChevronRight size={14} />
                        </button>
                    </div>
                    <div className="divide-y divide-gray-100 dark:divide-gray-700">
                        {data.recentPayments.map(payment => (
                            <div key={payment.id} className="p-4 flex items-center justify-between">
                                <div>
                                    <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{payment.family}</p>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{payment.method} • {payment.date}</p>
                                </div>
                                <div className="text-right">
                                    <p className={`font-bold ${payment.status === 'completed' ? 'text-emerald-500' : 'text-amber-500'}`}>
                                        {formatCurrency(payment.amount)}
                                    </p>
                                    <span className={`text-xs px-2 py-0.5 rounded-full ${payment.status === 'completed'
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                            : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                                        }`}>
                                        {payment.status === 'completed' ? 'Completado' : 'Pendiente'}
                                    </span>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Pending Invoices */}
                <div className={cardClass}>
                    <div className="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>Facturas Pendientes</h3>
                        <button className="text-indigo-500 text-sm flex items-center gap-1 hover:underline">
                            Ver todas <ChevronRight size={14} />
                        </button>
                    </div>
                    <div className="divide-y divide-gray-100 dark:divide-gray-700">
                        {data.pendingInvoices.map(invoice => (
                            <div key={invoice.id} className="p-4 flex items-center justify-between">
                                <div>
                                    <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{invoice.family}</p>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Vence: {invoice.dueDate}</p>
                                </div>
                                <div className="text-right">
                                    <p className={`font-bold ${invoice.daysOverdue > 0 ? 'text-red-500' : 'text-gray-700 dark:text-gray-300'}`}>
                                        {formatCurrency(invoice.amount)}
                                    </p>
                                    {invoice.daysOverdue > 0 && (
                                        <span className="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            {invoice.daysOverdue} días vencido
                                        </span>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );

    const renderContent = () => {
        switch (activeTab) {
            case 'overview':
                return renderOverview();
            case 'payments':
            case 'invoices':
            case 'receipts':
            case 'collections':
                return (
                    <div className={`${cardClass} p-12 text-center`}>
                        <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>
                            Sección de {tabs.find(t => t.id === activeTab)?.label} - En desarrollo
                        </p>
                    </div>
                );
            default:
                return null;
        }
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className={`text-3xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Centro Financiero</h1>
                    <p className={`${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Gestiona pagos, facturas y cobranza</p>
                </div>
                <div className="flex gap-2">
                    <button className={`px-4 py-2 rounded-lg ${darkMode ? 'bg-gray-700 hover:bg-gray-600' : 'bg-gray-100 hover:bg-gray-200'} flex items-center gap-2`}>
                        <Download size={18} /> Exportar
                    </button>
                    <button className="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg shadow-lg flex items-center gap-2">
                        <Plus size={18} /> Nuevo Pago
                    </button>
                </div>
            </div>

            {/* Tabs */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-1 shadow-sm flex gap-1 overflow-x-auto`}>
                {tabs.map(tab => {
                    const Icon = tab.icon;
                    const isActive = activeTab === tab.id;
                    return (
                        <button
                            key={tab.id}
                            onClick={() => setActiveTab(tab.id)}
                            className={`flex-1 min-w-[120px] px-4 py-2.5 rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2 ${isActive
                                    ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg'
                                    : darkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                                }`}
                        >
                            <Icon size={16} />
                            {tab.label}
                        </button>
                    );
                })}
            </div>

            {/* Content */}
            {renderContent()}
        </div>
    );
};

export default FinancialHub;
