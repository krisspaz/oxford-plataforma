import React, { useEffect, useState } from 'react';
import { FileText, Download, TrendingUp, AlertCircle } from 'lucide-react';
import api from '../../../services/api';
import { useAuth } from '../../../contexts/AuthContext';

const StudentGradesPage = () => {
    const { user } = useAuth();
    const [grades, setGrades] = useState([]);
    const [loading, setLoading] = useState(true);
    const [stats, setStats] = useState({ average: 0, highest: 0, lowest: 0 });

    useEffect(() => {
        fetchGrades();
    }, []);

    const fetchGrades = async () => {
        try {
            // Simulated API call - Replace with real endpoint like /api/student/grades
            // const { data } = await api.get('/student/grades');

            // Mock Data for MVP
            const mockData = [
                { id: 1, subject: 'Matemáticas', b1: 85, b2: 90, b3: 88, b4: null, final: null },
                { id: 2, subject: 'Ciencias Naturales', b1: 92, b2: 89, b3: 95, b4: null, final: null },
                { id: 3, subject: 'Idioma Español', b1: 78, b2: 82, b3: 80, b4: null, final: null },
                { id: 4, subject: 'Estudios Sociales', b1: 88, b2: 85, b3: 90, b4: null, final: null },
                { id: 5, subject: 'Inglés', b1: 95, b2: 98, b3: 96, b4: null, final: null },
            ];

            setGrades(mockData);
            calculateStats(mockData);
        } catch (error) {
            console.error("Error fetching grades:", error);
        } finally {
            setLoading(false);
        }
    };

    const calculateStats = (data) => {
        if (!data.length) return;
        const scores = data.flatMap(d => [d.b1, d.b2, d.b3, d.b4].filter(g => g !== null));
        const avg = scores.reduce((a, b) => a + b, 0) / scores.length;
        setStats({
            average: avg.toFixed(1),
            highest: Math.max(...scores),
            lowest: Math.min(...scores)
        });
    };

    if (loading) return <div className="p-10 text-center">Cargando notas...</div>;

    return (
        <div className="p-6 max-w-7xl mx-auto">
            <header className="mb-8">
                <h1 className="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <FileText className="text-indigo-600" />
                    Mis Calificaciones
                </h1>
                <p className="text-gray-500 mt-2">Ciclo Escolar 2026</p>
            </header>

            {/* Stats Cards */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div className="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div className="flex justify-between items-start">
                        <div>
                            <p className="text-sm text-gray-500">Promedio General</p>
                            <h3 className="text-2xl font-bold text-indigo-600">{stats.average}</h3>
                        </div>
                        <div className="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                            <TrendingUp size={20} />
                        </div>
                    </div>
                </div>

                <div className="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div className="flex justify-between items-start">
                        <div>
                            <p className="text-sm text-gray-500">Nota Más Alta</p>
                            <h3 className="text-2xl font-bold text-green-600">{stats.highest}</h3>
                        </div>
                        <div className="p-2 bg-green-50 rounded-lg text-green-600">
                            <TrendingUp size={20} />
                        </div>
                    </div>
                </div>

                <div className="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div className="flex justify-between items-start">
                        <div>
                            <p className="text-sm text-gray-500">Nota Más Baja</p>
                            <h3 className="text-2xl font-bold text-red-600">{stats.lowest}</h3>
                        </div>
                        <div className="p-2 bg-red-50 rounded-lg text-red-600">
                            <AlertCircle size={20} />
                        </div>
                    </div>
                </div>
            </div>

            {/* Grades Table */}
            <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left">
                        <thead className="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
                            <tr>
                                <th className="p-4 font-semibold">Materia</th>
                                <th className="p-4 font-semibold text-center">Bimestre 1</th>
                                <th className="p-4 font-semibold text-center">Bimestre 2</th>
                                <th className="p-4 font-semibold text-center">Bimestre 3</th>
                                <th className="p-4 font-semibold text-center">Bimestre 4</th>
                                <th className="p-4 font-semibold text-center">Promedio</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                            {grades.map((item) => {
                                const rowScores = [item.b1, item.b2, item.b3, item.b4].filter(x => x !== null);
                                const rowAvg = rowScores.length ? (rowScores.reduce((a, b) => a + b, 0) / rowScores.length).toFixed(0) : '-';

                                return (
                                    <tr key={item.id} className="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td className="p-4 font-medium text-gray-900 dark:text-white">{item.subject}</td>
                                        <td className="p-4 text-center">
                                            <span className={`px-2 py-1 rounded-md text-sm font-medium ${item.b1 >= 60 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                                                {item.b1 ?? '-'}
                                            </span>
                                        </td>
                                        <td className="p-4 text-center">
                                            <span className={`px-2 py-1 rounded-md text-sm font-medium ${item.b2 >= 60 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                                                {item.b2 ?? '-'}
                                            </span>
                                        </td>
                                        <td className="p-4 text-center">
                                            <span className={`px-2 py-1 rounded-md text-sm font-medium ${item.b3 >= 60 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                                                {item.b3 ?? '-'}
                                            </span>
                                        </td>
                                        <td className="p-4 text-center">
                                            <span className={`px-2 py-1 rounded-md text-sm font-medium ${!item.b4 ? 'bg-gray-100 text-gray-400' : item.b4 >= 60 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                                                {item.b4 ?? '-'}
                                            </span>
                                        </td>
                                        <td className="p-4 text-center font-bold text-indigo-600">
                                            {rowAvg}
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>
            </div>

            <div className="mt-6 flex justify-end">
                <button className="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <Download size={18} />
                    Descargar Boleta Oficial
                </button>
            </div>
        </div>
    );
};

export default StudentGradesPage;
