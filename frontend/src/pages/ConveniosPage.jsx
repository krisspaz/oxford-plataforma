import { useState } from 'react';
import { useTheme } from '../contexts/ThemeContext';

const ConveniosPage = () => {
    const { darkMode } = useTheme();
    // Mock Data
    const [convenios] = useState([
        { id: 1, name: 'Beca Parcial', discount: '25%', type: 'Porcentaje' },
        { id: 2, name: 'Descuento Hermanos', discount: '10%', type: 'Porcentaje' },
        { id: 3, name: 'Empleado', discount: '50%', type: 'Porcentaje' },
    ]);

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Catálogo de Convenios</h1>
                <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <Plus size={20} /> Nuevo Convenio
                </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {convenios.map(conv => (
                    <div key={conv.id} className={`p-6 rounded-xl border shadow-sm ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                        <div className="flex justify-between items-start mb-4">
                            <div className="p-3 bg-blue-100 text-blue-600 rounded-lg">
                                <Handshake size={24} />
                            </div>
                            <div className="flex gap-1">
                                <button className="p-1 text-gray-400 hover:text-blue-500"><Edit size={16} /></button>
                                <button className="p-1 text-gray-400 hover:text-red-500"><Trash size={16} /></button>
                            </div>
                        </div>
                        <h3 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{conv.name}</h3>
                        <div className="mt-2 flex justify-between items-center">
                            <span className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{conv.type}</span>
                            <span className="font-bold text-blue-600 text-lg">{conv.discount}</span>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default ConveniosPage;
