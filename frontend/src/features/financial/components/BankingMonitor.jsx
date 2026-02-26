import { useState, useEffect } from 'react';
import { financialEnterpriseService } from '../services/financialEnterpriseService';
import { useTheme } from '../../../contexts/ThemeContext';

const BankingMonitor = () => {
    const { darkMode } = useTheme();
    const [alerts, setAlerts] = useState([]);

    useEffect(() => {
        financialEnterpriseService.getBankingAlerts().then(setAlerts);
    }, []);

    return (
        <div className={`p-6 rounded-2xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h3 className={`text-xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                        <ShieldAlert className="text-purple-500" />
                        Seguridad Bancaria & Anti-Fraude
                    </h3>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        Monitoreo de anomalías financieras en tiempo real.
                    </p>
                </div>
                <div className="flex items-center gap-2 px-3 py-1 bg-green-900/30 text-green-400 rounded-lg border border-green-800">
                    <Fingerprint size={16} />
                    <span className="text-xs font-mono">DLP: ACTIVE</span>
                </div>
            </div>

            <div className="space-y-4">
                {alerts.length === 0 ? (
                    <p className="text-center py-4 text-gray-500">Sin alertas de seguridad.</p>
                ) : (
                    alerts.map(alert => (
                        <div key={alert.id} className={`p-4 rounded-xl border flex items-start gap-4 ${alert.severity === 'high' ? 'bg-red-500/10 border-red-500/30' : 'bg-orange-500/10 border-orange-500/30'
                            }`}>
                            <AlertTriangle className={alert.severity === 'high' ? 'text-red-500' : 'text-orange-500'} />
                            <div>
                                <h4 className={`font-bold text-sm ${darkMode ? 'text-white' : 'text-gray-900'}`}>{alert.type.toUpperCase()}</h4>
                                <p className={`text-sm ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{alert.description}</p>
                                <span className="text-xs text-gray-500 mt-1 block">{new Date(alert.timestamp).toLocaleString()}</span>
                            </div>
                        </div>
                    ))
                )}
            </div>
        </div>
    );
};

export default BankingMonitor;
