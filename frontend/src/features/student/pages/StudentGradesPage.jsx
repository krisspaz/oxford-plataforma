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
        const avg = scores.reduce((a, b) => a + b, 0) / scores.length;
        setStats({
            average: avg.toFixed(1),
            highest: Math.max(...scores),
            lowest: Math.min(...scores)
        });
    };

    const handleDownload = () => {
        try {
            import('jspdf').then(({ default: jsPDF }) => {
                import('jspdf-autotable').then(() => {
                    const doc = new jsPDF();

                    // Header
                    doc.setFontSize(20);
                    doc.setTextColor(40, 40, 40);
                    doc.text('CORPORACIÓN EDUCACIONAL OXFORD', 105, 20, { align: 'center' });
                    doc.setFontSize(16);
                    doc.setTextColor(100, 100, 100);
                    doc.text('BOLETA DE CALIFICACIONES - CICLO 2026', 105, 30, { align: 'center' });

                    doc.setDrawColor(200, 200, 200);
                    doc.line(14, 35, 196, 35);

                    // Student Info
                    doc.setFontSize(12);
                    doc.setTextColor(0, 0, 0);
                    doc.text(`Estudiante: ${user?.username || 'Estudiante'}`, 14, 45);
                    doc.text(`Fecha de Emisión: ${new Date().toLocaleDateString()}`, 14, 52);
                    doc.text(`Promedio General: ${stats.average}`, 14, 59);

                    // Grades Table
                    const headers = [['Materia', 'Bimestre 1', 'Bimestre 2', 'Bimestre 3', 'Bimestre 4', 'Promedio']];
                    const data = grades.map(item => {
                        const rowScores = [item.b1, item.b2, item.b3, item.b4].filter(x => x !== null);
                        const rowAvg = rowScores.length ? (rowScores.reduce((a, b) => a + b, 0) / rowScores.length).toFixed(0) : '-';
                        return [
                            item.subject,
                            item.b1 ?? '-',
                            item.b2 ?? '-',
                            item.b3 ?? '-',
                            item.b4 ?? '-',
                            rowAvg
                        ];
                    });

                    doc.autoTable({
                        startY: 70,
                        head: headers,
                        body: data,
                        theme: 'striped',
                        headStyles: { fillColor: [79, 70, 229], textColor: 255 }, // Indigo-600
                        styles: { fontSize: 10, cellPadding: 3 }
                    });

                    // Signatures
                    const finalY = doc.lastAutoTable.finalY + 40;
                    doc.setDrawColor(0, 0, 0);
                    doc.line(30, finalY, 80, finalY);
                    doc.text('Directora', 55, finalY + 5, { align: 'center' });

                    doc.line(130, finalY, 180, finalY);
                    doc.text('Secretaria', 155, finalY + 5, { align: 'center' });

                    doc.save(`Boleta_Calificaciones_${new Date().getFullYear()}.pdf`);
                });
            });
        } catch (error) {
            console.error("Error generating PDF", error);
        }
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
                <button
                    onClick={handleDownload}
                    className="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
                >
                    <Download size={18} />
                    Descargar Boleta Oficial
                </button>
            </div>
        </div>
    );
};

export default StudentGradesPage;
