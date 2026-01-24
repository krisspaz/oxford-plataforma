import React, { useState } from 'react';
import { useTheme } from '../../../contexts/ThemeContext';
import { Calendar, Plus, MapPin, Trash2 } from 'lucide-react';

const EventManager = () => {
    const { darkMode } = useTheme();
    const [events, setEvents] = useState([
        { id: 1, title: 'Aniversario del Colegio', date: '2024-03-15', type: 'holiday', location: 'Campus Central' },
        { id: 2, title: 'Entrega de Notas - I Bimestre', date: '2024-04-10', type: 'academic', location: 'Virtual' },
    ]);

    return (
        <div className={`p-6 rounded-2xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h3 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>Eventos Institucionales</h3>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Calendario oficial escolar.</p>
                </div>
                <button className="flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                    <Plus size={16} /> Nuevo Evento
                </button>
            </div>

            <div className="space-y-3">
                {events.map(event => (
                    <div key={event.id} className={`flex items-center p-4 rounded-xl border ${darkMode ? 'bg-gray-700/50 border-gray-600' : 'bg-gray-50 border-gray-200'}`}>
                        <div className={`p-3 rounded-lg mr-4 ${event.type === 'holiday' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600'}`}>
                            <Calendar size={20} />
                        </div>
                        <div className="flex-1">
                            <h4 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{event.title}</h4>
                            <div className="flex items-center gap-4 text-xs text-gray-500 mt-1">
                                <span>{event.date}</span>
                                <span className="flex items-center gap-1"><MapPin size={12} /> {event.location}</span>
                            </div>
                        </div>
                        <button className="p-2 text-gray-400 hover:text-red-500 transition-colors">
                            <Trash2 size={18} />
                        </button>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default EventManager;
