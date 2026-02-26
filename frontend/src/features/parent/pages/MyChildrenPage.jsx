import { useTheme } from '../../../contexts/ThemeContext';
import { useNavigate } from 'react-router-dom';

const MyChildrenPage = () => {
    const { darkMode } = useTheme();
    const navigate = useNavigate();

    // Mock family data
    const children = [
        {
            id: 1,
            name: 'Juanito Pérez',
            grade: '5to Primaria',
            section: 'A',
            avatar: 'JP',
            performance: 85,
            attendance: '98%',
            nextTask: 'Matemáticas: Ecuaciones (Mañana)',
            alerts: 0
        },
        {
            id: 2,
            name: 'María Pérez',
            grade: '2do Básico',
            section: 'B',
            avatar: 'MP',
            performance: 92,
            attendance: '100%',
            nextTask: 'Ciencias: Proyecto Volcán (Viernes)',
            alerts: 1
        }
    ];

    return (
        <div className={`min-h-screen p-8 transition-colors duration-300 ${darkMode ? 'bg-gray-900 text-white' : 'bg-gray-50 text-gray-800'}`}>
            <div className="max-w-6xl mx-auto">
                <div className="flex justify-between items-center mb-8">
                    <div>
                        <h1 className="text-3xl font-bold flex items-center gap-3">
                            <span className="p-2 bg-indigo-500 rounded-xl text-white shadow-lg shadow-indigo-500/30">
                                <Users size={28} />
                            </span>
                            Portal de Padres
                        </h1>
                        <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                            Vista general del rendimiento académico de tus hijos.
                        </p>
                    </div>
                </div>

                <div className="grid gap-6">
                    {children.map(child => (
                        <div key={child.id} className={`rounded-2xl p-6 shadow-lg border transition-all hover:shadow-xl ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>

                            {/* Header */}
                            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 border-b pb-4 dark:border-gray-700">
                                <div className="flex items-center gap-4">
                                    <div className="w-16 h-16 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md">
                                        {child.avatar}
                                    </div>
                                    <div>
                                        <h2 className="text-xl font-bold">{child.name}</h2>
                                        <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                            {child.grade} - Sección "{child.section}"
                                        </p>
                                    </div>
                                </div>
                                <div className="flex gap-2">
                                    <button
                                        onClick={() => navigate(`/alumno/notas`)} // In real app, pass child ID
                                        className={`px-4 py-2 rounded-lg text-sm font-medium ${darkMode ? 'bg-gray-700 hover:bg-gray-600' : 'bg-gray-100 hover:bg-gray-200'}`}
                                    >
                                        Ver Notas
                                    </button>
                                    <button
                                        onClick={() => navigate(`/alumno/tareas`)}
                                        className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-lg shadow-indigo-600/20"
                                    >
                                        Ver Tareas
                                    </button>
                                </div>
                            </div>

                            {/* Stats Grid */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-750' : 'bg-gray-50'}`}>
                                    <div className="flex items-center gap-2 mb-2 text-blue-500">
                                        <TrendingUp size={18} />
                                        <span className="text-xs font-bold uppercase">Rendimiento</span>
                                    </div>
                                    <p className="text-2xl font-bold">{child.performance}/100</p>
                                    <div className="w-full bg-gray-200 rounded-full h-1.5 mt-2 dark:bg-gray-700">
                                        <div className="bg-blue-500 h-1.5 rounded-full" style={{ width: `${child.performance}%` }}></div>
                                    </div>
                                </div>

                                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-750' : 'bg-gray-50'}`}>
                                    <div className="flex items-center gap-2 mb-2 text-green-500">
                                        <Clock size={18} />
                                        <span className="text-xs font-bold uppercase">Asistencia</span>
                                    </div>
                                    <p className="text-2xl font-bold">{child.attendance}</p>
                                    <p className="text-xs text-gray-500 mt-1">Asistencia regular</p>
                                </div>

                                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-750' : 'bg-gray-50'}`}>
                                    <div className="flex items-center gap-2 mb-2 text-purple-500">
                                        <BookOpen size={18} />
                                        <span className="text-xs font-bold uppercase">Próxima Tarea</span>
                                    </div>
                                    <p className="text-sm font-medium truncate" title={child.nextTask}>{child.nextTask}</p>
                                    <p className="text-xs text-gray-500 mt-1">Vence pronto</p>
                                </div>

                                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-750' : 'bg-gray-50'}`}>
                                    <div className="flex items-center gap-2 mb-2 text-orange-500">
                                        <AlertCircle size={18} />
                                        <span className="text-xs font-bold uppercase">Alertas</span>
                                    </div>
                                    {child.alerts > 0 ? (
                                        <p className="text-2xl font-bold text-red-500">{child.alerts}</p>
                                    ) : (
                                        <div className="flex items-center gap-2 text-green-500 mt-1">
                                            <Award size={24} />
                                            <span className="text-sm font-bold">Sin novedades</span>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default MyChildrenPage;
