import React, { useState } from 'react';
import { useTheme } from '../../../contexts/ThemeContext';
import RiskHeatmap from '../components/RiskHeatmap';
import CircuitStatus from '../components/CircuitStatus';
import { Brain, Sparkles, MessageSquare } from 'lucide-react';
import { aiEnterpriseService } from '../services/aiEnterpriseService';

const PredictiveDashboard = () => {
    const { darkMode } = useTheme();
    const [chatMessage, setChatMessage] = useState('');
    const [chatResponse, setChatResponse] = useState(null);
    const [loadingChat, setLoadingChat] = useState(false);

    const handleChat = async (e) => {
        e.preventDefault();
        if (!chatMessage.trim()) return;

        setLoadingChat(true);
        try {
            const result = await aiEnterpriseService.chat(chatMessage);
            setChatResponse(result?.data?.response_text || "No se pudo obtener respuesta.");
        } catch (error) {
            setChatResponse("Error de conexión con el servicio de IA.");
        } finally {
            setLoadingChat(false);
            setChatMessage('');
        }
    };

    return (
        <div className={`min-h-screen p-8 transition-colors duration-300 ${darkMode ? 'bg-gray-900 text-white' : 'bg-gray-50 text-gray-800'}`}>
            <div className="max-w-7xl mx-auto space-y-8">

                {/* Header */}
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 className="text-3xl font-bold bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent flex items-center gap-3">
                            <Brain className="text-blue-500" size={32} />
                            Oxford AI Predictor
                        </h1>
                        <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>
                            Análisis predictivo de deserción escolar y optimización de recursos.
                        </p>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Main Column */}
                    <div className="lg:col-span-2 space-y-8">
                        {/* Heatmap */}
                        <RiskHeatmap />

                        {/* Chat Interface */}
                        <div className={`p-6 rounded-2xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                            <h3 className={`text-lg font-bold mb-4 flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                                <Sparkles className="text-yellow-500" />
                                Consultas Inteligentes
                            </h3>

                            <div className={`h-48 overflow-y-auto mb-4 p-4 rounded-xl border ${darkMode ? 'bg-gray-900/50 border-gray-700' : 'bg-gray-50 border-gray-200'}`}>
                                {chatResponse ? (
                                    <div className="flex items-start gap-3">
                                        <div className="bg-blue-500 p-2 rounded-lg text-white">
                                            <Brain size={16} />
                                        </div>
                                        <p className={`text-sm ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>{chatResponse}</p>
                                    </div>
                                ) : (
                                    <p className="text-center text-gray-400 text-sm mt-16">
                                        Haz preguntas como: "¿Qué estudiantes tienen riesgo alto?" o "Sugiere optimizaciones de horario".
                                    </p>
                                )}
                            </div>

                            <form onSubmit={handleChat} className="relative">
                                <input
                                    type="text"
                                    value={chatMessage}
                                    onChange={(e) => setChatMessage(e.target.value)}
                                    placeholder="Escribe tu consulta a la IA..."
                                    className={`w-full pl-4 pr-12 py-3 rounded-xl border focus:ring-2 focus:ring-blue-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-200'}`}
                                />
                                <button
                                    type="submit"
                                    disabled={loadingChat}
                                    className="absolute right-2 top-2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                >
                                    <MessageSquare size={18} />
                                </button>
                            </form>
                        </div>
                    </div>

                    {/* Sidebar Column */}
                    <div className="space-y-8">
                        <CircuitStatus />

                        {/* Stats Widget */}
                        <div className={`p-6 rounded-2xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                            <h4 className={`font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-900'}`}>Estadísticas del Modelo</h4>
                            <div className="space-y-4">
                                <div className="flex justify-between items-center">
                                    <span className="text-sm text-gray-500">Precisión (Accuracy)</span>
                                    <span className="font-mono font-bold text-green-500">94.2%</span>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-sm text-gray-500">Predicciones (Hoy)</span>
                                    <span className="font-mono font-bold text-blue-500">1,240</span>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-sm text-gray-500">Latencia Promedio</span>
                                    <span className="font-mono font-bold text-orange-500">120ms</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default PredictiveDashboard;
