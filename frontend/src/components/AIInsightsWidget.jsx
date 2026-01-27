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
        <div className={`p-6 rounded-2xl border animate-pulse ${darkMode ? 'bg-slate-800/50 border-white/5' : 'bg-white border-slate-100 shadow-sm'}`}>
            <div className="flex items-center gap-3 mb-4">
                <div className={`w-8 h-8 rounded-full ${darkMode ? 'bg-slate-700' : 'bg-slate-200'}`}></div>
                <div className={`h-4 rounded w-1/3 ${darkMode ? 'bg-slate-700' : 'bg-slate-200'}`}></div>
            </div>
            <div className={`h-16 rounded w-full ${darkMode ? 'bg-slate-700' : 'bg-slate-200'}`}></div>
        </div>
    );

    if (!insight) return null;

    return (
        <div className={`relative overflow-hidden p-6 rounded-2xl border transition-all hover:shadow-lg group ${darkMode
            ? 'bg-gradient-to-br from-indigo-900/40 to-slate-900/90 border-indigo-500/30 backdrop-blur-md'
            : 'bg-white border-indigo-100 shadow-sm hover:shadow-md'
            }`}>

            {/* Background Decor */}
            <div className="absolute -top-6 -right-6 opacity-10 transition-transform duration-700 group-hover:rotate-12 group-hover:scale-110">
                <Brain size={140} className={darkMode ? 'text-indigo-400' : 'text-indigo-600'} />
            </div>

            {/* Header */}
            <div className="flex items-center gap-2 mb-4 relative z-10">
                <div className={`p-2 rounded-xl shadow-sm ${darkMode ? 'bg-indigo-500/20 text-indigo-300 ring-1 ring-inset ring-indigo-500/20' : 'bg-indigo-50 text-indigo-600 ring-1 ring-inset ring-indigo-100'}`}>
                    <Sparkles size={18} />
                </div>
                <h3 className={`font-bold text-lg tracking-tight ${darkMode ? 'text-white' : 'text-slate-900'}`}>
                    Análisis de IA
                </h3>
                <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider ${darkMode ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/20' : 'bg-indigo-50 text-indigo-600 border border-indigo-100'}`}>Beta</span>
            </div>

            {/* Content */}
            <div className="space-y-4 relative z-10">
                <div className="flex gap-3 items-start">
                    <AlertTriangle className={`mt-0.5 shrink-0 ${insight.alertLevel === 'high' ? 'text-red-500' : 'text-amber-500'}`} size={18} />
                    <div className="flex-1">
                        <p className={`text-sm font-medium leading-relaxed ${darkMode ? 'text-slate-200' : 'text-slate-700'}`}>
                            {insight.insight}
                        </p>
                        <p className={`text-xs mt-1.5 font-medium ${darkMode ? 'text-indigo-300/80' : 'text-indigo-600/80'}`}>
                            💡 Sugerencia: {insight.recommendation}
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
