import { useState, useEffect } from 'react';
import { aiEnterpriseService } from '../services/aiEnterpriseService';
import { useTheme } from '../../../contexts/ThemeContext';

const RiskHeatmap = () => {
    const { darkMode } = useTheme();
    const [predictions, setPredictions] = useState([]);

    useEffect(() => {
        aiEnterpriseService.getPredictions().then(setPredictions);
    }, []);

    const getColor = (score) => {
        if (score >= 90) return 'bg-red-500';
        if (score >= 70) return 'bg-orange-500';
        if (score >= 50) return 'bg-yellow-500';
        return 'bg-green-500';
    };

    return (
        <div className={`p-6 rounded-2xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h3 className={`text-xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                        <TrendingDown className="text-red-500" />
                        Mapa de Calor de Deserción
                    </h3>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        Estudiantes con mayor probabilidad de reprobación basada en IA.
                    </p>
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                {predictions.map(pred => (
                    <div key={pred.id} className={`flex items-center gap-4 p-4 rounded-xl border ${darkMode ? 'bg-gray-700/50 border-gray-600' : 'bg-gray-50 border-gray-200'}`}>
                        <div className={`w-12 h-12 rounded-full flex items-center justify-center font-bold text-white shadow-md ${getColor(pred.risk)}`}>
                            {pred.risk}%
                        </div>
                        <div className="flex-1">
                            <h4 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{pred.student}</h4>
                            <p className="text-xs text-red-500 font-semibold">{pred.subject}</p>
                            <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{pred.reason}</p>
                        </div>
                        <AlertTriangle className={darkMode ? 'text-gray-600' : 'text-gray-300'} />
                    </div>
                ))}
            </div>
        </div>
    );
};

export default RiskHeatmap;
