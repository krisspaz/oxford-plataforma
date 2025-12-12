import { useState } from 'react';
import { Outlet, Link, useLocation } from 'react-router-dom';
import { Menu, X, Home, Users, BookOpen, Calculator, Activity, LogOut } from 'lucide-react';

const Layout = () => {
    const [isSidebarOpen, setIsSidebarOpen] = useState(true);
    const location = useLocation();

    const menuItems = [
        { name: 'Dashboard', path: '/', icon: Home },
        { name: 'Estudiantes', path: '/students', icon: Users },
        { name: 'Académico (IA)', path: '/academic', icon: BookOpen },
        { name: 'Financiero', path: '/financial', icon: Calculator },
        { name: 'Riesgo IA', path: '/risk-analysis', icon: Activity },
    ];

    return (
        <div className="flex h-screen bg-gray-100">
            {/* Sidebar */}
            <aside
                className={`${isSidebarOpen ? 'w-64' : 'w-20'
                    } bg-oxford-primary text-white transition-all duration-300 flex flex-col shadow-xl`}
            >
                <div className="p-4 flex items-center justify-between border-b border-blue-800">
                    {isSidebarOpen && <span className="font-bold text-xl">Oxford Sys</span>}
                    <button
                        onClick={() => setIsSidebarOpen(!isSidebarOpen)}
                        className="p-1 hover:bg-blue-800 rounded"
                    >
                        {isSidebarOpen ? <X size={20} /> : <Menu size={20} />}
                    </button>
                </div>

                <nav className="flex-1 p-4 space-y-2">
                    {menuItems.map((item) => (
                        <Link
                            key={item.path}
                            to={item.path}
                            className={`flex items-center gap-3 p-3 rounded-lg transition-colors ${location.pathname === item.path
                                ? 'bg-oxford-secondary text-white shadow-md'
                                : 'hover:bg-blue-800 text-gray-300'
                                }`}
                        >
                            <item.icon size={20} />
                            {isSidebarOpen && <span>{item.name}</span>}
                        </Link>
                    ))}
                </nav>

                <div className="p-4 border-t border-blue-800">
                    <button className="flex items-center gap-3 w-full p-2 text-red-300 hover:bg-blue-900 rounded transition-colors bg-opacity-20">
                        <LogOut size={20} />
                        {isSidebarOpen && <span>Cerrar Sesión</span>}
                    </button>
                </div>
            </aside>

            {/* Main Content */}
            <div className="flex-1 flex flex-col overflow-hidden">
                <header className="bg-white shadow-sm p-4 flex justify-between items-center z-10">
                    <h2 className="text-xl font-semibold text-gray-800">
                        {menuItems.find(m => m.path === location.pathname)?.name || 'Colegio Oxford'}
                    </h2>
                    <div className="flex items-center gap-4">
                        <div className="text-right hidden sm:block">
                            <p className="text-sm font-medium text-gray-900">Administrador</p>
                            <p className="text-xs text-gray-500">admin@oxford.edu</p>
                        </div>
                        <div className="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-oxford-primary font-bold">
                            AD
                        </div>
                    </div>
                </header>

                <main className="flex-1 overflow-auto p-6">
                    <Outlet />
                </main>
            </div>
        </div>
    );
};

export default Layout;
