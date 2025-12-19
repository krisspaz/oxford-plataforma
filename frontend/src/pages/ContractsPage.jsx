import React, { useState } from 'react';
import { FileText, Download } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';

import { contractService } from '../services';

const ContractsPage = () => {
    const { darkMode } = useTheme();
    const [activeTab, setActiveTab] = useState('all'); // all, pending, signed
    const [contracts, setContracts] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadContracts();
    }, []);

    const loadContracts = async () => {
        setLoading(true);
        const { success, data } = await contractService.getAll();
        if (success && Array.isArray(data)) {
            setContracts(data);
        }
        setLoading(false);
    };

    const handleGenerate = async () => {
        const studentId = prompt("Ingrese ID del estudiante (Simulación)");
        if (!studentId) return;

        const { success, message } = await contractService.generate({ studentId, cycle: '2025' });
        if (success) {
            alert("Contrato generado exitosamente");
            loadContracts();
        } else {
            alert("Error al generar contrato: " + message);
        }
    };

    const filtered = activeTab === 'all'
        ? contracts
        : contracts.filter(c => activeTab === 'pending' ? c.status === 'PENDING' : c.status === 'SIGNED');

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Gestión de Contratos</h1>
                <button
                    onClick={handleGenerate}
                    className="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 flex items-center gap-2">
                    <FileText size={18} /> Generar Contrato
                </button>
            </div>

            {/* Tabs */}
            <div className="flex gap-4 border-b dark:border-gray-700 mb-6">
                <button
                    onClick={() => setActiveTab('all')}
                    className={`pb-2 px-4 font-medium ${activeTab === 'all' ? 'text-teal-600 border-b-2 border-teal-600' : 'text-gray-500'}`}
                >
                    Todos
                </button>
                <button
                    onClick={() => setActiveTab('pending')}
                    className={`pb-2 px-4 font-medium ${activeTab === 'pending' ? 'text-teal-600 border-b-2 border-teal-600' : 'text-gray-500'}`}
                >
                    Pendientes
                </button>
                <button
                    onClick={() => setActiveTab('signed')}
                    className={`pb-2 px-4 font-medium ${activeTab === 'signed' ? 'text-teal-600 border-b-2 border-teal-600' : 'text-gray-500'}`}
                >
                    Firmados
                </button>
            </div>

            <div className="grid gap-4">
                {filtered.map(contract => (
                    <div key={contract.id} className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white'} p-4 rounded-xl shadow-sm border flex justify-between items-center transition-all hover:shadow-md`}>
                        <div className="flex items-center gap-4">
                            <div className={`p-3 ${darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} rounded-xl text-blue-500`}>
                                <FileText size={24} />
                            </div>
                            <div>
                                <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{contract.student}</h3>
                                <div className="flex items-center gap-3 mt-1">
                                    <span className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Ciclo {contract.cycle}</span>
                                    <span className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>• {contract.date}</span>
                                </div>
                            </div>
                        </div>
                        <div className="flex items-center gap-4">
                            <span className={`px-3 py-1 text-xs font-semibold rounded-full ${contract.status === 'SIGNED'
                                ? 'bg-green-100 text-green-700 border border-green-200'
                                : 'bg-yellow-100 text-yellow-700 border border-yellow-200'
                                }`}>
                                {contract.status === 'SIGNED' ? 'Firmado' : 'Pendiente de Firma'}
                            </span>
                            <button className={`p-2 rounded-lg transition-colors ${darkMode ? 'text-gray-400 hover:bg-gray-700 hover:text-white' : 'text-gray-400 hover:bg-gray-100 hover:text-gray-900'}`} title="Descargar PDF">
                                <Download size={20} />
                            </button>
                        </div>
                    </div>
                ))}

                {filtered.length === 0 && (
                    <div className="p-12 text-center text-gray-400">
                        No se encontraron contratos en esta sección.
                    </div>
                )}
            </div>
        </div>
    );
};

export default ContractsPage;
