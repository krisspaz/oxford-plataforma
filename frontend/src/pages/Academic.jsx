import { useState } from 'react';
import axios from 'axios';
import { Calendar, Play, CheckCircle } from 'lucide-react';
import { useAuth } from '../contexts/AuthContext';

const Academic = () => {
    const [schedule, setSchedule] = useState(null);
    const [loading, setLoading] = useState(false);
    const { token } = useAuth();

    const generateSchedule = async () => {
        if (!token) {
            alert("No hay token. Inicia sesión nuevamente.");
            return;
        }

        setLoading(true);

        try {
            const { data } = await axios.post(
                'http://localhost:8000/api/schedule/generate',
                {},
                { headers: { Authorization: `Bearer ${token}` } }
            );

            setSchedule(data);
        } catch (err) {
            console.error(err);
            alert('Error generando el horario.');
        } finally {
            setLoading(false);
        }
    };

    const ConflictsBadge = () => (
        <div className="flex items-center gap-2 text-green-600 bg-green-50 p-3 rounded-lg">
            <CheckCircle size={20} />
            <span className="font-medium">
                Horario generado exitosamente con {schedule?.unassigned_count ?? 0} conflictos.
            </span>
        </div>
    );

    const EmptyState = () => (
        <div className="text-center py-10 text-gray-400 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
            <Calendar size={48} className="mx-auto mb-2 opacity-50" />
            <p>Presiona "Generar" para crear un nuevo horario.</p>
        </div>
    );

    const ScheduleGrid = () => (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {schedule.schedule.map((item, i) => (
                <div
                    key={i}
                    className="border border-gray-200 p-4 rounded-lg hover:shadow-md transition-shadow"
                >
                    <div className="flex justify-between items-center mb-2">
                        <span className="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">
                            {item.slot}
                        </span>
                        <span className="text-gray-500 text-sm">{item.group}</span>
                    </div>

                    <h3 className="font-bold text-gray-800">{item.subject}</h3>

                    <p className="text-sm text-gray-600 flex items-center gap-1 mt-1">
                        👨‍🏫 {item.teacher_name}
                    </p>
                </div>
            ))}
        </div>
    );

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className="text-2xl font-bold text-gray-800">Generador de Horarios (IA)</h1>

                <button
                    onClick={generateSchedule}
                    disabled={loading}
                    className="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg flex items-center gap-2 transition-colors disabled:opacity-50"
                >
                    {loading
                        ? <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                        : <Play size={20} />}
                    {loading ? 'Generando...' : 'Generar Automáticamente'}
                </button>
            </div>

            <div className="bg-white p-6 rounded-xl shadow-sm">
                <p className="text-gray-600 mb-4">
                    Este módulo utiliza IA para asignar profesores y materias, minimizando conflictos.
                </p>

                {!schedule && <EmptyState />}

                {schedule && (
                    <div className="space-y-6">
                        <ConflictsBadge />
                        <ScheduleGrid />
                    </div>
                )}
            </div>
        </div>
    );
};

export default Academic;
