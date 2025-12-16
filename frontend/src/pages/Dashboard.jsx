import { Users, AlertTriangle, GraduationCap, UserPlus, CreditCard, RefreshCw, XCircle, TrendingDown, Brain, ChevronRight, BookOpen, FileText, Calendar, Clock, Receipt, Edit, Package } from 'lucide-react';
import { useState, useEffect, useMemo } from 'react';
import { useNavigate } from 'react-router-dom';
import { useTheme } from '../contexts/ThemeContext';
import { useAuth } from '../contexts/AuthContext';

const Dashboard = () => {
    const navigate = useNavigate();
    const { darkMode } = useTheme();
    const { user } = useAuth();
    const [loading, setLoading] = useState(true);

    // Get user's primary role
    const userRole = useMemo(() => {
        if (!user?.roles || user.roles.length === 0) return 'ROLE_ALUMNO';
        const mainRole = user.roles.find(r => r !== 'ROLE_USER');
        return mainRole || user.roles[0];
    }, [user?.roles]);

    useEffect(() => {
        // Simulate loading
        const timer = setTimeout(() => setLoading(false), 500);
        return () => clearTimeout(timer);
    }, []);

    const StatCard = ({ title, value, icon: Icon, color, bg, onClick, isCustomIcon }) => (
        <div
            onClick={onClick}
            className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} rounded-2xl shadow-lg border p-6 flex items-center gap-4 transition-all hover:shadow-xl hover:-translate-y-1 ${onClick ? 'cursor-pointer' : ''}`}
        >
            <div className={`p-4 rounded-xl ${bg} flex items-center justify-center`}>
                {isCustomIcon ? Icon : <Icon size={28} className={color} />}
            </div>
            <div className="flex-1">
                <p className={`text-sm font-medium ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{title}</p>
                <p className={`text-3xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{loading ? '...' : value}</p>
            </div>
        </div>
    );

    const QuickAction = ({ title, icon: Icon, color, bgColor, onClick }) => (
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

    // ADMIN DASHBOARD
    const AdminDashboard = () => (
        <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <StatCard title="Total Estudiantes" value="156" icon={Users} color="text-blue-500" bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'} onClick={() => navigate('/students')} />
                <StatCard title="Ingresos del Mes" value="Q 45,250" icon={() => <span className="text-2xl font-bold text-green-500">Q</span>} color="text-green-500" bg={darkMode ? 'bg-green-900/30' : 'bg-green-100'} isCustomIcon onClick={() => navigate('/financial')} />
                <StatCard title="En Riesgo (IA)" value="8" icon={AlertTriangle} color="text-red-500" bg={darkMode ? 'bg-red-900/30' : 'bg-red-100'} />
                <StatCard title="Promedio General" value="85/100" icon={GraduationCap} color="text-purple-500" bg={darkMode ? 'bg-purple-900/30' : 'bg-purple-100'} />
            </div>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <QuickAction title="Inscribir Alumno" icon={UserPlus} color="text-blue-500" bgColor={darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} onClick={() => navigate('/inscripciones')} />
                <QuickAction title="Registrar Pago" icon={CreditCard} color="text-green-500" bgColor={darkMode ? 'bg-green-900/50' : 'bg-green-100'} onClick={() => navigate('/pagos')} />
                <QuickAction title="Ver Reportes" icon={FileText} color="text-purple-500" bgColor={darkMode ? 'bg-purple-900/50' : 'bg-purple-100'} onClick={() => navigate('/reports')} />
                <QuickAction title="Configuración" icon={RefreshCw} color="text-gray-500" bgColor={darkMode ? 'bg-gray-600' : 'bg-gray-100'} onClick={() => navigate('/settings')} />
            </div>
        </>
    );

    // SECRETARIA DASHBOARD
    const SecretariaDashboard = () => (
        <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <StatCard title="Inscripciones Hoy" value="3" icon={UserPlus} color="text-blue-500" bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'} onClick={() => navigate('/inscripciones')} />
                <StatCard title="Familias Registradas" value="89" icon={Users} color="text-teal-500" bg={darkMode ? 'bg-teal-900/30' : 'bg-teal-100'} onClick={() => navigate('/familias')} />
                <StatCard title="Pagos Pendientes" value="12" icon={CreditCard} color="text-orange-500" bg={darkMode ? 'bg-orange-900/30' : 'bg-orange-100'} onClick={() => navigate('/pagos')} />
                <StatCard title="Contratos del Mes" value="24" icon={FileText} color="text-purple-500" bg={darkMode ? 'bg-purple-900/30' : 'bg-purple-100'} onClick={() => navigate('/contracts')} />
            </div>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <QuickAction title="Nueva Inscripción" icon={UserPlus} color="text-blue-500" bgColor={darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} onClick={() => navigate('/inscripciones')} />
                <QuickAction title="Registrar Pago" icon={CreditCard} color="text-green-500" bgColor={darkMode ? 'bg-green-900/50' : 'bg-green-100'} onClick={() => navigate('/pagos')} />
                <QuickAction title="Generar Contrato" icon={FileText} color="text-purple-500" bgColor={darkMode ? 'bg-purple-900/50' : 'bg-purple-100'} onClick={() => navigate('/contracts')} />
                <QuickAction title="Estado de Cuenta" icon={Receipt} color="text-teal-500" bgColor={darkMode ? 'bg-teal-900/50' : 'bg-teal-100'} onClick={() => navigate('/estado-cuenta')} />
            </div>
        </>
    );

    // CONTABILIDAD DASHBOARD
    const ContabilidadDashboard = () => (
        <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <StatCard title="Ingresos Hoy" value="Q 8,450" icon={() => <span className="text-2xl font-bold text-green-500">Q</span>} color="text-green-500" bg={darkMode ? 'bg-green-900/30' : 'bg-green-100'} isCustomIcon onClick={() => navigate('/corte-dia')} />
                <StatCard title="Facturas Emitidas" value="15" icon={Receipt} color="text-blue-500" bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'} onClick={() => navigate('/comprobantes')} />
                <StatCard title="Anulaciones Pend." value="2" icon={XCircle} color="text-red-500" bg={darkMode ? 'bg-red-900/30' : 'bg-red-100'} onClick={() => navigate('/solicitudes')} />
                <StatCard title="Exoneraciones" value="5" icon={Package} color="text-purple-500" bg={darkMode ? 'bg-purple-900/30' : 'bg-purple-100'} onClick={() => navigate('/exoneration')} />
            </div>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <QuickAction title="Corte del Día" icon={FileText} color="text-green-500" bgColor={darkMode ? 'bg-green-900/50' : 'bg-green-100'} onClick={() => navigate('/corte-dia')} />
                <QuickAction title="Comprobantes" icon={Receipt} color="text-blue-500" bgColor={darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} onClick={() => navigate('/comprobantes')} />
                <QuickAction title="Anulaciones" icon={XCircle} color="text-red-500" bgColor={darkMode ? 'bg-red-900/50' : 'bg-red-100'} onClick={() => navigate('/solicitudes')} />
                <QuickAction title="Paquetes" icon={Package} color="text-purple-500" bgColor={darkMode ? 'bg-purple-900/50' : 'bg-purple-100'} onClick={() => navigate('/paquetes')} />
            </div>
        </>
    );

    // DOCENTE DASHBOARD
    const DocenteDashboard = () => (
        <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <StatCard title="Mis Materias" value="4" icon={BookOpen} color="text-blue-500" bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'} onClick={() => navigate('/mis-materias')} />
                <StatCard title="Estudiantes Totales" value="120" icon={Users} color="text-teal-500" bg={darkMode ? 'bg-teal-900/30' : 'bg-teal-100'} onClick={() => navigate('/students')} />
                <StatCard title="Notas Pendientes" value="8" icon={Edit} color="text-orange-500" bg={darkMode ? 'bg-orange-900/30' : 'bg-orange-100'} onClick={() => navigate('/carga-notas')} />
            </div>
            <div className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} p-6 rounded-2xl shadow-lg border mt-6`}>
                <h3 className={`text-xl font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-900'}`}>Mis Materias Asignadas</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {['Matemáticas - 3ro Primaria A', 'Matemáticas - 3ro Primaria B', 'Ciencias - 4to Primaria', 'Matemáticas - 5to Primaria'].map((materia, i) => (
                        <div key={i} className={`p-4 rounded-xl ${darkMode ? 'bg-gray-700/50' : 'bg-gray-50'} flex items-center gap-4`}>
                            <div className={`p-3 rounded-lg ${darkMode ? 'bg-blue-900/50' : 'bg-blue-100'}`}>
                                <BookOpen size={20} className="text-blue-500" />
                            </div>
                            <div>
                                <p className={`font-semibold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{materia}</p>
                                <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>30 estudiantes</p>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
            <div className="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6">
                <QuickAction title="Cargar Notas" icon={Edit} color="text-blue-500" bgColor={darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} onClick={() => navigate('/carga-notas')} />
                <QuickAction title="Ver Horario" icon={Clock} color="text-green-500" bgColor={darkMode ? 'bg-green-900/50' : 'bg-green-100'} onClick={() => navigate('/horarios')} />
                <QuickAction title="Calendario" icon={Calendar} color="text-purple-500" bgColor={darkMode ? 'bg-purple-900/50' : 'bg-purple-100'} onClick={() => navigate('/calendar')} />
            </div>
        </>
    );

    // COORDINACION DASHBOARD
    const CoordinacionDashboard = () => (
        <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <StatCard title="Docentes Activos" value="18" icon={Users} color="text-blue-500" bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'} onClick={() => navigate('/docentes')} />
                <StatCard title="Materias" value="24" icon={BookOpen} color="text-teal-500" bg={darkMode ? 'bg-teal-900/30' : 'bg-teal-100'} onClick={() => navigate('/materias')} />
                <StatCard title="Bimestre Actual" value="II" icon={Calendar} color="text-purple-500" bg={darkMode ? 'bg-purple-900/30' : 'bg-purple-100'} onClick={() => navigate('/bimestres')} />
                <StatCard title="Notas Por Cerrar" value="5" icon={FileText} color="text-orange-500" bg={darkMode ? 'bg-orange-900/30' : 'bg-orange-100'} onClick={() => navigate('/cierre-notas')} />
            </div>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <QuickAction title="Asignaciones" icon={BookOpen} color="text-blue-500" bgColor={darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} onClick={() => navigate('/asignaciones')} />
                <QuickAction title="Cierre Notas" icon={FileText} color="text-orange-500" bgColor={darkMode ? 'bg-orange-900/50' : 'bg-orange-100'} onClick={() => navigate('/cierre-notas')} />
                <QuickAction title="Reportes" icon={FileText} color="text-purple-500" bgColor={darkMode ? 'bg-purple-900/50' : 'bg-purple-100'} onClick={() => navigate('/reports')} />
                <QuickAction title="Estudiantes" icon={Users} color="text-teal-500" bgColor={darkMode ? 'bg-teal-900/50' : 'bg-teal-100'} onClick={() => navigate('/students')} />
            </div>
        </>
    );

    // ALUMNO DASHBOARD  
    const AlumnoDashboard = () => (
        <>
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <StatCard title="Promedio General" value="82/100" icon={GraduationCap} color="text-blue-500" bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'} onClick={() => navigate('/mis-notas')} />
                <StatCard title="Materias" value="8" icon={BookOpen} color="text-teal-500" bg={darkMode ? 'bg-teal-900/30' : 'bg-teal-100'} />
                <StatCard title="Saldo Pendiente" value="Q 450" icon={() => <span className="text-2xl font-bold text-orange-500">Q</span>} color="text-orange-500" bg={darkMode ? 'bg-orange-900/30' : 'bg-orange-100'} isCustomIcon onClick={() => navigate('/mi-estado-cuenta')} />
            </div>
            <div className="grid grid-cols-2 gap-4 mt-6">
                <QuickAction title="Ver Notas" icon={FileText} color="text-blue-500" bgColor={darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} onClick={() => navigate('/mis-notas')} />
                <QuickAction title="Mi Horario" icon={Clock} color="text-green-500" bgColor={darkMode ? 'bg-green-900/50' : 'bg-green-100'} onClick={() => navigate('/mi-horario')} />
            </div>
        </>
    );

    // PADRE DASHBOARD
    const PadreDashboard = () => (
        <>
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <StatCard title="Hijos Inscritos" value="2" icon={Users} color="text-blue-500" bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'} onClick={() => navigate('/students')} />
                <StatCard title="Promedio Hijos" value="78/100" icon={GraduationCap} color="text-teal-500" bg={darkMode ? 'bg-teal-900/30' : 'bg-teal-100'} onClick={() => navigate('/mis-notas')} />
                <StatCard title="Saldo Total" value="Q 1,200" icon={() => <span className="text-2xl font-bold text-orange-500">Q</span>} color="text-orange-500" bg={darkMode ? 'bg-orange-900/30' : 'bg-orange-100'} isCustomIcon onClick={() => navigate('/estado-cuenta')} />
            </div>
            <div className="grid grid-cols-2 gap-4 mt-6">
                <QuickAction title="Ver Notas" icon={FileText} color="text-blue-500" bgColor={darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} onClick={() => navigate('/mis-notas')} />
                <QuickAction title="Estado de Cuenta" icon={Receipt} color="text-green-500" bgColor={darkMode ? 'bg-green-900/50' : 'bg-green-100'} onClick={() => navigate('/estado-cuenta')} />
            </div>
        </>
    );

    // INFORMATICA DASHBOARD
    const InformaticaDashboard = () => (
        <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <StatCard title="Usuarios Activos" value="45" icon={Users} color="text-blue-500" bg={darkMode ? 'bg-blue-900/30' : 'bg-blue-100'} onClick={() => navigate('/usuarios')} />
                <StatCard title="Roles del Sistema" value="9" icon={Users} color="text-purple-500" bg={darkMode ? 'bg-purple-900/30' : 'bg-purple-100'} onClick={() => navigate('/roles')} />
                <StatCard title="Catálogos" value="12" icon={FileText} color="text-teal-500" bg={darkMode ? 'bg-teal-900/30' : 'bg-teal-100'} onClick={() => navigate('/catalogos')} />
                <StatCard title="Configuraciones" value="8" icon={RefreshCw} color="text-orange-500" bg={darkMode ? 'bg-orange-900/30' : 'bg-orange-100'} onClick={() => navigate('/settings')} />
            </div>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <QuickAction title="Gestión Usuarios" icon={Users} color="text-blue-500" bgColor={darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} onClick={() => navigate('/usuarios')} />
                <QuickAction title="Roles y Permisos" icon={Users} color="text-purple-500" bgColor={darkMode ? 'bg-purple-900/50' : 'bg-purple-100'} onClick={() => navigate('/roles')} />
                <QuickAction title="Catálogos" icon={FileText} color="text-teal-500" bgColor={darkMode ? 'bg-teal-900/50' : 'bg-teal-100'} onClick={() => navigate('/catalogos')} />
                <QuickAction title="Configuración" icon={RefreshCw} color="text-orange-500" bgColor={darkMode ? 'bg-orange-900/50' : 'bg-orange-100'} onClick={() => navigate('/settings')} />
            </div>
        </>
    );

    // Render appropriate dashboard based on role
    const renderDashboard = () => {
        switch (userRole) {
            case 'ROLE_ADMIN':
            case 'ROLE_SUPER_ADMIN':
                return <AdminDashboard />;
            case 'ROLE_SECRETARIA':
                return <SecretariaDashboard />;
            case 'ROLE_CONTABILIDAD':
                return <ContabilidadDashboard />;
            case 'ROLE_DOCENTE':
                return <DocenteDashboard />;
            case 'ROLE_COORDINACION':
                return <CoordinacionDashboard />;
            case 'ROLE_DIRECCION':
                return <CoordinacionDashboard />;
            case 'ROLE_INFORMATICA':
                return <InformaticaDashboard />;
            case 'ROLE_ALUMNO':
                return <AlumnoDashboard />;
            case 'ROLE_PADRE':
                return <PadreDashboard />;
            default:
                return <AdminDashboard />;
        }
    };

    // Get welcome message based on role
    const getWelcomeMessage = () => {
        const messages = {
            'ROLE_ADMIN': 'Panel de Administración',
            'ROLE_SUPER_ADMIN': 'Panel de Super Administrador',
            'ROLE_SECRETARIA': 'Bienvenida a Secretaría',
            'ROLE_CONTABILIDAD': 'Panel de Contabilidad',
            'ROLE_DOCENTE': 'Panel del Docente',
            'ROLE_COORDINACION': 'Panel de Coordinación',
            'ROLE_DIRECCION': 'Panel de Dirección',
            'ROLE_INFORMATICA': 'Panel de Informática',
            'ROLE_ALUMNO': 'Mi Portal Estudiantil',
            'ROLE_PADRE': 'Portal de Padres',
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

            {renderDashboard()}
        </div>
    );
};

export default Dashboard;
