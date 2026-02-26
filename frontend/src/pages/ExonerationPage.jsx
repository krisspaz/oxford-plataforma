import { toast } from '../utils/toast';
import { useState } from 'react';
import { useTheme } from '../contexts/ThemeContext';

const ExonerationPage = () => {
    const { darkMode } = useTheme();
    const [requests, setRequests] = useState([
        { id: 1, student: 'Juan Perez', amount: 500, reason: 'Baja del colegio', status: 'PENDING' },
        { id: 2, student: 'Maria Lopez', amount: 300, reason: 'Error en cobro', status: 'APPROVED' },
    ]);

    const handleApprove = (id) => {
        setRequests(requests.map(r => r.id === id ? { ...r, status: 'APPROVED' } : r));
        toast.info('Solicitud Aprobada');
    };

    return (
        <div className="p-6">
            <h1 className={`text-2xl font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Solicitudes de Exoneración</h1>
            <p className={`mb-6 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                Este módulo gestiona las solicitudes de baja enviadas por Secretaría. Aquí podrá autorizar la anulación de saldos pendientes cuando un estudiante se retira.
                <br /><span className="text-sm italic opacity-80">Nota: El listado aparecerá vacío hasta que se genere una nueva solicitud.</span>
            </p>
            <div className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white'} rounded-xl shadow-sm overflow-hidden border`}>
                {requests.length === 0 ? (
                    <div className="p-12 text-center">
                        <div className={`p-4 rounded-full bg-gray-100 dark:bg-gray-700 inline-flex mb-4`}>
                            <svg className="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 className={`text-lg font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>No hay solicitudes</h3>
                        <p className={`mt-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>No hay solicitudes de exoneración pendientes en este momento.</p>
                    </div>
                ) : (
                    <table className="w-full">
                        <thead className={`${darkMode ? 'bg-gray-700 border-gray-600' : 'bg-gray-50'} border-b`}>
                            <tr>
                                <th className={`px-6 py-3 text-left text-xs font-medium ${darkMode ? 'text-gray-300' : 'text-gray-500'} uppercase tracking-wider`}>Estudiante</th>
                                <th className={`px-6 py-3 text-left text-xs font-medium ${darkMode ? 'text-gray-300' : 'text-gray-500'} uppercase tracking-wider`}>Motivo</th>
                                <th className={`px-6 py-3 text-left text-xs font-medium ${darkMode ? 'text-gray-300' : 'text-gray-500'} uppercase tracking-wider`}>Monto</th>
                                <th className={`px-6 py-3 text-left text-xs font-medium ${darkMode ? 'text-gray-300' : 'text-gray-500'} uppercase tracking-wider`}>Estado</th>
                                <th className={`px-6 py-3 text-left text-xs font-medium ${darkMode ? 'text-gray-300' : 'text-gray-500'} uppercase tracking-wider`}>Acciones</th>
                            </tr>
                        </thead>
                        <tbody className={`${darkMode ? 'bg-gray-800' : 'bg-white'} divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-200'}`}>
                            {requests.map((req) => (
                                <tr key={req.id} className={darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}>
                                    <td className={`px-6 py-4 whitespace-nowrap ${darkMode ? 'text-white' : 'text-gray-900'}`}>{req.student}</td>
                                    <td className={`px-6 py-4 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>{req.reason}</td>
                                    <td className={`px-6 py-4 ${darkMode ? 'text-green-400' : 'text-green-600'} font-medium`}>Q {req.amount}</td>
                                    <td className="px-6 py-4">
                                        <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${req.status === 'APPROVED' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}`}>
                                            {req.status}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        {req.status === 'PENDING' && (
                                            <button onClick={() => handleApprove(req.id)} className="text-teal-500 hover:text-teal-400">Aprobar</button>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </div>
        </div>
    );
};

export default ExonerationPage;
