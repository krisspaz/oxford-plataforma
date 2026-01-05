import React, { useState, useEffect } from 'react';
import { AlertTriangle, TrendingDown, CheckCircle, ShieldAlert } from 'lucide-react';
import AiService from '../services/AiService';

const RiskDashboard = ({ students }) => {
    const [risks, setRisks] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const analyzeRisks = async () => {
            if (!students || students.length === 0) {
                setLoading(false);
                return;
            }

            setLoading(true);
            const riskPromises = students.map(async (student) => {
                try {
                    // Prepare data structure expected by Python
                    // In a real app we might fetch grades here or pass them in
                    const studentData = {
                        id: student.id,
                        grades: student.grades || [], // Assume grades are attached or we mock them for demo
                        attendance: student.attendance || 100
                    };

                    const analysis = await AiService.predictRisk(studentData);
                    return { ...student, analysis };
                } catch (e) {
                    console.error("AI Risk Failure:", e);
                    return null;
                }
            });

            const results = await Promise.all(riskPromises);

            // Filter only At Risk
            const atRisk = results.filter(r => r && r.analysis && r.analysis.risk_level !== 'SAFE');
            // Sort by risk score descending
            atRisk.sort((a, b) => b.analysis.risk_score - a.analysis.risk_score);

            setRisks(atRisk);
            setLoading(false);
        };

        analyzeRisks();
    }, [students]);

    if (loading) return <div className="p-4 text-center text-gray-500">🧠 La IA está analizando el rendimiento académico...</div>;

    if (risks.length === 0) {
        return (
            <div className="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                <CheckCircle className="w-12 h-12 text-green-500 mx-auto mb-2" />
                <h3 className="text-lg font-semibold text-green-700">Sin Riesgos Detectados</h3>
                <p className="text-green-600">Todos los estudiantes analizados presentan un rendimiento seguro.</p>
            </div>
        );
    }

    return (
        <div className="space-y-4">
            <h3 className="text-lg font-bold text-gray-800 flex items-center gap-2">
                <ShieldAlert className="w-5 h-5 text-red-500" />
                Alertas Académicas (IA)
            </h3>

            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                {risks.map((item) => (
                    <div key={item.id} className={`relative p-4 rounded-xl border-l-4 shadow-sm bg-white
                        ${item.analysis.risk_level === 'CRITICAL' ? 'border-l-red-500' : 'border-l-yellow-500'}
                    `}>
                        <div className="flex justify-between items-start mb-2">
                            <div>
                                <h4 className="font-bold text-gray-900">{item.name || `Estudiante #${item.id}`}</h4>
                                <span className={`text-xs px-2 py-0.5 rounded-full font-bold
                                    ${item.analysis.risk_level === 'CRITICAL'
                                        ? 'bg-red-100 text-red-700'
                                        : 'bg-yellow-100 text-yellow-700'}
                                `}>
                                    Riesgo: {item.analysis.risk_score}%
                                </span>
                            </div>
                            {item.analysis.risk_level === 'CRITICAL' && (
                                <AlertTriangle className="w-5 h-5 text-red-500" />
                            )}
                        </div>

                        <div className="space-y-1 mt-3">
                            {item.analysis.alerts.map((alert, i) => (
                                <div key={i} className="flex items-start gap-2 text-sm text-gray-600">
                                    <TrendingDown className="w-3 h-3 mt-1 flex-shrink-0 text-gray-400" />
                                    <span>{alert}</span>
                                </div>
                            ))}
                        </div>

                        {item.analysis.recommendations.length > 0 && (
                            <div className="mt-3 pt-3 border-t border-gray-100">
                                <p className="text-xs font-semibold text-gray-500 uppercase">Sugerencia:</p>
                                <p className="text-sm text-indigo-600 italic">
                                    "{item.analysis.recommendations[0]}"
                                </p>
                            </div>
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
};

export default RiskDashboard;
