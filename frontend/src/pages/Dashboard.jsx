import { Users, DollarSign, AlertTriangle, GraduationCap } from 'lucide-react';

const Dashboard = () => {
    const stats = [
        { title: 'Total Estudiantes', value: '1,234', icon: Users, color: 'text-blue-600', bg: 'bg-blue-100' },
        { title: 'Ingresos del Mes', value: 'Q 45,670', icon: DollarSign, color: 'text-green-600', bg: 'bg-green-100' },
        { title: 'En Riesgo (IA)', value: '12', icon: AlertTriangle, color: 'text-red-600', bg: 'bg-red-100' },
        { title: 'Promedio General', value: '85/100', icon: GraduationCap, color: 'text-purple-600', bg: 'bg-purple-100' },
    ];

    const StatCard = ({ stat }) => (
        <div className="bg-white rounded-xl shadow-sm p-6 flex items-center gap-4 transition-all hover:scale-[1.03] hover:shadow-md cursor-pointer">
            <div className={`p-3 rounded-full ${stat.bg} ${stat.color}`}>
                <stat.icon size={24} />
            </div>
            <div>
                <p className="text-sm text-gray-500 font-medium">{stat.title}</p>
                <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
            </div>
        </div>
    );

    const RecentItem = ({ time }) => (
        <div className="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
            <div className="w-10 h-10 bg-gray-200 rounded-full" />
            <div>
                <p className="text-sm font-medium text-gray-900">Estudiante registrado</p>
                <p className="text-xs text-gray-500">Hace {time} horas</p>
            </div>
        </div>
    );

    const QuickAction = ({ icon: Icon, label }) => (
        <button className="p-4 border-2 border-dashed border-gray-300 rounded-xl hover:border-blue-600 hover:bg-blue-50 text-gray-600 hover:text-blue-600 transition-all flex flex-col items-center justify-center gap-2">
            <Icon size={24} />
            <span className="font-medium">{label}</span>
        </button>
    );

    return (
        <div className="space-y-8">
            <h1 className="text-3xl font-bold text-gray-900">Panel Principal</h1>

            {/* Stats */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {stats.map((stat, i) => (
                    <StatCard stat={stat} key={i} />
                ))}
            </div>

            {/* Two column layout */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {/* Recent Activity */}
                <div className="bg-white p-6 rounded-xl shadow-sm">
                    <h3 className="text-lg font-bold mb-4">Actividad Reciente</h3>
                    <div className="space-y-3">
                        {[1, 2, 3].map((t) => (
                            <RecentItem key={t} time={t} />
                        ))}
                    </div>
                </div>

                {/* Quick Actions */}
                <div className="bg-white p-6 rounded-xl shadow-sm">
                    <h3 className="text-lg font-bold mb-4">Accesos Rápidos</h3>
                    <div className="grid grid-cols-2 gap-4">
                        <QuickAction icon={Users} label="Inscribir Alumno" />
                        <QuickAction icon={DollarSign} label="Registrar Pago" />
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;
