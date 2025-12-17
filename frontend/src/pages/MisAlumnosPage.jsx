import React, { useState, useEffect } from 'react';
import { Users, Search, Filter, GraduationCap, BookOpen, RefreshCw, User, Phone, Mail } from 'lucide-react';
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

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;

    // Demo: Courses assigned to this teacher
    const teacherCourses = [
        { id: 1, name: 'Matemáticas', grades: ['3ro Primaria', '4to Primaria', '5to Primaria'] },
        { id: 2, name: 'Física', grades: ['1ro Básico', '2do Básico'] },
    ];

    // Demo: Students from teacher's courses only
    const teacherStudents = [
        // 3ro Primaria A - Matemáticas
        { id: 1, code: 'ALU-2025-001', firstName: 'María', lastName: 'García López', grade: '3ro Primaria', section: 'A', course: 'Matemáticas', guardian: 'Juan García', phone: '5555-1234', email: 'juan@email.com' },
        { id: 2, code: 'ALU-2025-002', firstName: 'Carlos', lastName: 'Martínez Pérez', grade: '3ro Primaria', section: 'A', course: 'Matemáticas', guardian: 'Ana Pérez', phone: '5555-5678', email: 'ana@email.com' },
        { id: 3, code: 'ALU-2025-003', firstName: 'Ana', lastName: 'López Hernández', grade: '3ro Primaria', section: 'A', course: 'Matemáticas', guardian: 'Pedro López', phone: '5555-9012', email: 'pedro@email.com' },
        // 3ro Primaria B - Matemáticas
        { id: 4, code: 'ALU-2025-010', firstName: 'Luis', lastName: 'Rodríguez Castro', grade: '3ro Primaria', section: 'B', course: 'Matemáticas', guardian: 'María Castro', phone: '5555-3456', email: 'maria@email.com' },
        { id: 5, code: 'ALU-2025-011', firstName: 'Sofía', lastName: 'Hernández Gómez', grade: '3ro Primaria', section: 'B', course: 'Matemáticas', guardian: 'José Gómez', phone: '5555-7890', email: 'jose@email.com' },
        // 4to Primaria A - Matemáticas
        { id: 6, code: 'ALU-2025-020', firstName: 'Diego', lastName: 'Morales Ruiz', grade: '4to Primaria', section: 'A', course: 'Matemáticas', guardian: 'Laura Ruiz', phone: '5555-2345', email: 'laura@email.com' },
        { id: 7, code: 'ALU-2025-021', firstName: 'Valentina', lastName: 'Torres Méndez', grade: '4to Primaria', section: 'A', course: 'Matemáticas', guardian: 'Roberto Torres', phone: '5555-6789', email: 'roberto@email.com' },
        // 5to Primaria A - Matemáticas
        { id: 8, code: 'ALU-2025-030', firstName: 'Sebastián', lastName: 'Ramírez Díaz', grade: '5to Primaria', section: 'A', course: 'Matemáticas', guardian: 'Carmen Díaz', phone: '5555-0123', email: 'carmen@email.com' },
        // 1ro Básico A - Física
        { id: 9, code: 'ALU-2025-050', firstName: 'Andrea', lastName: 'Vásquez Solís', grade: '1ro Básico', section: 'A', course: 'Física', guardian: 'Miguel Solís', phone: '5555-4567', email: 'miguel@email.com' },
        { id: 10, code: 'ALU-2025-051', firstName: 'Fernando', lastName: 'Aguilar Paz', grade: '1ro Básico', section: 'A', course: 'Física', guardian: 'Sandra Paz', phone: '5555-8901', email: 'sandra@email.com' },
        // 2do Básico A - Física
        { id: 11, code: 'ALU-2025-060', firstName: 'Gabriela', lastName: 'Ortiz Luna', grade: '2do Básico', section: 'A', course: 'Física', guardian: 'Carlos Luna', phone: '5555-2341', email: 'carlos@email.com' },
        { id: 12, code: 'ALU-2025-061', firstName: 'Ricardo', lastName: 'Mendoza Cruz', grade: '2do Básico', section: 'A', course: 'Física', guardian: 'Patricia Cruz', phone: '5555-6785', email: 'patricia@email.com' },
    ];

    useEffect(() => {
        setTimeout(() => {
            setStudents(teacherStudents);
            setFilteredStudents(teacherStudents);
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

        if (selectedCourse) {
            filtered = filtered.filter(s => s.course === selectedCourse);
        }

        if (selectedGrade) {
            filtered = filtered.filter(s => s.grade === selectedGrade);
        }

        if (selectedSection) {
            filtered = filtered.filter(s => s.section === selectedSection);
        }

        setFilteredStudents(filtered);
    }, [searchTerm, selectedCourse, selectedGrade, selectedSection, students]);

    const clearFilters = () => {
        setSearchTerm('');
        setSelectedCourse('');
        setSelectedGrade('');
        setSelectedSection('');
    };

    const getUniqueGrades = () => {
        const grades = [...new Set(students.map(s => s.grade))];
        return grades.sort();
    };

    const getUniqueSections = () => {
        return [...new Set(students.map(s => s.section))].sort();
    };

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando alumnos...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            {/* Header */}
            <div>
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Mis Alumnos</h1>
                <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>
                    Estudiantes de los cursos que imparto
                </p>
            </div>

            {/* Stats */}
            <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-sm`}>
                    <div className="flex items-center gap-3">
                        <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${darkMode ? 'bg-blue-900/50' : 'bg-blue-100'}`}>
                            <Users className="text-blue-500" size={20} />
                        </div>
                        <div>
                            <p className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{students.length}</p>
                            <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Total alumnos</p>
                        </div>
                    </div>
                </div>
                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-sm`}>
                    <div className="flex items-center gap-3">
                        <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${darkMode ? 'bg-green-900/50' : 'bg-green-100'}`}>
                            <BookOpen className="text-green-500" size={20} />
                        </div>
                        <div>
                            <p className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{teacherCourses.length}</p>
                            <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cursos asignados</p>
                        </div>
                    </div>
                </div>
                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-sm`}>
                    <div className="flex items-center gap-3">
                        <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${darkMode ? 'bg-purple-900/50' : 'bg-purple-100'}`}>
                            <GraduationCap className="text-purple-500" size={20} />
                        </div>
                        <div>
                            <p className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{getUniqueGrades().length}</p>
                            <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Grados</p>
                        </div>
                    </div>
                </div>
                <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'} shadow-sm`}>
                    <div className="flex items-center gap-3">
                        <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${darkMode ? 'bg-orange-900/50' : 'bg-orange-100'}`}>
                            <Filter className="text-orange-500" size={20} />
                        </div>
                        <div>
                            <p className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{filteredStudents.length}</p>
                            <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Filtrados</p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Filters */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm`}>
                <div className="flex flex-wrap gap-4 items-end">
                    <div className="flex-1 min-w-[200px]">
                        <label className={`block text-xs font-medium mb-1 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Buscar</label>
                        <div className="relative">
                            <Search className={`absolute left-3 top-1/2 -translate-y-1/2 ${darkMode ? 'text-gray-400' : 'text-gray-400'}`} size={16} />
                            <input
                                type="text"
                                placeholder="Nombre o código..."
                                value={searchTerm}
                                onChange={e => setSearchTerm(e.target.value)}
                                className={`${inputClass} pl-9 w-full`}
                            />
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
                    <button
                        onClick={clearFilters}
                        className={`px-4 py-2 rounded-lg ${darkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`}
                    >
                        Limpiar
                    </button>
                </div>
            </div>

            {/* Students List */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead>
                            <tr className={darkMode ? 'bg-gray-700/50' : 'bg-gray-50'}>
                                <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Alumno</th>
                                <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Código</th>
                                <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Grado / Sección</th>
                                <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Curso</th>
                                <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Encargado</th>
                                <th className={`text-left p-4 font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Contacto</th>
                            </tr>
                        </thead>
                        <tbody>
                            {filteredStudents.map((student, idx) => (
                                <tr key={student.id} className={`border-t ${darkMode ? 'border-gray-700 hover:bg-gray-700/50' : 'border-gray-100 hover:bg-gray-50'}`}>
                                    <td className="p-4">
                                        <div className="flex items-center gap-3">
                                            <div className={`w-10 h-10 rounded-full flex items-center justify-center ${darkMode ? 'bg-teal-900/50' : 'bg-teal-100'}`}>
                                                <User className="text-teal-500" size={18} />
                                            </div>
                                            <div>
                                                <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                                    {student.firstName} {student.lastName}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className={`p-4 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                                        <span className={`px-2 py-1 rounded text-xs font-mono ${darkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>
                                            {student.code}
                                        </span>
                                    </td>
                                    <td className={`p-4 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                                        {student.grade} "{student.section}"
                                    </td>
                                    <td className="p-4">
                                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${darkMode ? 'bg-blue-900/50 text-blue-300' : 'bg-blue-100 text-blue-700'}`}>
                                            {student.course}
                                        </span>
                                    </td>
                                    <td className={`p-4 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>
                                        {student.guardian}
                                    </td>
                                    <td className="p-4">
                                        <div className="flex items-center gap-3">
                                            <a href={`tel:${student.phone}`} className={`flex items-center gap-1 text-sm ${darkMode ? 'text-gray-400 hover:text-white' : 'text-gray-500 hover:text-gray-700'}`}>
                                                <Phone size={14} />
                                                {student.phone}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {filteredStudents.length === 0 && (
                    <div className="p-12 text-center">
                        <Users className={`mx-auto ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} size={48} />
                        <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>No se encontraron alumnos</p>
                    </div>
                )}
            </div>
        </div>
    );
};

export default MisAlumnosPage;
