import { useState } from 'react';
import { useApi } from '@/hooks/useApi';

const AiInsightsPage = () => {
    // eslint-disable-next-line unused-imports/no-unused-vars
    const { fetch, post } = useApi();
    // eslint-disable-next-line unused-imports/no-unused-vars
    const [loading, setLoading] = useState(false);
    // eslint-disable-next-line unused-imports/no-unused-vars
    const [students, setStudents] = useState([]);
    const [analyzing, setAnalyzing] = useState(false);

    // Mock initial student data since we don't have a bulk student endpoint with grades handy yet
    // In a real scenario, we'd fetch students, then ask AI to analyze them in batch.
    // eslint-disable-next-line unused-imports/no-unused-vars
    const [mockStudents, setMockStudents] = useState([
        { id: 101, name: 'Juan Pérez', grade: '5to Bach', photo: null },
        { id: 102, name: 'Maria Gonzalez', grade: '5to Bach', photo: null },
        { id: 103, name: 'Carlos Ruiz', grade: '4to Bach', photo: null },
    ]);

    const [risks, setRisks] = useState({});

    const runAnalysis = async () => {
        setAnalyzing(true);
        const results = {};

        try {
            // We analyze students one by one for demo purposes
            // In production, this should be a bulk endpoint
            const analysisPromises = mockStudents.map(async (student) => {
                // Synthesize some grade history for the AI
                const mockGrades = [
                    { subject: 'Matemáticas', score: student.id === 101 ? 55 : (student.id === 102 ? 85 : 72), history: [60, 58, 55] },
                    { subject: 'Ciencias', score: student.id === 101 ? 62 : 90, history: [70, 65, 62] },
                    { subject: 'Lenguaje', score: 80, history: [80, 82, 80] }
                ];

                const response = await post('/ai/risk-analysis', {
                    student_id: student.id,
                    grades: mockGrades
                });

                results[student.id] = response;
            });

            await Promise.all(analysisPromises);
            setRisks(results);
        } catch (error) {
            console.error("AI Analysis Failed", error);
        } finally {
            setAnalyzing(false);
        }
    };

    const getRiskColor = (level) => {
        switch (level) {
            case 'CRITICAL': return 'bg-red-500/20 border-red-500 text-red-500';
            case 'WARNING': return 'bg-orange-500/20 border-orange-500 text-orange-500';
            default: return 'bg-emerald-500/20 border-emerald-500 text-emerald-500';
        }
    };

    return (
        <div className="p-6 space-y-6 bg-slate-950 min-h-screen text-slate-100 font-mono">
            <div className="flex justify-between items-center">
                <div>
                    <h1 className="text-3xl font-bold flex items-center gap-3 text-purple-400">
                        <Brain className="w-8 h-8" />
                        OXFORD AI PREDICTOR
                    </h1>
                    <p className="text-slate-400 mt-1">Análisis Predictivo de Rendimiento Académico</p>
                </div>
                <button
                    onClick={runAnalysis}
                    disabled={analyzing}
                    className="flex items-center gap-2 px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {analyzing ? (
                        <>
                            <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                            ANALIZANDO...
                        </>
                    ) : (
                        <>
                            <Search className="w-5 h-5" />
                            EJECUTAR ANÁLISIS
                        </>
                    )}
                </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {mockStudents.map(student => {
                    const result = risks[student.id];
                    return (
                        <Card key={student.id} className="bg-slate-900 border-slate-800 overflow-hidden relative group hover:border-purple-500/50 transition-colors">
                            {analyzing && (
                                <div className="absolute inset-0 bg-purple-500/5 flex items-center justify-center backdrop-blur-sm z-10">
                                    <div className="font-mono text-purple-300 animate-pulse">Scanning...</div>
                                </div>
                            )}

                            <CardHeader className="flex flex-row items-center gap-4 pb-2">
                                <div className="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center text-xl font-bold text-slate-500">
                                    {student.name.charAt(0)}
                                </div>
                                <div>
                                    <CardTitle className="text-lg text-slate-200">{student.name}</CardTitle>
                                    <p className="text-sm text-slate-500">{student.grade}</p>
                                </div>
                            </CardHeader>

                            <CardContent>
                                {result ? (
                                    <div className="space-y-4 animate-in fade-in duration-500">
                                        <div className={`p-3 rounded-md border ${getRiskColor(result.risk_level)} flex justify-between items-center`}>
                                            <span className="font-bold">RIESGO: {result.risk_level}</span>
                                            <span className="text-2xl font-bold">{result.risk_score}%</span>
                                        </div>

                                        {result.alerts.length > 0 && (
                                            <div className="space-y-2">
                                                <p className="text-xs font-bold text-slate-400 uppercase tracking-wider">Alertas Detectadas</p>
                                                {result.alerts.map((alert, idx) => (
                                                    <div key={idx} className="flex items-start gap-2 text-sm text-slate-300">
                                                        <AlertTriangle className="w-4 h-4 text-yellow-500 shrink-0 mt-0.5" />
                                                        <span>{alert}</span>
                                                    </div>
                                                ))}
                                            </div>
                                        )}

                                        {result.recommendations.length > 0 && (
                                            <div className="mt-4 pt-4 border-t border-slate-800">
                                                <p className="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">Sugerencias IA</p>
                                                {result.recommendations.map((rec, idx) => (
                                                    <div key={idx} className="flex items-start gap-2 text-xs text-slate-400 bg-slate-950 p-2 rounded">
                                                        <CheckCircle className="w-3 h-3 text-emerald-500 shrink-0 mt-0.5" />
                                                        <span>{rec}</span>
                                                    </div>
                                                ))}
                                            </div>
                                        )}
                                    </div>
                                ) : (
                                    <div className="h-40 flex flex-col items-center justify-center text-slate-600 gap-2 p-6 border-2 border-dashed border-slate-800 rounded-lg">
                                        <Brain className="w-10 h-10 opacity-20" />
                                        <p className="text-sm">Esperando análisis...</p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    );
                })}
            </div>
        </div>
    );
};

export default AiInsightsPage;
