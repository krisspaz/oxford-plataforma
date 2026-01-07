import React, { useState, useEffect, useRef } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { X } from 'lucide-react';

/**
 * Centro de Notificaciones
 * Features:
 * - Bell icon con badge
 * - Dropdown con lista
 * - Tipos: calificaciones, pagos, mensajes, tareas
 * - Modal para ver detalles completos
 */
const NotificationCenter = () => {
    const { user } = useAuth();
    const navigate = useNavigate();
    const [notifications, setNotifications] = useState([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [isOpen, setIsOpen] = useState(false);
    const [selectedNotification, setSelectedNotification] = useState(null);
    const dropdownRef = useRef(null);

    // Mock notifications (conectar con backend real)
    useEffect(() => {
        const mockNotifications = [
            {
                id: 1,
                type: 'grade',
                title: 'Nueva calificación',
                message: 'Se registró tu nota en Matemáticas: 85',
                fullMessage: 'Se ha registrado una nueva calificación en la materia de Matemáticas. Tu nota es de 85 puntos sobre 100. Esta calificación corresponde al examen parcial del primer bimestre. Si tienes alguna duda, puedes contactar a tu profesor.',
                time: '5 minutos',
                read: false,
                icon: '📊'
            },
            {
                id: 2,
                type: 'payment',
                title: 'Pago pendiente',
                message: 'Tienes una cuota pendiente de Enero',
                fullMessage: 'Tienes una cuota pendiente correspondiente al mes de Enero 2025. El monto es de Q750.00. Por favor, realiza tu pago antes del día 15 para evitar recargos. Puedes pagar en caja o mediante transferencia bancaria.',
                time: '1 hora',
                read: false,
                icon: '💰'
            },
            {
                id: 3,
                type: 'message',
                title: 'Nuevo mensaje',
                message: 'Coordinación te envió un mensaje',
                fullMessage: 'Mensaje de Coordinación Académica:\n\nEstimado estudiante,\n\nLe recordamos que la próxima semana iniciarán las evaluaciones del primer bimestre. Asegúrese de revisar el calendario de exámenes publicado en el portal.\n\nAtentamente,\nCoordinación Académica',
                time: '2 horas',
                read: false,
                icon: '💬'
            },
            {
                id: 4,
                type: 'task',
                title: 'Tarea asignada',
                message: 'Nueva tarea: Ejercicios de Física Cap. 5',
                fullMessage: 'Se te ha asignado una nueva tarea:\n\n📚 Materia: Física\n📝 Descripción: Ejercicios del Capítulo 5 - Movimiento Rectilíneo Uniforme\n📅 Fecha de entrega: 20 de Enero 2025\n⭐ Puntos: 15 pts\n\nInstrucciones: Resolver los ejercicios 1-15 del libro de texto.',
                time: '1 día',
                read: true,
                icon: '📝'
            },
            {
                id: 5,
                type: 'event',
                title: 'Evento próximo',
                message: 'Reunión de padres mañana a las 4:00 PM',
                fullMessage: 'Recordatorio de evento:\n\n📅 Reunión de Padres de Familia\n🕓 Hora: 4:00 PM\n📍 Lugar: Salón de Usos Múltiples\n\nSe tratarán los siguientes temas:\n- Avance académico del primer bimestre\n- Calendario de actividades\n- Información sobre pagos\n\nEs importante su asistencia.',
                time: '1 día',
                read: true,
                icon: '📅'
            },
        ];

        setNotifications(mockNotifications);
        setUnreadCount(mockNotifications.filter(n => !n.read).length);
    }, []);

    // Close dropdown when clicking outside
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
                setIsOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const handleNotificationClick = (notification) => {
        // Mark as read
        if (!notification.read) {
            setNotifications(prev => prev.map(n =>
                n.id === notification.id ? { ...n, read: true } : n
            ));
            setUnreadCount(prev => Math.max(0, prev - 1));
        }
        // Open detail modal
        setSelectedNotification(notification);
        setIsOpen(false);
    };

    const handleMarkAllAsRead = () => {
        setNotifications(prev => prev.map(n => ({ ...n, read: true })));
        setUnreadCount(0);
    };

    const handleClearAll = () => {
        setNotifications([]);
        setUnreadCount(0);
    };

    const typeColors = {
        grade: 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
        payment: 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400',
        message: 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
        task: 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
        event: 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400',
    };

    return (
        <>
            <div className="relative" ref={dropdownRef}>
                {/* Bell Button */}
                <button
                    onClick={() => setIsOpen(!isOpen)}
                    className="relative p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors"
                    aria-label="Notificaciones"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>

                    {/* Badge */}
                    {unreadCount > 0 && (
                        <span className="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse">
                            {unreadCount > 9 ? '9+' : unreadCount}
                        </span>
                    )}
                </button>

                {/* Dropdown */}
                {isOpen && (
                    <div className="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">
                        {/* Header */}
                        <div className="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <h3 className="font-semibold text-gray-900 dark:text-white">
                                Notificaciones
                            </h3>
                            {unreadCount > 0 && (
                                <button
                                    onClick={handleMarkAllAsRead}
                                    className="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400"
                                >
                                    Marcar todas como leídas
                                </button>
                            )}
                        </div>

                        {/* Notifications List */}
                        <div className="max-h-96 overflow-y-auto">
                            {notifications.length === 0 ? (
                                <div className="p-8 text-center">
                                    <div className="text-4xl mb-2">🔔</div>
                                    <p className="text-gray-500 dark:text-gray-400">No tienes notificaciones</p>
                                </div>
                            ) : (
                                notifications.map((notification) => (
                                    <div
                                        key={notification.id}
                                        onClick={() => handleNotificationClick(notification)}
                                        className={`p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors ${!notification.read ? 'bg-blue-50/50 dark:bg-blue-900/10' : ''
                                            }`}
                                    >
                                        <div className="flex gap-3">
                                            <div className={`w-10 h-10 rounded-full flex items-center justify-center text-lg ${typeColors[notification.type]}`}>
                                                {notification.icon}
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <div className="flex items-center justify-between">
                                                    <p className={`font-medium text-sm ${!notification.read ? 'text-gray-900 dark:text-white' : 'text-gray-700 dark:text-gray-300'}`}>
                                                        {notification.title}
                                                    </p>
                                                    {!notification.read && (
                                                        <span className="w-2 h-2 bg-blue-500 rounded-full" />
                                                    )}
                                                </div>
                                                <p className="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                    {notification.message}
                                                </p>
                                                <p className="text-xs text-gray-400 mt-1">
                                                    {notification.time}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>

                        {/* Footer */}
                        {notifications.length > 0 && (
                            <div className="p-3 border-t border-gray-200 dark:border-gray-700 flex justify-between">
                                <button
                                    onClick={handleClearAll}
                                    className="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                                >
                                    Limpiar todas
                                </button>
                                <button
                                    onClick={() => { setIsOpen(false); navigate('/notificaciones'); }}
                                    className="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 font-medium"
                                >
                                    Ver todas →
                                </button>
                            </div>
                        )}
                    </div>
                )}
            </div>

            {/* Notification Detail Modal */}
            {selectedNotification && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-[100] p-4" onClick={() => setSelectedNotification(null)}>
                    <div
                        className="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
                        onClick={e => e.stopPropagation()}
                    >
                        {/* Modal Header */}
                        <div className={`p-4 flex items-center gap-3 ${typeColors[selectedNotification.type]}`}>
                            <div className="w-12 h-12 rounded-full bg-white/30 dark:bg-black/20 flex items-center justify-center text-2xl">
                                {selectedNotification.icon}
                            </div>
                            <div className="flex-1">
                                <h3 className="font-bold text-lg">{selectedNotification.title}</h3>
                                <p className="text-sm opacity-75">{selectedNotification.time}</p>
                            </div>
                            <button
                                onClick={() => setSelectedNotification(null)}
                                className="p-2 hover:bg-white/20 rounded-lg transition-colors"
                            >
                                <X size={20} />
                            </button>
                        </div>

                        {/* Modal Body */}
                        <div className="p-6">
                            <p className="text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">
                                {selectedNotification.fullMessage || selectedNotification.message}
                            </p>
                        </div>

                        {/* Modal Footer */}
                        <div className="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                            <button
                                onClick={() => setSelectedNotification(null)}
                                className="px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                            >
                                Cerrar
                            </button>
                            <button className="px-4 py-2 bg-gradient-to-r from-obs-pink to-obs-purple text-white rounded-lg hover:opacity-90 transition-opacity">
                                Ir a detalle
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default NotificationCenter;

