import React, { useState } from 'react';
import { Activity, Shield, Search, Filter } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';

const LogsPage = () => {
    const { darkMode } = useTheme();

    // Mock data - In a real app this would come from an API
    const [logs] = useState([
        { id: 1, action: 'LOGIN', user: 'admin', ip: '192.168.1.10', date: '2025-01-05 08:30:12', details: 'Inicio de sesión exitoso' },
        { id: 2, action: 'CREATE_STUDENT', user: 'secretaria', ip: '192.168.1.15', date: '2025-01-05 09:15:45', details: 'Estudiante creado: Juan Pérez' },
        { id: 3, action: 'UPDATE_GRADE', user: 'docente_math', ip: '192.168.1.20', date: '2025-01-05 10:22:01', details: 'Nota modificada: Matemáticas - Parcial 1' },
        { id: 4, action: 'PRINT_REPORT', user: 'director', ip: '192.168.1.12', date: '2025-01-05 11:05:33', details: 'Reporte generado: Boleta Calificaciones' },
        { id: 5, action: 'LOGIN_FAILED', user: 'unknown', ip: '192.168.1.100', date: '2025-01-05 11:30:00', details: 'Contraseña incorrecta' },
    ]);

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <div>
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'} flex items-center gap-2`}>
                        <Activity className="text-blue-500" /> Logs del Sistema
                    </h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>Registro de actividades y eventos de seguridad.</p>
                </div>
            </div>

            <div className={`p-4 rounded-xl border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} shadow-sm`}>
                <div className="flex gap-4">
                    <div className="relative flex-1">
                        <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={18} />
                        <input
                            type="text"
                            placeholder="Buscar en logs..."
                            className={`w-full pl-10 pr-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'}`}
                        />
                    </div>
                </div>
            </div>

            <div className={`rounded-xl shadow-lg border overflow-hidden ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                <table className="w-full">
                    <thead className={darkMode ? 'bg-gray-700/50' : 'bg-gray-50'}>
                        <tr className={`text-left text-sm ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                            <th className="p-4">Fecha/Hora</th>
                            <th className="p-4">Acción</th>
                            <th className="p-4">Usuario</th>
                            <th className="p-4">IP</th>
                            <th className="p-4">Detalles</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
                        {logs.map(log => (
                            <tr key={log.id} className={`hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                <td className="p-4 font-mono text-xs">{log.date}</td>
                                <td className="p-4">
                                    <span className={`px-2 py-1 rounded text-xs font-bold 
                                        ${log.action.includes('LOGIN') ? 'bg-green-100 text-green-700' :
                                            log.action.includes('FAILED') ? 'bg-red-100 text-red-700' :
                                                'bg-blue-100 text-blue-700'}`}>
                                        {log.action}
                                    </span>
                                </td>
                                <td className="p-4 font-medium">{log.user}</td>
                                <td className="p-4 font-mono text-xs text-gray-500">{log.ip}</td>
                                <td className="p-4 text-sm">{log.details}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            <p className="text-center text-xs text-gray-500 mt-4">
                Mostrando últimos {logs.length} eventos. Los registros se archivan cada 30 días.
            </p>
        </div>
    );
};

export default LogsPage;
