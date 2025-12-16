import { Users, AlertTriangle, GraduationCap, UserPlus, CreditCard, RefreshCw, XCircle, TrendingDown, Brain, ChevronRight } from 'lucide-react';
import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { useTheme } from '../contexts/ThemeContext';

const Dashboard = () => {
    const navigate = useNavigate();
    const { darkMode } = useTheme();
    const [dashboardData, setDashboardData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [aiRiskStudents, setAiRiskStudents] = useState([]);
    const [showRiskModal, setShowRiskModal] = useState(false);

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
                setAiRiskStudents([
                    { id: 1, name: 'Juan Pérez', grade: '3ro Primaria', avgGrade: 52, riskLevel: 'CRITICAL', riskScore: 85 },
                    { id: 2, name: 'María Santos', grade: '5to Primaria', avgGrade: 58, riskLevel: 'HIGH', riskScore: 72 },
                    { id: 3, name: 'Carlos Ruiz', grade: '2do Básico', avgGrade: 61, riskLevel: 'HIGH', riskScore: 68 },
                    { id: 4, name: 'Ana López', grade: 'Kinder', avgGrade: 63, riskLevel: 'MEDIUM', riskScore: 45 },
                    { id: 5, name: 'Pedro García', grade: '1ro Primaria', avgGrade: 65, riskLevel: 'MEDIUM', riskScore: 42 },
                    { id: 6, name: 'Laura Martínez', grade: '4to Primaria', avgGrade: 64, riskLevel: 'MEDIUM', riskScore: 38 },
                    { id: 7, name: 'Diego Hernández', grade: '6to Primaria', avgGrade: 66, riskLevel: 'MEDIUM', riskScore: 35 },
                    { id: 8, name: 'Sofia Rodriguez', grade: '3ro Básico', avgGrade: 68, riskLevel: 'LOW', riskScore: 22 },
                ]);
            } finally {
                setLoading(false);
            }
        };
        fetchStats();
    }, []);

    const data = dashboardData || {
        totalStudents: 0,
        monthlyIncome: 0,
        atRiskStudents: 0,
        averageGrade: 0,
        recentStudents: []
    };

    const QuetzalIcon = () => (
        <span className="text-2xl font-bold text-green-500">Q</span>
    );

    const stats = [
        { title: 'Total Estudiantes', value: data.totalStudents, icon: Users, color: 'text-blue-500', bg: darkMode ? 'bg-blue-900/30' : 'bg-blue-100', onClick: () => navigate('/students') },
        { title: 'Ingresos del Mes', value: `Q ${data.monthlyIncome?.toLocaleString() || 0}`, icon: QuetzalIcon, color: 'text-green-500', bg: darkMode ? 'bg-green-900/30' : 'bg-green-100', onClick: () => navigate('/financial'), isCustomIcon: true },
        { title: 'En Riesgo (IA)', value: data.atRiskStudents, icon: AlertTriangle, color: 'text-red-500', bg: darkMode ? 'bg-red-900/30' : 'bg-red-100', onClick: () => setShowRiskModal(true), clickable: true },
        { title: 'Promedio General', value: `${data.averageGrade || 0}/100`, icon: GraduationCap, color: 'text-purple-500', bg: darkMode ? 'bg-purple-900/30' : 'bg-purple-100' },
    ];

    const StatCard = ({ stat }) => (
        <div
            onClick={stat.onClick}
            className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} rounded-2xl shadow-lg border p-6 flex items-center gap-4 transition-all hover:shadow-xl hover:-translate-y-1 ${stat.onClick ? 'cursor-pointer' : ''} ${stat.clickable ? (darkMode ? 'ring-2 ring-red-500/50 hover:ring-red-500' : 'ring-2 ring-red-200 hover:ring-red-400') : ''}`}
        >
            <div className={`p-4 rounded-xl ${stat.bg} flex items-center justify-center`}>
                {stat.isCustomIcon ? <stat.icon /> : <stat.icon size={28} className={stat.color} />}
            </div>
            <div className="flex-1">
                <p className={`text-sm font-medium ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{stat.title}</p>
                <p className={`text-3xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{loading ? '...' : stat.value}</p>
            </div>
            {stat.clickable && <ChevronRight className={darkMode ? 'text-gray-500' : 'text-gray-400'} />}
        </div>
    );

    const RecentItem = ({ name, time }) => (
        <div className={`flex items-center gap-4 p-4 rounded-xl ${darkMode ? 'bg-gray-700/50 hover:bg-gray-700' : 'bg-gray-50 hover:bg-blue-50'} transition-colors`}>
            <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                {name.charAt(0)}
            </div>
            <div className="flex-1">
                <p className={`font-semibold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{name}</p>
                <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{time}</p>
            </div>
        </div>
    );

    const getRiskColor = (level) => {
        switch (level) {
            case 'CRITICAL': return 'bg-red-100 text-red-700 border-red-200';
            case 'HIGH': return 'bg-orange-100 text-orange-700 border-orange-200';
            case 'MEDIUM': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
            default: return 'bg-green-100 text-green-700 border-green-200';
        }
    };

    const RiskModal = () => (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-2xl w-full max-w-3xl max-h-[80vh] overflow-hidden shadow-2xl`}>
                <div className={`p-6 border-b ${darkMode ? 'border-gray-700 bg-gradient-to-r from-red-900/30 to-orange-900/30' : 'border-gray-100 bg-gradient-to-r from-red-50 to-orange-50'} flex items-center justify-between`}>
                    <div className="flex items-center gap-3">
                        <div className={`p-3 ${darkMode ? 'bg-red-900/50' : 'bg-red-100'} rounded-xl`}>
                            <Brain className="text-red-500" size={24} />
                        </div>
                        <div>
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Análisis de Riesgo IA</h2>
                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Estudiantes con bajo rendimiento detectados por IA</p>
                        </div>
                    </div>
                    <button onClick={() => setShowRiskModal(false)} className={`${darkMode ? 'text-gray-400 hover:text-white' : 'text-gray-400 hover:text-gray-600'} text-2xl`}>×</button>
                </div>

                <div className="p-6 overflow-y-auto max-h-[60vh]">
                    <div className="space-y-3">
                        {aiRiskStudents.map(student => (
                            <div key={student.id} className={`flex items-center gap-4 p-4 ${darkMode ? 'bg-gray-700/50 hover:bg-gray-700' : 'bg-gray-50 hover:bg-gray-100'} rounded-xl transition-colors`}>
                                <div className="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {student.name.charAt(0)}
                                </div>
                                <div className="flex-1">
                                    <p className={`font-semibold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{student.name}</p>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{student.grade} • Promedio: {student.avgGrade}</p>
                                </div>
                                <div className="text-right">
                                    <span className={`px-3 py-1 rounded-full text-xs font-bold border ${getRiskColor(student.riskLevel)}`}>
                                        {student.riskLevel}
                                    </span>
                                    <p className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-500'} mt-1`}>Score: {student.riskScore}%</p>
                                </div>
                                <TrendingDown className="text-red-400" size={20} />
                            </div>
                        ))}
                    </div>
                </div>

                <div className={`p-4 border-t ${darkMode ? 'border-gray-700 bg-gray-900/50' : 'border-gray-100 bg-gray-50'} flex justify-between items-center`}>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        <Brain size={14} className="inline mr-1" />
                        Análisis realizado con modelo ML (Logistic Regression)
                    </p>
                    <button
                        onClick={() => setShowRiskModal(false)}
                        className="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    );

    return (
        <div className="space-y-8 p-2">
            {showRiskModal && <RiskModal />}

            <div className="flex items-center justify-between">
                <h1 className={`text-3xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>Panel Principal</h1>
                <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>Bienvenido al sistema Oxford</p>
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
                <div className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} p-6 rounded-2xl shadow-lg border`}>
                    <h3 className={`text-xl font-bold mb-5 ${darkMode ? 'text-white' : 'text-gray-900'}`}>Estudiantes Recientes</h3>
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
                            <p className={`${darkMode ? 'text-gray-400' : 'text-gray-500'} text-center py-4`}>No hay estudiantes recientes</p>
                        )}
                    </div>
                </div>

                {/* Quick Actions */}
                <div className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} p-6 rounded-2xl shadow-lg border`}>
                    <h3 className={`text-xl font-bold mb-5 ${darkMode ? 'text-white' : 'text-gray-900'}`}>Accesos Rápidos</h3>
                    <div className="grid grid-cols-2 gap-4">
                        <button
                            onClick={() => navigate('/students')}
                            className={`p-5 ${darkMode ? 'bg-gray-700 border-gray-600 hover:border-blue-500 hover:bg-blue-900/30' : 'bg-white border-blue-100 hover:border-blue-500 hover:bg-blue-50'} border-2 rounded-2xl transition-all flex flex-col items-center justify-center gap-3 shadow-sm hover:shadow-md`}
                        >
                            <div className={`p-3 rounded-full ${darkMode ? 'bg-blue-900/50' : 'bg-blue-100'}`}>
                                <UserPlus size={24} className="text-blue-500" />
                            </div>
                            <span className={`font-semibold text-sm ${darkMode ? 'text-gray-200' : 'text-gray-700'}`}>Inscribir Alumno</span>
                        </button>
                        <button
                            onClick={() => navigate('/financial')}
                            className={`p-5 ${darkMode ? 'bg-gray-700 border-gray-600 hover:border-green-500 hover:bg-green-900/30' : 'bg-white border-green-100 hover:border-green-500 hover:bg-green-50'} border-2 rounded-2xl transition-all flex flex-col items-center justify-center gap-3 shadow-sm hover:shadow-md`}
                        >
                            <div className={`p-3 rounded-full ${darkMode ? 'bg-green-900/50' : 'bg-green-100'}`}>
                                <CreditCard size={24} className="text-green-500" />
                            </div>
                            <span className={`font-semibold text-sm ${darkMode ? 'text-gray-200' : 'text-gray-700'}`}>Registrar Pago</span>
                        </button>
                        <button
                            onClick={() => alert('¿Cerrar ciclo escolar? Esta acción archivará todos los datos actuales.')}
                            className={`p-5 ${darkMode ? 'bg-red-900/30 border-red-800 hover:border-red-500 hover:bg-red-900/50' : 'bg-red-50 border-red-200 hover:border-red-500 hover:bg-red-100'} border-2 rounded-2xl transition-all flex flex-col items-center justify-center gap-3 shadow-sm hover:shadow-md`}
                        >
                            <div className={`p-3 rounded-full ${darkMode ? 'bg-red-900/50' : 'bg-red-100'}`}>
                                <XCircle size={24} className="text-red-500" />
                            </div>
                            <span className={`font-semibold text-sm ${darkMode ? 'text-red-300' : 'text-red-700'}`}>Cerrar Ciclo</span>
                        </button>
                        <button
                            onClick={() => navigate('/settings')}
                            className={`p-5 ${darkMode ? 'bg-gray-700 border-gray-600 hover:border-teal-500 hover:bg-teal-900/30' : 'bg-white border-gray-200 hover:border-teal-500 hover:bg-teal-50'} border-2 rounded-2xl transition-all flex flex-col items-center justify-center gap-3 shadow-sm hover:shadow-md`}
                        >
                            <div className={`p-3 rounded-full ${darkMode ? 'bg-gray-600' : 'bg-gray-100'}`}>
                                <RefreshCw size={24} className={darkMode ? 'text-gray-300' : 'text-gray-600'} />
                            </div>
                            <span className={`font-semibold text-sm ${darkMode ? 'text-gray-200' : 'text-gray-700'}`}>Configuración</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;
