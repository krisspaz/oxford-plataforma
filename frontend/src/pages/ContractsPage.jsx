import React, { useState } from 'react';
import { FileText, Download } from 'lucide-react';

const ContractsPage = () => {
    const contracts = [
        { id: 1, student: 'Estudiante 1', cycle: '2025', status: 'SIGNED' },
        { id: 2, student: 'Estudiante 2', cycle: '2025', status: 'PENDING' },
    ];

    return (
        <div className="p-6">
            <h1 className="text-2xl font-bold mb-6 text-gray-800">Gestión de Contratos</h1>
            <div className="grid gap-4">
                {contracts.map(contract => (
                    <div key={contract.id} className="bg-white p-4 rounded-lg shadow flex justify-between items-center">
                        <div className="flex items-center gap-4">
                            <div className="p-3 bg-blue-100 rounded-lg text-blue-600">
                                <FileText size={24} />
                            </div>
                            <div>
                                <h3 className="font-bold">{contract.student}</h3>
                                <p className="text-sm text-gray-500">Ciclo {contract.cycle}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-4">
                            <span className={`px-2 py-1 text-xs rounded-full ${contract.status === 'SIGNED' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`}>
                                {contract.status === 'SIGNED' ? 'Firmado' : 'Pendiente'}
                            </span>
                            <button className="p-2 text-gray-500 hover:text-blue-600">
                                <Download size={20} />
                            </button>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default ContractsPage;
