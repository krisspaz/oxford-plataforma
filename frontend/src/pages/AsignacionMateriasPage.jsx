import React, { useState, useEffect } from 'react';
import { Book, User, Search, Save, CheckCircle, AlertCircle, ChevronRight, X } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { catalogService } from '../services';

const AsignacionMateriasPage = () => {
    const { darkMode } = useTheme();
    const [grades, setGrades] = useState([]);
    const [teachers, setTeachers] = useState([]);
    const [selectedGrade, setSelectedGrade] = useState(null);
    const [assignments, setAssignments] = useState([]); // [{id, subject, teacher}]
    const [loading, setLoading] = useState(true);

    // Mock Subjects
    const subjectsList = [
        'Matemáticas', 'Idioma Español', 'Ciencias Naturales', 'Ciencias Sociales',
        'Inglés', 'Educación Física', 'Artes Plásticas', 'Música', 'Computación'
    ];

    useEffect(() => {
        loadCatalogs();
    }, []);

    const loadCatalogs = async () => {
        try {
            const [gradesData, teachersData] = await Promise.all([
                catalogService.getGrades(),
                catalogService.getTeachers()
            ]);
            if (gradesData.success) setGrades(gradesData.data);
            // Mock teachers if API fails or returns basic
            setTeachers(teachersData.success ? teachersData.data : [
                { id: 1, name: 'Juan Perez' }, { id: 2, name: 'Maria Lopez' }, { id: 3, name: 'Carlos Ruiz' }
            ]);
        } catch (error) {
            console.error("Error loading catalogs", error);
        } finally {
            setLoading(false);
        }
    };

    const handleSelectGrade = (grade) => {
        setSelectedGrade(grade);
        // Reset or load existing assignments for this grade
        // Mocking existing assignments
        setAssignments(subjectsList.map((sub, idx) => ({
            id: idx,
            subject: sub,
            teacherId: '' // No teacher assigned yet
        })));
    };

    const handleAssignTeacher = (subjectId, teacherId) => {
        setAssignments(prev => prev.map(a => a.id === subjectId ? { ...a, teacherId } : a));
    };

    return (
        <div className="flex h-[calc(100vh-100px)] gap-6">
            {/* Sidebar: Grades List */}
            <div className={`w-1/3 rounded-2xl shadow-lg border p-6 flex flex-col ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                <h2 className={`text-xl font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-900'}`}>Seleccionar Grado</h2>
                <div className="relative mb-4">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={18} />
                    <input
                        type="text"
                        placeholder="Buscar grado..."
                        className={`w-full pl-9 pr-4 py-2 rounded-lg border ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-gray-50 border-gray-200 text-gray-800'} text-sm focus:ring-2 focus:ring-blue-500 outline-none`}
                    />
                </div>
                <div className="overflow-y-auto flex-1 space-y-2 pr-2 custom-scrollbar">
                    {grades.map(grade => (
                        <button
                            key={grade.id}
                            onClick={() => handleSelectGrade(grade)}
                            className={`w-full text-left p-4 rounded-xl transition-all border ${selectedGrade?.id === grade.id
                                ? (darkMode ? 'bg-blue-900/30 border-blue-500/50 ring-1 ring-blue-500' : 'bg-blue-50 border-blue-200 ring-1 ring-blue-500')
                                : (darkMode ? 'bg-gray-700/50 border-transparent hover:bg-gray-700' : 'bg-gray-50 border-transparent hover:bg-white hover:shadow-md')}`}
                        >
                            <div className="flex justify-between items-center">
                                <span className={`font-bold ${darkMode ? 'text-gray-200' : 'text-gray-800'}`}>{grade.name}</span>
                                {selectedGrade?.id === grade.id && <ChevronRight size={18} className="text-blue-500" />}
                            </div>
                            <span className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{grade.level || 'General'}</span>
                        </button>
                    ))}
                </div>
            </div>

            {/* Main Area: Assignments */}
            <div className="flex-1 flex flex-col">
                {selectedGrade ? (
                    <div className={`flex-1 rounded-2xl shadow-lg border p-8 flex flex-col ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                        <div className="flex justify-between items-center mb-6">
                            <div>
                                <h2 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{selectedGrade.name}</h2>
                                <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>Asignación de materias y docentes encargados.</p>
                            </div>
                            <button className="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-green-500/20 flex items-center gap-2 transition-transform hover:-translate-y-1">
                                <Save size={20} /> Guardar Cambios
                            </button>
                        </div>

                        <div className="overflow-y-auto flex-1 pr-2">
                            <table className="w-full">
                                <thead className={`sticky top-0 ${darkMode ? 'bg-gray-800' : 'bg-white'} z-10`}>
                                    <tr className={`text-left text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'} border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                                        <th className="pb-4 pl-4 font-medium">Materia / Nivel</th>
                                        <th className="pb-4 font-medium">Docente Encargado</th>
                                        <th className="pb-4 pr-4 text-right font-medium">Estado</th>
                                    </tr>
                                </thead>
                                <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                                    {assignments.map((assignment) => (
                                        <tr key={assignment.id} className="group">
                                            <td className="py-4 pl-4 align-middle">
                                                <div className="flex items-center gap-3">
                                                    <div className={`p-2 rounded-lg ${darkMode ? 'bg-gray-700 text-blue-400' : 'bg-blue-50 text-blue-600'}`}>
                                                        <Book size={20} />
                                                    </div>
                                                    <span className={`font-medium ${darkMode ? 'text-gray-200' : 'text-gray-700'}`}>{assignment.subject}</span>
                                                </div>
                                            </td>
                                            <td className="py-4 align-middle">
                                                <div className="relative">
                                                    <User className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={16} />
                                                    <select
                                                        value={assignment.teacherId}
                                                        onChange={(e) => handleAssignTeacher(assignment.id, e.target.value)}
                                                        className={`w-full md:w-64 pl-9 pr-8 py-2 rounded-lg border appearance-none cursor-pointer transition-colors ${darkMode
                                                            ? 'bg-gray-700 border-gray-600 text-white hover:border-gray-500 focus:border-blue-500'
                                                            : 'bg-gray-50 border-gray-200 text-gray-900 hover:border-gray-300 focus:border-blue-500'} focus:ring-1 focus:ring-blue-500 outline-none`}
                                                    >
                                                        <option value="">-- Seleccionar Docente --</option>
                                                        {teachers.map(t => (
                                                            <option key={t.id} value={t.id}>{t.name}</option>
                                                        ))}
                                                    </select>
                                                </div>
                                            </td>
                                            <td className="py-4 pr-4 align-middle text-right">
                                                {assignment.teacherId ? (
                                                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                                        <CheckCircle size={14} /> Asignado
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                                        <AlertCircle size={14} /> Pendiente
                                                    </span>
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                ) : (
                    <div className={`flex-1 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center gap-4 ${darkMode ? 'border-gray-700 bg-gray-800/50' : 'border-gray-200 bg-gray-50'}`}>
                        <div className={`p-6 rounded-full ${darkMode ? 'bg-gray-800 text-gray-600' : 'bg-white text-gray-300'}`}>
                            <Book size={48} />
                        </div>
                        <p className={`text-lg font-medium ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Selecciona un grado para gestionar sus materias</p>
                    </div>
                )}
            </div>
        </div>
    );
};

export default AsignacionMateriasPage;
