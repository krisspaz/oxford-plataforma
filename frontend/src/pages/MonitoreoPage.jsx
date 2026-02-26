import { useState, useEffect } from 'react';
import { Activity, Server, Users, AlertTriangle } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import api from '../services/api';

const MonitoreoPage = () => {
    const { darkMode } = useTheme();
    const [stats, setStats] = useState(null);
    const [logs, setLogs] = useState([]);
    const [loading, setLoading] = useState(true);

    const loadData = async () => {
        setLoading(true);
        try {
            const [statsRes, logsRes] = await Promise.all([
                api.get('/monitoring/stats'),
                api.get('/monitoring/logs')
            ]);

            if (statsRes.success) setStats(statsRes.data);
            if (logsRes.success) setLogs(logsRes.data);
        } catch (error) {
            console.error("Error loading monitoring data:", error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadData();
        const interval = setInterval(loadData, 30000); // Refresh every 30s
        return () => clearInterval(interval);
    }, []);

    // eslint-disable-next-line unused-imports/no-unused-vars
    const StatCard = ({ title, value, icon: Icon, color }) => (
        <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} p-6 rounded-xl shadow-sm border-l-4 ${color}`}>
            <div className="flex justify-between items-start">
                <div>
                    <p className={`text-sm font-medium ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{title}</p>
                    <h3 className={`text-2xl font-bold mt-1 ${darkMode ? 'text-white' : 'text-gray-800'}`}>{value}</h3>
                </div>
                <div className={`p-3 rounded-lg ${darkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>
                    <Icon size={24} className={darkMode ? 'text-white' : 'text-gray-600'} />
                </div>
            </div>
        </div>
    );

    if (loading && !stats) {
        return (
            <div className="flex items-center justify-center h-64">
                <RefreshCw size={32} className="animate-spin text-teal-500" />
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Monitoreo del Sistema</h1>
                <button onClick={loadData} className="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <RefreshCw size={20} className={loading ? 'animate-spin' : ''} />
                </button>
            </div>

            {/* Stats Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <StatCard title="Uso de CPU" value={`${stats?.cpu}%`} icon={Activity} color="border-blue-500" />
                <StatCard title="Uso de Memoria" value={`${stats?.memory}%`} icon={Server} color="border-purple-500" />
                <StatCard title="Usuarios Activos" value={stats?.activeUsers} icon={Users} color="border-green-500" />
                <StatCard title="Errores (Hoy)" value={stats?.errors} icon={AlertTriangle} color="border-red-500" />
            </div>

            {/* Logs Section */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                <div className={`p-6 border-b ${darkMode ? 'border-gray-700' : 'bg-white border-gray-200 text-gray-900'}`}>
                    <h2 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'} flex items-center gap-2`}>
                        <FileText size={20} /> Logs Recientes
                    </h2>
                </div>
                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead className={darkMode ? 'bg-gray-700' : 'bg-gray-50'}>
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mensaje</th>
                            </tr>
                        </thead>
                        <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-200'}`}>
                            {logs.map((log) => (
                                <tr key={log.id}>
                                    <td className={`px-6 py-4 whitespace-nowrap text-sm ${darkMode ? 'text-gray-300' : 'text-gray-500'}`}>
                                        {log.timestamp}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            ${log.level === 'ERROR' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' :
                                                log.level === 'WARNING' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' :
                                                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'}`}>
                                            {log.level}
                                        </span>
                                    </td>
                                    <td className={`px-6 py-4 text-sm ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                                        {log.message}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
};

export default MonitoreoPage;
