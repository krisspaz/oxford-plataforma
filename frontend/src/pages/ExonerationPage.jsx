import React, { useState, useEffect } from 'react';

const ExonerationPage = () => {
    // Stub data for now as we focused on Fixtures
    const [requests, setRequests] = useState([
        { id: 1, student: 'Juan Perez', amount: 500, reason: 'Baja del colegio', status: 'PENDING' },
        { id: 2, student: 'Maria Lopez', amount: 300, reason: 'Error en cobro', status: 'APPROVED' },
    ]);

    const handleApprove = (id) => {
        setRequests(requests.map(r => r.id === id ? { ...r, status: 'APPROVED' } : r));
        alert('Solicitud Aprobada');
    };

    return (
        <div className="p-6">
            <h1 className="text-2xl font-bold mb-6 text-gray-800">Solicitudes de Exoneración</h1>
            <div className="bg-white rounded-xl shadow-sm overflow-hidden">
                <table className="w-full">
                    <thead className="bg-gray-50 border-b">
                        <tr>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiante</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mootivo</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {requests.map((req) => (
                            <tr key={req.id}>
                                <td className="px-6 py-4 whitespace-nowrap">{req.student}</td>
                                <td className="px-6 py-4">{req.reason}</td>
                                <td className="px-6 py-4">Q {req.amount}</td>
                                <td className="px-6 py-4">
                                    <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${req.status === 'APPROVED' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}`}>
                                        {req.status}
                                    </span>
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    {req.status === 'PENDING' && (
                                        <button onClick={() => handleApprove(req.id)} className="text-indigo-600 hover:text-indigo-900">Aprobar</button>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default ExonerationPage;
