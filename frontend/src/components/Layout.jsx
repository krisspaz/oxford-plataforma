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
    Database
} from 'lucide-react';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { getMenuForRole, ROLE_LABELS } from '../config/roleMenus';

// Icon mapping from string to component
const ICON_MAP = {
    Home, BookOpen, Calendar, Users, Settings, GraduationCap, DollarSign,
    FileText, UserPlus, User, CreditCard, FileCheck, FileSpreadsheet,
    Receipt, AlertCircle, Package, MinusCircle, Layers, LayoutGrid,
    Book, Link: LinkIcon, Lock, BarChart, Edit, Clock, Bookmark,
    UserCog, Shield, Menu, Database, Bell
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
        return getMenuForRole(userRole);
    }, [userRole]);

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
                        ? 'bg-gradient-to-b from-gray-800 to-gray-900 border-r border-gray-700'
                        : 'bg-gradient-to-b from-teal-500 to-teal-700'
                    } text-white transition-all duration-300 flex flex-col relative`}
            >
                {/* Toggle Button */}
                <button
                    onClick={() => setIsSidebarOpen(!isSidebarOpen)}
                    className={`absolute -right-3 top-8 ${darkMode ? 'bg-gray-700 text-gray-200' : 'bg-white text-teal-600'} rounded-full p-1 shadow-lg hover:bg-opacity-90 transition-colors z-50`}
                >
                    {isSidebarOpen ? <ChevronLeft size={16} /> : <ChevronRight size={16} />}
                </button>

                {/* Logo */}
                <div className="p-5 flex items-center gap-3">
                    <div className={`w-10 h-10 ${darkMode ? 'bg-teal-600/30' : 'bg-white/20'} rounded-xl flex items-center justify-center`}>
                        <GraduationCap size={24} className="text-white" />
                    </div>
                    {isSidebarOpen && (
                        <div>
                            <h1 className="font-bold text-lg leading-tight">Oxford</h1>
                            <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-teal-100'} opacity-80`}>Portal Estudiantil</p>
                        </div>
                    )}
                </div>

                {/* Navigation - Role-based */}
                <nav className="flex-1 px-3 py-4 overflow-y-auto">
                    {menuSections.map((section, sectionIndex) => (
                        <div key={sectionIndex} className="mb-4">
                            {isSidebarOpen && (
                                <p className={`px-4 py-2 text-xs font-semibold uppercase tracking-wider ${darkMode ? 'text-gray-500' : 'text-teal-200/70'}`}>
                                    {section.section}
                                </p>
                            )}
                            <div className="space-y-1">
                                {section.items.map((item) => {
                                    const isActive = location.pathname === item.path ||
                                        (item.path === '/dashboard' && location.pathname === '/');
                                    return (
                                        <Link
                                            key={item.id}
                                            to={item.path}
                                            className={`flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 ${isActive
                                                ? darkMode
                                                    ? 'bg-teal-600 text-white shadow-lg font-semibold'
                                                    : 'bg-white text-teal-700 shadow-lg font-semibold'
                                                : darkMode
                                                    ? 'text-gray-300 hover:bg-gray-700/50'
                                                    : 'text-teal-100 hover:bg-white/10'
                                                }`}
                                            title={!isSidebarOpen ? item.label : undefined}
                                        >
                                            {getIcon(item.icon)}
                                            {isSidebarOpen && <span className="text-sm">{item.label}</span>}
                                        </Link>
                                    );
                                })}
                            </div>
                        </div>
                    ))}
                </nav>

                {/* User Profile & Logout */}
                <div className={`p-4 border-t ${darkMode ? 'border-gray-700' : 'border-white/20'}`}>
                    {/* User Info */}
                    <div className="flex items-center gap-3 mb-4">
                        <div className={`w-10 h-10 ${darkMode ? 'bg-gray-700' : 'bg-teal-800'} rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md`}>
                            {user?.email?.charAt(0)?.toUpperCase() || 'U'}
                        </div>
                        {isSidebarOpen && (
                            <div className="flex-1 min-w-0">
                                <p className="text-sm font-semibold text-white truncate">
                                    {user?.email?.split('@')[0] || 'Usuario'}
                                </p>
                                <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-teal-200'} truncate`}>{roleLabel}</p>
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
                        <span className={`px-3 py-1 rounded-full text-xs font-semibold ${darkMode ? 'bg-teal-900/50 text-teal-300' : 'bg-teal-100 text-teal-700'}`}>
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
                        <button className={`relative p-2 ${darkMode ? 'text-gray-400 hover:text-white hover:bg-gray-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'} rounded-full transition-colors`}>
                            <Bell size={22} />
                            <span className="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        {/* User Avatar */}
                        <div className="flex items-center gap-3">
                            <div className="text-right hidden sm:block">
                                <p className={`text-sm font-semibold ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                                    {user?.email?.split('@')[0] || 'Usuario'}
                                </p>
                                <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{user?.email || 'usuario@oxford.edu.gt'}</p>
                            </div>
                            <div className="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-700 rounded-full flex items-center justify-center text-white font-bold shadow-md">
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
