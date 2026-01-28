import React, { useEffect, useState } from 'react';
import { FileText, Download, TrendingUp, AlertCircle, Loader } from 'lucide-react';
import api from '../../../services/api';
import { useAuth } from '../../../contexts/AuthContext';
import { toast } from '../../../utils/toast';

const StudentGradesPage = () => {
    const { user } = useAuth();
    const [grades, setGrades] = useState([]);
    const [loading, setLoading] = useState(true);
    const [downloading, setDownloading] = useState(false);
    const [stats, setStats] = useState({ average: 0, highest: 0, lowest: 0 });

    useEffect(() => {
        fetchGrades();
    }, []);

    const fetchGrades = async () => {
        setLoading(true);
        try {
            // Real API Call
            const { data } = await api.get('/students/me/grades');

            // Validate response format
            if (Array.isArray(data)) {
                setGrades(data);
                calculateStats(data);
            } else {
                console.error("Link invalid format:", data);
                setGrades([]);
            }
        } catch (error) {
            console.error("Error fetching grades:", error);
            setGrades([]);
        } finally {
            setLoading(false);
        }
    };

    const calculateStats = (data) => {
        if (!data.length) return;
        const scores = data.flatMap(d => [d.b1, d.b2, d.b3, d.b4].filter(g => g !== null));
        if (scores.length === 0) return;

        const avg = scores.reduce((a, b) => a + b, 0) / scores.length;
        setStats({
            average: avg.toFixed(1),
            highest: Math.max(...scores),
            lowest: Math.min(...scores)
        });
    };

    const handleDownload = async () => {
        setDownloading(true);
        const toastId = toast.loading("Generando boleta oficial...");

        try {
            const response = await api.get('/reports/grades/me', {
                responseType: 'blob'
            });

            // Create blob link to download
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `Boleta_Calificaciones_${new Date().getFullYear()}.pdf`);

            // Append to html to click js
            document.body.appendChild(link);
            link.click();

            // Clean up
            link.parentNode.removeChild(link);
            window.URL.revokeObjectURL(url);

            toast.dismiss(toastId);
            toast.success("Boleta descargada exitosamente");
        } catch (error) {
            console.error("Error generating PDF", error);
            toast.dismiss(toastId);
            toast.error("Error al descargar boleta. Contacte a secretaría.");
        } finally {
            setDownloading(false);
        }
    };

    if (loading) return (
        <div className="flex justify-center items-center min-h-[400px]">
            <div className="text-center text-gray-500">
                <Loader className="animate-spin mx-auto mb-2" />
                Cargando historial académico...
            </div>
        </div>
    );

    return (
        <div className="p-6 max-w-7xl mx-auto">
            <header className="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <FileText className="text-indigo-600" />
                        Mis Calificaciones
                    </h1>
                    <p className="text-gray-500 mt-2">Ciclo Escolar {new Date().getFullYear()}</p>
                </div>
                <button
                    onClick={handleDownload}
                    disabled={downloading}
                    className={`flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-white shadow-lg transition-all 
                        ${downloading
                            ? 'bg-gray-400 cursor-not-allowed'
                            : 'bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 hover:-translate-y-1 shadow-indigo-500/30'}`}
                >
                    {downloading ? <Loader className="animate-spin" size={20} /> : <Download size={20} />}
                    {downloading ? 'Generando...' : 'Descargar Boleta Oficial'}
                </button>
            </header>

            {/* Stats Cards */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div className="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-md transition-all">
                    <div className="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <TrendingUp size={60} className="text-indigo-600" />
                    </div>
                    <div>
                        <p className="text-sm font-medium text-gray-500 mb-1">Promedio General</p>
                        <h3 className="text-3xl font-bold text-indigo-600">{stats.average}</h3>
                    </div>
                </div>

                <div className="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-md transition-all">
                    <div className="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <TrendingUp size={60} className="text-green-600" />
                    </div>
                    <div>
                        <p className="text-sm font-medium text-gray-500 mb-1">Nota Más Alta</p>
                        <h3 className="text-3xl font-bold text-green-600">{stats.highest}</h3>
                    </div>
                </div>

                <div className="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-md transition-all">
                    <div className="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <AlertCircle size={60} className="text-red-600" />
                    </div>
                    <div>
                        <p className="text-sm font-medium text-gray-500 mb-1">Nota Más Baja</p>
                        <h3 className="text-3xl font-bold text-red-600">{stats.lowest}</h3>
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
                                <th className="p-4 font-semibold text-center w-32">Bimestre 1</th>
                                <th className="p-4 font-semibold text-center w-32">Bimestre 2</th>
                                <th className="p-4 font-semibold text-center w-32">Bimestre 3</th>
                                <th className="p-4 font-semibold text-center w-32">Bimestre 4</th>
                                <th className="p-4 font-semibold text-center w-32 bg-gray-50 dark:bg-gray-700">Promedio</th>
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
                                            {item.b1 !== null ? (
                                                <span className={`px-2.5 py-1 rounded-lg text-sm font-bold ${item.b1 >= 60 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                                                    {item.b1}
                                                </span>
                                            ) : <span className="text-gray-300">-</span>}
                                        </td>
                                        <td className="p-4 text-center">
                                            {item.b2 !== null ? (
                                                <span className={`px-2.5 py-1 rounded-lg text-sm font-bold ${item.b2 >= 60 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                                                    {item.b2}
                                                </span>
                                            ) : <span className="text-gray-300">-</span>}
                                        </td>
                                        <td className="p-4 text-center">
                                            {item.b3 !== null ? (
                                                <span className={`px-2.5 py-1 rounded-lg text-sm font-bold ${item.b3 >= 60 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                                                    {item.b3}
                                                </span>
                                            ) : <span className="text-gray-300">-</span>}
                                        </td>
                                        <td className="p-4 text-center">
                                            {item.b4 !== null ? (
                                                <span className={`px-2.5 py-1 rounded-lg text-sm font-bold ${item.b4 >= 60 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                                                    {item.b4}
                                                </span>
                                            ) : <span className="text-gray-300">-</span>}
                                        </td>
                                        <td className="p-4 text-center font-black text-indigo-600 bg-gray-50/50 dark:bg-gray-700/30">
                                            {rowAvg}
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
};

export default StudentGradesPage;
