import React from 'react';
import { useTheme } from '../../../contexts/ThemeContext';
import AttendanceRegister from '../components/AttendanceRegister';
import EventManager from '../components/EventManager';
import { Briefcase } from 'lucide-react';

const DailyOperationsDashboard = () => {
    const { darkMode } = useTheme();

    return (
        <div className={`min-h-screen p-8 transition-colors duration-300 ${darkMode ? 'bg-gray-900 text-white' : 'bg-gray-50 text-gray-800'}`}>
            <div className="max-w-7xl mx-auto space-y-8">
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 className="text-3xl font-bold flex items-center gap-3">
                            <div className="p-2 bg-teal-500 rounded-lg text-white">
                                <Briefcase size={24} />
                            </div>
                            Operaciones Académicas
                        </h1>
                        <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>
                            Control diario de asistencia y calendario escolar.
                        </p>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div className="lg:col-span-2">
                        <AttendanceRegister />
                    </div>
                    <div>
                        <EventManager />
                    </div>
                </div>
            </div>
        </div>
    );
};

export default DailyOperationsDashboard;
