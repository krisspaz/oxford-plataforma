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

// Icon mapping
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
        <div className={`flex h-screen ${darkMode ? 'bg-slate-950' : 'bg-slate-50'} transition-colors duration-300`}>
            {/* Sidebar */}
            <aside
                className={`${isSidebarOpen ? 'w-72' : 'w-20'} 
                ${darkMode
                        ? 'bg-slate-900/95 border-r border-white/5 text-slate-300'
                        : 'bg-white border-r border-slate-200 text-slate-600 shadow-sm'
                    } transition-all duration-300 flex flex-col relative z-30 backdrop-blur-xl`}
            >
                {/* Toggle Button */}
                <button
                    onClick={() => setIsSidebarOpen(!isSidebarOpen)}
                    className={`absolute -right-3 top-8 ${darkMode
                        ? 'bg-slate-800 text-slate-200 border-slate-700 hover:bg-slate-700'
                        : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 shadow-sm'
                        } border rounded-full p-1.5 hover:scale-105 transition-all z-50`}
                >
                    {isSidebarOpen ? <ChevronLeft size={16} /> : <ChevronRight size={16} />}
                </button>

                {/* Logo */}
                <div className="p-6 flex items-center gap-4">
                    <div className={`w-12 h-12 bg-white rounded-2xl flex items-center justify-center p-1.5 overflow-hidden shadow-sm ${!darkMode ? 'border border-slate-100' : ''}`}>
                        <img src="/logo-obs.jpg" alt="Logo OBS" className="w-full h-full object-contain" />
                    </div>
                    {isSidebarOpen && (
                        <div className="animate-fade-in">
                            <h1 className={`font-bold text-xl leading-none mb-0.5 ${darkMode ? 'text-white' : 'text-slate-900'}`}>Oxford</h1>
                            <p className={`text-xs font-medium uppercase tracking-wider ${darkMode ? 'text-slate-500' : 'text-slate-400'}`}>Portal</p>
                        </div>
                    )}
                </div>

                {/* Navigation */}
                <nav className="flex-1 px-4 py-2 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-800">
                    {menuSections.map((section, sectionIndex) => {
                        const themeColor = SECTION_THEMES[section.section] || 'obs-pink';

                        const getActiveClasses = (theme) => {
                            if (!darkMode) {
                                return 'bg-slate-100 text-slate-900 font-semibold shadow-sm ring-1 ring-slate-900/5';
                            }
                            switch (theme) {
                                case 'obs-green': return 'bg-obs-green/10 text-obs-green border border-obs-green/20';
                                case 'obs-blue': return 'bg-obs-blue/10 text-obs-blue border border-obs-blue/20';
                                case 'obs-purple': return 'bg-obs-purple/10 text-obs-purple border border-obs-purple/20';
                                default: return 'bg-obs-pink/10 text-obs-pink border border-obs-pink/20';
                            }
                        };

                        return (
                            <div key={sectionIndex} className="mb-6">
                                {isSidebarOpen && (
                                    <p className={`px-4 py-2 text-xs font-bold uppercase tracking-wider flex items-center gap-2 ${darkMode ? 'text-slate-500' : 'text-slate-400'}`}>
                                        <span className={`w-1.5 h-1.5 rounded-full ${darkMode ? 'bg-slate-600' : 'bg-slate-300'}`}></span>
                                        {section.section}
                                    </p>
                                )}
                                <div className="space-y-1">
                                    {section.items.map((item) => {
                                        const isActive = location.pathname === item.path ||
                                            (item.path === '/dashboard' && location.pathname === '/');
                                        const activeClass = getActiveClasses(themeColor);

                                        return (
                                            <Link
                                                key={item.id}
                                                to={item.path}
                                                className={`flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative ${isActive
                                                    ? activeClass
                                                    : darkMode
                                                        ? 'text-slate-400 hover:bg-white/5 hover:text-slate-200'
                                                        : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900'
                                                    }`}
                                            >
                                                <span className={`${isActive ? 'opacity-100' : 'opacity-70 group-hover:opacity-100'}`}>
                                                    {getIcon(item.icon, 20)}
                                                </span>
                                                {isSidebarOpen && <span className="text-sm font-medium">{item.label}</span>}
                                            </Link>
                                        );
                                    })}
                                </div>
                            </div>
                        )
                    })}
                </nav>

                {/* User Profile */}
                <div className={`p-4 mx-2 mb-2 rounded-2xl ${darkMode ? 'bg-slate-800/50 border border-white/5' : 'bg-slate-50 border border-slate-100'}`}>
                    <div className="flex items-center gap-3 mb-3">
                        <div className={`w-9 h-9 ${darkMode ? 'bg-slate-700 text-white' : 'bg-white text-slate-900 border border-slate-200'} rounded-xl flex items-center justify-center font-bold text-sm shadow-sm`}>
                            {user?.email?.charAt(0)?.toUpperCase() || 'U'}
                        </div>
                        {isSidebarOpen && (
                            <div className="flex-1 min-w-0">
                                <p className={`text-sm font-semibold truncate ${darkMode ? 'text-slate-200' : 'text-slate-900'}`}>
                                    {user?.email?.split('@')[0] || 'Usuario'}
                                </p>
                                <p className={`text-[10px] uppercase tracking-wide truncate ${darkMode ? 'text-slate-500' : 'text-slate-400'}`}>{roleLabel}</p>
                            </div>
                        )}
                    </div>
                    <button
                        onClick={handleLogout}
                        className={`flex items-center justify-center gap-2 w-full px-3 py-2 ${darkMode
                            ? 'text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20'
                            : 'text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-100'
                            } rounded-lg transition-all text-xs font-semibold uppercase tracking-wide`}
                    >
                        <LogOut size={14} />
                        {isSidebarOpen && <span>Cerrar Sesión</span>}
                    </button>
                </div>
            </aside>

            {/* Main Content */}
            <div className="flex-1 flex flex-col overflow-hidden relative">
                <header className={`${darkMode ? 'bg-slate-900/80 border-slate-800' : 'bg-white/80 border-slate-200'} backdrop-blur-xl px-8 py-5 flex justify-between items-center z-20 border-b sticky top-0`}>
                    <div>
                        <h2 className={`text-2xl font-bold tracking-tight ${darkMode ? 'text-white' : 'text-slate-900'}`}>
                            {allMenuItems.find(m => m.path === location.pathname)?.label || (location.pathname === '/' ? 'Vista General' : 'Página')}
                        </h2>
                    </div>
                    <div className="flex items-center gap-4">
                        <button onClick={toggleDarkMode} className={`p-2.5 rounded-full transition-all hover:scale-105 active:scale-95 ${darkMode ? 'bg-slate-800 text-amber-400 hover:bg-slate-700' : 'bg-white text-slate-400 hover:text-indigo-600 shadow-sm border border-slate-200'}`}>
                            {darkMode ? <Sun size={18} /> : <Moon size={18} />}
                        </button>
                        <div className="h-8 w-px bg-slate-200 dark:bg-slate-700 mx-2"></div>
                        <NotificationCenter />
                    </div>
                </header>
                <main className={`flex-1 overflow-auto p-8 relative ${darkMode ? 'bg-slate-950' : 'bg-slate-50'}`}>
                    <div className="max-w-7xl mx-auto pb-10">
                        <Outlet />
                    </div>
                </main>
            </div>
        </div>
    );
};

export default Layout;
