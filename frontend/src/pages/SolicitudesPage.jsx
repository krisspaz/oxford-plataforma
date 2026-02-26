import { toast } from '../utils/toast';
import { useState, useEffect, useMemo } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import { useAuth } from '../contexts/AuthContext';
import { requestService } from '../services';

const SolicitudesPage = () => {
    const { darkMode } = useTheme();
    const { user } = useAuth();
    const [filter, setFilter] = useState('pendiente');
    const [showDetailModal, setShowDetailModal] = useState(false);
    const [selectedRequest, setSelectedRequest] = useState(null);
    const [loading, setLoading] = useState(true);
    const [actionLoading, setActionLoading] = useState(null);
    const [requests, setRequests] = useState([]);

    // Determine user role for authorization
    const userRole = useMemo(() => {
        if (!user?.roles) return null;
        if (user.roles.includes('ROLE_SUPER_ADMIN') || user.roles.includes('ROLE_ADMIN')) return 'ADMIN';
        if (user.roles.includes('ROLE_ACCOUNTANT') || user.roles.includes('ROLE_CONTABILIDAD')) return 'CONTABILIDAD';
        return 'OTHER';
    }, [user?.roles]);

    // Status workflow for SAT cancellations (dual authorization)
    const STATUS_WORKFLOW = {
        'PENDIENTE_CONTABILIDAD': { label: 'Pendiente Contabilidad', color: 'yellow', next: 'PENDIENTE_ADMIN', approver: 'CONTABILIDAD' },
        'PENDIENTE_ADMIN': { label: 'Pendiente Admin', color: 'orange', next: 'ANULADO', approver: 'ADMIN' },
        'PENDIENTE': { label: 'Pendiente', color: 'yellow', next: 'APROBADA', approver: 'ADMIN' },
        'APROBADA': { label: 'Aprobada', color: 'green', next: null, approver: null },
        'ANULADO': { label: 'Anulado', color: 'green', next: null, approver: null },
        'RECHAZADA': { label: 'Rechazada', color: 'red', next: null, approver: null },
    };

    useEffect(() => {
        loadRequests();
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [filter]);

    const loadRequests = async () => {
        setLoading(true);
        try {
            let response;
            if (filter === 'pendiente') {
                response = await requestService.getPending();
            } else if (filter === 'procesadas') {
                response = await requestService.getAll({ status: 'PROCESSED' });
            } else {
                response = await requestService.getAll();
            }

            // Handle API response structure
            if (response.success) {
                setRequests(response.data);
            } else if (Array.isArray(response)) {
                setRequests(response);
            } else if (response.data && Array.isArray(response.data)) {
                setRequests(response.data);
            }
        } catch (error) {
            console.error('Error loading requests:', error);
        } finally {
            setLoading(false);
        }
    };

    const getTypeLabel = (type) => {
        switch (type) {
            case 'ANULACION_FACTURA': return { label: 'Anulación Factura SAT', color: 'red', isSAT: true };
            case 'ANULACION_RECIBO': return { label: 'Anulación Recibo SAT', color: 'orange', isSAT: true };
            case 'HABILITACION_NOTAS': return { label: 'Habilitación Notas', color: 'blue', isSAT: false };
            default: return { label: type, color: 'gray', isSAT: false };
        }
    };

    // Check if current user can approve this request
    const canApprove = (request) => {
        const statusInfo = STATUS_WORKFLOW[request.status];
        if (!statusInfo?.approver) return false;
        return userRole === statusInfo.approver || userRole === 'ADMIN';
    };

    const handleAction = async (id, action) => {
        setActionLoading(id);
        try {
            const request = requests.find(r => r.id === id);
            const typeInfo = getTypeLabel(request?.type);
            const statusInfo = STATUS_WORKFLOW[request?.status];

            let response;
            if (action === 'APROBADA') {
                // For SAT cancellations, move to next step in workflow
                if (typeInfo?.isSAT && statusInfo?.next) {
                    response = await requestService.approve(id, { nextStatus: statusInfo.next });
                } else {
                    response = await requestService.approve(id);
                }
            } else {
                response = await requestService.reject(id, 'Rechazado por administración');
            }

            if (response.success) {
                const newStatus = typeInfo?.isSAT && statusInfo?.next && action === 'APROBADA'
                    ? statusInfo.next
                    : action;

                setRequests(prev => prev.map(r => r.id === id ? { ...r, status: newStatus } : r));

                // If filter is 'pendiente' and it's no longer pending, remove from view
                if (filter === 'pendiente' && (newStatus === 'APROBADA' || newStatus === 'ANULADO' || newStatus === 'RECHAZADA')) {
                    setRequests(prev => prev.filter(r => r.id !== id));
                }
                setShowDetailModal(false);

                // Show appropriate message
                if (typeInfo?.isSAT && newStatus === 'PENDIENTE_ADMIN') {
                    toast.success('Aprobado por Contabilidad. Pendiente aprobación de Admin.');
                } else if (newStatus === 'ANULADO' || newStatus === 'APROBADA') {
                    toast.success('Solicitud procesada exitosamente.');
                }
            }
        } catch (error) {
            console.error('Error processing request:', error);
            toast.error('Error al procesar la solicitud');
        } finally {
            setActionLoading(null);
        }
    };

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando solicitudes...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Solicitudes</h1>
                <div className="flex items-center gap-2">
                    <span className="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">
                        {requests.filter(r => r.status === 'PENDIENTE').length} pendientes
                    </span>
                </div>
            </div>

            {/* Filters */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm flex gap-2`}>
                <button onClick={() => setFilter('pendiente')} className={`px-4 py-2 rounded-lg font-medium ${filter === 'pendiente' ? 'bg-teal-600 text-white' : darkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}>
                    <Clock size={16} className="inline mr-2" />Pendientes
                </button>
                <button onClick={() => setFilter('procesadas')} className={`px-4 py-2 rounded-lg font-medium ${filter === 'procesadas' ? 'bg-teal-600 text-white' : darkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}>
                    <Check size={16} className="inline mr-2" />Procesadas
                </button>
                <button onClick={() => setFilter('todas')} className={`px-4 py-2 rounded-lg font-medium ${filter === 'todas' ? 'bg-teal-600 text-white' : darkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}>
                    <FileText size={16} className="inline mr-2" />Todas
                </button>
            </div>

            {/* Requests List */}
            <div className="space-y-3">
                {requests.map(request => {
                    const typeInfo = getTypeLabel(request.type);
                    return (
                        <div key={request.id} className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm p-4`}>
                            <div className="flex items-start justify-between">
                                <div className="flex items-start gap-4">
                                    <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${request.status === 'PENDIENTE' || request.status === 'PENDIENTE_CONTABILIDAD' ? 'bg-yellow-100 text-yellow-600' :
                                            request.status === 'PENDIENTE_ADMIN' ? 'bg-orange-100 text-orange-600' :
                                                request.status === 'APROBADA' || request.status === 'ANULADO' ? 'bg-green-100 text-green-600' :
                                                    'bg-red-100 text-red-600'
                                        }`}>
                                        {request.status === 'PENDIENTE' || request.status === 'PENDIENTE_CONTABILIDAD' ? <Clock size={20} /> :
                                            request.status === 'PENDIENTE_ADMIN' ? <Shield size={20} /> :
                                                request.status === 'APROBADA' || request.status === 'ANULADO' ? <Check size={20} /> : <X size={20} />}
                                    </div>
                                    <div>
                                        <div className="flex items-center gap-2 mb-1">
                                            <span className={`px-2 py-0.5 rounded text-xs font-medium ${typeInfo.color === 'red' ? 'bg-red-100 text-red-700' :
                                                typeInfo.color === 'orange' ? 'bg-orange-100 text-orange-700' :
                                                    'bg-blue-100 text-blue-700'
                                                }`}>{typeInfo.label}</span>
                                            <span className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{request.document}</span>
                                            {typeInfo.isSAT && (
                                                <span className="px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700">
                                                    Doble Autorización
                                                </span>
                                            )}
                                        </div>
                                        <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                            Estudiante: {request.student} • Solicitado por: {request.requestedBy}
                                        </p>
                                        {/* Status Workflow Indicator for SAT */}
                                        {typeInfo.isSAT && (
                                            <div className="flex items-center gap-2 mt-2">
                                                <div className={`flex items-center gap-1 px-2 py-1 rounded text-xs ${request.status === 'PENDIENTE_CONTABILIDAD' || request.status === 'PENDIENTE_ADMIN' || request.status === 'ANULADO'
                                                        ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'
                                                    }`}>
                                                    <UserCheck size={12} /> Contabilidad
                                                </div>
                                                <ChevronRight size={12} className="text-gray-400" />
                                                <div className={`flex items-center gap-1 px-2 py-1 rounded text-xs ${request.status === 'PENDIENTE_ADMIN' || request.status === 'ANULADO'
                                                        ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'
                                                    }`}>
                                                    <Shield size={12} /> Admin
                                                </div>
                                                <ChevronRight size={12} className="text-gray-400" />
                                                <div className={`flex items-center gap-1 px-2 py-1 rounded text-xs ${request.status === 'ANULADO'
                                                        ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'
                                                    }`}>
                                                    <Check size={12} /> Anulado
                                                </div>
                                            </div>
                                        )}
                                        <p className={`text-sm ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>{request.date}</p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-2">
                                    {canApprove(request) && request.status !== 'APROBADA' && request.status !== 'ANULADO' && request.status !== 'RECHAZADA' && (
                                        <>
                                            <button
                                                onClick={() => handleAction(request.id, 'APROBADA')}
                                                disabled={actionLoading === request.id}
                                                className="p-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 flex items-center gap-1"
                                                title={getTypeLabel(request.type).isSAT ? 'Aprobar (siguiente paso)' : 'Aprobar'}
                                            >
                                                {actionLoading === request.id ? <RefreshCw size={18} className="animate-spin" /> : <Check size={18} />}
                                            </button>
                                            <button
                                                onClick={() => handleAction(request.id, 'RECHAZADA')}
                                                disabled={actionLoading === request.id}
                                                className="p-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200"
                                                title="Rechazar"
                                            >
                                                <X size={18} />
                                            </button>
                                        </>
                                    )}
                                    {!canApprove(request) && request.status !== 'APROBADA' && request.status !== 'ANULADO' && request.status !== 'RECHAZADA' && (
                                        <span className={`text-xs px-2 py-1 rounded ${darkMode ? 'bg-gray-700 text-gray-400' : 'bg-gray-100 text-gray-500'}`}>
                                            {STATUS_WORKFLOW[request.status]?.approver === 'CONTABILIDAD' ? 'Requiere Contabilidad' : 'Requiere Admin'}
                                        </span>
                                    )}
                                    <button onClick={() => { setSelectedRequest(request); setShowDetailModal(true); }} className={`p-2 rounded-lg ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100'}`}>
                                        <Eye size={18} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                                    </button>
                                </div>
                            </div>
                            <div className={`mt-3 p-3 rounded-lg ${darkMode ? 'bg-gray-700' : 'bg-gray-50'}`}>
                                <p className={`text-sm ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                                    <strong>Motivo:</strong> {request.reason}
                                </p>
                            </div>
                        </div>
                    );
                })}

                {requests.length === 0 && (
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                        <Check size={48} className={`mx-auto mb-4 ${darkMode ? 'text-green-500' : 'text-green-400'}`} />
                        <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>No hay solicitudes {filter === 'pendiente' ? 'pendientes' : ''}</p>
                    </div>
                )}
            </div>

            {/* Detail Modal */}
            {showDetailModal && selectedRequest && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-md p-6`}>
                        <div className="flex items-center justify-between mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Detalle de Solicitud</h2>
                            <button onClick={() => setShowDetailModal(false)}><X size={20} /></button>
                        </div>
                        <div className={`space-y-3 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                            <div><strong>Tipo:</strong> {getTypeLabel(selectedRequest.type).label}</div>
                            <div><strong>Documento:</strong> {selectedRequest.document}</div>
                            <div><strong>Estudiante:</strong> {selectedRequest.student}</div>
                            <div><strong>Solicitado por:</strong> {selectedRequest.requestedBy}</div>
                            <div><strong>Fecha:</strong> {selectedRequest.date}</div>
                            <div><strong>Motivo:</strong> {selectedRequest.reason}</div>
                            <div><strong>Estado:</strong> <span className={`px-2 py-1 rounded text-xs ${selectedRequest.status === 'PENDIENTE' ? 'bg-yellow-100 text-yellow-700' :
                                selectedRequest.status === 'APROBADA' ? 'bg-green-100 text-green-700' :
                                    'bg-red-100 text-red-700'
                                }`}>{selectedRequest.status}</span></div>
                        </div>
                        {selectedRequest.status === 'PENDIENTE' && (
                            <div className="flex gap-3 mt-6">
                                <button onClick={() => handleAction(selectedRequest.id, 'APROBADA')} disabled={actionLoading} className="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg disabled:opacity-50">
                                    {actionLoading ? 'Procesando...' : 'Aprobar'}
                                </button>
                                <button onClick={() => handleAction(selectedRequest.id, 'RECHAZADA')} disabled={actionLoading} className="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg disabled:opacity-50">
                                    Rechazar
                                </button>
                            </div>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
};

export default SolicitudesPage;
