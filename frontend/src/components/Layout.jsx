import { useState, useMemo } from 'react';
import { Outlet, Link, useLocation, useNavigate } from 'react-router-dom';
import {
    Home,
    BookOpen,
    Calendar,
    Users,
    Settings,
    LogOut,
    GraduationCap,
    DollarSign,
    FileText,
    Bell,
    ChevronLeft,
    ChevronRight,
    Moon,
    Sun,
    UserPlus,
    User,
    CreditCard,
    FileCheck,
    FileSpreadsheet,
    Receipt,
    AlertCircle,
    Package,
    MinusCircle,
    Layers,
    LayoutGrid,
    Book,
    Link as LinkIcon,
    Lock,
    BarChart,
    Edit,
    Clock,
    Bookmark,
    UserCog,
    Shield,
    Menu,
    Database,
    Brain
} from 'lucide-react';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { getMenuForRole, getCombinedMenu, ROLE_LABELS } from '../config/roleMenus';
import NotificationCenter from './NotificationCenter';

// Icon mapping from string to component
const ICON_MAP = {
    Home, BookOpen, Calendar, Users, Settings, GraduationCap, DollarSign,
    FileText, UserPlus, User, CreditCard, FileCheck, FileSpreadsheet,
    Receipt, AlertCircle, Package, MinusCircle, Layers, LayoutGrid,
    Book, Link: LinkIcon, Lock, BarChart, Edit, Clock, Bookmark,
    UserCog, Shield, Menu, Database, Bell, Brain
};

// Section Color Mapping
const SECTION_THEMES = {
    'Principal': 'obs-pink',
    'Finanzas': 'obs-green',
    'Gestión Cobros': 'obs-green',
    'Académico': 'obs-blue',
    'Gestión Académica': 'obs-blue',
    'Organización': 'obs-blue',
    'Reportes': 'obs-blue',
    'Administración': 'obs-purple',
    'Sistema': 'obs-purple',
    'Inscripciones': 'obs-orange',
    'Inteligencia Artificial': 'obs-pink',
    'Docente': 'obs-blue',
    'Estudiante': 'obs-green',
    'Padres': 'obs-orange',
    'Configuración': 'gray-400',
    // Teacher Specific
    'Mis Clases': 'obs-blue',
    'Actividades': 'obs-orange',
    'Calificaciones': 'obs-green'
};

const Layout = () => {
    const [isSidebarOpen, setIsSidebarOpen] = useState(true);
    const location = useLocation();
    const navigate = useNavigate();
    const { user, logout } = useAuth();
    const { darkMode, toggleDarkMode } = useTheme();

    // Get user's primary role
    const userRole = useMemo(() => {
        if (!user?.roles || user.roles.length === 0) return 'ROLE_ALUMNO';
        // Filter out ROLE_USER and get the first actual role
        const mainRole = user.roles.find(r => r !== 'ROLE_USER');
        return mainRole || user.roles[0];
    }, [user?.roles]);

    // Get role label
    const roleLabel = useMemo(() => {
        return ROLE_LABELS[userRole] || 'Usuario';
    }, [userRole]);

    // Get menu items for this role
    const menuSections = useMemo(() => {
        if (user?.roles && user.roles.length > 0) {
            return getCombinedMenu(user.roles);
        }
        return getMenuForRole(userRole);
    }, [user?.roles, userRole]);

    // Flatten menu items for header title lookup
    const allMenuItems = useMemo(() => {
        return menuSections.flatMap(section => section.items);
    }, [menuSections]);

    const handleLogout = () => {
        logout();
        navigate('/login');
    };

    const getIcon = (iconName, size = 20) => {
        const IconComponent = ICON_MAP[iconName];
        return IconComponent ? <IconComponent size={size} /> : <Home size={size} />;
    };

    return (
        <div className={`flex h-screen ${darkMode ? 'bg-gray-900' : 'bg-gray-50'}`}>
            {/* Sidebar */}
            <aside
                className={`${isSidebarOpen ? 'w-64' : 'w-20'} 
                ${darkMode
                        ? 'bg-gradient-to-b from-gray-900 to-black border-r border-gray-800'
                        : 'bg-oxford-primary'
                    } text-white transition-all duration-300 flex flex-col relative`}
            >
                {/* Toggle Button */}
                <button
                    onClick={() => setIsSidebarOpen(!isSidebarOpen)}
                    className={`absolute -right-3 top-8 ${darkMode ? 'bg-gray-700 text-gray-200' : 'bg-oxford-secondary text-white'} rounded-full p-1 shadow-lg hover:bg-opacity-90 transition-colors z-50`}
                >
                    {isSidebarOpen ? <ChevronLeft size={16} /> : <ChevronRight size={16} />}
                </button>

                {/* Logo */}
                <div className="p-5 flex items-center gap-3">
                    <div className={`w-12 h-12 bg-white rounded-xl flex items-center justify-center p-1 overflow-hidden`}>
                        <img src="/logo-obs.jpg" alt="Logo OBS" className="w-full h-full object-contain" />
                    </div>
                    {isSidebarOpen && (
                        <div>
                            <h1 className="font-bold text-lg leading-tight">Oxford</h1>
                            <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-white/70'} opacity-80`}>Portal Estudiantil</p>
                        </div>
                    )}
                </div>

                {/* Navigation - Role-based */}
                <nav className="flex-1 px-3 py-4 overflow-y-auto">
                    {menuSections.map((section, sectionIndex) => {
                        const themeColor = SECTION_THEMES[section.section] || 'obs-pink';
                        // Map tailwind colors since we can't dynamically construct full class names safely in all setups
                        // But here we rely on the safelist or just explicit styles if needed. 
                        // Actually, let's use style objects or known classes if possible.
                        // Ideally we use text-${themeColor} but let's try to map to specific utility classes.

                        const getActiveClasses = (theme) => {
                            switch (theme) {
                                case 'obs-green': return darkMode ? 'bg-obs-green text-white' : 'bg-obs-green text-white';
                                case 'obs-blue': return darkMode ? 'bg-obs-blue text-white' : 'bg-obs-blue text-white';
                                case 'obs-orange': return darkMode ? 'bg-obs-orange text-white' : 'bg-obs-orange text-white';
                                case 'obs-purple': return darkMode ? 'bg-obs-purple text-white' : 'bg-obs-purple text-white';
                                default: return darkMode ? 'bg-obs-pink text-white' : 'bg-obs-pink text-white';
                            }
                        };

                        const getTextClass = (theme) => {
                            switch (theme) {
                                case 'obs-green': return 'text-obs-green';
                                case 'obs-blue': return 'text-obs-blue';
                                case 'obs-orange': return 'text-obs-orange';
                                case 'obs-purple': return 'text-obs-purple';
                                default: return 'text-obs-pink';
                            }
                        };

                        return (
                            <div key={sectionIndex} className="mb-6">
                                {isSidebarOpen && (
                                    <p className={`px-4 py-2 text-xs font-bold uppercase tracking-wider flex items-center gap-2 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                                        <span className={`w-2 h-2 rounded-full bg-${themeColor.replace('obs-', 'obs-')}`}></span>
                                        {section.section}
                                    </p>
                                )}
                                <div className="space-y-1">
                                    {section.items.map((item) => {
                                        const isActive = location.pathname === item.path ||
                                            (item.path === '/dashboard' && location.pathname === '/');

                                        const activeClass = getActiveClasses(themeColor);
                                        const inactiveIconClass = getTextClass(themeColor);

                                        return (
                                            <Link
                                                key={item.id}
                                                to={item.path}
                                                className={`flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative overflow-hidden ${isActive
                                                    ? `${activeClass} shadow-lg font-semibold`
                                                    : darkMode
                                                        ? 'text-gray-400 hover:bg-white/5 hover:text-white'
                                                        : 'text-white/70 hover:bg-white/10 hover:text-white'
                                                    }`}
                                                title={!isSidebarOpen ? item.label : undefined}
                                            >
                                                {/* Hover Gradient Effect */}
                                                {!isActive && <div className={`absolute inset-0 opacity-0 group-hover:opacity-10 transition-opacity bg-${themeColor}`}></div>}

                                                <span className={`${isActive ? 'text-white' : inactiveIconClass} transition-colors group-hover:text-white`}>
                                                    {getIcon(item.icon)}
                                                </span>
                                                {isSidebarOpen && <span className="text-sm">{item.label}</span>}

                                                {/* Active Indicator Dot */}
                                                {isActive && isSidebarOpen && (
                                                    <div className="absolute right-3 w-1.5 h-1.5 rounded-full bg-white/50"></div>
                                                )}
                                            </Link>
                                        );
                                    })}
                                </div>
                            </div>
                        )
                    })}
                </nav>

                {/* User Profile & Logout */}
                <div className={`p-4 border-t ${darkMode ? 'border-gray-700' : 'border-white/20'}`}>
                    {/* User Info */}
                    <div className="flex items-center gap-3 mb-4">
                        <div className={`w-10 h-10 ${darkMode ? 'bg-gray-700' : 'bg-oxford-blue'} rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md`}>
                            {user?.email?.charAt(0)?.toUpperCase() || 'U'}
                        </div>
                        {isSidebarOpen && (
                            <div className="flex-1 min-w-0">
                                <p className="text-sm font-semibold text-white truncate">
                                    {user?.email?.split('@')[0] || 'Usuario'}
                                </p>
                                <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-white/70'} truncate`}>{roleLabel}</p>
                            </div>
                        )}
                    </div>

                    {/* Logout Button */}
                    <button
                        onClick={handleLogout}
                        className={`flex items-center gap-3 w-full px-4 py-2.5 ${darkMode ? 'text-red-400 hover:bg-red-500/20' : 'text-red-200 hover:bg-red-500/20'} hover:text-white rounded-xl transition-all`}
                    >
                        <LogOut size={18} />
                        {isSidebarOpen && <span className="text-sm">Cerrar Sesión</span>}
                    </button>
                </div>
            </aside>

            {/* Main Content */}
            <div className="flex-1 flex flex-col overflow-hidden">
                {/* Header */}
                <header className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} shadow-sm px-6 py-4 flex justify-between items-center z-10 border-b`}>
                    <h2 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                        {allMenuItems.find(m => m.path === location.pathname)?.label ||
                            (location.pathname === '/' ? 'Dashboard' : 'Página')}
                    </h2>
                    <div className="flex items-center gap-4">
                        {/* Role Badge */}
                        <span className={`px-3 py-1 rounded-full text-xs font-semibold ${darkMode ? 'bg-oxford-blue/20 text-oxford-blue' : 'bg-oxford-primary/10 text-oxford-primary'}`}>
                            {roleLabel}
                        </span>

                        {/* Dark Mode Toggle */}
                        <button
                            onClick={toggleDarkMode}
                            className={`p-2 ${darkMode ? 'bg-gray-700 text-yellow-400' : 'bg-gray-100 text-gray-600'} rounded-full transition-colors hover:scale-110`}
                            title={darkMode ? 'Modo Claro' : 'Modo Oscuro'}
                        >
                            {darkMode ? <Sun size={20} /> : <Moon size={20} />}
                        </button>

                        {/* Notifications */}
                        <NotificationCenter />

                        {/* User Avatar */}
                        <div className="flex items-center gap-3">
                            <div className="text-right hidden sm:block">
                                <p className={`text-sm font-semibold ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                                    {user?.email?.split('@')[0] || 'Usuario'}
                                </p>
                                <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{user?.email || 'usuario@oxford.edu.gt'}</p>
                            </div>
                            <div className="w-10 h-10 bg-oxford-primary rounded-full flex items-center justify-center text-white font-bold shadow-md">
                                {user?.email?.charAt(0)?.toUpperCase() || 'U'}
                            </div>
                        </div>
                    </div>
                </header>

                {/* Main Content Area */}
                <main className={`flex-1 overflow-auto p-6 ${darkMode ? 'bg-gray-900' : 'bg-gray-50'}`}>
                    <Outlet />
                </main>
            </div>
        </div>
    );
};

export default Layout;
