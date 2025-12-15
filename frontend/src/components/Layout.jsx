import { useState } from 'react';
import { Outlet, Link, useLocation, useNavigate } from 'react-router-dom';
import {
    Home,
    BookOpen,
    Calendar,
    CheckSquare,
    Users,
    Settings,
    LogOut,
    GraduationCap,
    DollarSign,
    FileText,
    Bell,
    ChevronLeft,
    ChevronRight
} from 'lucide-react';
import { useAuth } from '../contexts/AuthContext';

const Layout = () => {
    const [isSidebarOpen, setIsSidebarOpen] = useState(true);
    const location = useLocation();
    const navigate = useNavigate();
    const { user, logout } = useAuth();

    const menuItems = [
        { name: 'Inicio', path: '/', icon: Home },
        { name: 'Estudiantes', path: '/students', icon: Users },
        { name: 'Académico (IA)', path: '/academic', icon: GraduationCap },
        { name: 'Calendario', path: '/calendar', icon: Calendar },
        { name: 'Financiero', path: '/financial', icon: DollarSign },
        { name: 'Exoneraciones', path: '/exonerations', icon: FileText },
        { name: 'Contratos', path: '/contracts', icon: BookOpen },
        { name: 'Reportes', path: '/reports', icon: FileText },
        { name: 'Configuración', path: '/settings', icon: Settings },
    ];

    const handleLogout = () => {
        logout();
        navigate('/login');
    };

    return (
        <div className="flex h-screen bg-gray-50">
            {/* Sidebar */}
            <aside
                className={`${isSidebarOpen ? 'w-64' : 'w-20'} 
                bg-gradient-to-b from-teal-500 to-teal-700 text-white transition-all duration-300 flex flex-col relative`}
            >
                {/* Toggle Button */}
                <button
                    onClick={() => setIsSidebarOpen(!isSidebarOpen)}
                    className="absolute -right-3 top-8 bg-white text-teal-600 rounded-full p-1 shadow-lg hover:bg-gray-100 transition-colors z-50"
                >
                    {isSidebarOpen ? <ChevronLeft size={16} /> : <ChevronRight size={16} />}
                </button>

                {/* Logo */}
                <div className="p-5 flex items-center gap-3">
                    <div className="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <GraduationCap size={24} className="text-white" />
                    </div>
                    {isSidebarOpen && (
                        <div>
                            <h1 className="font-bold text-lg leading-tight">Oxford</h1>
                            <p className="text-xs text-teal-100 opacity-80">Portal Estudiantil</p>
                        </div>
                    )}
                </div>

                {/* Navigation */}
                <nav className="flex-1 px-3 py-4 space-y-1">
                    {menuItems.map((item) => {
                        const isActive = location.pathname === item.path;
                        return (
                            <Link
                                key={item.path}
                                to={item.path}
                                className={`flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 ${isActive
                                        ? 'bg-white text-teal-700 shadow-lg font-semibold'
                                        : 'text-teal-100 hover:bg-white/10'
                                    }`}
                            >
                                <item.icon size={20} />
                                {isSidebarOpen && <span className="text-sm">{item.name}</span>}
                            </Link>
                        );
                    })}
                </nav>

                {/* User Profile & Logout */}
                <div className="p-4 border-t border-white/20">
                    {/* User Info */}
                    <div className="flex items-center gap-3 mb-4">
                        <div className="w-10 h-10 bg-teal-800 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md">
                            {user?.email?.charAt(0)?.toUpperCase() || 'U'}
                        </div>
                        {isSidebarOpen && (
                            <div className="flex-1 min-w-0">
                                <p className="text-sm font-semibold text-white truncate">
                                    {user?.email?.split('@')[0] || 'Usuario'}
                                </p>
                                <p className="text-xs text-teal-200 truncate">Administrador</p>
                            </div>
                        )}
                    </div>

                    {/* Logout Button */}
                    <button
                        onClick={handleLogout}
                        className="flex items-center gap-3 w-full px-4 py-2.5 text-red-200 hover:bg-red-500/20 hover:text-white rounded-xl transition-all"
                    >
                        <LogOut size={18} />
                        {isSidebarOpen && <span className="text-sm">Cerrar Sesión</span>}
                    </button>
                </div>
            </aside>

            {/* Main Content */}
            <div className="flex-1 flex flex-col overflow-hidden">
                {/* Header */}
                <header className="bg-white shadow-sm px-6 py-4 flex justify-between items-center z-10 border-b border-gray-100">
                    <h2 className="text-2xl font-bold text-gray-800">
                        {menuItems.find(m => m.path === location.pathname)?.name || 'Dashboard'}
                    </h2>
                    <div className="flex items-center gap-4">
                        {/* Notifications */}
                        <button className="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors">
                            <Bell size={22} />
                            <span className="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        {/* User Avatar */}
                        <div className="flex items-center gap-3">
                            <div className="text-right hidden sm:block">
                                <p className="text-sm font-semibold text-gray-900">
                                    {user?.email?.split('@')[0] || 'Administrador'}
                                </p>
                                <p className="text-xs text-gray-500">{user?.email || 'admin@oxford.edu'}</p>
                            </div>
                            <div className="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-700 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                                {user?.email?.charAt(0)?.toUpperCase() || 'A'}
                            </div>
                        </div>
                    </div>
                </header>

                {/* Main Content Area */}
                <main className="flex-1 overflow-auto p-6 bg-gray-50">
                    <Outlet />
                </main>
            </div>
        </div>
    );
};

export default Layout;
