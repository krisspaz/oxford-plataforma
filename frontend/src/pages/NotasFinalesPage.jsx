import React, { useState, useEffect } from 'react';
import { FileText, Download, TrendingUp, AlertCircle, CheckCircle } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { useAuth } from '../contexts/AuthContext';
import { gradeRecordService, teacherService } from '../services'; // Ensure this service exists or mock it

import { usePdfExport } from '../hooks/usePdfExport';

const NotasFinalesPage = () => {
    const { darkMode } = useTheme();
    const { user } = useAuth();
    const { exportTable } = usePdfExport(); // Hook
    const [loading, setLoading] = useState(false);

    const [selectedSubject, setSelectedSubject] = useState('');
    const [subjects, setSubjects] = useState([]);
    const [grades, setGrades] = useState([]);

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;

    // Load subjects
    useEffect(() => {
        const loadSubjects = async () => {
            if (user?.id) {
                try {
                    const mySubjects = await teacherService.getSubjects(user.id);
                    setSubjects(mySubjects.map(s => ({
                        id: s.id,
                        name: s.subject?.name || s.name || 'Materia',
                        grade: s.grade?.name || s.grade || 'Grado',
                        full_name: `${s.subject?.name || s.name} - ${s.grade?.name || s.grade}`
                    })));
                } catch (err) {
                    console.error("Error loading subjects:", err);
                }
            }
        };
        loadSubjects();
    }, [user]);

    // Load grades when subject changes
    useEffect(() => {
        if (!selectedSubject) {
            setGrades([]);
            return;
        }

        const loadGrades = async () => {
            setLoading(true);
            try {
                const response = await gradeRecordService.getTeacherSummary(selectedSubject);
                if (response.success) {
                    setGrades(response.data);
                } else {
                    setGrades([]);
                }
            } catch (error) {
                console.error("Error loading final grades:", error);
                setGrades([]);
            } finally {
                setLoading(false);
            }
        };

        loadGrades();
    }, [selectedSubject]);

    // Export Function
    const handleExportPDF = () => {
        if (grades.length === 0) {
            alert("No hay datos para exportar.");
            return;
        }

        const subjectName = subjects.find(s => s.id === parseInt(selectedSubject))?.name || 'Materia Desconocida';
        const gradeLevel = subjects.find(s => s.id === parseInt(selectedSubject))?.grade || '';

        const columns = ["Estudiante", "B1", "B2", "B3", "B4", "Final", "Estado"];
        const data = grades.map(g => [
            g.student,
            g.b1 ?? '-',
            g.b2 ?? '-',
            g.b3 ?? '-',
            g.b4 ?? '-',
            g.final,
            g.status
        ]);

        exportTable({
            title: 'Cuadro de Notas Finales',
            subtitle: `${subjectName} - ${gradeLevel}`,
            columns: columns,
            data: data,
            filename: `notas_${subjectName}_${gradeLevel}.pdf`,
            autoTableOptions: {
                didParseCell: function (data) {
                    if (data.section === 'body' && data.column.index === 5) {
                        // Colorize Final Grade
                        const val = parseFloat(data.cell.raw);
                        if (val < 60) {
                            data.cell.styles.textColor = [220, 53, 69]; // Red
                            data.cell.styles.fontStyle = 'bold';
                        } else {
                            data.cell.styles.textColor = [25, 135, 84]; // Green
                            data.cell.styles.fontStyle = 'bold';
                        }
                    }
                }
            }
        });
    };

    return (
        <div className={`p-6 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h1 className="text-2xl font-bold mb-2">Notas Finales</h1>
                    <p className={`${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                        Resumen de rendimiento académico por estudiante.
                    </p>
                </div>
                <div className="flex gap-4 items-end">
                    <div className="w-64">
                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Materia</label>
                        <select value={selectedSubject} onChange={e => setSelectedSubject(e.target.value)} className={`w-full ${inputClass}`}>
                            <option value="">Seleccionar materia...</option>
                            {subjects.map(s => (
                                <option key={s.id} value={s.id}>{s.full_name || `${s.name} - ${s.grade}`}</option>
                            ))}
                        </select>
                    </div>
                    {grades.length > 0 && (
                        <button onClick={handleExportPDF} className="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg flex items-center gap-2 h-10 transition-colors">
                            <Download size={18} /> PDF
                        </button>
                    )}
                </div>
            </div>

            {/* Table Code remains Same... */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden border ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                {grades.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className={`${darkMode ? 'bg-gray-700' : 'bg-gray-50'} border-b ${darkMode ? 'border-gray-600' : 'border-gray-200'}`}>
                                <tr>
                                    <th className="px-6 py-3 text-left font-semibold">Estudiante</th>
                                    <th className="px-4 py-3 text-center">Bimestre 1</th>
                                    <th className="px-4 py-3 text-center">Bimestre 2</th>
                                    <th className="px-4 py-3 text-center">Bimestre 3</th>
                                    <th className="px-4 py-3 text-center">Bimestre 4</th>
                                    <th className="px-4 py-3 text-center font-bold">Promedio Final</th>
                                    <th className="px-4 py-3 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                                {grades.map((row) => (
                                    <tr key={row.id} className={`${darkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'}`}>
                                        <td className="px-6 py-4 font-medium">{row.student}</td>
                                        <td className="px-4 py-4 text-center">{row.b1 ?? '-'}</td>
                                        <td className="px-4 py-4 text-center">{row.b2 ?? '-'}</td>
                                        <td className="px-4 py-4 text-center">{row.b3 ?? '-'}</td>
                                        <td className="px-4 py-4 text-center">{row.b4 ?? '-'}</td>
                                        <td className={`px-4 py-4 text-center font-bold ${row.final < 60 ? 'text-red-500' : 'text-green-500'}`}>{row.final}</td>
                                        <td className="px-4 py-4 text-center">
                                            <span className={`px-2 py-1 rounded-full text-xs font-bold ${row.status === 'APROBADO'
                                                ? (darkMode ? 'bg-green-900/40 text-green-400' : 'bg-green-100 text-green-700')
                                                : (darkMode ? 'bg-red-900/40 text-red-400' : 'bg-red-100 text-red-700')
                                                }`}>
                                                {row.status}
                                            </span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="p-12 text-center text-gray-500">
                        {loading ? 'Cargando datos...' : 'Seleccione una materia para ver el resumen.'}
                    </div>
                )}
            </div>
        </div>
    );
};

export default NotasFinalesPage;
