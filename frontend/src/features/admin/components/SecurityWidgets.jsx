import React from 'react';
import { useTheme } from '../../../contexts/ThemeContext';
import { ShieldCheck, Users, Lock, Activity } from 'lucide-react';

const SecurityWidgets = () => {
    const { darkMode } = useTheme();

    const StatCard = ({ icon: Icon, label, value, color }) => (
        <div className={`p-6 rounded-2xl border shadow-sm ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
            <div className="flex items-center justify-between mb-4">
                <div className={`p-3 rounded-xl ${color === 'green' ? 'bg-green-100 text-green-600' : color === 'purple' ? 'bg-purple-100 text-purple-600' : color === 'blue' ? 'bg-blue-100 text-blue-600' : 'bg-orange-100 text-orange-600'}`}>
                    <Icon size={24} />
                </div>
                <span className={`text-xs font-bold px-2 py-1 rounded-full ${color === 'green' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}`}>
                    +2.4%
                </span>
            </div>
            <h4 className={`text-2xl font-bold mb-1 ${darkMode ? 'text-white' : 'text-gray-900'}`}>{value}</h4>
            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{label}</p>
        </div>
    );

    return (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <StatCard icon={Users} label="Usuarios Activos" value="142" color="blue" />
            <StatCard icon={ShieldCheck} label="Amenazas Bloqueadas" value="3" color="green" />
            <StatCard icon={Lock} label="2FA Habilitado" value="85%" color="purple" />
            <StatCard icon={Activity} label="Salud del Sistema" value="98%" color="orange" />
        </div>
    );
};

export default SecurityWidgets;
