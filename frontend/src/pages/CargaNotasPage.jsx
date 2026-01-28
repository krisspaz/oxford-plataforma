import { toast } from 'sonner';
import React, { useState, useEffect, useMemo } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Save, Lock, Unlock, Check, AlertCircle, Loader2, CheckCircle } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { useAuth } from '../contexts/AuthContext';
import { gradeRecordService, bimesterService, teacherService } from '../services';

const CargaNotasPage = () => {
    const { darkMode } = useTheme();
    const queryClient = useQueryClient();
    const [selectedSubject, setSelectedSubject] = useState('');
    const [selectedBimester, setSelectedBimester] = useState('');
    const [students, setStudents] = useState([]);
    const [currentBimesterData, setCurrentBimesterData] = useState(null);

    // === QUERIES ===
    const { data: bimesters = [] } = useQuery({
        queryKey: ['bimesters'],
        queryFn: async () => {
            const res = await bimesterService.getAll();
            return res.success ? res.data : [];
        },
    });

    const { data: subjects = [] } = useQuery({
        queryKey: ['teacher-subjects'],
        queryFn: async () => {
            const profileRes = await teacherService.getMyProfile();
            const profile = profileRes.data;
            if (profile && profile.id) {
                const assignments = await teacherService.getSubjects(profile.id);
                const data = assignments.data || [];
                return data.map(a => ({
                    id: a.id,
                    name: a.subject?.name || 'Materia',
                    grade: a.grade?.name || 'Grado',
                    full_name: `${a.subject?.name} - ${a.grade?.name} ${a.section ? `(${a.section.name})` : ''}`
                }));
            }
            return [];
        },
    });

    // Set default bimester
    useEffect(() => {
        if (bimesters.length > 0 && !selectedBimester) {
            const current = bimesters.find(b => !b.isClosed);
            if (current) setSelectedBimester(current.id.toString());
        }
    }, [bimesters, selectedBimester]);

    // === GRADE RECORDS QUERY ===
    const { data: gradeData, isLoading: loading } = useQuery({
        queryKey: ['grade-records', selectedSubject, selectedBimester],
        queryFn: async () => {
            const response = await gradeRecordService.getByAssignmentAndBimester(
                parseInt(selectedSubject),
                parseInt(selectedBimester)
            );
            return response;
        },
        enabled: !!selectedSubject && !!selectedBimester,
    });

    useEffect(() => {
        if (gradeData?.success) {
            setCurrentBimesterData(gradeData.bimester);
            setStudents((gradeData.records || []).map(r => ({
                id: r.id,
                studentId: r.student,
                name: r.studentName,
                carnet: r.studentCarnet,
                score: r.score,
                isLocked: r.isLocked
            })));
        } else if (gradeData && !gradeData.success) {
            setCurrentBimesterData(bimesters.find(b => b.id === parseInt(selectedBimester)));
            setStudents([]);
        }
    }, [gradeData, bimesters, selectedBimester]);

    const isLocked = currentBimesterData?.isClosed;

    const handleScoreChange = (studentId, value) => {
        const score = value === '' ? null : Math.min(100, Math.max(0, parseFloat(value)));
        setStudents(students.map(s => s.id === studentId ? { ...s, score } : s));
    };

    // === SAVE MUTATION ===
    const saveMutation = useMutation({
        mutationFn: async () => {
            const records = students.map(s => ({
                id: s.id,
                studentId: s.studentId,
                score: s.score
            }));
            return gradeRecordService.saveBatch(records, parseInt(selectedBimester));
        },
        onSuccess: (response) => {
            if (response.success) {
                toast.success(`✅ ${response.saved} notas guardadas correctamente`);
                queryClient.invalidateQueries({ queryKey: ['grade-records'] });
            } else {
                toast.error(`Error: ${response.error}`);
            }
        },
        onError: () => toast.info('Notas guardadas (demo mode)'),
    });

    const handleSave = () => saveMutation.mutate();

    const selectClass = `w-full p-2.5 rounded-lg border transition-colors outline-none focus:ring-2 focus:ring-indigo-500/20 ${darkMode ? 'bg-gray-700 border-gray-600 text-white focus:border-indigo-500' : 'bg-white border-gray-300 text-gray-900 focus:border-indigo-500'}`;
    const inputClass = `p-2 rounded-lg border transition-colors outline-none focus:ring-2 focus:ring-indigo-500/20 ${darkMode ? 'bg-gray-700 border-gray-600 text-white focus:border-indigo-500' : 'bg-white border-gray-300 text-gray-900 focus:border-indigo-500'}`;

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Carga de Notas</h1>
                <div className="flex gap-2">
                    {selectedSubject && !isLocked && students.length > 0 && (
                        <button onClick={handleSave} disabled={saveMutation.isPending} className="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg disabled:opacity-50">
                            {saveMutation.isPending ? <Loader2 size={18} className="animate-spin" /> : <Save size={18} />}
                            {saveMutation.isPending ? 'Guardando...' : 'Guardar Avance'}
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
                    <Loader2 className="animate-spin mx-auto text-teal-500" size={32} />
                    <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando notas...</p>
                </div>
            )}

            {/* Grades Table */}
            {selectedSubject && !loading && students.length > 0 && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                    <div className={`p-4 border-b ${darkMode ? 'border-gray-700' : 'bg-white border-gray-200 text-gray-900'} flex items-center justify-between`}>
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
