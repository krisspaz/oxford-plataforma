import React, { useState, useEffect } from 'react';
import { Users, Search, Filter, GraduationCap, BookOpen, RefreshCw, User, Phone, Mail, Calendar, Check, X, Clock, FileText, Download, ChevronLeft, ChevronRight } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { useAuth } from '../contexts/AuthContext';

const MisAlumnosPage = () => {
    const { darkMode } = useTheme();
    const { user } = useAuth();
    const [loading, setLoading] = useState(true);
    const [students, setStudents] = useState([]);
    const [filteredStudents, setFilteredStudents] = useState([]);
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedCourse, setSelectedCourse] = useState('');
    const [selectedGrade, setSelectedGrade] = useState('');
    const [selectedSection, setSelectedSection] = useState('');
    const [activeTab, setActiveTab] = useState('lista'); // 'lista' | 'asistencia' | 'reporte'
    const [attendanceDate, setAttendanceDate] = useState(new Date().toISOString().split('T')[0]);
    const [attendance, setAttendance] = useState({});
    const [selectedBimester, setSelectedBimester] = useState('1');

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;

    // Demo: Courses assigned to this teacher
    const teacherCourses = [
        { id: 1, name: 'Matemáticas', grades: ['3ro Primaria', '4to Primaria', '5to Primaria'] },
        { id: 2, name: 'Física', grades: ['1ro Básico', '2do Básico'] },
    ];

    // Demo: Current class based on schedule
    const currentClass = {
        period: '07:30 - 08:15',
        grade: '3ro Primaria',
        section: 'A',
        course: 'Matemáticas'
    };

    // Demo: Students from teacher's courses
    const teacherStudents = [
        { id: 1, code: 'ALU-2025-001', firstName: 'María', lastName: 'García López', grade: '3ro Primaria', section: 'A', course: 'Matemáticas', guardian: 'Juan García', phone: '5555-1234', email: 'juan@email.com' },
        { id: 2, code: 'ALU-2025-002', firstName: 'Carlos', lastName: 'Martínez Pérez', grade: '3ro Primaria', section: 'A', course: 'Matemáticas', guardian: 'Ana Pérez', phone: '5555-5678', email: 'ana@email.com' },
        { id: 3, code: 'ALU-2025-003', firstName: 'Ana', lastName: 'López Hernández', grade: '3ro Primaria', section: 'A', course: 'Matemáticas', guardian: 'Pedro López', phone: '5555-9012', email: 'pedro@email.com' },
        { id: 4, code: 'ALU-2025-010', firstName: 'Luis', lastName: 'Rodríguez Castro', grade: '3ro Primaria', section: 'B', course: 'Matemáticas', guardian: 'María Castro', phone: '5555-3456', email: 'maria@email.com' },
        { id: 5, code: 'ALU-2025-011', firstName: 'Sofía', lastName: 'Hernández Gómez', grade: '3ro Primaria', section: 'B', course: 'Matemáticas', guardian: 'José Gómez', phone: '5555-7890', email: 'jose@email.com' },
        { id: 6, code: 'ALU-2025-020', firstName: 'Diego', lastName: 'Morales Ruiz', grade: '4to Primaria', section: 'A', course: 'Matemáticas', guardian: 'Laura Ruiz', phone: '5555-2345', email: 'laura@email.com' },
        { id: 7, code: 'ALU-2025-021', firstName: 'Valentina', lastName: 'Torres Méndez', grade: '4to Primaria', section: 'A', course: 'Matemáticas', guardian: 'Roberto Torres', phone: '5555-6789', email: 'roberto@email.com' },
        { id: 8, code: 'ALU-2025-030', firstName: 'Sebastián', lastName: 'Ramírez Díaz', grade: '5to Primaria', section: 'A', course: 'Matemáticas', guardian: 'Carmen Díaz', phone: '5555-0123', email: 'carmen@email.com' },
    ];

    // Demo: Attendance history for reports
    const attendanceHistory = {
        1: { // María
            '2025-01-06': 'present', '2025-01-07': 'present', '2025-01-08': 'absent', '2025-01-09': 'present', '2025-01-10': 'present',
            '2025-01-13': 'present', '2025-01-14': 'late', '2025-01-15': 'present', '2025-01-16': 'present', '2025-01-17': 'present',
        },
        2: { // Carlos
            '2025-01-06': 'present', '2025-01-07': 'absent', '2025-01-08': 'present', '2025-01-09': 'present', '2025-01-10': 'late',
            '2025-01-13': 'present', '2025-01-14': 'present', '2025-01-15': 'present', '2025-01-16': 'absent', '2025-01-17': 'present',
        },
    };

    useEffect(() => {
        setTimeout(() => {
            setStudents(teacherStudents);
            setFilteredStudents(teacherStudents);
            // Initialize attendance for today
            const initialAttendance = {};
            teacherStudents.forEach(s => {
                initialAttendance[s.id] = 'present';
            });
            setAttendance(initialAttendance);
            setLoading(false);
        }, 500);
    }, []);

    useEffect(() => {
        let filtered = [...students];
        if (searchTerm) {
            const term = searchTerm.toLowerCase();
            filtered = filtered.filter(s =>
                s.firstName.toLowerCase().includes(term) ||
                s.lastName.toLowerCase().includes(term) ||
                s.code.toLowerCase().includes(term)
            );
        }
        if (selectedCourse) filtered = filtered.filter(s => s.course === selectedCourse);
        if (selectedGrade) filtered = filtered.filter(s => s.grade === selectedGrade);
        if (selectedSection) filtered = filtered.filter(s => s.section === selectedSection);
        setFilteredStudents(filtered);
    }, [searchTerm, selectedCourse, selectedGrade, selectedSection, students]);

    const clearFilters = () => {
        setSearchTerm('');
        setSelectedCourse('');
        setSelectedGrade('');
        setSelectedSection('');
    };

    const getUniqueGrades = () => [...new Set(students.map(s => s.grade))].sort();
    const getUniqueSections = () => [...new Set(students.map(s => s.section))].sort();

    const toggleAttendance = (studentId, status) => {
        setAttendance(prev => ({ ...prev, [studentId]: status }));
    };

    const saveAttendance = () => {
        console.log('Saving attendance:', attendance, 'for date:', attendanceDate);
        alert('✅ Asistencia guardada correctamente');
    };

    const getAttendanceStats = (studentId) => {
        const history = attendanceHistory[studentId] || {};
        const total = Object.keys(history).length || 1;
        const present = Object.values(history).filter(v => v === 'present').length;
        const absent = Object.values(history).filter(v => v === 'absent').length;
        const late = Object.values(history).filter(v => v === 'late').length;
        const percentage = Math.round((present / total) * 100);
        return { total, present, absent, late, percentage };
    };

    const exportReport = (studentId) => {
        const student = students.find(s => s.id === studentId);
        alert(`📄 Exportando reporte de asistencia para ${student?.firstName} ${student?.lastName}`);
    };

    const sendToParent = (studentId) => {
        const student = students.find(s => s.id === studentId);
        alert(`📧 Enviando reporte al encargado de ${student?.firstName} ${student?.lastName}`);
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
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Mis Alumnos</h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>
                        Estudiantes de los cursos que imparto
                    </p>
                </div>
            </div>

            {/* Tabs */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-1 shadow-sm flex gap-1`}>
                <button
                    onClick={() => setActiveTab('lista')}
                    className={`flex-1 py-3 px-4 rounded-lg font-medium transition-all flex items-center justify-center gap-2 ${activeTab === 'lista' ? 'bg-teal-600 text-white' : darkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'
                        }`}
                >
                    <Users size={18} /> Lista de Alumnos
                </button>
                <button
                    onClick={() => setActiveTab('asistencia')}
                    className={`flex-1 py-3 px-4 rounded-lg font-medium transition-all flex items-center justify-center gap-2 ${activeTab === 'asistencia' ? 'bg-teal-600 text-white' : darkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'
                        }`}
                >
                    <Check size={18} /> Tomar Asistencia
                </button>
                <button
                    onClick={() => setActiveTab('reporte')}
                    className={`flex-1 py-3 px-4 rounded-lg font-medium transition-all flex items-center justify-center gap-2 ${activeTab === 'reporte' ? 'bg-teal-600 text-white' : darkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'
                        }`}
                >
                    <FileText size={18} /> Reporte Bimestral
                </button>
            </div>

            {/* TAB: Lista de Alumnos */}
            {activeTab === 'lista' && (
                <>
                    {/* Filters */}
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm`}>
                        <div className="flex flex-wrap gap-4 items-end">
                            <div className="flex-1 min-w-[200px]">
                                <label className={`block text-xs font-medium mb-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Buscar</label>
                                <div className="relative">
                                    <Search className={`absolute left-3 top-1/2 -translate-y-1/2 ${darkMode ? 'text-gray-400' : 'text-gray-400'}`} size={16} />
                                    <input type="text" placeholder="Nombre o código..." value={searchTerm} onChange={e => setSearchTerm(e.target.value)} className={`${inputClass} pl-9 w-full`} />
                                </div>
                            </div>
                            <div className="w-40">
                                <label className={`block text-xs font-medium mb-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Curso</label>
                                <select value={selectedCourse} onChange={e => setSelectedCourse(e.target.value)} className={`${inputClass} w-full`}>
                                    <option value="">Todos</option>
                                    {teacherCourses.map(c => <option key={c.id} value={c.name}>{c.name}</option>)}
                                </select>
                            </div>
                            <div className="w-40">
                                <label className={`block text-xs font-medium mb-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Grado</label>
                                <select value={selectedGrade} onChange={e => setSelectedGrade(e.target.value)} className={`${inputClass} w-full`}>
                                    <option value="">Todos</option>
                                    {getUniqueGrades().map(g => <option key={g} value={g}>{g}</option>)}
                                </select>
                            </div>
                            <div className="w-32">
                                <label className={`block text-xs font-medium mb-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Sección</label>
                                <select value={selectedSection} onChange={e => setSelectedSection(e.target.value)} className={`${inputClass} w-full`}>
                                    <option value="">Todas</option>
                                    {getUniqueSections().map(s => <option key={s} value={s}>{s}</option>)}
                                </select>
                            </div>
                            <button onClick={clearFilters} className={`px-4 py-2 rounded-lg ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'}`}>
                                Limpiar
                            </button>
                        </div>
                    </div>

                    {/* Students Table */}
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                        <table className="w-full">
                            <thead>
                                <tr className={darkMode ? 'bg-gray-700/50' : 'bg-gray-50'}>
                                    <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Alumno</th>
                                    <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Código</th>
                                    <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Grado</th>
                                    <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Curso</th>
                                    <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Encargado</th>
                                    <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Contacto</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filteredStudents.map(student => (
                                    <tr key={student.id} className={`border-t ${darkMode ? 'border-gray-700 hover:bg-gray-700/50' : 'border-gray-100 hover:bg-gray-50'}`}>
                                        <td className="p-4">
                                            <div className="flex items-center gap-3">
                                                <div className={`w-10 h-10 rounded-full flex items-center justify-center ${darkMode ? 'bg-teal-900/50' : 'bg-teal-100'}`}>
                                                    <User className="text-teal-500" size={18} />
                                                </div>
                                                <span className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{student.firstName} {student.lastName}</span>
                                            </div>
                                        </td>
                                        <td className={`p-4 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                                            <span className={`px-2 py-1 rounded text-xs font-mono ${darkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>{student.code}</span>
                                        </td>
                                        <td className={`p-4 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{student.grade} "{student.section}"</td>
                                        <td className="p-4">
                                            <span className={`px-2 py-1 rounded-full text-xs font-medium ${darkMode ? 'bg-blue-900/50 text-blue-300' : 'bg-blue-100 text-blue-700'}`}>{student.course}</span>
                                        </td>
                                        <td className={`p-4 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{student.guardian}</td>
                                        <td className="p-4">
                                            <a href={`tel:${student.phone}`} className={`flex items-center gap-1 text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                                <Phone size={14} /> {student.phone}
                                            </a>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        {filteredStudents.length === 0 && (
                            <div className="p-12 text-center">
                                <Users className={darkMode ? 'text-gray-600 mx-auto' : 'text-gray-300 mx-auto'} size={48} />
                                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>No se encontraron alumnos</p>
                            </div>
                        )}
                    </div>
                </>
            )}

            {/* TAB: Tomar Asistencia */}
            {activeTab === 'asistencia' && (
                <>
                    {/* Current Class Info */}
                    <div className={`p-4 rounded-xl ${darkMode ? 'bg-teal-900/30 border-teal-700' : 'bg-teal-50 border-teal-200'} border`}>
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-4">
                                <Clock className="text-teal-500" size={24} />
                                <div>
                                    <p className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                        {currentClass.course} - {currentClass.grade} "{currentClass.section}"
                                    </p>
                                    <p className={`text-sm ${darkMode ? 'text-teal-300' : 'text-teal-700'}`}>
                                        Período: {currentClass.period}
                                    </p>
                                </div>
                            </div>
                            <input
                                type="date"
                                value={attendanceDate}
                                onChange={e => setAttendanceDate(e.target.value)}
                                className={inputClass}
                            />
                        </div>
                    </div>

                    {/* Attendance List */}
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                        <table className="w-full">
                            <thead>
                                <tr className={darkMode ? 'bg-gray-700/50' : 'bg-gray-50'}>
                                    <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>#</th>
                                    <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Alumno</th>
                                    <th className={`text-center p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Asistencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filteredStudents.filter(s => s.grade === currentClass.grade && s.section === currentClass.section).map((student, idx) => (
                                    <tr key={student.id} className={`border-t ${darkMode ? 'border-gray-700' : 'border-gray-100'}`}>
                                        <td className={`p-4 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{idx + 1}</td>
                                        <td className="p-4">
                                            <div className="flex items-center gap-3">
                                                <div className={`w-10 h-10 rounded-full flex items-center justify-center ${attendance[student.id] === 'present' ? 'bg-green-100' :
                                                        attendance[student.id] === 'absent' ? 'bg-red-100' : 'bg-yellow-100'
                                                    }`}>
                                                    <User className={
                                                        attendance[student.id] === 'present' ? 'text-green-500' :
                                                            attendance[student.id] === 'absent' ? 'text-red-500' : 'text-yellow-500'
                                                    } size={18} />
                                                </div>
                                                <span className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                                    {student.firstName} {student.lastName}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="p-4">
                                            <div className="flex justify-center gap-2">
                                                <button
                                                    onClick={() => toggleAttendance(student.id, 'present')}
                                                    className={`px-4 py-2 rounded-lg flex items-center gap-2 transition-all ${attendance[student.id] === 'present'
                                                            ? 'bg-green-500 text-white'
                                                            : darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'
                                                        }`}
                                                >
                                                    <Check size={16} /> Presente
                                                </button>
                                                <button
                                                    onClick={() => toggleAttendance(student.id, 'late')}
                                                    className={`px-4 py-2 rounded-lg flex items-center gap-2 transition-all ${attendance[student.id] === 'late'
                                                            ? 'bg-yellow-500 text-white'
                                                            : darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'
                                                        }`}
                                                >
                                                    <Clock size={16} /> Tarde
                                                </button>
                                                <button
                                                    onClick={() => toggleAttendance(student.id, 'absent')}
                                                    className={`px-4 py-2 rounded-lg flex items-center gap-2 transition-all ${attendance[student.id] === 'absent'
                                                            ? 'bg-red-500 text-white'
                                                            : darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'
                                                        }`}
                                                >
                                                    <X size={16} /> Ausente
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>

                        <div className={`p-4 border-t ${darkMode ? 'border-gray-700' : 'border-gray-200'} flex justify-end`}>
                            <button onClick={saveAttendance} className="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg flex items-center gap-2">
                                <Check size={18} /> Guardar Asistencia
                            </button>
                        </div>
                    </div>
                </>
            )}

            {/* TAB: Reporte Bimestral */}
            {activeTab === 'reporte' && (
                <>
                    {/* Bimester Selector */}
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm flex items-center justify-between`}>
                        <div className="flex items-center gap-4">
                            <label className={`font-medium ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Bimestre:</label>
                            <select value={selectedBimester} onChange={e => setSelectedBimester(e.target.value)} className={inputClass}>
                                <option value="1">1er Bimestre</option>
                                <option value="2">2do Bimestre</option>
                                <option value="3">3er Bimestre</option>
                                <option value="4">4to Bimestre</option>
                            </select>
                        </div>
                        <div className="flex gap-2">
                            <button className={`px-4 py-2 rounded-lg flex items-center gap-2 ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'}`}>
                                <Download size={18} /> Exportar Todo
                            </button>
                        </div>
                    </div>

                    {/* Attendance Report */}
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                        <table className="w-full">
                            <thead>
                                <tr className={darkMode ? 'bg-gray-700/50' : 'bg-gray-50'}>
                                    <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Alumno</th>
                                    <th className={`text-center p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Asistencias</th>
                                    <th className={`text-center p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Tardanzas</th>
                                    <th className={`text-center p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Ausencias</th>
                                    <th className={`text-center p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>% Asistencia</th>
                                    <th className={`text-center p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filteredStudents.slice(0, 5).map(student => {
                                    const stats = getAttendanceStats(student.id);
                                    return (
                                        <tr key={student.id} className={`border-t ${darkMode ? 'border-gray-700' : 'border-gray-100'}`}>
                                            <td className="p-4">
                                                <div className="flex items-center gap-3">
                                                    <div className={`w-10 h-10 rounded-full flex items-center justify-center ${darkMode ? 'bg-teal-900/50' : 'bg-teal-100'}`}>
                                                        <User className="text-teal-500" size={18} />
                                                    </div>
                                                    <div>
                                                        <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{student.firstName} {student.lastName}</p>
                                                        <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{student.grade} "{student.section}"</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="p-4 text-center">
                                                <span className={`px-3 py-1 rounded-full text-sm font-medium ${darkMode ? 'bg-green-900/50 text-green-300' : 'bg-green-100 text-green-700'}`}>
                                                    {stats.present}
                                                </span>
                                            </td>
                                            <td className="p-4 text-center">
                                                <span className={`px-3 py-1 rounded-full text-sm font-medium ${darkMode ? 'bg-yellow-900/50 text-yellow-300' : 'bg-yellow-100 text-yellow-700'}`}>
                                                    {stats.late}
                                                </span>
                                            </td>
                                            <td className="p-4 text-center">
                                                <span className={`px-3 py-1 rounded-full text-sm font-medium ${darkMode ? 'bg-red-900/50 text-red-300' : 'bg-red-100 text-red-700'}`}>
                                                    {stats.absent}
                                                </span>
                                            </td>
                                            <td className="p-4 text-center">
                                                <div className={`inline-flex items-center gap-2 px-3 py-1 rounded-full ${stats.percentage >= 80 ? (darkMode ? 'bg-green-900/50 text-green-300' : 'bg-green-100 text-green-700') :
                                                        stats.percentage >= 60 ? (darkMode ? 'bg-yellow-900/50 text-yellow-300' : 'bg-yellow-100 text-yellow-700') :
                                                            (darkMode ? 'bg-red-900/50 text-red-300' : 'bg-red-100 text-red-700')
                                                    }`}>
                                                    <span className="font-bold">{stats.percentage}%</span>
                                                </div>
                                            </td>
                                            <td className="p-4">
                                                <div className="flex justify-center gap-2">
                                                    <button onClick={() => exportReport(student.id)} className={`p-2 rounded-lg ${darkMode ? 'bg-gray-700 hover:bg-gray-600' : 'bg-gray-100 hover:bg-gray-200'}`} title="Descargar PDF">
                                                        <Download size={16} className={darkMode ? 'text-gray-300' : 'text-gray-600'} />
                                                    </button>
                                                    <button onClick={() => sendToParent(student.id)} className="p-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white" title="Enviar a Encargado">
                                                        <Mail size={16} />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                </>
            )}
        </div>
    );
};

export default MisAlumnosPage;
