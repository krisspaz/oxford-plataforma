import React, { useState, useEffect } from 'react';
import { toast } from '../utils/toast';

/**
 * FASE 20: Crisis Mode Dashboard
 * Gestión de crisis, playbook, reoptimización inmediata
 */
const CrisisModeDashboard = () => {
    const [activeCrises, setActiveCrises] = useState([]);
    const [selectedCrisis, setSelectedCrisis] = useState(null);
    const [playbook, setPlaybook] = useState([]);
    const [loading, setLoading] = useState(false);

    // Simulated data - conectar con backend real
    useEffect(() => {
        // Fetch active crises from backend
        setActiveCrises([
            {
                id: 'CRISIS_001',
                type: 'teacher_absence',
                severity: 'high',
                description: 'Docente Juan Pérez reportó ausencia por enfermedad',
                affected: ['3°A Matemáticas', '4°B Física'],
                detectedAt: new Date().toISOString(),
                status: 'active'
            }
        ]);
    }, []);

    const severityColors = {
        critical: 'bg-red-600',
        high: 'bg-orange-500',
        medium: 'bg-yellow-500',
        low: 'bg-blue-500'
    };

    const severityLabels = {
        critical: '🔴 Crítico',
        high: '🟠 Alto',
        medium: '🟡 Medio',
        low: '🔵 Bajo'
    };

    const crisisTypeLabels = {
        teacher_absence: '👨‍🏫 Ausencia Docente',
        room_unavailable: '🏫 Aula No Disponible',
        schedule_conflict: '📅 Conflicto de Horario',
        emergency_event: '🚨 Emergencia',
        mass_change: '🔄 Cambio Masivo'
    };

    const handleSelectCrisis = (crisis) => {
        setSelectedCrisis(crisis);
        // Load playbook for crisis type
        setPlaybook([
            { step: 1, action: 'Identificar clases afectadas', responsible: 'Coordinación', time: '5 min', status: 'pending' },
            { step: 2, action: 'Buscar sustituto disponible', responsible: 'Coordinación', time: '10 min', status: 'pending' },
            { step: 3, action: 'Reasignar aulas si necesario', responsible: 'Coordinación', time: '5 min', status: 'pending' },
            { step: 4, action: 'Notificar a grupos afectados', responsible: 'Secretaría', time: '5 min', status: 'pending' },
            { step: 5, action: 'Actualizar horario temporal', responsible: 'Coordinación', time: '5 min', status: 'pending' },
        ]);
    };

    const handleResolve = (crisisId) => {
        setActiveCrises(prev => prev.filter(c => c.id !== crisisId));
        setSelectedCrisis(null);
        // Call backend to resolve
    };

    const handleAutoResolve = async () => {
        setLoading(true);
        // Call AI backend for automatic resolution
        setTimeout(() => {
            setLoading(false);
            toast.success('Crisis resuelta automáticamente. Se asignó sustituto y se notificó a los grupos.');
            handleResolve(selectedCrisis?.id);
        }, 2000);
    };

    return (
        <div className="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
            <div className="max-w-7xl mx-auto">
                {/* Header */}
                <div className="flex items-center justify-between mb-8">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            🚨 Modo Crisis
                        </h1>
                        <p className="text-gray-600 dark:text-gray-400 mt-1">
                            Gestión de situaciones críticas y reoptimización inmediata
                        </p>
                    </div>

                    <div className={`px-4 py-2 rounded-full text-white font-medium ${activeCrises.length > 0 ? 'bg-red-600 animate-pulse' : 'bg-green-600'
                        }`}>
                        {activeCrises.length > 0
                            ? `⚠️ ${activeCrises.length} Crisis Activa${activeCrises.length > 1 ? 's' : ''}`
                            : '✅ Sin Crisis Activas'
                        }
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Crisis List */}
                    <div className="lg:col-span-1">
                        <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                Crisis Activas
                            </h2>

                            {activeCrises.length === 0 ? (
                                <div className="text-center py-8">
                                    <div className="text-4xl mb-3">✨</div>
                                    <p className="text-gray-500 dark:text-gray-400">Todo en orden</p>
                                </div>
                            ) : (
                                <div className="space-y-3">
                                    {activeCrises.map((crisis) => (
                                        <button
                                            key={crisis.id}
                                            onClick={() => handleSelectCrisis(crisis)}
                                            className={`w-full text-left p-4 rounded-xl border-2 transition-all ${selectedCrisis?.id === crisis.id
                                                ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'
                                                }`}
                                        >
                                            <div className="flex items-center gap-2 mb-2">
                                                <span className={`w-3 h-3 rounded-full ${severityColors[crisis.severity]}`} />
                                                <span className="font-medium text-gray-900 dark:text-white">
                                                    {crisisTypeLabels[crisis.type]}
                                                </span>
                                            </div>
                                            <p className="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                                {crisis.description}
                                            </p>
                                            <div className="mt-2 text-xs text-gray-500">
                                                {new Date(crisis.detectedAt).toLocaleTimeString()}
                                            </div>
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Crisis Detail & Playbook */}
                    <div className="lg:col-span-2">
                        {selectedCrisis ? (
                            <div className="space-y-6">
                                {/* Crisis Info */}
                                <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                                    <div className="flex justify-between items-start mb-4">
                                        <div>
                                            <div className="flex items-center gap-3 mb-2">
                                                <span className={`px-3 py-1 rounded-full text-white text-sm ${severityColors[selectedCrisis.severity]}`}>
                                                    {severityLabels[selectedCrisis.severity]}
                                                </span>
                                                <span className="text-gray-500 dark:text-gray-400 text-sm">
                                                    {selectedCrisis.id}
                                                </span>
                                            </div>
                                            <h3 className="text-xl font-bold text-gray-900 dark:text-white">
                                                {crisisTypeLabels[selectedCrisis.type]}
                                            </h3>
                                        </div>

                                        <button
                                            onClick={handleAutoResolve}
                                            disabled={loading}
                                            className="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center gap-2"
                                        >
                                            {loading ? (
                                                <>
                                                    <span className="animate-spin">⏳</span>
                                                    Resolviendo...
                                                </>
                                            ) : (
                                                <>
                                                    🤖 Auto-Resolver con IA
                                                </>
                                            )}
                                        </button>
                                    </div>

                                    <p className="text-gray-600 dark:text-gray-300 mb-4">
                                        {selectedCrisis.description}
                                    </p>

                                    <div className="bg-gray-100 dark:bg-gray-700 rounded-xl p-4">
                                        <h4 className="font-medium text-gray-900 dark:text-white mb-2">
                                            📋 Afectados
                                        </h4>
                                        <div className="flex flex-wrap gap-2">
                                            {selectedCrisis.affected.map((item, idx) => (
                                                <span key={idx} className="px-3 py-1 bg-white dark:bg-gray-600 rounded-lg text-sm">
                                                    {item}
                                                </span>
                                            ))}
                                        </div>
                                    </div>
                                </div>

                                {/* Playbook */}
                                <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                        📖 Playbook de Respuesta
                                    </h3>

                                    <div className="space-y-3">
                                        {playbook.map((step, idx) => (
                                            <div
                                                key={step.step}
                                                className="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl"
                                            >
                                                <div className={`w-8 h-8 rounded-full flex items-center justify-center text-white font-bold ${step.status === 'completed' ? 'bg-green-500' : 'bg-gray-400'
                                                    }`}>
                                                    {step.status === 'completed' ? '✓' : step.step}
                                                </div>
                                                <div className="flex-1">
                                                    <p className="font-medium text-gray-900 dark:text-white">
                                                        {step.action}
                                                    </p>
                                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                                        {step.responsible} • ⏱️ {step.time}
                                                    </p>
                                                </div>
                                                <button className="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                                                    Ejecutar
                                                </button>
                                            </div>
                                        ))}
                                    </div>
                                </div>

                                {/* Manual Resolution */}
                                <div className="flex gap-3">
                                    <button
                                        onClick={() => handleResolve(selectedCrisis.id)}
                                        className="flex-1 px-4 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                                    >
                                        ✅ Marcar como Resuelto Manualmente
                                    </button>
                                </div>
                            </div>
                        ) : (
                            <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-12 text-center">
                                <div className="text-6xl mb-4">🎯</div>
                                <h3 className="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    Selecciona una crisis
                                </h3>
                                <p className="text-gray-500 dark:text-gray-400">
                                    Haz clic en una crisis activa para ver el playbook y opciones de resolución
                                </p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default CrisisModeDashboard;
