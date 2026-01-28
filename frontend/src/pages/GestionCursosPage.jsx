import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Settings, BookOpen, Plus, Edit, Search, Filter, MoreVertical, Layout, Grid } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { catalogService } from '../services';

const GestionCursosPage = () => {
    const { darkMode } = useTheme();
    const navigate = useNavigate();
    const [grades, setGrades] = useState([]);
    const [loading, setLoading] = useState(true);
    const [viewMode, setViewMode] = useState('grid'); // 'grid' | 'list'

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        try {
            const response = await catalogService.getGrades();
            if (response.success) setGrades(response.data);
        } catch (error) {
            console.error(error);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="space-y-8">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className={`text-3xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Gestión de Grados</h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>Administra los grados, secciones y materias asignadas.</p>
                </div>
                <div className="flex gap-3">
                    <button className={`p-2 rounded-lg ${viewMode === 'list' ? (darkMode ? 'bg-gray-700 text-white' : 'bg-gray-200 text-gray-800') : (darkMode ? 'text-gray-400 hover:text-white' : 'text-gray-500 hover:text-gray-800')}`} onClick={() => setViewMode('list')}>
                        <Layout size={20} />
                    </button>
                    <button className={`p-2 rounded-lg ${viewMode === 'grid' ? (darkMode ? 'bg-gray-700 text-white' : 'bg-gray-200 text-gray-800') : (darkMode ? 'text-gray-400 hover:text-white' : 'text-gray-500 hover:text-gray-800')}`} onClick={() => setViewMode('grid')}>
                        <Grid size={20} />
                    </button>
                    <button
                        onClick={() => navigate('/academico/cursos')}
                        className="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-xl flex items-center gap-2 shadow-lg shadow-teal-500/20 transition-all hover:-translate-y-1">
                        <Plus size={20} /> Gestionar Niveles
                    </button>
                </div>
            </div>

            {/* Filters Bar */}
            <div className={`p-4 rounded-xl border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} flex flex-col md:flex-row gap-4 items-center`}>
                <div className="relative flex-1 w-full">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                    <input
                        type="text"
                        placeholder="Buscar grado..."
                        className={`w-full pl-10 pr-4 py-2 rounded-lg border ${darkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-gray-50 border-gray-200 text-gray-900 placeholder-gray-500'} focus:ring-2 focus:ring-teal-500 outline-none`}
                    />
                </div>
                <button className={`flex items-center gap-2 px-4 py-2 rounded-lg border ${darkMode ? 'border-gray-600 hover:bg-gray-700 text-gray-300' : 'border-gray-200 hover:bg-gray-50 text-gray-600'}`}>
                    <Filter size={20} /> Filtros
                </button>
            </div>

            <div className={`${viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6' : 'flex flex-col gap-4'}`}>
                {grades.map(grade => (
                    <div key={grade.id} className={`${darkMode ? 'bg-gray-800 border-gray-700 hover:border-gray-600' : 'bg-white border-gray-100 hover:border-teal-200'} border rounded-2xl p-6 shadow-md hover:shadow-xl transition-all duration-300 group`}>
                        <div className="flex justify-between items-start mb-4">
                            <div className="flex items-center gap-3">
                                <div className={`p-3 rounded-xl ${darkMode ? 'bg-gray-700 text-teal-400' : 'bg-teal-50 text-teal-600'} group-hover:scale-110 transition-transform`}>
                                    <BookOpen size={24} />
                                </div>
                                <div>
                                    <h3 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{grade.name}</h3>
                                    <span className={`text-xs font-bold px-2 py-0.5 rounded-full ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-500'}`}>
                                        {grade.level || 'General'}
                                    </span>
                                </div>
                            </div>
                            <button className={`p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                <MoreVertical size={20} />
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div className={`p-3 rounded-xl ${darkMode ? 'bg-gray-700/50' : 'bg-gray-50'} flex justify-between items-center`}>
                                <span className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Secciones</span>
                                <div className="flex gap-1">
                                    {(grade.sections || ['A']).map(s => (
                                        <span key={s} className="w-6 h-6 flex items-center justify-center rounded-full bg-teal-100 text-teal-700 text-xs font-bold ring-1 ring-white dark:ring-gray-700">{s}</span>
                                    ))}
                                </div>
                            </div>

                            <div className="flex justify-between items-center text-sm">
                                <span className={darkMode ? 'text-gray-400' : 'text-gray-500'}>Materias Asignadas</span>
                                <span className={`font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{Math.floor(Math.random() * 8) + 4}</span>
                            </div>
                        </div>

                        <div className="flex gap-3 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button className="flex-1 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold shadow-lg shadow-teal-500/20 transition-all hover:translate-y-px">
                                Gestionar
                            </button>
                            <button className={`px-3 rounded-xl border ${darkMode ? 'border-gray-600 hover:bg-gray-700' : 'border-gray-200 hover:bg-gray-50'}`}>
                                <Settings size={20} className={darkMode ? 'text-gray-400' : 'text-gray-600'} />
                            </button>
                        </div>
                    </div>
                ))}

                {/* Add New Card (Only in Grid) */}
                {viewMode === 'grid' && (
                    <button onClick={() => navigate('/academico/grados')} className={`border-2 border-dashed rounded-2xl flex flex-col items-center justify-center gap-4 min-h-[280px] transition-all group ${darkMode ? 'border-gray-700 hover:border-teal-500 hover:bg-gray-800' : 'border-gray-300 hover:border-teal-500 hover:bg-teal-50/50'}`}>
                        <div className={`p-4 rounded-full transition-transform group-hover:scale-110 ${darkMode ? 'bg-gray-800 text-gray-500 group-hover:text-teal-400' : 'bg-gray-100 text-gray-400 group-hover:text-teal-600 group-hover:bg-white'}`}>
                            <Plus size={40} />
                        </div>
                        <span className={`font-medium text-lg ${darkMode ? 'text-gray-500 group-hover:text-teal-400' : 'text-gray-400 group-hover:text-teal-600'}`}>Agregar Grado</span>
                    </button>
                )}
            </div>
        </div>
    );
};

export default GestionCursosPage;
