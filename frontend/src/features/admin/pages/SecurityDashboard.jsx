import { useState } from 'react';
import { useTheme } from '../../../contexts/ThemeContext';
import { Shield, Lock, FileText } from 'lucide-react';

const SecurityDashboard = () => {
    const { darkMode } = useTheme();
    const [activeTab, setActiveTab] = useState('overview');

    const tabs = [
        { id: 'overview', label: 'Panorama General', icon: Shield },
        { id: 'audit', label: 'Auditoría', icon: FileText },
        { id: 'rules', label: 'Gobernanza & Reglas', icon: Lock },
    ];

    return (
        <div className={`min-h-screen p-8 transition-colors duration-300 ${darkMode ? 'bg-gray-900 text-white' : 'bg-gray-50 text-gray-800'}`}>
            <div className="max-w-7xl mx-auto space-y-8">

                {/* Header */}
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 className="text-3xl font-bold bg-gradient-to-r from-purple-500 to-pink-500 bg-clip-text text-transparent">
                            Centro de Comando de Seguridad
                        </h1>
                        <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>
                            Monitoreo avanzado, prevención de fraudes y gobernanza institucional.
                        </p>
                    </div>
                    <div className="flex gap-2">
                        <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold flex items-center gap-1">
                            <span className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            SISTEMA SEGURO
                        </span>
                    </div>
                </div>

                {/* Navigation Tabs */}
                <div className="flex gap-2 border-b border-gray-200 dark:border-gray-700 pb-1">
                    {tabs.map(tab => (
                        <button
                            key={tab.id}
                            onClick={() => setActiveTab(tab.id)}
                            className={`flex items-center gap-2 px-4 py-3 rounded-t-lg font-medium transition-all ${activeTab === tab.id
                                    ? `text-purple-600 border-b-2 border-purple-600 ${darkMode ? 'bg-purple-900/20' : 'bg-purple-50'}`
                                    : `text-gray-500 hover:text-gray-700 ${darkMode ? 'hover:text-gray-300' : ''}`
                                }`}
                        >
                            <tab.icon size={18} />
                            {tab.label}
                        </button>
                    ))}
                </div>

                {/* Content */}
                <div className="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    {activeTab === 'overview' && (
                        <>
                            <SecurityWidgets />
                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <AuditLogTable />
                                <RulesManager />
                            </div>
                        </>
                    )}

                    {activeTab === 'audit' && <AuditLogTable />}

                    {activeTab === 'rules' && <RulesManager />}
                </div>
            </div>
        </div>
    );
};

export default SecurityDashboard;
