import React, { useEffect, useState } from 'react';
import { Brain, Sparkles, TrendingUp, AlertTriangle, ArrowRight } from 'lucide-react';
import aiApi from '../services/aiApi';

const AIInsightsWidget = ({ darkMode, userRole }) => {
    const [insight, setInsight] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const loadInsights = async () => {
            try {
                const data = await aiApi.getInsights(userRole);
                setInsight(data);
            } catch (error) {
                console.error("AI Insight Error:", error);
            } finally {
                setLoading(false);
            }
        };
        loadInsights();
    }, [userRole]);

    if (loading) return (
        <div className={`p-6 rounded-2xl shadow-lg border ${darkMode ? 'bg-indigo-900/20 border-indigo-700/50' : 'bg-indigo-50 border-indigo-100'} animate-pulse`}>
            <div className="flex items-center gap-3 mb-4">
                <div className="w-8 h-8 rounded-full bg-indigo-200/50"></div>
                <div className="h-4 bg-indigo-200/50 rounded w-1/3"></div>
            </div>
            <div className="h-16 bg-indigo-200/50 rounded w-full"></div>
        </div>
    );

    if (!insight) return null;

    return (
        <div className={`relative overflow-hidden p-6 rounded-2xl shadow-lg border transition-all hover:shadow-xl ${darkMode ? 'bg-gradient-to-br from-indigo-900/40 to-purple-900/40 border-indigo-500/30' : 'bg-gradient-to-br from-indigo-50 to-purple-50 border-indigo-100'}`}>

            {/* Background Decor */}
            <div className="absolute top-0 right-0 p-4 opacity-10">
                <Brain size={100} />
            </div>

            {/* Header */}
            <div className="flex items-center gap-2 mb-4 relative z-10">
                <div className={`p-2 rounded-lg ${darkMode ? 'bg-indigo-500/20 text-indigo-300' : 'bg-indigo-100 text-indigo-600'}`}>
                    <Sparkles size={20} />
                </div>
                <h3 className={`font-bold text-lg ${darkMode ? 'text-indigo-100' : 'text-indigo-900'}`}>
                    Análisis de IA
                </h3>
                <span className="text-xs font-mono px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-500 border border-indigo-500/20">BETA</span>
            </div>

            {/* Content */}
            <div className="space-y-4 relative z-10">
                <div className="flex gap-3 items-start">
                    <AlertTriangle className={`mt-1 shrink-0 ${insight.alertLevel === 'high' ? 'text-red-500' : 'text-orange-500'}`} size={18} />
                    <div>
                        <p className={`text-sm font-medium ${darkMode ? 'text-gray-200' : 'text-gray-800'}`}>
                            {insight.insight}
                        </p>
                        <p className={`text-xs mt-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                            Sugerencia: {insight.recommendation}
                        </p>
                    </div>
                </div>

                <div className="flex gap-3 items-start">
                    <TrendingUp className="mt-1 shrink-0 text-green-500" size={18} />
                    <p className={`text-sm font-medium ${darkMode ? 'text-gray-200' : 'text-gray-800'}`}>
                        {insight.positiveTrend}
                    </p>
                </div>
            </div>

            {/* Footer Action */}
            <div className="mt-4 pt-4 border-t border-indigo-500/10 flex justify-end relative z-10">
                <button className={`text-xs font-bold flex items-center gap-1 transition-colors ${darkMode ? 'text-indigo-300 hover:text-indigo-200' : 'text-indigo-600 hover:text-indigo-700'}`}>
                    Ver Reporte Completo <ArrowRight size={14} />
                </button>
            </div>
        </div>
    );
};

export default AIInsightsWidget;
