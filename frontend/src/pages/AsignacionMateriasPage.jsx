import { toast } from '../utils/toast';
import React, { useState, useEffect } from 'react';
import { Book, User, Search, Save, CheckCircle, AlertCircle, ChevronRight, RefreshCw } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { subjectService, teacherService, gradeService, academicService } from '../services';

const AsignacionMateriasPage = () => {
    const { darkMode } = useTheme();
    const [grades, setGrades] = useState([]);
    const [teachers, setTeachers] = useState([]);
    const [subjects, setSubjects] = useState([]);
    const [selectedGrade, setSelectedGrade] = useState(null);
    const [assignments, setAssignments] = useState([]); // [{subjectId, teacherId, hours}]
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        loadCatalogs();
    }, []);

    const loadCatalogs = async () => {
        try {
            const [gradesData, teachersData, subjectsData] = await Promise.all([
                gradeService.getAll(),
                teacherService.getAll({ active: true }),
                subjectService.getAll({ active: true })
            ]);

            if (gradesData) setGrades(gradesData);
            if (teachersData) setTeachers(teachersData);
            if (subjectsData) setSubjects(subjectsData);
        } catch (error) {
            console.error("Error loading catalogs", error);
        } finally {
            setLoading(false);
        }
    };

    const handleSelectGrade = async (grade) => {
        setSelectedGrade(grade);
        setLoading(true);
        // Load existing assignments logic here if API supported it directly for a grade
        // For now, we init with subjects and try to map if we had previous data
        // Ideally we fetch current assignments for this grade
        // Since we don't have a direct "get assignments by grade" endpoint in academicService yet (only assign), 
        // we might rely on what we have or just show defaults.
        // Actually, let's init assignments with all subjects and empty teachers
        // IMPROVEMENT: Fetch existing assignments if possible.

        try {
            // Mocking fetch of existing assignments or using a new endpoint if created
            // For now, reset to blank state for subjects
            const initialAssignments = subjects.map(sub => ({
                subjectId: sub.id,
                subjectName: sub.name,
                teacherId: '',
                hours: 5
            }));
            setAssignments(initialAssignments);
        } catch (error) {
            console.error(error);
        } finally {
            setLoading(false);
        }
    };

    const handleAssignTeacher = (subjectId, teacherId) => {
        setAssignments(prev => prev.map(a =>
            a.subjectId === subjectId ? { ...a, teacherId } : a
        ));
    };

    const handleSave = async () => {
        if (!selectedGrade) return;
        setSaving(true);
        try {
            // Filter only assigned
            const payload = {
                gradeId: selectedGrade.id,
                assignments: assignments
                    .filter(a => a.teacherId)
                    .map(a => ({
                        subjectId: a.subjectId,
                        teacherId: a.teacherId,
                        hours: a.hours
                    }))
            };

            await academicService.assignSubjects(payload);
            toast.info('Asignaciones guardadas correctamente');
        } catch (error) {
            console.error("Error saving", error);
            toast.info('Error al guardar asignaciones');
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="flex h-[calc(100vh-100px)] gap-6">
            {/* Sidebar: Grades List */}
            <div className={`w-1/3 rounded-2xl shadow-lg border p-6 flex flex-col ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                <h2 className={`text-xl font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-900'}`}>Seleccionar Grado</h2>
                <div className="overflow-y-auto flex-1 space-y-2 pr-2 custom-scrollbar">
                    {grades.map(grade => (
                        <button
                            key={grade.id}
                            onClick={() => handleSelectGrade(grade)}
                            className={`w-full text-left p-4 rounded-xl transition-all border ${selectedGrade?.id === grade.id
                                ? (darkMode ? 'bg-blue-900/30 border-blue-500/50 ring-1 ring-blue-500' : 'bg-blue-50 border-blue-200 ring-1 ring-blue-500')
                                : (darkMode ? 'bg-gray-700/50 border-transparent hover:bg-gray-700' : 'bg-gray-50 border-transparent hover:bg-white hover:shadow-md')}`}
                        >
                            <span className={`font-bold ${darkMode ? 'text-gray-200' : 'text-gray-800'}`}>{grade.name}</span>
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
                            <button
                                onClick={handleSave}
                                disabled={saving}
                                className="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-green-500/20 flex items-center gap-2 transition-transform hover:-translate-y-1 disabled:opacity-50"
                            >
                                {saving ? <RefreshCw className="animate-spin" size={20} /> : <Save size={20} />}
                                {saving ? 'Guardando...' : 'Guardar Cambios'}
                            </button>
                        </div>

                        <div className="overflow-y-auto flex-1 pr-2">
                            <table className="w-full">
                                <thead className={`sticky top-0 ${darkMode ? 'bg-gray-800' : 'bg-white'} z-10`}>
                                    <tr className={`text-left text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'} border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                                        <th className="pb-4 pl-4 font-medium">Materia</th>
                                        <th className="pb-4 font-medium">Docente Encargado</th>
                                        <th className="pb-4 pr-4 text-right font-medium">Estado</th>
                                    </tr>
                                </thead>
                                <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                                    {assignments.map((assignment) => (
                                        <tr key={assignment.subjectId} className="group">
                                            <td className="py-4 pl-4 align-middle">
                                                <div className="flex items-center gap-3">
                                                    <div className={`p-2 rounded-lg ${darkMode ? 'bg-gray-700 text-blue-400' : 'bg-blue-50 text-blue-600'}`}>
                                                        <Book size={20} />
                                                    </div>
                                                    <span className={`font-medium ${darkMode ? 'text-gray-200' : 'text-gray-700'}`}>{assignment.subjectName}</span>
                                                </div>
                                            </td>
                                            <td className="py-4 align-middle">
                                                <select
                                                    value={assignment.teacherId}
                                                    onChange={(e) => handleAssignTeacher(assignment.subjectId, e.target.value)}
                                                    className={`w-full md:w-64 pl-4 pr-8 py-2 rounded-lg border appearance-none cursor-pointer transition-colors ${darkMode
                                                        ? 'bg-gray-700 border-gray-600 text-white hover:border-gray-500 focus:border-blue-500'
                                                        : 'bg-white border-gray-200 text-gray-900 hover:border-gray-300 focus:border-blue-500'} focus:ring-1 focus:ring-blue-500 outline-none`}
                                                >
                                                    <option value="">-- Seleccionar Docente --</option>
                                                    {teachers.map(t => (
                                                        <option key={t.id} value={t.id}>{t.firstName} {t.lastName}</option>
                                                    ))}
                                                </select>
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
