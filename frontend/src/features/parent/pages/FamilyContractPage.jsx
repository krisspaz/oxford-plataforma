import { useTheme } from '../../../contexts/ThemeContext';

const FamilyContractPage = () => {
    const { darkMode } = useTheme();

    const contracts = [
        { id: 1, name: 'Contrato de Servicios Educativos 2024', status: 'signed', date: '15/01/2024' },
        { id: 2, name: 'Normativa de Convivencia 2024', status: 'signed', date: '15/01/2024' },
    ];

    return (
        <div className={`min-h-screen p-8 transition-colors duration-300 ${darkMode ? 'bg-gray-900 text-white' : 'bg-gray-50 text-gray-800'}`}>
            <div className="max-w-4xl mx-auto">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold flex items-center gap-3">
                        <FileText className="text-teal-500" size={32} />
                        Documentación Familiar
                    </h1>
                    <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        Visualiza y descarga tus contratos firmados.
                    </p>
                </div>

                <div className={`rounded-xl overflow-hidden shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'}`}>
                    <div className="p-6 space-y-4">
                        {contracts.map(contract => (
                            <div key={contract.id} className={`flex items-center justify-between p-4 rounded-lg border ${darkMode ? 'bg-gray-700/50 border-gray-600' : 'bg-gray-50 border-gray-100'}`}>
                                <div className="flex items-center gap-4">
                                    <div className="p-3 bg-teal-100 text-teal-600 rounded-full">
                                        <FileText size={24} />
                                    </div>
                                    <div>
                                        <h3 className="font-bold text-lg">{contract.name}</h3>
                                        <div className="flex items-center gap-2 text-sm text-green-500 mt-1">
                                            <CheckCircle size={14} />
                                            <span className="font-medium">Firmado el {contract.date}</span>
                                        </div>
                                    </div>
                                </div>
                                <button className={`p-2 rounded-lg transition-colors ${darkMode ? 'hover:bg-gray-600 text-gray-300' : 'hover:bg-gray-200 text-gray-500'}`}>
                                    <Download size={20} />
                                </button>
                            </div>
                        ))}
                    </div>
                    <div className={`p-4 text-center text-sm border-t ${darkMode ? 'bg-gray-900/50 border-gray-700 text-gray-500' : 'bg-gray-50 border-gray-100 text-gray-400'}`}>
                        Para renovar contratos o actualizar datos, favor contactar a secretaría.
                    </div>
                </div>
            </div>
        </div>
    );
};

export default FamilyContractPage;
