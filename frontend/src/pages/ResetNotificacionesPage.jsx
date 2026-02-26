import { useState } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import api from '../services/api';

const ResetNotificacionesPage = () => {
    const { darkMode } = useTheme();
    const [loading, setLoading] = useState(false);
    const [successMsg, setSuccessMsg] = useState('');
    const [errorMsg, setErrorMsg] = useState('');

    const handleReset = async () => {
        if (!window.confirm('¿Estás seguro de que deseas ELIMINAR TODAS las notificaciones del sistema? Esta acción no se puede deshacer.')) {
            return;
        }

        setLoading(true);
        setErrorMsg('');
        setSuccessMsg('');

        try {
            const response = await api.delete('/notifications/reset');
            setSuccessMsg(response.message || 'Notificaciones eliminadas correctamente.');
        } catch (error) {
            console.error('Error resetting notifications:', error);
            setErrorMsg('Error al intentar resetear las notificaciones.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <div>
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'} flex items-center gap-2`}>
                        <BellOff className="text-red-500" /> Reset de Notificaciones
                    </h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>Herramienta administrativa para limpiar el historial de notificaciones.</p>
                </div>
            </div>

            <div className={`p-8 rounded-xl border flex flex-col items-center justify-center text-center gap-6 ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} shadow-lg`}>

                <div className="p-4 bg-red-100 dark:bg-red-900/30 rounded-full">
                    <AlertTriangle size={48} className="text-red-600 dark:text-red-400" />
                </div>

                <div className="max-w-md">
                    <h3 className={`text-xl font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Zona de Peligro</h3>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>
                        Esta acción eliminará <strong>todas</strong> las notificaciones de <strong>todos</strong> los usuarios del sistema.
                        Utilízalo solo cuando necesites hacer una limpieza general o por mantenimiento.
                    </p>
                </div>

                {successMsg && (
                    <div className="flex items-center gap-2 text-green-600 bg-green-100 px-4 py-2 rounded-lg">
                        <CheckCircle size={20} />
                        <span>{successMsg}</span>
                    </div>
                )}

                {errorMsg && (
                    <div className="text-red-600 bg-red-100 px-4 py-2 rounded-lg">
                        {errorMsg}
                    </div>
                )}

                <button
                    onClick={handleReset}
                    disabled={loading}
                    className={`flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-white transition-colors
                        ${loading ? 'bg-gray-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 shadow-red-500/30 shadow-lg'}`}
                >
                    {loading ? 'Procesando...' : (
                        <>
                            <Trash2 size={20} />
                            Resetear Todas las Notificaciones
                        </>
                    )}
                </button>
            </div>
        </div>
    );
};

export default ResetNotificacionesPage;
