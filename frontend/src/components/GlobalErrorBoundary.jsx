import React from 'react';
import { useRouteError } from 'react-router-dom';
import { AlertTriangle, Home, RefreshCw } from 'lucide-react';

const GlobalErrorBoundary = () => {
    const error = useRouteError();
    console.error("Global Error Caught:", error);

    return (
        <div className="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-4">
            <div className="bg-white rounded-2xl shadow-xl max-w-lg w-full p-8 text-center">
                <div className="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <AlertTriangle size={32} />
                </div>

                <h1 className="text-2xl font-bold text-gray-900 mb-2">Algo salió mal</h1>
                <p className="text-gray-600 mb-6">
                    Hemos encontrado un error inesperado. Nuestro equipo ha sido notificado.
                </p>

                <div className="bg-gray-100 rounded-lg p-4 text-left font-mono text-xs text-red-600 mb-6 overflow-auto max-h-48">
                    {error?.message || error?.statusText || "Error desconocido"}
                </div>

                <div className="flex gap-3 justify-center">
                    <button
                        onClick={() => window.location.reload()}
                        className="flex items-center gap-2 px-5 py-2.5 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors font-medium"
                    >
                        <RefreshCw size={18} /> Reintentar
                    </button>
                    <button
                        onClick={() => window.location.href = '/'}
                        className="flex items-center gap-2 px-5 py-2.5 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors font-medium"
                    >
                        <Home size={18} /> Ir al Inicio
                    </button>
                </div>
            </div>
        </div>
    );
};

export default GlobalErrorBoundary;
