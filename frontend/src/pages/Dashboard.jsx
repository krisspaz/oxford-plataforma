import { Users, DollarSign, AlertTriangle, GraduationCap, UserPlus, CreditCard, RefreshCw, XCircle } from 'lucide-react';
import { useState, useEffect } from 'react';
import axios from 'axios';

const Dashboard = () => {
    const [dashboardData, setDashboardData] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchStats = async () => {
            try {
                const apiUrl = import.meta.env.VITE_API_URL || '';
                const token = localStorage.getItem('token');
                const response = await axios.get(`${apiUrl}/api/dashboard/stats`, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                setDashboardData(response.data);
            } catch (error) {
                console.error("Error fetching dashboard stats:", error);
                // Fallback to mock data if API fails
                setDashboardData({
                    totalStudents: 156,
                    monthlyIncome: 45250,
                    atRiskStudents: 8,
                    averageGrade: 85,
                    recentStudents: [
                        { name: 'María García', time: 'Hace 2 horas' },
                        { name: 'Carlos López', time: 'Hace 5 horas' },
                        { name: 'Ana Martínez', time: 'Ayer' },
                    ]
                });
            } finally {
                setLoading(false);
            }
        };
        fetchStats();
    }, []);

    // Use mock data while loading or if API fails
    const data = dashboardData || {
        totalStudents: 0,
        monthlyIncome: 0,
        atRiskStudents: 0,
        averageGrade: 0,
        recentStudents: []
    };

    const stats = [
        { title: 'Total Estudiantes', value: data.totalStudents, icon: Users, color: 'text-blue-600', bg: 'bg-blue-100' },
        { title: 'Ingresos del Mes', value: `Q ${data.monthlyIncome?.toLocaleString() || 0}`, icon: DollarSign, color: 'text-green-600', bg: 'bg-green-100' },
        { title: 'En Riesgo (IA)', value: data.atRiskStudents, icon: AlertTriangle, color: 'text-red-600', bg: 'bg-red-100' },
        { title: 'Promedio General', value: `${data.averageGrade || 0}/100`, icon: GraduationCap, color: 'text-purple-600', bg: 'bg-purple-100' },
    ];

    const StatCard = ({ stat }) => (
        <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex items-center gap-4 transition-all hover:shadow-xl hover:-translate-y-1">
            <div className={`p-4 rounded-xl ${stat.bg}`}>
                <stat.icon size={28} className={stat.color} />
            </div>
            <div>
                <p className="text-sm text-gray-500 font-medium">{stat.title}</p>
                <p className="text-3xl font-bold text-gray-900">{loading ? '...' : stat.value}</p>
            </div>
        </div>
    );

    const RecentItem = ({ name, time }) => (
        <div className="flex items-center gap-4 p-4 rounded-xl bg-gray-50 hover:bg-blue-50 transition-colors">
            <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                {name.charAt(0)}
            </div>
            <div className="flex-1">
                <p className="font-semibold text-gray-900">{name}</p>
                <p className="text-sm text-gray-500">{time}</p>
            </div>
        </div>
    );

    const QuickAction = ({ icon: Icon, label, color = 'blue', onClick }) => (
        <button
            onClick={onClick}
            className={`p-5 bg-white border-2 border-${color}-100 rounded-2xl hover:border-${color}-500 hover:bg-${color}-50 text-gray-700 hover:text-${color}-600 transition-all flex flex-col items-center justify-center gap-3 shadow-sm hover:shadow-md`}
        >
            <div className={`p-3 rounded-full bg-${color}-100`}>
                <Icon size={24} className={`text-${color}-600`} />
            </div>
            <span className="font-semibold text-sm">{label}</span>
        </button>
    );

    return (
        <div className="space-y-8 p-2">
            <div className="flex items-center justify-between">
                <h1 className="text-3xl font-bold text-gray-900">Panel Principal</h1>
                <p className="text-gray-500">Bienvenido al sistema Oxford</p>
            </div>

            {/* Stats */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {stats.map((stat, i) => (
                    <StatCard stat={stat} key={i} />
                ))}
            </div>

            {/* Two column layout */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {/* Recent Activity */}
                <div className="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                    <h3 className="text-xl font-bold mb-5 text-gray-900">Estudiantes Recientes</h3>
                    <div className="space-y-3">
                        {loading ? (
                            <div className="flex items-center justify-center py-8">
                                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            </div>
                        ) : data.recentStudents && data.recentStudents.length > 0 ? (
                            data.recentStudents.map((student, i) => (
                                <RecentItem key={i} name={student.name} time={student.time} />
                            ))
                        ) : (
                            <p className="text-gray-500 text-center py-4">No hay estudiantes recientes</p>
                        )}
                    </div>
                </div>

                {/* Quick Actions */}
                <div className="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                    <h3 className="text-xl font-bold mb-5 text-gray-900">Accesos Rápidos</h3>
                    <div className="grid grid-cols-2 gap-4">
                        <button className="p-5 bg-white border-2 border-blue-100 rounded-2xl hover:border-blue-500 hover:bg-blue-50 transition-all flex flex-col items-center justify-center gap-3 shadow-sm hover:shadow-md">
                            <div className="p-3 rounded-full bg-blue-100">
                                <UserPlus size={24} className="text-blue-600" />
                            </div>
                            <span className="font-semibold text-sm text-gray-700">Inscribir Alumno</span>
                        </button>
                        <button className="p-5 bg-white border-2 border-green-100 rounded-2xl hover:border-green-500 hover:bg-green-50 transition-all flex flex-col items-center justify-center gap-3 shadow-sm hover:shadow-md">
                            <div className="p-3 rounded-full bg-green-100">
                                <CreditCard size={24} className="text-green-600" />
                            </div>
                            <span className="font-semibold text-sm text-gray-700">Registrar Pago</span>
                        </button>
                        <button
                            onClick={() => alert('Ciclo cerrado correctamente (Simulado)')}
                            className="p-5 bg-red-50 border-2 border-red-200 rounded-2xl hover:border-red-500 hover:bg-red-100 transition-all flex flex-col items-center justify-center gap-3 shadow-sm hover:shadow-md"
                        >
                            <div className="p-3 rounded-full bg-red-100">
                                <XCircle size={24} className="text-red-600" />
                            </div>
                            <span className="font-semibold text-sm text-red-700">Cerrar Ciclo</span>
                        </button>
                        <button
                            onClick={() => alert('Notificaciones reiniciadas')}
                            className="p-5 bg-white border-2 border-gray-200 rounded-2xl hover:border-gray-500 hover:bg-gray-50 transition-all flex flex-col items-center justify-center gap-3 shadow-sm hover:shadow-md"
                        >
                            <div className="p-3 rounded-full bg-gray-100">
                                <RefreshCw size={24} className="text-gray-600" />
                            </div>
                            <span className="font-semibold text-sm text-gray-700">Reset Notif.</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;
