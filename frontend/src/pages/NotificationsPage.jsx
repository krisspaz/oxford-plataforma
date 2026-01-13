import React, { useState, useEffect } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import { Bell, Filter, Trash2, Check, ChevronLeft, X } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

const INITIAL_NOTIFICATIONS = [
    {
        id: 1,
        type: 'grade',
        title: 'Nueva calificación',
        message: 'Se registró tu nota en Matemáticas: 85',
        fullMessage: 'Se ha registrado una nueva calificación en la materia de Matemáticas. Tu nota es de 85 puntos sobre 100. Esta calificación corresponde al examen parcial del primer bimestre. Si tienes alguna duda, puedes contactar a tu profesor.',
        time: '5 minutos',
        date: '2025-01-06',
        read: false,
        icon: '📊'
    },
    {
        id: 2,
        type: 'payment',
        title: 'Pago pendiente',
        message: 'Tienes una cuota pendiente de Enero',
        fullMessage: 'Tienes una cuota pendiente correspondiente al mes de Enero 2025. El monto es de Q750.00. Por favor, realiza tu pago antes del día 15 para evitar recargos.',
        time: '1 hora',
        date: '2025-01-06',
        read: false,
        icon: '💰'
    },
    {
        id: 3,
        type: 'message',
        title: 'Nuevo mensaje',
        message: 'Coordinación te envió un mensaje',
        fullMessage: 'Mensaje de Coordinación Académica:\n\nEstimado estudiante,\n\nLe recordamos que la próxima semana iniciarán las evaluaciones del primer bimestre.\n\nAtentamente,\nCoordinación Académica',
        time: '2 horas',
        date: '2025-01-06',
        read: false,
        icon: '💬'
    },
    {
        id: 4,
        type: 'task',
        title: 'Tarea asignada',
        message: 'Nueva tarea: Ejercicios de Física Cap. 5',
        fullMessage: 'Se te ha asignado una nueva tarea:\n\n📚 Materia: Física\n📝 Descripción: Ejercicios del Capítulo 5\n📅 Fecha de entrega: 20 de Enero 2025\n⭐ Puntos: 15 pts',
        time: '1 día',
        date: '2025-01-05',
        read: true,
        icon: '📝'
    },
    {
        id: 5,
        type: 'event',
        title: 'Evento próximo',
        message: 'Reunión de padres mañana a las 4:00 PM',
        fullMessage: 'Recordatorio de evento:\n\n📅 Reunión de Padres de Familia\n🕓 Hora: 4:00 PM\n📍 Lugar: Salón de Usos Múltiples',
        time: '1 día',
        date: '2025-01-05',
        read: true,
        icon: '📅'
    },
    {
        id: 6,
        type: 'grade',
        title: 'Calificación actualizada',
        message: 'Se actualizó tu nota en Física: 92',
        fullMessage: 'Tu calificación en Física ha sido actualizada a 92 puntos.',
        time: '2 días',
        date: '2025-01-04',
        read: true,
        icon: '📊'
    },
];

const NotificationsPage = () => {
    const { darkMode } = useTheme();
    const navigate = useNavigate();
    const [filter, setFilter] = useState('all');
    const [selectedNotification, setSelectedNotification] = useState(null);

    // Load notifications from localStorage or use initial data
    const [notifications, setNotifications] = useState(() => {
        const saved = localStorage.getItem('oxford_notifications');
        return saved ? JSON.parse(saved) : INITIAL_NOTIFICATIONS;
    });

    // Persist to localStorage whenever notifications change
    useEffect(() => {
        localStorage.setItem('oxford_notifications', JSON.stringify(notifications));
    }, [notifications]);

    const typeColors = {
        grade: 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
        payment: 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400',
        message: 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
        task: 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
        event: 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400',
    };

    const typeLabels = {
        all: 'Todas',
        grade: 'Calificaciones',
        payment: 'Pagos',
        message: 'Mensajes',
        task: 'Tareas',
        event: 'Eventos',
    };

    const filteredNotifications = filter === 'all'
        ? notifications
        : notifications.filter(n => n.type === filter);

    const unreadCount = notifications.filter(n => !n.read).length;

    const handleMarkAsRead = (id) => {
        setNotifications(prev => prev.map(n =>
            n.id === id ? { ...n, read: true } : n
        ));
    };

    const handleMarkAllAsRead = () => {
        setNotifications(prev => prev.map(n => ({ ...n, read: true })));
    };

    const handleDelete = (id) => {
        setNotifications(prev => prev.filter(n => n.id !== id));
        if (selectedNotification?.id === id) {
            setSelectedNotification(null);
        }
    };

    const handleClearAll = () => {
        if (confirm('¿Estás seguro de eliminar todas las notificaciones?')) {
            setNotifications([]);
            setSelectedNotification(null);
        }
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-4">
                    <button
                        onClick={() => navigate(-1)}
                        className={`p-2 rounded-lg ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100'}`}
                    >
                        <ChevronLeft size={24} className={darkMode ? 'text-gray-400' : 'text-gray-600'} />
                    </button>
                    <div>
                        <h1 className={`text-2xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                            <Bell className="text-obs-pink" /> Notificaciones
                        </h1>
                        <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>
                            {unreadCount > 0 ? `${unreadCount} sin leer` : 'Todas leídas'}
                        </p>
                    </div>
                </div>
                <div className="flex gap-2">
                    {unreadCount > 0 && (
                        <button
                            onClick={handleMarkAllAsRead}
                            className={`px-4 py-2 rounded-lg flex items-center gap-2 ${darkMode ? 'bg-gray-700 hover:bg-gray-600 text-gray-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'}`}
                        >
                            <Check size={16} /> Marcar todas como leídas
                        </button>
                    )}
                    {notifications.length > 0 && (
                        <button
                            onClick={handleClearAll}
                            className={`px-4 py-2 rounded-lg flex items-center gap-2 ${darkMode ? 'bg-red-900/30 hover:bg-red-900/50 text-red-400' : 'bg-red-100 hover:bg-red-200 text-red-600'}`}
                        >
                            <Trash2 size={16} /> Limpiar todas
                        </button>
                    )}
                </div>
            </div>

            {/* Filter Tabs */}
            <div className={`flex gap-2 p-1 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-gray-100'}`}>
                {Object.entries(typeLabels).map(([key, label]) => (
                    <button
                        key={key}
                        onClick={() => setFilter(key)}
                        className={`px-4 py-2 rounded-lg text-sm font-medium transition-all ${filter === key
                            ? 'bg-gradient-to-r from-obs-pink to-obs-purple text-white shadow-lg'
                            : darkMode ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-900'
                            }`}
                    >
                        {label}
                    </button>
                ))}
            </div>

            {/* Content */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Notifications List */}
                <div className={`lg:col-span-2 ${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-2xl shadow-lg overflow-hidden`}>
                    {filteredNotifications.length === 0 ? (
                        <div className="p-12 text-center">
                            <Bell size={48} className={`mx-auto ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                            <p className={`mt-4 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                No hay notificaciones {filter !== 'all' && `de tipo "${typeLabels[filter]}"`}
                            </p>
                        </div>
                    ) : (
                        <div className="divide-y divide-gray-200 dark:divide-gray-700">
                            {filteredNotifications.map((notification) => (
                                <div
                                    key={notification.id}
                                    onClick={() => {
                                        handleMarkAsRead(notification.id);
                                        setSelectedNotification(notification);
                                    }}
                                    className={`p-4 cursor-pointer transition-colors ${selectedNotification?.id === notification.id
                                        ? darkMode ? 'bg-obs-pink/10' : 'bg-obs-pink/5'
                                        : !notification.read
                                            ? darkMode ? 'bg-blue-900/10' : 'bg-blue-50'
                                            : darkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'
                                        }`}
                                >
                                    <div className="flex gap-4">
                                        <div className={`w-12 h-12 rounded-full flex items-center justify-center text-xl flex-shrink-0 ${typeColors[notification.type]}`}>
                                            {notification.icon}
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <div className="flex items-center justify-between">
                                                <p className={`font-semibold ${!notification.read ? (darkMode ? 'text-white' : 'text-gray-900') : (darkMode ? 'text-gray-300' : 'text-gray-700')}`}>
                                                    {notification.title}
                                                </p>
                                                <div className="flex items-center gap-2">
                                                    {!notification.read && (
                                                        <span className="w-2 h-2 bg-obs-pink rounded-full" />
                                                    )}
                                                    <button
                                                        onClick={(e) => {
                                                            e.stopPropagation();
                                                            handleDelete(notification.id);
                                                        }}
                                                        className={`p-1 rounded hover:bg-red-100 dark:hover:bg-red-900/30`}
                                                    >
                                                        <X size={14} className="text-red-500" />
                                                    </button>
                                                </div>
                                            </div>
                                            <p className={`text-sm truncate ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                                {notification.message}
                                            </p>
                                            <p className={`text-xs mt-1 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                                                {notification.time} • {notification.date}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                {/* Detail Panel */}
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-2xl shadow-lg overflow-hidden`}>
                    {selectedNotification ? (
                        <>
                            <div className={`p-4 ${typeColors[selectedNotification.type]}`}>
                                <div className="flex items-center gap-3">
                                    <div className="w-12 h-12 rounded-full bg-white/30 flex items-center justify-center text-2xl">
                                        {selectedNotification.icon}
                                    </div>
                                    <div>
                                        <h3 className="font-bold text-lg">{selectedNotification.title}</h3>
                                        <p className="text-sm opacity-75">{selectedNotification.time}</p>
                                    </div>
                                </div>
                            </div>
                            <div className="p-6">
                                <p className={`whitespace-pre-line leading-relaxed ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                    {selectedNotification.fullMessage || selectedNotification.message}
                                </p>
                            </div>
                            <div className={`p-4 border-t ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                                <button
                                    onClick={() => handleDelete(selectedNotification.id)}
                                    className={`w-full px-4 py-2 rounded-lg flex items-center justify-center gap-2 ${darkMode ? 'bg-red-900/30 hover:bg-red-900/50 text-red-400' : 'bg-red-100 hover:bg-red-200 text-red-600'}`}
                                >
                                    <Trash2 size={16} /> Eliminar notificación
                                </button>
                            </div>
                        </>
                    ) : (
                        <div className="p-12 text-center">
                            <Bell size={48} className={`mx-auto ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                            <p className={`mt-4 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                Selecciona una notificación para ver los detalles
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default NotificationsPage;
