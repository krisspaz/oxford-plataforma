import React, { useState, useEffect } from 'react';
import { Shield, AlertTriangle, Lock, Activity, RefreshCw } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useApi } from '@/hooks/useApi';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';

const SecurityDashboard = () => {
    const { fetch } = useApi();
    const [alerts, setAlerts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [stats, setStats] = useState({ critical: 0, high: 0, total: 0 });

    const loadData = async () => {
        setLoading(true);
        try {
            const response = await fetch('/anomaly_alerts');
            const data = response['hydra:member'] || [];
            setAlerts(data);

            // Calculate stats
            const critical = data.filter(a => a.severity === 'critical').length;
            const high = data.filter(a => a.severity === 'high').length;
            setStats({ critical, high, total: data.length });
        } catch (error) {
            console.error('Failed to load security alerts', error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadData();
        const interval = setInterval(loadData, 30000); // Live refresh every 30s
        return () => clearInterval(interval);
    }, []);

    const getSeverityColor = (severity) => {
        switch (severity) {
            case 'critical': return 'text-red-500 border-red-500 bg-red-500/10';
            case 'high': return 'text-orange-500 border-orange-500 bg-orange-500/10';
            case 'medium': return 'text-yellow-500 border-yellow-500 bg-yellow-500/10';
            default: return 'text-blue-500 border-blue-500 bg-blue-500/10';
        }
    };

    return (
        <div className="p-6 space-y-6 bg-slate-950 min-h-screen text-slate-100 font-mono">
            <div className="flex justify-between items-center">
                <div>
                    <h1 className="text-3xl font-bold flex items-center gap-3 text-emerald-400">
                        <Shield className="w-8 h-8" />
                        CENTRO DE COMANDO DE SEGURIDAD
                    </h1>
                    <p className="text-slate-400 mt-1">Monitoreo de Anomalías en Tiempo Real</p>
                </div>
                <button
                    onClick={loadData}
                    className="p-2 bg-slate-800 hover:bg-slate-700 rounded-full transition-colors"
                >
                    <RefreshCw className={`w-5 h-5 ${loading ? 'animate-spin' : ''}`} />
                </button>
            </div>

            {/* Stats Grid */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                <Card className="bg-slate-900 border-slate-800">
                    <CardHeader className="pb-2">
                        <CardTitle className="text-sm text-slate-400">Amenazas Totales</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="text-3xl font-bold text-white">{stats.total}</div>
                    </CardContent>
                </Card>

                <Card className="bg-slate-900 border-red-900/50">
                    <CardHeader className="pb-2">
                        <CardTitle className="text-sm text-red-400 flex items-center gap-2">
                            <AlertTriangle className="w-4 h-4" /> CRÍTICAS
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="text-3xl font-bold text-red-500 animate-pulse">{stats.critical}</div>
                    </CardContent>
                </Card>

                <Card className="bg-slate-900 border-orange-900/50">
                    <CardHeader className="pb-2">
                        <CardTitle className="text-sm text-orange-400">Riesgo Alto</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="text-3xl font-bold text-orange-500">{stats.high}</div>
                    </CardContent>
                </Card>

                <Card className="bg-slate-900 border-emerald-900/50">
                    <CardHeader className="pb-2">
                        <CardTitle className="text-sm text-emerald-400">Estado del Sistema</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="text-3xl font-bold text-emerald-500 flex items-center gap-2">
                            <Lock className="w-6 h-6" /> SEGURO
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Main Content Area */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {/* Live Feed */}
                <div className="lg:col-span-2 space-y-4">
                    <h2 className="text-xl font-semibold flex items-center gap-2 text-slate-300">
                        <Activity className="w-5 h-5 text-emerald-400" />
                        Live Feed de Anomalías
                    </h2>
                    <div className="space-y-3">
                        {alerts.length === 0 ? (
                            <div className="p-8 text-center text-slate-500 bg-slate-900/50 rounded-lg border border-slate-800 border-dashed">
                                No hay anomalías detectadas en las últimas 24 horas.
                            </div>
                        ) : (
                            alerts.map((alert) => (
                                <div
                                    key={alert.id}
                                    className={`p-4 rounded-lg border-l-4 bg-slate-900 flex justify-between items-start transition-all hover:bg-slate-800 ${getSeverityColor(alert.severity)}`}
                                >
                                    <div>
                                        <div className="flex items-center gap-2 mb-1">
                                            <span className="font-bold uppercase text-xs px-2 py-0.5 rounded bg-black/30">
                                                {alert.type}
                                            </span>
                                            <span className="text-xs text-slate-500">
                                                {new Date(alert.createdAt).toLocaleString()}
                                            </span>
                                        </div>
                                        <p className="text-slate-200">{alert.description}</p>
                                        <p className="text-xs text-slate-400 mt-2 font-mono">
                                            ACTION: {alert.action} | ID: {alert.id}
                                        </p>
                                    </div>
                                    {!alert.resolved && (
                                        <button className="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-xs rounded border border-emerald-500/50 hover:bg-emerald-500/30 transition-colors">
                                            RESOLVER
                                        </button>
                                    )}
                                </div>
                            ))
                        )}
                    </div>
                </div>

                {/* Visualizations / Side Panel */}
                <div className="space-y-6">
                    <Card className="bg-slate-900 border-slate-800 h-64">
                        <CardHeader>
                            <CardTitle className="text-sm text-slate-400">Actividad de Red</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="h-full flex items-center justify-center text-slate-600">
                                <ResponsiveContainer width="100%" height="100%">
                                    <LineChart data={[{ v: 10 }, { v: 20 }, { v: 15 }, { v: 40 }, { v: 30 }, { v: 50 }, { v: 45 }]}>
                                        <Line type="monotone" dataKey="v" stroke="#10b981" strokeWidth={2} dot={false} />
                                        <Tooltip contentStyle={{ backgroundColor: '#1e293b', border: 'none' }} />
                                    </LineChart>
                                </ResponsiveContainer>
                            </div>
                        </CardContent>
                    </Card>

                    <div className="p-4 bg-emerald-900/20 border border-emerald-500/30 rounded-lg">
                        <h3 className="text-emerald-400 font-bold mb-2">Protocolo Activo</h3>
                        <p className="text-sm text-emerald-200/70">
                            El sistema está operando bajo parámetros normales. La inteligencia artificial está monitoreando activamente patrones de intrusión.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    );
};

export default SecurityDashboard;
