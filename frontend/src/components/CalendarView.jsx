import React, { useState, useEffect } from 'react';
import FullCalendar from '@fullcalendar/react';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import esLocale from '@fullcalendar/core/locales/es';

/**
 * Calendario Visual con FullCalendar
 * Features:
 * - Vista mes/semana/día
 * - Eventos escolares
 * - Exámenes, vacaciones, reuniones
 */
const CalendarView = () => {
    const [events, setEvents] = useState([]);
    const [selectedEvent, setSelectedEvent] = useState(null);

    // Mock events (conectar con backend real)
    useEffect(() => {
        const schoolEvents = [
            {
                id: '1',
                title: '📚 Examen de Matemáticas',
                start: new Date().toISOString().split('T')[0],
                end: new Date().toISOString().split('T')[0],
                backgroundColor: '#3b82f6',
                borderColor: '#3b82f6',
                extendedProps: { type: 'exam', description: 'Capítulos 1-5' }
            },
            {
                id: '2',
                title: '🎉 Día del Estudiante',
                start: new Date(Date.now() + 5 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                backgroundColor: '#10b981',
                borderColor: '#10b981',
                extendedProps: { type: 'event', description: 'Actividades recreativas' }
            },
            {
                id: '3',
                title: '👨‍👩‍👧 Reunión de Padres',
                start: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0] + 'T16:00:00',
                end: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0] + 'T18:00:00',
                backgroundColor: '#f59e0b',
                borderColor: '#f59e0b',
                extendedProps: { type: 'meeting', description: 'Salón de actos' }
            },
            {
                id: '4',
                title: '🏖️ Vacaciones de Semana Santa',
                start: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                end: new Date(Date.now() + 37 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                backgroundColor: '#8b5cf6',
                borderColor: '#8b5cf6',
                extendedProps: { type: 'holiday', description: 'No hay clases' }
            },
            {
                id: '5',
                title: '📝 Entrega de Proyecto Final',
                start: new Date(Date.now() + 14 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                backgroundColor: '#ef4444',
                borderColor: '#ef4444',
                extendedProps: { type: 'deadline', description: 'Proyecto de ciencias' }
            },
        ];

        setEvents(schoolEvents);
    }, []);

    const handleEventClick = (info) => {
        setSelectedEvent({
            title: info.event.title,
            start: info.event.start,
            end: info.event.end,
            ...info.event.extendedProps
        });
    };

    const handleDateClick = (info) => {
        // Date click handler
        // Aquí se puede abrir modal para crear evento
    };

    const closeModal = () => setSelectedEvent(null);

    const typeIcons = {
        exam: '📚',
        event: '🎉',
        meeting: '👥',
        holiday: '🏖️',
        deadline: '📝'
    };

    return (
        <div className="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
            <div className="max-w-7xl mx-auto">
                {/* Header */}
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        📅 Calendario Escolar
                    </h1>
                    <p className="text-gray-600 dark:text-gray-400 mt-1">
                        Eventos, exámenes, vacaciones y reuniones
                    </p>
                </div>

                {/* Legend */}
                <div className="flex flex-wrap gap-4 mb-6">
                    <div className="flex items-center gap-2">
                        <span className="w-3 h-3 rounded-full bg-blue-500" />
                        <span className="text-sm text-gray-600 dark:text-gray-400">Exámenes</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <span className="w-3 h-3 rounded-full bg-green-500" />
                        <span className="text-sm text-gray-600 dark:text-gray-400">Eventos</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <span className="w-3 h-3 rounded-full bg-yellow-500" />
                        <span className="text-sm text-gray-600 dark:text-gray-400">Reuniones</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <span className="w-3 h-3 rounded-full bg-purple-500" />
                        <span className="text-sm text-gray-600 dark:text-gray-400">Vacaciones</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <span className="w-3 h-3 rounded-full bg-red-500" />
                        <span className="text-sm text-gray-600 dark:text-gray-400">Entregas</span>
                    </div>
                </div>

                {/* Calendar */}
                <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <FullCalendar
                        plugins={[dayGridPlugin, timeGridPlugin, interactionPlugin]}
                        initialView="dayGridMonth"
                        headerToolbar={{
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay'
                        }}
                        locale={esLocale}
                        events={events}
                        eventClick={handleEventClick}
                        dateClick={handleDateClick}
                        height="auto"
                        aspectRatio={1.8}
                        eventDisplay="block"
                        dayMaxEvents={3}
                        moreLinkText={(num) => `+${num} más`}
                    />
                </div>

                {/* Event Detail Modal */}
                {selectedEvent && (
                    <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" onClick={closeModal}>
                        <div
                            className="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6"
                            onClick={(e) => e.stopPropagation()}
                        >
                            <div className="flex items-center justify-between mb-4">
                                <h3 className="text-xl font-bold text-gray-900 dark:text-white">
                                    {selectedEvent.title}
                                </h3>
                                <button
                                    onClick={closeModal}
                                    className="text-gray-500 hover:text-gray-700"
                                >
                                    ✕
                                </button>
                            </div>

                            <div className="space-y-3">
                                <div className="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                                    <span>📅</span>
                                    <span>
                                        {new Date(selectedEvent.start).toLocaleDateString('es-GT', {
                                            weekday: 'long',
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric'
                                        })}
                                    </span>
                                </div>

                                {selectedEvent.end && (
                                    <div className="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                                        <span>⏰</span>
                                        <span>
                                            {new Date(selectedEvent.start).toLocaleTimeString('es-GT', {
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            })} - {new Date(selectedEvent.end).toLocaleTimeString('es-GT', {
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            })}
                                        </span>
                                    </div>
                                )}

                                {selectedEvent.description && (
                                    <div className="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                                        <span>📝</span>
                                        <span>{selectedEvent.description}</span>
                                    </div>
                                )}

                                <div className="flex items-center gap-3">
                                    <span>🏷️</span>
                                    <span className="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm capitalize">
                                        {selectedEvent.type}
                                    </span>
                                </div>
                            </div>

                            <button
                                onClick={closeModal}
                                className="w-full mt-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:opacity-90 transition-opacity"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default CalendarView;
