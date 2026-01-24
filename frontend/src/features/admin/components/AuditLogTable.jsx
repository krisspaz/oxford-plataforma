import React, { useState, useEffect } from 'react';
import { securityService } from '../services/securityService';
import { useTheme } from '../../../contexts/ThemeContext';
import { Shield, AlertTriangle, CheckCircle, Search, Filter, Clock } from 'lucide-react';

const AuditLogTable = () => {
    const { darkMode } = useTheme();
    const [logs, setLogs] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState('');

    useEffect(() => {
        const fetchLogs = async () => {
            try {
                const data = await securityService.getAuditLogs();
                setLogs(data);
            } catch (error) {
                console.error('Error loading audit logs', error);
            } finally {
                setLoading(false);
            }
        };
        fetchLogs();
    }, []);

    const getActionIcon = (action) => {
        if (action.includes('FAIL') || action.includes('VIOLATION')) return <AlertTriangle className="text-red-500" size={18} />;
        if (action.includes('SUCCESS') || action.includes('PAYMENT')) return <CheckCircle className="text-green-500" size={18} />;
        return <Clock className="text-blue-500" size={18} />;
    };

    const filteredLogs = logs.filter(log =>
        log.action.toLowerCase().includes(filter.toLowerCase()) ||
        log.user?.email?.toLowerCase().includes(filter.toLowerCase())
    );

    return (
        <div className={`rounded-2xl shadow-lg border p-6 ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
            <div className="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h3 className={`text-xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                        <Shield className="text-purple-500" />
                        Registro de Auditoría
                    </h3>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        Monitoreo de acciones del sistema en tiempo real.
                    </p>
                </div>

                <div className="relative">
                    <Search className={`absolute left-3 top-2.5 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`} size={18} />
                    <input
                        type="text"
                        placeholder="Buscar log..."
                        value={filter}
                        onChange={(e) => setFilter(e.target.value)}
                        className={`pl-10 pr-4 py-2 rounded-lg border focus:ring-2 focus:ring-purple-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-gray-50 border-gray-200'}`}
                    />
                </div>
            </div>

            <div className="overflow-x-auto">
                <table className="w-full">
                    <thead className={`border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                        <tr>
                            <th className={`text-left py-3 px-4 font-semibold text-sm ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Acción</th>
                            <th className={`text-left py-3 px-4 font-semibold text-sm ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Usuario</th>
                            <th className={`text-left py-3 px-4 font-semibold text-sm ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Detalles</th>
                            <th className={`text-left py-3 px-4 font-semibold text-sm ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>IP</th>
                            <th className={`text-left py-3 px-4 font-semibold text-sm ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Fecha</th>
                        </tr>
                    </thead>
                    <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                        {loading ? (
                            <tr><td colSpan="5" className="text-center py-8">Cargando registros...</td></tr>
                        ) : filteredLogs.length === 0 ? (
                            <tr><td colSpan="5" className="text-center py-8">No se encontraron registros.</td></tr>
                        ) : (
                            filteredLogs.map((log) => (
                                <tr key={log.id} className={`${darkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'} transition-colors`}>
                                    <td className="py-3 px-4">
                                        <div className="flex items-center gap-2">
                                            {getActionIcon(log.action)}
                                            <span className={`font-medium text-sm ${darkMode ? 'text-gray-200' : 'text-gray-800'}`}>
                                                {log.action}
                                            </span>
                                        </div>
                                    </td>
                                    <td className={`py-3 px-4 text-sm ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                                        {log.user?.email || 'Sistema'}
                                    </td>
                                    <td className={`py-3 px-4 text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        {log.details || `${log.entityType} #${log.entityId || 'N/A'}`}
                                    </td>
                                    <td className={`py-3 px-4 text-xs font-mono ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                                        {log.ipAddress}
                                    </td>
                                    <td className={`py-3 px-4 text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        {new Date(log.createdAt).toLocaleString()}
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default AuditLogTable;
