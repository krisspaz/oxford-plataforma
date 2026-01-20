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
    Brain,
    MessageSquare
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
    UserCog, Shield, Menu, Database, Bell, Brain, MessageSquare
};

// Section Color Mapping
const SECTION_THEMES = {
    'Principal': 'obs-blue',
    'Finanzas': 'obs-blue',
    'Gestión Cobros': 'obs-blue',
    'Académico': 'obs-blue',
    'Gestión Académica': 'obs-blue',
    'Organización': 'obs-blue',
    'Reportes': 'obs-blue',
    'Administración': 'obs-purple',
    'Sistema': 'obs-purple',
    'Inscripciones': 'obs-blue',
    'Inteligencia Artificial': 'obs-blue',
    'Docente': 'obs-blue',
    'Estudiante': 'obs-blue',
    'Padres': 'obs-blue',
    'Configuración': 'gray-500',
    'Mis Clases': 'obs-blue',
    'Actividades': 'obs-blue',
    'Calificaciones': 'obs-blue'
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
        const mainRole = user.roles.find(r => r !== 'ROLE_USER');
        return mainRole || user.roles[0];
    }, [user?.roles]);

    const roleLabel = useMemo(() => {
        return ROLE_LABELS[userRole] || 'Usuario';
    }, [userRole]);

    const menuSections = useMemo(() => {
        if (user?.roles && user.roles.length > 0) {
            return getCombinedMenu(user.roles);
        }
        return getMenuForRole(userRole);
    }, [user?.roles, userRole]);

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
        <div className={`flex h-screen ${darkMode ? 'bg-gray-900' : 'bg-white'}`}>
            {/* Sidebar */}
            <aside
                className={`${isSidebarOpen ? 'w-64' : 'w-20'} 
                ${darkMode
                        ? 'bg-gradient-to-b from-gray-900 to-black border-r border-gray-800 text-white'
                        : 'bg-slate-50 border-r border-slate-200 text-slate-600'
                    } transition-all duration-300 flex flex-col relative z-20`}
            >
                {/* Toggle Button */}
                <button
                    onClick={() => setIsSidebarOpen(!isSidebarOpen)}
                    className={`absolute -right-3 top-8 ${darkMode ? 'bg-gray-700 text-gray-200 border-gray-600' : 'bg-white text-slate-600 border-slate-200'} border rounded-full p-1 shadow-md hover:scale-105 transition-all z-50`}
                >
                    {isSidebarOpen ? <ChevronLeft size={16} /> : <ChevronRight size={16} />}
                </button>

                {/* Logo */}
                <div className="p-5 flex items-center gap-3">
                    <div className={`w-12 h-12 bg-white rounded-xl flex items-center justify-center p-1 overflow-hidden ${!darkMode ? 'shadow-sm border border-gray-100' : ''}`}>
                        <img src="/logo-obs.jpg" alt="Logo OBS" className="w-full h-full object-contain" />
                    </div>
                    {isSidebarOpen && (
                        <div>
                            <h1 className={`font-bold text-lg leading-tight ${darkMode ? 'text-white' : 'text-slate-900'}`}>Oxford</h1>
                            <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-slate-500'} opacity-80`}>Portal Estudiantil</p>
                        </div>
                    )}
                </div>

                {/* Navigation */}
                <nav className="flex-1 px-3 py-4 overflow-y-auto">
                    {menuSections.map((section, sectionIndex) => {
                        const themeColor = SECTION_THEMES[section.section] || 'obs-pink';

                        const getActiveClasses = (theme) => {
                            // Light Mode: White Card Effect (Clean/Apple Style)
                            if (!darkMode) return 'bg-white text-oxford-primary font-semibold shadow-sm ring-1 ring-slate-950/5';

                            // Dark Mode: Glowing Effect
                            switch (theme) {
                                case 'obs-green': return 'bg-obs-green text-white';
                                case 'obs-blue': return 'bg-obs-blue text-white';
                                case 'obs-orange': return 'bg-obs-orange text-white';
                                case 'obs-purple': return 'bg-obs-purple text-white';
                                default: return 'bg-obs-pink text-white';
                            }
                        };

                        const getTextClass = (theme) => {
                            // Light Mode: Dark Gray for inactive
                            if (!darkMode) return 'text-slate-500 group-hover:text-slate-900';

                            // Dark Mode: Colored text
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
                                    <p className={`px-4 py-2 text-xs font-bold uppercase tracking-wider flex items-center gap-2 ${darkMode ? 'text-gray-500' : 'text-slate-400'}`}>
                                        <span className={`w-2 h-2 rounded-full ${darkMode ? `bg-${themeColor.replace('obs-', 'obs-')}` : 'bg-slate-300'}`}></span>
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
                                                    ? `${activeClass} ${darkMode ? 'shadow-lg' : ''}`
                                                    : darkMode
                                                        ? 'text-gray-400 hover:bg-white/5 hover:text-white'
                                                        : 'text-slate-500 hover:bg-white hover:shadow-sm hover:text-slate-900'
                                                    }`}
                                                title={!isSidebarOpen ? item.label : undefined}
                                            >
                                                {/* Hover Gradient Effect (Dark Mode Only) */}
                                                {!isActive && darkMode && <div className={`absolute inset-0 opacity-0 group-hover:opacity-10 transition-opacity bg-${themeColor}`}></div>}

                                                <span className={`${isActive ? (darkMode ? 'text-white' : 'text-oxford-primary') : inactiveIconClass} transition-colors ${!darkMode && isActive ? 'text-oxford-primary' : ''}`}>
                                                    {getIcon(item.icon)}
                                                </span>
                                                {isSidebarOpen && <span className="text-sm">{item.label}</span>}

                                                {/* Active Indicator (Dark Mode: Dot) - Light mode uses Card style instead */}
                                                {isActive && isSidebarOpen && darkMode && (
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
                <div className={`p-4 border-t ${darkMode ? 'border-gray-700' : 'border-slate-200 bg-white/50'}`}>
                    {/* User Info */}
                    <div className="flex items-center gap-3 mb-4">
                        <div className={`w-10 h-10 ${darkMode ? 'bg-gray-700 text-white' : 'bg-white text-oxford-primary border border-slate-200'} rounded-full flex items-center justify-center font-bold text-sm shadow-sm`}>
                            {user?.email?.charAt(0)?.toUpperCase() || 'U'}
                        </div>
                        {isSidebarOpen && (
                            <div className="flex-1 min-w-0">
                                <p className={`text-sm font-semibold truncate ${darkMode ? 'text-white' : 'text-slate-800'}`}>
                                    {user?.email?.split('@')[0] || 'Usuario'}
                                </p>
                                <p className={`text-xs truncate ${darkMode ? 'text-gray-400' : 'text-slate-500'}`}>{roleLabel}</p>
                            </div>
                        )}
                    </div>

                    {/* Logout Button */}
                    <button
                        onClick={handleLogout}
                        className={`flex items-center gap-3 w-full px-4 py-2.5 ${darkMode ? 'text-red-400 hover:bg-red-500/10' : 'text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-100'} rounded-xl transition-all font-medium`}
                    >
                        <LogOut size={18} />
                        {isSidebarOpen && <span className="text-sm">Cerrar Sesión</span>}
                    </button>
                </div>
            </aside>

            {/* Main Content */}
            <div className="flex-1 flex flex-col overflow-hidden relative">
                {/* Header */}
                <header className={`${darkMode ? 'bg-gray-800/95 border-gray-700' : 'bg-white/80 border-slate-200'} backdrop-blur-md px-6 py-4 flex justify-between items-center z-10 border-b relative`}>
                    <h2 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-slate-800'}`}>
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
                            className={`p-2 ${darkMode ? 'bg-gray-700 text-yellow-400' : 'bg-slate-100 text-slate-600'} rounded-full transition-colors hover:scale-110`}
                            title={darkMode ? 'Modo Claro' : 'Modo Oscuro'}
                        >
                            {darkMode ? <Sun size={20} /> : <Moon size={20} />}
                        </button>

                        {/* Notifications */}
                        <NotificationCenter />

                        {/* User Avatar */}
                        <div className="flex items-center gap-3">
                            <div className="text-right hidden sm:block">
                                <p className={`text-sm font-semibold ${darkMode ? 'text-white' : 'text-slate-900'}`}>
                                    {user?.email?.split('@')[0] || 'Usuario'}
                                </p>
                                <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-slate-500'}`}>{user?.email || 'usuario@oxford.edu.gt'}</p>
                            </div>
                            <div className={`w-10 h-10 rounded-full flex items-center justify-center text-white font-bold shadow-md ${darkMode ? 'bg-gray-700' : 'bg-[#002046]'}`}>
                                {user?.email?.charAt(0)?.toUpperCase() || 'U'}
                            </div>
                        </div>
                    </div>
                </header>

                {/* Main Content Area */}
                <main className={`flex-1 overflow-auto p-6 ${darkMode ? 'bg-gray-900' : 'bg-white'}`}>
                    <Outlet />
                </main>
            </div>
        </div>
    );
};

export default Layout;
