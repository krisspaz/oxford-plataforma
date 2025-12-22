import React, { useState, useEffect } from 'react';
import { Layers, Plus, Edit, Trash, Check, X, BookOpen, ChevronRight, Users, GraduationCap } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { catalogService } from '../services';

const CursosNivelesPage = () => {
    const { darkMode } = useTheme();
    const [levels, setLevels] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        try {
            const response = await catalogService.getAcademicLevels();
            if (response.success) setLevels(response.data);
        } catch (error) {
            console.error(error);
            // Enhanced Mock data
            setLevels([
                { id: 1, name: 'Preprimaria', code: 'PRE', students: 45, courses: 3, color: 'bg-pink-500' },
                { id: 2, name: 'Primaria', code: 'PRI', students: 120, courses: 6, color: 'bg-blue-500' },
                { id: 3, name: 'Básico', code: 'BAS', students: 85, courses: 3, color: 'bg-purple-500' },
                { id: 4, name: 'Diversificado', code: 'DIV', students: 60, courses: 5, color: 'bg-orange-500' },
            ]);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="space-y-8">
            <div className="flex justify-between items-center">
                <div>
                    <h1 className={`text-3xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Niveles Educativos</h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>Gestiona la estructura académica y cursos por nivel.</p>
                </div>
                <button className="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl shadow-lg shadow-indigo-500/30 flex items-center gap-2 font-medium transition-all hover:-translate-y-1">
                    <Plus size={20} /> Nuevo Nivel
                </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {levels.map((level) => (
                    <div key={level.id} className={`group relative overflow-hidden rounded-2xl ${darkMode ? 'bg-gray-800/80 border-gray-700' : 'bg-white border-gray-100'} border shadow-lg hover:shadow-2xl transition-all duration-300`}>
                        <div className={`absolute top-0 left-0 w-2 h-full ${level.color || 'bg-gray-500'}`}></div>
                        <div className="p-6">
                            <div className="flex justify-between items-start mb-4">
                                <div className={`p-3 rounded-xl ${level.color ? level.color + '/20' : 'bg-gray-100'} ${level.color ? level.color.replace('bg-', 'text-') : 'text-gray-600'}`}>
                                    <Layers size={28} />
                                </div>
                                <div className="flex gap-1">
                                    <button className={`p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        <Edit size={16} />
                                    </button>
                                </div>
                            </div>

                            <h3 className={`text-2xl font-bold mb-1 ${darkMode ? 'text-white' : 'text-gray-900'}`}>{level.name}</h3>
                            <p className="text-sm text-gray-400 font-mono mb-6">Código: {level.code}</p>

                            <div className="space-y-3">
                                <div className="flex items-center justify-between text-sm">
                                    <span className="flex items-center gap-2 text-gray-500">
                                        <Users size={16} /> Estudiantes
                                    </span>
                                    <span className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{level.students || 0}</span>
                                </div>
                                <div className="flex items-center justify-between text-sm">
                                    <span className="flex items-center gap-2 text-gray-500">
                                        <BookOpen size={16} /> Grados/Cursos
                                    </span>
                                    <span className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{level.courses || 0}</span>
                                </div>
                            </div>

                            <button className={`w-full mt-6 py-2 rounded-lg border ${darkMode ? 'border-gray-600 hover:bg-gray-700 text-gray-300' : 'border-gray-200 hover:bg-gray-50 text-gray-600'} text-sm font-medium transition-colors flex items-center justify-center gap-2 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/20 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 group-hover:border-indigo-200 dark:group-hover:border-indigo-800`}>
                                Ver Grados <ChevronRight size={16} />
                            </button>
                        </div>
                    </div>
                ))}
            </div>

            <div className={`mt-8 p-6 rounded-2xl ${darkMode ? 'bg-indigo-900/20 border-indigo-800' : 'bg-indigo-50 border-indigo-100'} border`}>
                <div className="flex items-start gap-4">
                    <div className="p-3 bg-indigo-100 text-indigo-600 rounded-lg">
                        <GraduationCap size={24} />
                    </div>
                    <div>
                        <h4 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>Resumen Académico</h4>
                        <p className={`text-sm mt-1 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                            Actualmente cuenta con <strong>{levels.reduce((acc, l) => acc + (l.courses || 0), 0)} grados</strong> activos distribuidos en {levels.length} niveles educativos.
                            Para crear un nuevo grado, seleccione primero el nivel correspondiente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default CursosNivelesPage;
