import React, { useState } from 'react';

const NotasFinalesPage = () => {
    const [year, setYear] = useState(new Date().getFullYear());

    return (
        <div className="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
            <h1 className="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Notas Finales del Ciclo</h1>

            <div className="flex gap-4 mb-6">
                <select
                    value={year}
                    onChange={(e) => setYear(e.target.value)}
                    className="p-2 border rounded"
                >
                    <option value="2026">2026</option>
                    <option value="2025">2025</option>
                </select>
                <button className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Consultar
                </button>
            </div>

            <div className="bg-blue-50 p-4 rounded text-blue-800">
                Seleccione un ciclo y sección para calcular promedios finales y generar actas.
            </div>

            {/* Placeholder for Data Grid */}
            <div className="mt-8 border-2 border-dashed border-gray-300 p-12 text-center text-gray-500">
                La tabla de resultados aparecerá aquí.
            </div>
        </div>
    );
};

export default NotasFinalesPage;
