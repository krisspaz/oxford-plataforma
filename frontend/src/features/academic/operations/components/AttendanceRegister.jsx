import React, { useState } from 'react';
import { useTheme } from '../../../../contexts/ThemeContext';
import { Check, X, Clock, Save, Calendar } from 'lucide-react';
import { toast } from 'sonner';

const AttendanceRegister = () => {
    const { darkMode } = useTheme();
    const [date, setDate] = useState(new Date().toISOString().split('T')[0]);
    const [students, setStudents] = useState([
        { id: 101, name: 'Juan Pérez', status: 'present' },
        { id: 102, name: 'Ana García', status: 'present' },
        { id: 103, name: 'Carlos Ruiz', status: 'absent' },
        { id: 104, name: 'Sofía López', status: 'late' },
        { id: 105, name: 'Miguel Ángel', status: 'present' },
    ]);

    const handleStatusChange = (id, newStatus) => {
        setStudents(prev => prev.map(s => s.id === id ? { ...s, status: newStatus } : s));
    };

    const handleSave = () => {
        // Here we would call AttendanceController API
        toast.success(`Asistencia guardada para el ${date}`);
    };

    const StatusButton = ({ student, type, icon: Icon, colorClass }) => (
        <button
            onClick={() => handleStatusChange(student.id, type)}
            className={`p-2 rounded-lg transition-all ${student.status === type
                ? colorClass
                : darkMode ? 'bg-gray-700 text-gray-400 hover:bg-gray-600' : 'bg-gray-100 text-gray-400 hover:bg-gray-200'
                }`}
            title={type.toUpperCase()}
        >
            <Icon size={18} />
        </button>
    );

    return (
        <div className={`p-6 rounded-2xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
            <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h3 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>Registro de Asistencia</h3>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Matemáticas - Grado 5to Primaria</p>
                </div>
                <div className="flex items-center gap-3">
                    <div className="relative">
                        <Calendar className={`absolute left-3 top-2.5 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`} size={16} />
                        <input
                            type="date"
                            value={date}
                            onChange={(e) => setDate(e.target.value)}
                            className={`pl-10 pr-4 py-2 rounded-lg border outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-gray-50 border-gray-200 text-gray-900'}`}
                        />
                    </div>
                    <button
                        onClick={handleSave}
                        className="flex items-center gap-2 px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium"
                    >
                        <Save size={18} />
                        Guardar
                    </button>
                </div>
            </div>

            <div className="overflow-x-auto">
                <table className="w-full border-collapse">
                    <thead>
                        <tr className={`border-b ${darkMode ? 'border-gray-700' : 'border-gray-100'}`}>
                            <th className={`text-left py-3 px-4 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Estudiante</th>
                            <th className={`text-center py-3 px-4 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Presente</th>
                            <th className={`text-center py-3 px-4 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Tarde</th>
                            <th className={`text-center py-3 px-4 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Ausente</th>
                        </tr>
                    </thead>
                    <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                        {students.map(student => (
                            <tr key={student.id} className={`${darkMode ? 'hover:bg-gray-700/30' : 'hover:bg-gray-50'}`}>
                                <td className={`py-3 px-4 font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{student.name}</td>
                                <td className="py-3 px-4 text-center">
                                    <StatusButton
                                        student={student}
                                        type="present"
                                        icon={Check}
                                        colorClass="bg-green-100 text-green-600 ring-2 ring-green-500 ring-offset-2 dark:ring-offset-gray-800"
                                    />
                                </td>
                                <td className="py-3 px-4 text-center">
                                    <StatusButton
                                        student={student}
                                        type="late"
                                        icon={Clock}
                                        colorClass="bg-orange-100 text-orange-600 ring-2 ring-orange-500 ring-offset-2 dark:ring-offset-gray-800"
                                    />
                                </td>
                                <td className="py-3 px-4 text-center">
                                    <StatusButton
                                        student={student}
                                        type="absent"
                                        icon={X}
                                        colorClass="bg-red-100 text-red-600 ring-2 ring-red-500 ring-offset-2 dark:ring-offset-gray-800"
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default AttendanceRegister;
