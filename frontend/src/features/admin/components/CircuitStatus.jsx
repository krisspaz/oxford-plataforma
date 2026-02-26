import { useState, useEffect } from 'react';
import { aiEnterpriseService } from '../services/aiEnterpriseService';
import { useTheme } from '../../../contexts/ThemeContext';

const CircuitStatus = () => {
    const { darkMode } = useTheme();
    const [health, setHealth] = useState(null);

    useEffect(() => {
        const checkHealth = async () => {
            const status = await aiEnterpriseService.getHealth();
            setHealth(status);
        };
        checkHealth();
        const interval = setInterval(checkHealth, 30000); // Poll every 30s
        return () => clearInterval(interval);
    }, []);

    const isHealthy = health?.status === 'healthy';
    const isBroken = health?.circuit_broken;

    return (
        <div className={`p-6 rounded-2xl shadow-lg border relative overflow-hidden ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
            {/* Background Pulse */}
            <div className={`absolute -right-10 -top-10 w-40 h-40 rounded-full blur-3xl opacity-20 ${isHealthy ? 'bg-green-500' : 'bg-red-500'}`}></div>

            <div className="flex justify-between items-start mb-4 relative z-10">
                <div>
                    <h3 className={`text-lg font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                        <Activity className={isHealthy ? "text-green-500" : "text-red-500"} />
                        Salud del Sistema IA
                    </h3>
                    <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        Estado del Cortacircuitos (Circuit Breaker)
                    </p>
                </div>
                <div className={`px-3 py-1 rounded-full text-xs font-bold border ${isHealthy
                        ? 'bg-green-100 text-green-700 border-green-200'
                        : 'bg-red-100 text-red-700 border-red-200'
                    }`}>
                    {isHealthy ? 'ONLINE' : 'DEGRADED'}
                </div>
            </div>

            <div className="flex items-center gap-4 relative z-10">
                <div className={`p-3 rounded-full ${isHealthy ? 'bg-green-500/20 text-green-500' : 'bg-red-500/20 text-red-500'}`}>
                    {isHealthy ? <Zap size={24} /> : <ServerCrash size={24} />}
                </div>
                <div>
                    <h4 className={`text-2xl font-mono font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                        {isBroken ? 'CIRCUIT OPEN' : 'CIRCUIT CLOSED'}
                    </h4>
                    <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        {isBroken
                            ? 'Tráfico bloqueado por seguridad. Microservicio inestable.'
                            : 'Flujo de peticiones normal. Latencia estable.'}
                    </p>
                </div>
            </div>
        </div>
    );
};

export default CircuitStatus;
