import { Users, AlertTriangle, GraduationCap, UserPlus, CreditCard, RefreshCw, XCircle, TrendingDown, Brain, ChevronRight, BookOpen, FileText, Calendar, Clock, Receipt, Edit, Package, Layers, Shield, Database, School, Activity, BarChart, TrendingUp, Award, Bell, CheckCircle, Lightbulb, Zap, Rocket, Check, AlertCircle, Sparkles, Filter, Search, MoreVertical, Layout, Grid } from 'lucide-react';
import { useState, useEffect, useMemo } from 'react';
import { useNavigate } from 'react-router-dom';
import { useTheme } from '../contexts/ThemeContext';
import { useAuth } from '../contexts/AuthContext';
import api from '../services/api';
import activityService from '../services/activityService';
import scheduleService from '../services/scheduleService';

const StatCard = ({ title, value, icon: Icon, color, bg, onClick, isCustomIcon, darkMode }) => (
    <div
        onClick={onClick}
        className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} rounded-2xl shadow-lg border p-6 flex items-center gap-4 transition-all hover:shadow-xl hover:-translate-y-1 ${onClick ? 'cursor-pointer' : ''}`}
    >
        <div className={`p-4 rounded-xl ${bg} flex items-center justify-center`}>
            {isCustomIcon ? Icon : <Icon size={28} className={color} />}
        </div>
        <div className="flex-1">
            <p className={`text-sm font-medium ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{title}</p>
            <p className={`text-3xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{value}</p>
        </div>
    </div>
);

const QuickAction = ({ title, icon: Icon, color, bgColor, onClick, darkMode }) => (
    <button
        onClick={onClick}
        className={`p-5 ${darkMode ? 'bg-gray-700 border-gray-600' : 'bg-white border-gray-200'} border-2 rounded-2xl transition-all flex flex-col items-center justify-center gap-3 shadow-sm hover:shadow-md hover:scale-105`}
    >
        <div className={`p-3 rounded-full ${bgColor}`}>
            <Icon size={24} className={color} />
        </div>
        <span className={`font-semibold text-sm ${darkMode ? 'text-gray-200' : 'text-gray-700'}`}>{title}</span>
    </button>
);

// --- DASHBOARD COMPONENTS ---

const AdminDashboard = ({ stats, navigate, darkMode }) => (
    <>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <StatCard
                title="Usuarios Registrados"
                value={stats?.totalUsers || 0}
                icon={Users}
                color="text-blue-500"
                bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'}
                darkMode={darkMode}
                onClick={() => navigate('/admin/usuarios')}
            />
            <StatCard
                title="Materias Activas"
                value={stats?.activeSubjects || 0}
                icon={BookOpen}
                color="text-purple-500"
                bg={darkMode ? 'bg-purple-900/30' : 'bg-purple-100'}
                darkMode={darkMode}
                onClick={() => navigate('/academico/materias')}
            />
            <StatCard
                title="Ciclo Escolar Actual"
                value={stats?.activeCycle || 'Activo'}
                icon={Calendar}
                color="text-orange-500"
                bg={darkMode ? 'bg-orange-900/30' : 'bg-orange-100'}
                darkMode={darkMode}
                onClick={() => navigate('/admin/cierre-escolar')}
            />
        </div>

        {/* Listado Previo de Estudiantes (Requested Feature) */}
        <div className={`mt-8 ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} rounded-2xl shadow-lg border p-6`}>
            <div className="flex items-center justify-between mb-6">
                <h3 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>Listado de Estudiantes (Vista Previa)</h3>
                <button
                    onClick={() => navigate('/secretaria/inscripciones')} // Navigating to inscriptions/students list
                    className="text-blue-500 hover:text-blue-600 font-medium flex items-center gap-1"
                >
                    Ver Todos <ChevronRight size={16} />
                </button>
            </div>
            <div className="overflow-x-auto">
                <table className="w-full">
                    <thead>
                        <tr className={`text-left text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                            <th className="pb-3 pl-2">Nombre</th>
                            <th className="pb-3">Ciclo</th>
                            <th className="pb-3">Grado/Nivel</th>
                            <th className="pb-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        {stats?.recentStudents?.slice(0, 5).map((student, i) => (
                            <tr key={i} className={`border-t ${darkMode ? 'border-gray-700' : 'border-gray-100'}`}>
                                <td className={`py-3 pl-2 font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{student.name}</td>
                                <td className={`py-3 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{student.cycle}</td>
                                <td className={`py-3 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{student.course}</td>
                                <td className="py-3">
                                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${student.status === 'ACTIVE'
                                        ? (darkMode ? 'bg-green-900/30 text-green-400' : 'bg-green-100 text-green-700')
                                        : (darkMode ? 'bg-red-900/30 text-red-400' : 'bg-red-100 text-red-700')
                                        }`}>
                                        {student.status === 'ACTIVE' ? 'Activo' : 'Inactivo'}
                                    </span>
                                </td>
                            </tr>
                        ))}
                        {(!stats?.recentStudents || stats.recentStudents.length === 0) && (
                            <tr>
                                <td colSpan="4" className="py-6 text-center text-gray-500">No hay estudiantes recientes para mostrar.</td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    </>
);

const SecretariaDashboard = ({ navigate, darkMode }) => (
    <>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard title="Inscripciones Hoy" value="3" icon={UserPlus} color="text-blue-500" bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'} darkMode={darkMode} onClick={() => navigate('/secretaria/inscripciones')} />
            <StatCard title="Familias" value="89" icon={Users} color="text-teal-500" bg={darkMode ? 'bg-teal-900/30' : 'bg-teal-100'} darkMode={darkMode} onClick={() => navigate('/secretaria/familias')} />
            {/* <StatCard title="Pagos Pendientes" value="12" icon={CreditCard} color="text-orange-500" bg={darkMode ? 'bg-orange-900/30' : 'bg-orange-100'} darkMode={darkMode} onClick={() => navigate('/finanzas/pagos')} /> */}
            <StatCard title="Contratos" value="24" icon={FileText} color="text-purple-500" bg={darkMode ? 'bg-purple-900/30' : 'bg-purple-100'} darkMode={darkMode} onClick={() => navigate('/secretaria/contratos')} />
        </div>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <QuickAction title="Nueva Inscripción" icon={UserPlus} color="text-blue-500" bgColor={darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} onClick={() => navigate('/secretaria/inscripciones')} darkMode={darkMode} />
            {/* <QuickAction title="Registrar Pago" icon={CreditCard} color="text-green-500" bgColor={darkMode ? 'bg-green-900/50' : 'bg-green-100'} onClick={() => navigate('/finanzas/pagos')} darkMode={darkMode} /> */}
            <QuickAction title="Generar Contrato" icon={FileText} color="text-purple-500" bgColor={darkMode ? 'bg-purple-900/50' : 'bg-purple-100'} onClick={() => navigate('/secretaria/contratos')} darkMode={darkMode} />
            {/* <QuickAction title="Estado de Cuenta" icon={Receipt} color="text-teal-500" bgColor={darkMode ? 'bg-teal-900/50' : 'bg-teal-100'} onClick={() => navigate('/finanzas/estado-cuenta')} darkMode={darkMode} /> */}
        </div>
    </>
);

const ContabilidadDashboard = ({ navigate, darkMode }) => (
    <>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard title="Ingresos Hoy" value="Q 8,450" icon={() => <span className="text-2xl font-bold text-green-500">Q</span>} color="text-green-500" bg={darkMode ? 'bg-green-900/30' : 'bg-green-100'} isCustomIcon darkMode={darkMode} onClick={() => { }} />
            <StatCard title="Facturas" value="15" icon={Receipt} color="text-blue-500" bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'} darkMode={darkMode} onClick={() => { }} />
            <StatCard title="Anulaciones" value="2" icon={XCircle} color="text-red-500" bg={darkMode ? 'bg-red-900/30' : 'bg-red-100'} darkMode={darkMode} onClick={() => { }} />
            <StatCard title="Exoneraciones" value="5" icon={Package} color="text-purple-500" bg={darkMode ? 'bg-purple-900/30' : 'bg-purple-100'} darkMode={darkMode} onClick={() => { }} />
        </div>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <QuickAction title="Corte del Día" icon={FileText} color="text-green-500" bgColor={darkMode ? 'bg-green-900/50' : 'bg-green-100'} onClick={() => navigate('/finanzas/corte-dia')} darkMode={darkMode} />
            <QuickAction title="Comprobantes" icon={Receipt} color="text-blue-500" bgColor={darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} onClick={() => navigate('/finanzas/comprobantes')} darkMode={darkMode} />
            <QuickAction title="Solicitudes" icon={XCircle} color="text-red-500" bgColor={darkMode ? 'bg-red-900/50' : 'bg-red-100'} onClick={() => navigate('/finanzas/solicitudes')} darkMode={darkMode} />
            <QuickAction title="Paquetes" icon={Package} color="text-purple-500" bgColor={darkMode ? 'bg-purple-900/50' : 'bg-purple-100'} onClick={() => navigate('/finanzas/paquetes')} darkMode={darkMode} />
        </div>
    </>
);



const StudentDashboard = ({ navigate, darkMode, stats }) => {
    const [schedule, setSchedule] = useState([]);
    const [activities, setActivities] = useState([]);
    const [loadingSchedule, setLoadingSchedule] = useState(true);

    useEffect(() => {
        const loadData = async () => {
            try {
                // Load Schedule
                const scheduleData = await scheduleService.getMyStudentSchedule();
                const today = new Date().getDay(); // 0=Sun, 1=Mon...
                const todaySchedule = scheduleData
                    .filter(s => s.dayOfWeek === today)
                    .sort((a, b) => a.startTime.localeCompare(b.startTime));
                setSchedule(todaySchedule);

                // Load Activities
                const activitiesData = await activityService.getAll({ audience: 'STUDENTS', limit: 3 });
                setActivities(activitiesData);
            } catch (error) {
                console.error("Error loading student dashboard data", error);
            } finally {
                setLoadingSchedule(false);
            }
        };
        loadData();
    }, []);

    return (
        <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <StatCard title="Promedio General" value={stats?.student?.average || '85.4'} icon={Award} color="text-yellow-500" bg={darkMode ? 'bg-yellow-900/30' : 'bg-yellow-100'} darkMode={darkMode} />
                <StatCard title="Tareas Pendientes" value={stats?.student?.pendingTasks || 0} icon={FileText} color="text-red-500" bg={darkMode ? 'bg-red-900/30' : 'bg-red-100'} darkMode={darkMode} onClick={() => navigate('/alumno/tareas')} />
                <StatCard title="Próxima Clase" value={schedule[0] ? schedule[0].subject.name : (stats?.student?.nextClass || 'N/A')} icon={BookOpen} color="text-blue-500" bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'} darkMode={darkMode} onClick={() => navigate('/alumno/horario')} />
                <StatCard title="Asistencia" value="95%" icon={CheckCircle} color="text-green-500" bg={darkMode ? 'bg-green-900/30' : 'bg-green-100'} darkMode={darkMode} />
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                {/* Horario de Clases (Real Data) */}
                <div className={`lg:col-span-2 ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} p-6 rounded-2xl shadow-lg border`}>
                    <h3 className={`text-xl font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-900'}`}>Mi Horario de Hoy</h3>
                    <div className="space-y-4">
                        {loadingSchedule ? (
                            <p className="text-gray-500 text-center py-4">Cargando horario...</p>
                        ) : schedule.length > 0 ? (
                            schedule.map((item, index) => (
                                <div key={item.id} className={`flex items-center gap-4 p-3 rounded-lg border border-l-4 ${['border-l-blue-500', 'border-l-green-500', 'border-l-purple-500', 'border-l-orange-500'][index % 4]} hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors`}>
                                    <div className="w-16 text-center">
                                        <p className="text-sm font-bold text-gray-500">{item.startTime}</p>
                                        <p className="text-xs text-gray-400">Hoy</p>
                                    </div>
                                    <div>
                                        <p className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{item.subject.name}</p>
                                        <p className="text-sm text-gray-500">{item.teacher?.name || 'Profesor asignado'} - {item.classroom || 'Salón N/A'}</p>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <p className="text-gray-500 text-center py-4">No tienes clases programadas para hoy.</p>
                        )}
                    </div>
                </div>

                {/* Actividades Anuales (Real Data) */}
                <div className={`p-6 rounded-2xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                    <h3 className={`text-xl font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-900'}`}>Próximas Actividades</h3>
                    <ul className="space-y-4">
                        {activities.length > 0 ? activities.map((activity, index) => (
                            <li key={activity.id} className="flex gap-3 items-start">
                                <div className={`${['bg-orange-100 text-orange-600', 'bg-green-100 text-green-600', 'bg-blue-100 text-blue-600'][index % 3]} p-2 rounded-lg`}>
                                    {activity.icon === 'Users' ? <Users size={20} /> :
                                        activity.icon === 'TrendingUp' ? <TrendingUp size={20} /> :
                                            <Award size={20} />}
                                </div>
                                <div>
                                    <p className={`font-bold text-sm ${darkMode ? 'text-white' : 'text-gray-900'}`}>{activity.title}</p>
                                    <p className="text-xs text-gray-500">{new Date(activity.date).toLocaleDateString()} - {activity.location}</p>
                                </div>
                            </li>
                        )) : (
                            <p className="text-gray-500 text-sm">No hay actividades próximas.</p>
                        )}
                    </ul>
                </div>
            </div>

            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <QuickAction title="Mis Tareas" icon={Edit} color="text-blue-500" bgColor={darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} onClick={() => navigate('/alumno/tareas')} darkMode={darkMode} />
                <QuickAction title="Mis Notas" icon={FileText} color="text-green-500" bgColor={darkMode ? 'bg-green-900/50' : 'bg-green-100'} onClick={() => navigate('/alumno/notas')} darkMode={darkMode} />
                <QuickAction title="Calendario" icon={Calendar} color="text-purple-500" bgColor={darkMode ? 'bg-purple-900/50' : 'bg-purple-100'} onClick={() => navigate('/alumno/horario')} darkMode={darkMode} />
                <QuickAction title="Anuncios" icon={Bell} color="text-orange-500" bgColor={darkMode ? 'bg-orange-900/50' : 'bg-orange-100'} onClick={() => { }} darkMode={darkMode} />
            </div>
        </>
    );
};
const ParentDashboard = ({ navigate, darkMode, stats }) => {
    const [activities, setActivities] = useState([]);

    useEffect(() => {
        const loadActivities = async () => {
            try {
                const data = await activityService.getAll({ audience: 'PARENTS', limit: 3 });
                setActivities(data);
            } catch (error) {
                console.error("Error loading parent activities", error);
            }
        };
        loadActivities();
    }, []);

    return (
        <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <StatCard title="Mis Hijos" value={stats?.parent?.childrenCount || 2} icon={Users} color="text-blue-500" bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'} darkMode={darkMode} onClick={() => navigate('/padres/hijos')} />
                <StatCard title="Pagos Pendientes" value={stats?.parent?.pendingPayments || 0} icon={CreditCard} color="text-red-500" bg={darkMode ? 'bg-red-900/30' : 'bg-red-100'} darkMode={darkMode} onClick={() => navigate('/finanzas/estado-cuenta')} />
                <StatCard title="Próxima Reunión" value="15 Mar" icon={Calendar} color="text-teal-500" bg={darkMode ? 'bg-teal-900/30' : 'bg-teal-100'} darkMode={darkMode} />
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                {/* Actividades Anuales (Real Data) */}
                <div className={`lg:col-span-2 ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} p-6 rounded-2xl shadow-lg border`}>
                    <div className="flex justify-between items-center mb-4">
                        <h3 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>Actividades Anuales</h3>
                        <span className="text-xs font-medium px-2 py-1 bg-blue-100 text-blue-700 rounded-full">Ciclo 2025</span>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {activities.length > 0 ? activities.map((activity, index) => (
                            <div key={activity.id} className={`p-4 rounded-xl border ${darkMode ? 'bg-gray-700/30 border-gray-600' : 'bg-orange-50 border-orange-100'}`}>
                                <div className="flex items-center gap-2 mb-2 text-orange-600">
                                    {activity.icon === 'Users' ? <Users size={18} /> :
                                        activity.icon === 'TrendingUp' ? <TrendingUp size={18} /> :
                                            <Award size={18} />}
                                    <span className="font-bold text-sm">{activity.title}</span>
                                </div>
                                <p className={`text-sm ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{new Date(activity.date).toLocaleDateString()}</p>
                                <p className="text-xs text-gray-400 mt-1">{activity.location}</p>
                            </div>
                        )) : (
                            <div className="col-span-3 text-center text-gray-500 py-4">No hay actividades próximas cargadas.</div>
                        )}
                    </div>
                </div>

                {/* Quick Actions List */}
                <div className={`flex flex-col gap-3 justify-center ${darkMode ? 'bg-gray-800/50' : 'bg-gray-50'} p-4 rounded-2xl`}>
                    <QuickAction title="Boletas de Notas" icon={FileText} color="text-blue-500" bgColor={darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} onClick={() => navigate('/reports')} darkMode={darkMode} />
                    <QuickAction title="Horario de Hijos" icon={Clock} color="text-indigo-500" bgColor={darkMode ? 'bg-indigo-900/50' : 'bg-indigo-100'} onClick={() => navigate('/padres/hijos')} darkMode={darkMode} />
                    <QuickAction title="Estado de Cuenta" icon={CreditCard} color="text-green-500" bgColor={darkMode ? 'bg-green-900/50' : 'bg-green-100'} onClick={() => navigate('/finanzas/estado-cuenta')} darkMode={darkMode} />
                    <QuickAction title="Contratos" icon={FileText} color="text-purple-500" bgColor={darkMode ? 'bg-purple-900/50' : 'bg-purple-100'} onClick={() => navigate('/padres/contrato')} darkMode={darkMode} />
                </div>
            </div>
        </>
    );
};

// --- REUSABLE ACTIVITY WIDGET ---
const ActivitiesWidget = ({ darkMode }) => {
    const [activities, setActivities] = useState([]);

    useEffect(() => {
        const loadActivities = async () => {
            try {
                // Fetch ALL or by Role? User wants ALL for everyone.
                // "esas 4 que las vean, papas, hijos, maestros, director y coordinación"
                // So we can use 'ALL' audience or just general fetch.
                const data = await activityService.getAll({ audience: 'ALL', limit: 4 });
                setActivities(data);
            } catch (error) {
                console.error("Error loading activities", error);
            }
        };
        loadActivities();
    }, []);

    return (
        <div className={`p-6 rounded-2xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} h-full`}>
            <div className="flex justify-between items-center mb-4">
                <h3 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>Actividades Anuales</h3>
                <span className="text-xs font-medium px-2 py-1 bg-blue-100 text-blue-700 rounded-full">Próximos</span>
            </div>
            <ul className="space-y-4">
                {activities.length > 0 ? activities.map((activity, index) => (
                    <li key={activity.id} className="flex gap-3 items-start">
                        <div className={`${['bg-orange-100 text-orange-600', 'bg-green-100 text-green-600', 'bg-blue-100 text-blue-600', 'bg-purple-100 text-purple-600'][index % 4]} p-2 rounded-lg`}>
                            {activity.icon === 'Users' ? <Users size={20} /> :
                                activity.icon === 'TrendingUp' ? <TrendingUp size={20} /> :
                                    activity.icon === 'Clock' ? <Clock size={20} /> :
                                        <Award size={20} />}
                        </div>
                        <div>
                            <p className={`font-bold text-sm ${darkMode ? 'text-white' : 'text-gray-900'}`}>{activity.title}</p>
                            <p className="text-xs text-gray-500">{new Date(activity.date).toLocaleDateString()} - {activity.location}</p>
                        </div>
                    </li>
                )) : (
                    <p className="text-gray-500 text-sm">No hay actividades próximas.</p>
                )}
            </ul>
        </div>
    );
};

// --- UPDATED DASHBOARD COMPONENTS ---

const DocenteDashboard = ({ navigate, darkMode, stats }) => (
    <>
        {/* Resumen de Clases y Actividades */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard
                title="Clases Asignadas"
                value={stats?.teacher?.myClassesCount || 5}
                icon={BookOpen}
                color="text-blue-500"
                bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'}
                darkMode={darkMode}
                onClick={() => navigate('/mis-alumnos')}
            />
            <StatCard
                title="Total Alumnos"
                value={stats?.teacher?.myStudentsCount || 120}
                icon={Users}
                color="text-teal-500"
                bg={darkMode ? 'bg-teal-900/30' : 'bg-teal-100'}
                darkMode={darkMode}
                onClick={() => navigate('/mis-alumnos')}
            />
            <StatCard
                title="Tareas Activas"
                value={stats?.teacher?.activeTasks || 8}
                icon={FileText}
                color="text-purple-500"
                bg={darkMode ? 'bg-purple-900/30' : 'bg-purple-100'}
                darkMode={darkMode}
                onClick={() => navigate('/gestion-tareas')}
            />
            <StatCard
                title="Notas Pendientes"
                value={stats?.teacher?.pendingGrades || 3}
                icon={AlertTriangle}
                color="text-orange-500"
                bg={darkMode ? 'bg-orange-900/30' : 'bg-orange-100'}
                darkMode={darkMode}
                onClick={() => navigate('/carga-notas')}
            />
        </div>

        {/* Acceso Rápido y Horario Próximo */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <div className={`lg:col-span-2 ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} p-6 rounded-2xl shadow-lg border`}>
                <h3 className={`text-xl font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-900'}`}>Mis Materias & Horario</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-700/50' : 'bg-gray-50'} border ${darkMode ? 'border-gray-600' : 'border-gray-200'}`}>
                        <p className="text-sm text-gray-400 font-bold mb-1">AHORA (7:30 - 8:15)</p>
                        <p className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Matemáticas - 5to Primaria A</p>
                        <p className="text-sm text-blue-500">Salón 105</p>
                    </div>
                    <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-700/50' : 'bg-gray-50'} border ${darkMode ? 'border-gray-600' : 'border-gray-200'}`}>
                        <p className="text-sm text-gray-400 font-bold mb-1">SIGUIENTE (8:15 - 9:00)</p>
                        <p className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Ciencias Naturales - 4to Primaria B</p>
                        <p className="text-sm text-blue-500">Laboratorio 1</p>
                    </div>
                </div>
                <div className="mt-4 flex justify-end">
                    <button onClick={() => navigate('/mi-horario')} className="text-blue-500 hover:text-blue-400 text-sm font-bold flex items-center gap-1">
                        Ver Horario Completo <ChevronRight size={16} />
                    </button>
                </div>
            </div>

            {/* Quick Stats/Actions + Activities */}
            <div className="space-y-4">
                {/* Insert Activities Widget for Teachers */}
                <ActivitiesWidget darkMode={darkMode} />
            </div>
        </div>
    </>
);

const DirectorDashboard = ({ stats, navigate, darkMode }) => (
    <>
        {/* Key Metrics */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard
                title="Estudiantes Activos"
                value={stats?.totalStudents || 0}
                icon={GraduationCap}
                color="text-blue-500"
                bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'}
                darkMode={darkMode}
                onClick={() => navigate('/students')}
            />
            <StatCard
                title="Docentes"
                value={stats?.totalTeachers || 15}
                icon={Users}
                color="text-teal-500"
                bg={darkMode ? 'bg-teal-900/30' : 'bg-teal-100'}
                darkMode={darkMode}
                onClick={() => navigate('/academico/docentes')}
            />
            <StatCard
                title="Materias Activas"
                value={stats?.totalSubjects || 0}
                icon={BookOpen}
                color="text-purple-500"
                bg={darkMode ? 'bg-purple-900/30' : 'bg-purple-100'}
                darkMode={darkMode}
                onClick={() => navigate('/academico/materias')}
            />
            <StatCard
                title="Ciclo Escolar"
                value={stats?.activeCycle || '2025'}
                icon={School}
                color="text-orange-500"
                bg={darkMode ? 'bg-orange-900/30' : 'bg-orange-100'}
                darkMode={darkMode}
            />
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
            {/* Quick Actions (Col Span 2) */}
            <div className="lg:col-span-2 grid grid-cols-2 gap-4">
                <div className="col-span-2 mb-2">
                    <h3 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Gestión Académica</h3>
                </div>
                <QuickAction title="IA Horarios" icon={Brain} color="text-indigo-600" bgColor={darkMode ? 'bg-indigo-900/50' : 'bg-indigo-100'} onClick={() => navigate('/academico/horarios')} darkMode={darkMode} />
                <QuickAction title="Supervisión Notas" icon={FileText} color="text-green-600" bgColor={darkMode ? 'bg-green-900/50' : 'bg-green-100'} onClick={() => navigate('/academico/cuadros')} darkMode={darkMode} />
                <QuickAction title="Gestión Niveles" icon={Layers} color="text-blue-600" bgColor={darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} onClick={() => navigate('/academico/niveles')} darkMode={darkMode} />
                <QuickAction title="Reportes General" icon={BarChart} color="text-orange-600" bgColor={darkMode ? 'bg-orange-900/50' : 'bg-orange-100'} onClick={() => navigate('/reports')} darkMode={darkMode} />
            </div>

            {/* Activities Widget for Director */}
            <div>
                <ActivitiesWidget darkMode={darkMode} />
            </div>
        </div>

        {/* Recent Students List */}
        <div className={`mt-8 ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} rounded-2xl shadow-lg border p-6`}>
            {/* ... Existing table code ... */}
            <div className="flex items-center justify-between mb-6">
                <h3 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>Estudiantes Recientes</h3>
                <button
                    onClick={() => navigate('/students')}
                    className="text-blue-500 hover:text-blue-600 font-medium flex items-center gap-1"
                >
                    Ver Todos <ChevronRight size={16} />
                </button>
            </div>
            <div className="overflow-x-auto">
                <table className="w-full">
                    <thead>
                        <tr className={`text-left text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                            <th className="pb-3 pl-2">Nombre</th>
                            <th className="pb-3">Ciclo</th>
                            <th className="pb-3">Grado/Nivel</th>
                            <th className="pb-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        {stats?.recentStudents?.map((student, i) => (
                            <tr key={i} className={`border-t ${darkMode ? 'border-gray-700' : 'border-gray-100'}`}>
                                <td className={`py-3 pl-2 font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{student.name}</td>
                                <td className={`py-3 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{student.cycle}</td>
                                <td className={`py-3 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{student.course}</td>
                                <td className="py-3">
                                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${student.status === 'ACTIVE'
                                        ? (darkMode ? 'bg-green-900/30 text-green-400' : 'bg-green-100 text-green-700')
                                        : (darkMode ? 'bg-red-900/30 text-red-400' : 'bg-red-100 text-red-700')
                                        }`}>
                                        {student.status === 'ACTIVE' ? 'Activo' : 'Inactivo'}
                                    </span>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    </>
);


const CoordinationDashboard = ({ stats, navigate, darkMode }) => (
    <>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard title="Total Alumnos" value={stats?.totalStudents || 0} icon={Users} color="text-blue-500" bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'} darkMode={darkMode} />
            <StatCard title="Docentes" value={stats?.totalTeachers || 0} icon={Users} color="text-teal-500" bg={darkMode ? 'bg-teal-900/30' : 'bg-teal-100'} darkMode={darkMode} />
            <StatCard title="Materias" value={stats?.activeSubjects || 0} icon={BookOpen} color="text-purple-500" bg={darkMode ? 'bg-purple-900/30' : 'bg-purple-100'} darkMode={darkMode} />
            <StatCard title="Eventos" value="4" icon={Calendar} color="text-orange-500" bg={darkMode ? 'bg-orange-900/30' : 'bg-orange-100'} darkMode={darkMode} />
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <div className="lg:col-span-2 space-y-6">
                {/* Quick Actions */}
                <div className={`p-6 rounded-2xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                    <h3 className={`text-lg font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-900'}`}>Gestión Coordinación</h3>
                    <div className="grid grid-cols-2 gap-4">
                        <QuickAction title="Horarios" icon={Clock} color="text-indigo-500" bgColor={darkMode ? 'bg-indigo-900/50' : 'bg-indigo-100'} onClick={() => navigate('/academico/horarios')} darkMode={darkMode} />
                        <QuickAction title="Planificaciones" icon={FileText} color="text-green-500" bgColor={darkMode ? 'bg-green-900/50' : 'bg-green-100'} onClick={() => { }} darkMode={darkMode} />
                        <QuickAction title="Observaciones" icon={AlertTriangle} color="text-orange-500" bgColor={darkMode ? 'bg-orange-900/50' : 'bg-orange-100'} onClick={() => { }} darkMode={darkMode} />
                        <QuickAction title="Reportes y Boletas" icon={BarChart} color="text-blue-500" bgColor={darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} onClick={() => navigate('/academico/boletas')} darkMode={darkMode} />
                    </div>
                </div>
            </div>
            {/* Activities */}
            <div className="h-full">
                <ActivitiesWidget darkMode={darkMode} />
            </div>
        </div>
    </>
);

// --- MAIN COMPONENT ---

const Dashboard = () => {
    const navigate = useNavigate();
    const { darkMode } = useTheme();
    const { user } = useAuth();
    const [loading, setLoading] = useState(true);
    const [stats, setStats] = useState(null);

    const userRole = useMemo(() => {
        if (!user?.roles || user.roles.length === 0) return 'ROLE_ALUMNO';
        const mainRole = user.roles.find(r => r !== 'ROLE_USER');
        return mainRole || user.roles[0];
    }, [user?.roles]);

    useEffect(() => {
        const fetchStats = async () => {
            try {
                // Modified to fetch stats for ALL users
                const data = await api.get('/dashboard/stats');
                setStats(data);
            } catch (error) {
                console.error("Error fetching dashboard stats:", error);
            } finally {
                setLoading(false);
            }
        };

        fetchStats();
    }, [userRole]);

    const renderDashboard = () => {
        switch (userRole) {
            case 'ROLE_SUPER_ADMIN':
            case 'ROLE_ADMIN':
                return <AdminDashboard stats={stats} navigate={navigate} darkMode={darkMode} />;
            case 'ROLE_SECRETARY':
            case 'ROLE_SECRETARIA': // Español
                return <SecretariaDashboard navigate={navigate} darkMode={darkMode} stats={stats} />;
            case 'ROLE_ACCOUNTANT':
            case 'ROLE_CONTABILIDAD': // Español
                return <ContabilidadDashboard navigate={navigate} darkMode={darkMode} stats={stats} />;
            case 'ROLE_TEACHER':
            case 'ROLE_DOCENTE': // Español
                return <DocenteDashboard navigate={navigate} darkMode={darkMode} stats={stats} />;
            case 'ROLE_INFORMATICS':
            case 'ROLE_INFORMATICA': // Español
                return <div className="p-10 text-center text-gray-500">Panel de Informática (En Desarrollo)</div>;
            case 'ROLE_STUDENT':
            case 'ROLE_ALUMNO': // Español
                return <StudentDashboard navigate={navigate} darkMode={darkMode} stats={stats} />;
            case 'ROLE_PARENT':
            case 'ROLE_PADRE': // Español
                return <ParentDashboard navigate={navigate} darkMode={darkMode} stats={stats} />;
            case 'ROLE_COORDINATION':
            case 'ROLE_COORDINACION': // Español
                return <CoordinationDashboard stats={stats} navigate={navigate} darkMode={darkMode} />;
            case 'ROLE_DIRECTOR':
            case 'ROLE_DIRECCION':
                return <DirectorDashboard stats={stats} navigate={navigate} darkMode={darkMode} />;
            default:
                return <div className="p-10 text-center text-gray-500">Dashboard en construcción para {userRole}</div>;
        }
    };

    const getWelcomeMessage = () => {
        const messages = {
            'ROLE_SUPER_ADMIN': 'Panel de Super Administrador',
            'ROLE_ADMIN': 'Panel de Administración',
            'ROLE_SECRETARY': 'Panel de Secretaría',
            'ROLE_ACCOUNTANT': 'Panel de Contabilidad',
            'ROLE_TEACHER': 'Panel del Docente',
            'ROLE_INFORMATICS': 'Panel de Informática',
            'ROLE_COORDINATION': 'Panel de Coordinación',
            'ROLE_DIRECTOR': 'Panel de Dirección',
            'ROLE_STUDENT': 'Mi Portal Estudiantil',
            'ROLE_PARENT': 'Portal de Padres',
        };
        return messages[userRole] || 'Bienvenido al Sistema Oxford';
    };

    return (
        <div className="space-y-8 p-2">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className={`text-3xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{getWelcomeMessage()}</h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>
                        Hola, {user?.email?.split('@')[0] || 'Usuario'}
                    </p>
                </div>
            </div>

            {loading ? (
                <div className="flex justify-center items-center h-64">
                    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                </div>
            ) : (
                renderDashboard()
            )}
        </div>
    );
};

export default Dashboard;
