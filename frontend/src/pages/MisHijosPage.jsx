import { useState, useEffect } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import scheduleService from '../services/scheduleService';
import familyService from '../services/familyService';

const MisHijosPage = () => {
    const { darkMode } = useTheme();
    const [children, setChildren] = useState([]);
    const [loading, setLoading] = useState(true);
    const [selectedChild, setSelectedChild] = useState(null);
    const [schedule, setSchedule] = useState([]);
    const [loadingSchedule, setLoadingSchedule] = useState(false);

    useEffect(() => {
        const fetchChildren = async () => {
            try {
                const res = await familyService.getMyChildren();
                if (res.data) {
                    setChildren(res.data);
                }
            } catch (error) {
                console.error("Error fetching children", error);
                // Fallback to empty or error state, but removing mock as requested to "fix" it
            } finally {
                setLoading(false);
            }
        };

        fetchChildren();
    }, []);

    const handleViewSchedule = async (child) => {
        setSelectedChild(child);
        setLoadingSchedule(true);
        try {

            // Fetch real schedule from backend
            const data = await scheduleService.getStudentSchedule(child.id);
            if (Array.isArray(data)) {
                setSchedule(data);
            } else {
                console.warn("Schedule data is not an array", data);
                setSchedule([]);
            }
        } catch (error) {
            console.error("Error loading schedule", error);
        } finally {
            setLoadingSchedule(false);
        }
    };

    if (loading) return <div className="p-8 text-center">Cargando mis hijos...</div>;

    return (
        <div className="space-y-6">
            <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Mis Hijos</h1>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {children.map(child => (
                    <div key={child.id} className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg overflow-hidden border ${darkMode ? 'border-gray-700' : 'border-gray-100'}`}>
                        <div className="p-6">
                            <div className="flex items-center gap-4 mb-4">
                                <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-xl">
                                    {child.name.charAt(0)}
                                </div>
                                <div>
                                    <h3 className={`font-bold text-lg ${darkMode ? 'text-white' : 'text-gray-900'}`}>{child.name}</h3>
                                    <p className="text-gray-500">{child.grade} - Sección "{child.section}"</p>
                                </div>
                            </div>

                            <div className="space-y-2">
                                <button
                                    onClick={() => handleViewSchedule(child)}
                                    className={`w-full flex items-center justify-between p-3 rounded-lg ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'} transition-colors`}
                                >
                                    <span className="flex items-center gap-2 text-blue-500 font-medium">
                                        <Calendar size={18} /> Ver Horario
                                    </span>
                                    <ChevronRight size={16} className="text-gray-400" />
                                </button>
                                <button className={`w-full flex items-center justify-between p-3 rounded-lg ${darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'} transition-colors`}>
                                    <span className="flex items-center gap-2 text-green-500 font-medium">
                                        <BookOpen size={18} /> Boleta de Notas
                                    </span>
                                    <ChevronRight size={16} className="text-gray-400" />
                                </button>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {selectedChild && (
                <div className="mt-8">
                    <h2 className={`text-xl font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                        Horario de {selectedChild.name}
                    </h2>
                    {loadingSchedule ? (
                        <p>Cargando horario...</p>
                    ) : (
                        <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow p-6 overflow-x-auto`}>
                            <table className="w-full">
                                <thead>
                                    <tr className={`text-left ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        <th className="p-3">Hora</th>
                                        <th className="p-3">Lunes</th>
                                        <th className="p-3">Martes</th>
                                        <th className="p-3">Miércoles</th>
                                        <th className="p-3">Jueves</th>
                                        <th className="p-3">Viernes</th>
                                    </tr>
                                </thead>
                                <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                                    {['07:30 - 08:15', '08:15 - 09:00', '09:00 - 09:45'].map((timeSlot) => (
                                        <tr key={timeSlot}>
                                            <td className={`p-3 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-900'}`}>{timeSlot}</td>
                                            {[1, 2, 3, 4, 5].map(day => {
                                                const cls = schedule.find(s => s.dayOfWeek === day && timeSlot.startsWith(s.startTime));
                                                return (
                                                    <td key={day} className="p-3">
                                                        {cls ? (
                                                            <div className="bg-blue-100 text-blue-800 p-2 rounded text-sm">
                                                                <div className="font-bold">{cls.subject.name}</div>
                                                                <div className="text-xs">{cls.classroom}</div>
                                                            </div>
                                                        ) : <span className="text-gray-300">-</span>}
                                                    </td>
                                                );
                                            })}
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};

export default MisHijosPage;
