import React, { useState, useEffect } from 'react';
import { Search, RefreshCw, Check, User, Calendar, GraduationCap, CreditCard, FileText, ChevronRight, AlertCircle } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { studentService, catalogService, packageService, enrollmentService } from '../services';

const MatriculacionPage = () => {
    const { darkMode } = useTheme();
    const [searchTerm, setSearchTerm] = useState('');
    const [students, setStudents] = useState([]);
    const [selectedStudent, setSelectedStudent] = useState(null);
    const [loading, setLoading] = useState(false);
    const [searching, setSearching] = useState(false);
    const [submitting, setSubmitting] = useState(false);
    const [catalogs, setCatalogs] = useState({ grades: [], packages: [] });
    const [formData, setFormData] = useState({
        grade: '',
        section: '',
        package: '',
        jornada: 'MATUTINA'
    });

    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    useEffect(() => {
        loadCatalogs();
    }, []);

    const loadCatalogs = async () => {
        setLoading(true);
        try {
            const [gradesRes, packagesRes] = await Promise.all([
                catalogService.getGrades(),
                packageService.getAll({ active: true })
            ]);
            setCatalogs({
                grades: gradesRes.success ? gradesRes.data : [],
                packages: packagesRes.success ? packagesRes.data : []
            });
        } catch (error) {
            console.error("Error loading catalogs:", error);
            // Graceful degradation
            setCatalogs({ grades: [], packages: [] });
        } finally {
            setLoading(false);
        }
    };

    const searchStudents = async () => {
        if (!searchTerm.trim()) return;

        setSearching(true);
        try {
            const response = await studentService.getAll({ search: searchTerm, previousYear: true });
            if (response.success) {
                setStudents(response.data);
            }
        } catch (error) {
            console.error("Error searching students:", error);
            setStudents([]);
        } finally {
            setSearching(false);
        }
    };

    const selectStudent = (student) => {
        setSelectedStudent(student);
        // Pre-select next grade
        const gradeIndex = catalogs.grades.findIndex(g => g.name === student.previousGrade);
        if (gradeIndex >= 0 && gradeIndex < catalogs.grades.length - 1) {
            setFormData(prev => ({
                ...prev,
                grade: catalogs.grades[gradeIndex + 1].id,
                section: 'A'
            }));
        }
    };

    const handleSubmit = async () => {
        setSubmitting(true);
        try {
            const payload = {
                student: `/api/students/${selectedStudent.id}`,
                grade: `/api/grades/${formData.grade}`,
                section: formData.section,
                package: `/api/packages/${formData.package}`,
                status: 'ENROLLED',
                enrollmentDate: new Date().toISOString()
            };

            await enrollmentService.create(payload);

            alert(`✅ Reinscripción completada para ${selectedStudent.firstName} ${selectedStudent.lastName}`);
            setSelectedStudent(null);
            setStudents([]);
            setSearchTerm('');
            setFormData({ grade: '', section: '', package: '', jornada: 'MATUTINA' });
        } catch (error) {
            console.error('Error in enrollment:', error);
            alert('Error al procesar la reinscripción. Verifique los datos.');
        } finally {
            setSubmitting(false);
        }
    };

    const getNextGrade = (currentGrade) => {
        const grades = [
            'Kinder', 'Preparatoria', '1ro Primaria', '2do Primaria', '3ro Primaria',
            '4to Primaria', '5to Primaria', '6to Primaria', '1ro Básico', '2do Básico', '3ro Básico'
        ];
        const currentIndex = grades.indexOf(currentGrade);
        return currentIndex >= 0 && currentIndex < grades.length - 1 ? grades[currentIndex + 1] : 'Graduado';
    };

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Matriculación</h1>
                    <p className={`${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>Reinscripción de estudiantes del año anterior</p>
                </div>
            </div>

            {/* Info Banner */}
            <div className={`p-4 rounded-xl flex items-start gap-3 ${darkMode ? 'bg-blue-900/30 border-blue-700' : 'bg-blue-50 border-blue-200'} border`}>
                <AlertCircle className="text-blue-500 flex-shrink-0 mt-0.5" size={20} />
                <div>
                    <p className={`font-medium ${darkMode ? 'text-blue-300' : 'text-blue-700'}`}>Proceso de Reinscripción</p>
                    <p className={`text-sm ${darkMode ? 'text-blue-400' : 'text-blue-600'}`}>
                        Busque estudiantes del ciclo escolar anterior para reinscribirlos al nuevo año.
                        Los datos del estudiante y encargado se mantienen.
                    </p>
                </div>
            </div>

            {/* Search Section */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-6 shadow-sm`}>
                <h2 className={`text-lg font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Buscar Estudiante del Año Anterior</h2>

                <div className="flex gap-4">
                    <div className="flex-1">
                        <input
                            type="text"
                            className={inputClass}
                            placeholder="Buscar por nombre, apellido o código de estudiante..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && searchStudents()}
                        />
                    </div>
                    <button
                        onClick={searchStudents}
                        disabled={searching || !searchTerm.trim()}
                        className="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg flex items-center gap-2 disabled:opacity-50"
                    >
                        {searching ? <RefreshCw size={18} className="animate-spin" /> : <Search size={18} />}
                        Buscar
                    </button>
                </div>

                {/* Search Results */}
                {students.length > 0 && !selectedStudent && (
                    <div className="mt-6 space-y-3">
                        <h3 className={`font-medium ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                            Resultados ({students.length})
                        </h3>
                        {students.map((student) => (
                            <div
                                key={student.id}
                                onClick={() => selectStudent(student)}
                                className={`p-4 rounded-xl border-2 cursor-pointer transition-all ${darkMode
                                    ? 'border-gray-600 hover:border-teal-500 bg-gray-700'
                                    : 'border-gray-200 hover:border-teal-500 bg-gray-50'
                                    }`}
                            >
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-4">
                                        <div className={`w-12 h-12 rounded-full flex items-center justify-center ${darkMode ? 'bg-gray-600' : 'bg-teal-100'}`}>
                                            <User className={darkMode ? 'text-gray-300' : 'text-teal-600'} size={24} />
                                        </div>
                                        <div>
                                            <p className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                                {student.firstName} {student.lastName}
                                            </p>
                                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                                {student.code} • {student.previousGrade} "{student.previousSection}"
                                            </p>
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <p className={`text-sm font-medium ${darkMode ? 'text-teal-400' : 'text-teal-600'}`}>
                                            Próximo: {getNextGrade(student.previousGrade)}
                                        </p>
                                        <ChevronRight className={darkMode ? 'text-gray-500' : 'text-gray-400'} />
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* Selected Student - Reinscription Form */}
            {selectedStudent && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-6 shadow-sm`}>
                    <div className="flex items-center justify-between mb-6">
                        <h2 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Reinscribir Estudiante</h2>
                        <button
                            onClick={() => setSelectedStudent(null)}
                            className={`text-sm ${darkMode ? 'text-gray-400 hover:text-white' : 'text-gray-500 hover:text-gray-700'}`}
                        >
                            Cambiar estudiante
                        </button>
                    </div>

                    {/* Student Info Summary */}
                    <div className={`p-4 rounded-xl mb-6 ${darkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>
                        <div className="flex items-center gap-4">
                            <div className={`w-16 h-16 rounded-full flex items-center justify-center ${darkMode ? 'bg-teal-900' : 'bg-teal-100'}`}>
                                <User className={darkMode ? 'text-teal-400' : 'text-teal-600'} size={32} />
                            </div>
                            <div className="flex-1">
                                <p className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                    {selectedStudent.firstName} {selectedStudent.lastName}
                                </p>
                                <p className={`${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                                    Código: {selectedStudent.code}
                                </p>
                            </div>
                            <div className="text-right">
                                <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Grado anterior</p>
                                <p className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                    {selectedStudent.previousGrade} "{selectedStudent.previousSection}"
                                </p>
                            </div>
                        </div>
                        <div className={`mt-4 pt-4 border-t ${darkMode ? 'border-gray-600' : 'border-gray-200'}`}>
                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                <strong>Encargado:</strong> {selectedStudent.guardian} • Tel: {selectedStudent.guardianPhone}
                            </p>
                        </div>
                    </div>

                    {/* New Year Data */}
                    <h3 className={`font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Datos del Nuevo Ciclo Escolar</h3>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label className={labelClass}>Grado Nuevo *</label>
                            <select
                                className={inputClass}
                                value={formData.grade}
                                onChange={e => setFormData(prev => ({ ...prev, grade: e.target.value }))}
                            >
                                <option value="">Seleccionar...</option>
                                {catalogs.grades.map(g => <option key={g.id} value={g.id}>{g.name}</option>)}
                            </select>
                        </div>
                        <div>
                            <label className={labelClass}>Sección *</label>
                            <select
                                className={inputClass}
                                value={formData.section}
                                onChange={e => setFormData(prev => ({ ...prev, section: e.target.value }))}
                            >
                                <option value="">Seleccionar...</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                            </select>
                        </div>
                        <div>
                            <label className={labelClass}>Jornada</label>
                            <select
                                className={inputClass}
                                value={formData.jornada}
                                onChange={e => setFormData(prev => ({ ...prev, jornada: e.target.value }))}
                            >
                                <option value="MATUTINA">Matutina</option>
                                <option value="VESPERTINA">Vespertina</option>
                            </select>
                        </div>
                        <div>
                            <label className={labelClass}>Paquete *</label>
                            <select
                                className={inputClass}
                                value={formData.package}
                                onChange={e => setFormData(prev => ({ ...prev, package: e.target.value }))}
                            >
                                <option value="">Seleccionar...</option>
                                {catalogs.packages.map(p => <option key={p.id} value={p.id}>{p.name} - Q{p.totalPrice?.toLocaleString()}</option>)}
                            </select>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex justify-end gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button
                            onClick={() => setSelectedStudent(null)}
                            className={`px-4 py-2 rounded-lg ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200 text-gray-700'}`}
                        >
                            Cancelar
                        </button>
                        <button
                            onClick={handleSubmit}
                            disabled={submitting || !formData.grade || !formData.section || !formData.package}
                            className="flex items-center gap-2 px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg disabled:opacity-50"
                        >
                            {submitting ? <><RefreshCw size={18} className="animate-spin" /> Procesando...</> : <><Check size={18} /> Completar Reinscripción</>}
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
};

export default MatriculacionPage;
