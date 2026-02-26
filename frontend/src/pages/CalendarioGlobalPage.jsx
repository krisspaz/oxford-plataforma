import { useState, useEffect } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import eventService from '../services/eventService';
import taskService from '../services/taskService';

const CalendarioGlobalPage = () => {
    const { darkMode } = useTheme();
    const [currentDate, setCurrentDate] = useState(new Date());
    const [events, setEvents] = useState([]);
    const [tasks, setTasks] = useState([]);
    const [selectedDate, setSelectedDate] = useState(null);

    useEffect(() => {
        // eslint-disable-next-line react-hooks/immutability
        loadData();
    }, []);

    const loadData = async () => {
        try {
            // Seed data if empty (dev helper)
            await eventService.init().catch(() => { });

            const [eventsData, tasksData] = await Promise.all([
                eventService.getAll(),
                taskService.getMyTasks() // Assuming logged in user
            ]);
            setEvents(eventsData.data || eventsData); // Handle axios wrapper variations
            setTasks(tasksData || []);
        } catch (error) {
            console.error(error);
        }
    };

    // Calendar Helpers
    const getDaysInMonth = (date) => new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
    const getFirstDayOfMonth = (date) => new Date(date.getFullYear(), date.getMonth(), 1).getDay();

    const changeMonth = (delta) => {
        setCurrentDate(new Date(currentDate.getFullYear(), currentDate.getMonth() + delta, 1));
    };

    const renderCalendarGrid = () => {
        const totalDays = getDaysInMonth(currentDate);
        const startDay = getFirstDayOfMonth(currentDate);
        const days = [];

        // Empty cells for offset
        for (let i = 0; i < startDay; i++) {
            days.push(<div key={`empty-${i}`} className="h-24 md:h-32 border-b border-r border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50"></div>);
        }

        // Days
        for (let d = 1; d <= totalDays; d++) {
            const dateStr = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;

            // Filter items for this day
            const dayEvents = events.filter(e => e.date.startsWith(dateStr));
            const dayTasks = tasks.filter(t => t.dueDate && t.dueDate.startsWith(dateStr));

            const isToday = new Date().toDateString() === new Date(currentDate.getFullYear(), currentDate.getMonth(), d).toDateString();

            days.push(
                <div
                    key={d}
                    onClick={() => setSelectedDate({ date: dateStr, items: [...dayEvents, ...dayTasks] })}
                    className={`h-24 md:h-32 border-b border-r p-2 relative transition-colors cursor-pointer group
                        ${darkMode ? 'border-gray-800 hover:bg-gray-800' : 'border-gray-200 hover:bg-gray-50'}
                        ${selectedDate?.date === dateStr ? (darkMode ? 'bg-indigo-900/20' : 'bg-indigo-50') : ''}
                    `}
                >
                    <span className={`text-sm font-bold w-7 h-7 flex items-center justify-center rounded-full
                        ${isToday
                            ? 'bg-indigo-600 text-white'
                            : (darkMode ? 'text-gray-300' : 'text-gray-700')}
                    `}>
                        {d}
                    </span>

                    {/* Dots / Indicators */}
                    <div className="mt-1 space-y-1 overflow-hidden">
                        {dayEvents.map(ev => (
                            <div key={ev.id} className="text-[10px] truncate px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 font-medium">
                                {ev.title}
                            </div>
                        ))}
                        {dayTasks.map(tk => (
                            <div key={tk.id} className="text-[10px] truncate px-1.5 py-0.5 rounded bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 font-medium">
                                Ent: {tk.title}
                            </div>
                        ))}
                    </div>
                </div>
            );
        }

        return days;
    };

    const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

    return (
        <div className={`h-[calc(100vh-100px)] flex flex-col md:flex-row gap-6 p-4 md:p-8 overflow-hidden rounded-3xl shadow-2xl ${darkMode ? 'bg-[#151923]' : 'bg-white'}`}>

            {/* Main Calendar Area */}
            <div className="flex-1 flex flex-col min-h-0">
                {/* Header */}
                <div className="flex items-center justify-between mb-6">
                    <h1 className={`text-2xl font-bold capitalize ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                        {monthNames[currentDate.getMonth()]} {currentDate.getFullYear()}
                    </h1>
                    <div className="flex gap-2">
                        <button onClick={() => changeMonth(-1)} className={`p-2 rounded-xl border ${darkMode ? 'border-gray-700 hover:bg-gray-800' : 'border-gray-200 hover:bg-gray-50'}`}>
                            <ChevronLeft size={20} className={darkMode ? 'text-white' : 'text-gray-700'} />
                        </button>
                        <button onClick={() => setCurrentDate(new Date())} className={`px-4 py-2 rounded-xl text-sm font-bold ${darkMode ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-700'}`}>
                            Hoy
                        </button>
                        <button onClick={() => changeMonth(1)} className={`p-2 rounded-xl border ${darkMode ? 'border-gray-700 hover:bg-gray-800' : 'border-gray-200 hover:bg-gray-50'}`}>
                            <ChevronRight size={20} className={darkMode ? 'text-white' : 'text-gray-700'} />
                        </button>
                    </div>
                </div>

                {/* Grid Header */}
                <div className="grid grid-cols-7 mb-2 text-center">
                    {['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'].map(d => (
                        <div key={d} className={`text-xs font-bold uppercase tracking-wider ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                            {d}
                        </div>
                    ))}
                </div>

                {/* Grid Body */}
                <div className={`flex-1 overflow-y-auto border-t border-l ${darkMode ? 'border-gray-800' : 'bg-white border-gray-200 text-gray-900'}`}>
                    <div className="grid grid-cols-7">
                        {renderCalendarGrid()}
                    </div>
                </div>
            </div>

            {/* Sidebar Details (Responsive) */}
            <div className={`w-full md:w-80 flex flex-col border-l pl-0 md:pl-6 pt-6 md:pt-0 border-t md:border-t-0 ${darkMode ? 'border-gray-800' : 'bg-white border-gray-200 text-gray-900'}`}>
                <h2 className={`text-lg font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                    {selectedDate ? `Detalles del ${selectedDate.date}` : 'Próximos Eventos'}
                </h2>

                <div className="flex-1 overflow-y-auto space-y-4">
                    {(!selectedDate && events.length === 0 && tasks.length === 0) && (
                        <div className="text-center py-10 text-gray-500">
                            <CalIcon size={40} className="mx-auto mb-2 opacity-50" />
                            <p>No hay eventos programados</p>
                        </div>
                    )}

                    {(selectedDate ? selectedDate.items : [...events, ...tasks].slice(0, 5)).map((item, idx) => (
                        <div key={idx} className={`p-4 rounded-xl border-l-4 shadow-sm ${item.type // Is Event?
                                ? (darkMode ? 'bg-blue-900/10 border-blue-500' : 'bg-blue-50 border-blue-500')
                                : (darkMode ? 'bg-red-900/10 border-red-500' : 'bg-red-50 border-red-500')
                            }`}>
                            <h3 className={`font-bold mb-1 ${darkMode ? 'text-gray-200' : 'text-gray-800'}`}>
                                {item.title}
                            </h3>
                            <div className="space-y-1 text-xs text-gray-500">
                                <div className="flex items-center gap-1.5">
                                    <Clock size={12} />
                                    {item.date ? new Date(item.date).toLocaleDateString() : item.dueDate}
                                </div>
                                {item.location && (
                                    <div className="flex items-center gap-1.5">
                                        <MapPin size={12} />
                                        {item.location}
                                    </div>
                                )}
                                {item.type && (
                                    <div className="flex items-center gap-1.5">
                                        <Info size={12} />
                                        {item.type}
                                    </div>
                                )}
                            </div>
                        </div>
                    ))}
                </div>

                {/* Legend */}
                <div className="mt-4 pt-4 border-t border-gray-700/10 grid grid-cols-2 gap-2 text-xs">
                    <div className="flex items-center gap-2 text-gray-500">
                        <div className="w-3 h-3 rounded bg-blue-500"></div> Eventos
                    </div>
                    <div className="flex items-center gap-2 text-gray-500">
                        <div className="w-3 h-3 rounded bg-red-500"></div> Tareas
                    </div>
                </div>
            </div>

        </div>
    );
};

export default CalendarioGlobalPage;
