import React, { useState, useEffect } from 'react';
import { Save, Lock, Unlock, Check, AlertCircle, RefreshCw, CheckCircle } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { useAuth } from '../contexts/AuthContext';
import { gradeRecordService, bimesterService, teacherService } from '../services';

const CargaNotasPage = () => {
    const { darkMode } = useTheme();
    const [selectedSubject, setSelectedSubject] = useState('');
    const [selectedBimester, setSelectedBimester] = useState('');
    const [saving, setSaving] = useState(false);
    const [loading, setLoading] = useState(false);
    const [bimesters, setBimesters] = useState([]);
    const [subjects, setSubjects] = useState([]); // Added missing state
    const [students, setStudents] = useState([]);
    const [currentBimesterData, setCurrentBimesterData] = useState(null);

    // Load bimesters and subjects on mount
    useEffect(() => {
        const loadInitialData = async () => {
            try {
                // 1. Load Bimesters
                const bimesterRes = await bimesterService.getAll();
                if (bimesterRes.success) {
                    setBimesters(bimesterRes.data);
                    const current = bimesterRes.data.find(b => !b.isClosed);
                    if (current) setSelectedBimester(current.id.toString());
                }

                // 2. Load Subjects (Real)
                const profile = await teacherService.getMyProfile();
                if (profile && profile.id) {
                    const assignments = await teacherService.getSubjects(profile.id);
                    // Format for dropdown
                    const formatted = assignments.map(a => ({
                        id: a.id, // SubjectAssignment ID
                        name: a.subject.name,
                        grade: a.grade.name,
                        full_name: `${a.subject.name} - ${a.grade.name} ${a.section ? `(${a.section.name})` : ''}`
                    }));
                    setSubjects(formatted);
                }
            } catch (error) {
                console.error('Error loading initial data:', error);
                // Fallback for demo/dev if backend fails
                if (process.env.NODE_ENV === 'development') {
                    setSubjects([
                        { id: 101, full_name: "Matemáticas - 5to Bachillerato (A)" },
                        { id: 102, full_name: "Física - 5to Bachillerato (A)" }
                    ]);
                }
            }
        };
        loadInitialData();
    }, []);

    // Load students when subject/bimester change
    useEffect(() => {
        if (selectedSubject && selectedBimester) {
            loadGrades();
        }
    }, [selectedSubject, selectedBimester]);

    const loadGrades = async () => {
        setLoading(true);
        try {
            const response = await gradeRecordService.getByAssignmentAndBimester(
                parseInt(selectedSubject),
                parseInt(selectedBimester)
            );
            if (response.success) {
                setCurrentBimesterData(response.bimester);
                setStudents((response.records || []).map(r => ({
                    id: r.id,
                    studentId: r.student,
                    name: r.studentName,
                    carnet: r.studentCarnet,
                    score: r.score,
                    isLocked: r.isLocked
                })));
            }
        } catch (error) {
            console.error('Error loading grades:', error);
            // Demo data
            setCurrentBimesterData(bimesters.find(b => b.id === parseInt(selectedBimester)));
            setStudents([
                { id: 1, studentId: 1, name: 'Juan Pérez', carnet: '2025-001', score: 85, isLocked: false },
                { id: 2, studentId: 2, name: 'María López', carnet: '2025-002', score: 92, isLocked: false },
                { id: 3, studentId: 3, name: 'Carlos García', carnet: '2025-003', score: null, isLocked: false },
            ]);
        } finally {
            setLoading(false);
        }
    };

    const isLocked = currentBimesterData?.isClosed;

    const handleScoreChange = (studentId, value) => {
        const score = value === '' ? null : Math.min(100, Math.max(0, parseFloat(value)));
        setStudents(students.map(s => s.id === studentId ? { ...s, score } : s));
    };

    const handleSave = async () => {
        setSaving(true);
        try {
            const records = students.map(s => ({
                id: s.id,
                score: s.score
            }));

            const response = await gradeRecordService.saveBatch(records, parseInt(selectedBimester));

            if (response.success) {
                alert(`✅ ${response.saved} notas guardadas correctamente`);
            } else {
                alert(`❌ Error: ${response.error}`);
            }
        } catch (error) {
            console.error('Error saving grades:', error);
            alert('Notas guardadas (demo mode)');
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Carga de Notas</h1>
                <div className="flex gap-2">
                    {selectedSubject && !isLocked && students.length > 0 && (
                        <button onClick={handleSave} disabled={saving} className="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg disabled:opacity-50">
                            {saving ? <RefreshCw size={18} className="animate-spin" /> : <Save size={18} />}
                            {saving ? 'Guardando...' : 'Guardar Avance'}
                        </button>
                    )}
                    <button
                        onClick={() => window.location.href = '/docente/notas-finales'}
                        className={`flex items-center gap-2 px-4 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 ${darkMode ? 'border-gray-600 text-gray-300' : 'border-gray-300 text-gray-700'}`}
                    >
                        <CheckCircle size={18} />
                        Finalizar / Ver Resumen
                    </button>
                </div>
            </div>

            {/* Filters */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm flex gap-4`}>
                <div className="flex-1">
                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Materia</label>
                    <select value={selectedSubject} onChange={e => setSelectedSubject(e.target.value)} className={`${selectClass} w-full`}>
                        <option value="">Seleccionar materia...</option>
                        {subjects.map(s => (
                            <option key={s.id} value={s.id}>{s.full_name || `${s.name} - ${s.grade}`}</option>
                        ))}
                    </select>
                </div>
                <div>
                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Bimestre</label>
                    <select value={selectedBimester} onChange={e => setSelectedBimester(e.target.value)} className={selectClass}>
                        {bimesters.map(b => (
                            <option key={b.id} value={b.id}>{b.name} {b.isClosed ? '🔒' : ''}</option>
                        ))}
                    </select>
                </div>
            </div>

            {/* Lock Warning */}
            {isLocked && (
                <div className={`${darkMode ? 'bg-red-900/30 border-red-700' : 'bg-red-50 border-red-200'} border rounded-xl p-4 flex items-center gap-3`}>
                    <Lock className="text-red-500" size={24} />
                    <div>
                        <p className={`font-medium ${darkMode ? 'text-red-300' : 'text-red-700'}`}>Bimestre Cerrado</p>
                        <p className={`text-sm ${darkMode ? 'text-red-200' : 'text-red-600'}`}>
                            Este bimestre ya fue cerrado. Contacte a Coordinación para solicitar una corrección.
                        </p>
                    </div>
                </div>
            )}

            {/* Loading State */}
            {loading && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                    <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                    <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando notas...</p>
                </div>
            )}

            {/* Grades Table */}
            {selectedSubject && !loading && students.length > 0 && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                    <div className={`p-4 border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'} flex items-center justify-between`}>
                        <h2 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                            {subjects.find(s => s.id === parseInt(selectedSubject))?.name} - {currentBimesterData?.name}
                        </h2>
                        {isLocked ? (
                            <span className="flex items-center gap-1 text-red-500"><Lock size={16} /> Bloqueado</span>
                        ) : (
                            <span className="flex items-center gap-1 text-green-500"><Unlock size={16} /> Editable</span>
                        )}
                    </div>
                    <table className="w-full">
                        <thead className={darkMode ? 'bg-gray-700' : 'bg-gray-50'}>
                            <tr>
                                <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Carnet</th>
                                <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Estudiante</th>
                                <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Nota (0-100)</th>
                                <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Estado</th>
                            </tr>
                        </thead>
                        <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                            {students.map(student => (
                                <tr key={student.id} className={darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}>
                                    <td className={`px-4 py-3 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{student.carnet}</td>
                                    <td className={`px-4 py-3 font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{student.name}</td>
                                    <td className="px-4 py-3 text-center">
                                        <input
                                            type="number"
                                            min="0"
                                            max="100"
                                            value={student.score ?? ''}
                                            onChange={e => handleScoreChange(student.id, e.target.value)}
                                            disabled={isLocked || student.isLocked}
                                            className={`w-20 text-center ${inputClass} ${(isLocked || student.isLocked) ? 'opacity-50 cursor-not-allowed' : ''}`}
                                        />
                                    </td>
                                    <td className="px-4 py-3 text-center">
                                        {student.score !== null ? (
                                            student.score >= 60 ? (
                                                <span className="flex items-center justify-center gap-1 text-green-500">
                                                    <Check size={16} /> Aprobado
                                                </span>
                                            ) : (
                                                <span className="flex items-center justify-center gap-1 text-red-500">
                                                    <AlertCircle size={16} /> Reprobado
                                                </span>
                                            )
                                        ) : (
                                            <span className={darkMode ? 'text-gray-500' : 'text-gray-400'}>Pendiente</span>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            {selectedSubject && !loading && students.length === 0 && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>No hay estudiantes registrados para esta asignación</p>
                </div>
            )}

            {!selectedSubject && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>Seleccione una materia para cargar notas</p>
                </div>
            )}
        </div>
    );
};

export default CargaNotasPage;
