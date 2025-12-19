import React from 'react';
import { Construction } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

const ComingSoon = () => {
    const navigate = useNavigate();

    return (
        <div className="flex flex-col items-center justify-center h-[80vh] bg-white rounded-2xl shadow-sm p-8 text-center">
            <div className="bg-teal-50 p-6 rounded-full mb-6">
                <Construction size={64} className="text-teal-600" />
            </div>
            <h1 className="text-3xl font-bold text-gray-800 mb-4">Próximamente</h1>
            <p className="text-gray-600 max-w-md mb-8">
                Estamos trabajando duro para traerte esta nueva funcionalidad.
                Esta página estará disponible en la próxima actualización del sistema.
            </p>
            <button
                onClick={() => navigate(-1)}
                className="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors font-medium"
            >
                Regresar
            </button>
        </div>
    );
};

export default ComingSoon;
