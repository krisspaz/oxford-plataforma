import React, { useState, useEffect } from 'react';
import { Download, FileText, Users, User, Filter } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { reportService, gradeService, bimesterService } from '../services';

const ReportsPage = () => {
    const { darkMode } = useTheme();
    const [reportType, setReportType] = useState('boletas'); // boletas, cuadros
    const [scope, setScope] = useState('massive'); // massive, individual
    const [grades, setGrades] = useState([]);
    const [bimesters, setBimesters] = useState([]);

    // Filters
    const [selectedGrade, setSelectedGrade] = useState('');
    const [selectedSection, setSelectedSection] = useState('');
    const [selectedBimester, setSelectedBimester] = useState('');
    const [studentSearch, setStudentSearch] = useState('');

    useEffect(() => {
        const loadOptions = async () => {
            try {
                // Fetch real data
                const gRes = await gradeService.getAll();
                // Check if response format is { success: true, data: [] } or just [] (Adjust based on inspection)
                // Assuming standard:
                if (gRes.success !== false) setGrades(gRes.data || gRes); // Fallback

                const bRes = await bimesterService.getAll();
                if (bRes.success !== false) setBimesters(bRes.data || bRes);
            } catch (e) {
                console.error("Error loading options", e);
            }
        };
        loadOptions();
    }, []);

    const handleGenerate = async () => {
        const params = {
            type: reportType,
            scope: scope,
            grade: selectedGrade,
            section: selectedSection,
            bimester: selectedBimester,
            studentSearch: studentSearch
        };

        const { success, message, details } = await reportService.generate(params);

        if (success) {
            alert(`${message}\n(Backend Mock: Link generado en ${details?.downloadUrl || 'N/A'})`);
        } else {
            alert('Error al generar reporte: ' + (message || 'Error desconocido'));
        }
    };

    const inputClass = `w-full px-3 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-teal-500 ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300'
        }`;

    return (
        <div className="space-y-6">
            <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Generación de Reportes</h1>

            <div className={`p-6 rounded-xl shadow-sm border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'}`}>

                {/* Type Selection */}
                <div className="flex gap-4 mb-6 border-b pb-6 dark:border-gray-700">
                    <button
                        onClick={() => setReportType('boletas')}
                        className={`flex-1 p-4 rounded-xl border-2 transition-all flex items-center justify-center gap-3 ${reportType === 'boletas'
                            ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300'
                            : 'border-gray-200 dark:border-gray-700 grayscale opacity-70'
                            }`}
                    >
                        <FileText size={24} />
                        <span className="font-bold">Boletas de Calificaciones</span>
                    </button>
                    <button
                        onClick={() => setReportType('cuadros')}
                        className={`flex-1 p-4 rounded-xl border-2 transition-all flex items-center justify-center gap-3 ${reportType === 'cuadros'
                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                            : 'border-gray-200 dark:border-gray-700 grayscale opacity-70'
                            }`}
                    >
                        <Users size={24} />
                        <span className="font-bold">Cuadros de Notas (MINEDUC)</span>
                    </button>
                </div>

                {/* Scope Selection */}
                <div className="mb-6 flex items-center gap-6">
                    <label className={`flex items-center gap-2 cursor-pointer ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                        <input
                            type="radio"
                            name="scope"
                            checked={scope === 'massive'}
                            onChange={() => setScope('massive')}
                            className="form-radio text-teal-600"
                        />
                        <Users size={18} /> Generación Masiva
                    </label>
                    <label className={`flex items-center gap-2 cursor-pointer ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                        <input
                            type="radio"
                            name="scope"
                            checked={scope === 'individual'}
                            onChange={() => setScope('individual')}
                            className="form-radio text-teal-600"
                        />
                        <User size={18} /> Por Estudiante
                    </label>
                </div>

                {/* Filters */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>Ciclo / Bimestre</label>
                        <select
                            className={inputClass}
                            value={selectedBimester}
                            onChange={(e) => setSelectedBimester(e.target.value)}
                        >
                            <option value="">Seleccione Bimestre</option>
                            {bimesters.map(b => (
                                <option key={b.id} value={b.id}>{b.name}</option>
                            ))}
                        </select>
                    </div>

                    {scope === 'massive' && (
                        <>
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>Grado</label>
                                <select
                                    className={inputClass}
                                    value={selectedGrade}
                                    onChange={(e) => setSelectedGrade(e.target.value)}
                                >
                                    <option value="">Todos los Grados</option>
                                    {grades.map(g => (
                                        <option key={g.id} value={g.id}>{g.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>Sección</label>
                                <select
                                    className={inputClass}
                                    value={selectedSection}
                                    onChange={(e) => setSelectedSection(e.target.value)}
                                >
                                    <option value="">Todas</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                </select>
                            </div>
                        </>
                    )}

                    {scope === 'individual' && (
                        <div className="col-span-2">
                            <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>Buscar Estudiante</label>
                            <input
                                type="text"
                                className={inputClass}
                                placeholder="Nombre o Carné..."
                                value={studentSearch}
                                onChange={(e) => setStudentSearch(e.target.value)}
                            />
                        </div>
                    )}
                </div>

                {/* Action */}
                <div className="flex justify-end pt-4 border-t dark:border-gray-700">
                    <button
                        onClick={handleGenerate}
                        className="px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl shadow-lg shadow-teal-900/20 flex items-center gap-2 font-bold"
                    >
                        <Download size={20} />
                        {scope === 'massive' ? 'Generar Reporte Masivo' : 'Generar Reporte Individual'}
                    </button>
                </div>

            </div>
        </div>
    );
};

export default ReportsPage;
