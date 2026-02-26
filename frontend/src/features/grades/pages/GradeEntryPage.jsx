import { useGradeEntry } from '../hooks/useGradeEntry';
import { toast } from '../../../utils/toast';

const GradeEntryPage = () => {
    const {
        assignments, bimesters, students, loading, saving, currentBimester,
        selections, actions
    } = useGradeEntry();

    const isLocked = currentBimester?.isClosed;

    const handleSave = async () => {
        const res = await actions.saveGrades();
        if (res.success) {
            toast.success('Notas guardadas exitosamente');
        } else {
            toast.error('Error al guardar: ' + res.error);
        }
    };

    return (
        <div className="space-y-6">
            <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Carga de Notas</h1>
                    <p className="text-gray-500 dark:text-gray-400">Ingreso de calificaciones por período</p>
                </div>

                <div className="flex gap-2">
                    <Button
                        variant="primary"
                        icon={saving ? <Spinner size="sm" /> : <Save size={18} />}
                        disabled={saving || isLocked || !selections.assignmentId}
                        onClick={handleSave}
                    >
                        {saving ? 'Guardando...' : 'Guardar Cambios'}
                    </Button>
                </div>
            </div>

            {/* Filters */}
            <Card className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label className="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                        Materia / Curso
                    </label>
                    <select
                        className="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        value={selections.assignmentId}
                        onChange={(e) => selections.setAssignmentId(e.target.value)}
                    >
                        <option value="">Seleccione una materia...</option>
                        {assignments.map(a => (
                            <option key={a.id} value={a.id}>{a.name}</option>
                        ))}
                    </select>
                </div>
                <div>
                    <label className="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                        Bimestre / Unidad
                    </label>
                    <select
                        className="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        value={selections.bimesterId}
                        onChange={(e) => selections.setBimesterId(e.target.value)}
                    >
                        <option value="">Seleccione...</option>
                        {bimesters.map(b => (
                            <option key={b.id} value={b.id}>
                                {b.name} {b.isClosed ? '(Cerrado)' : '(Activo)'}
                            </option>
                        ))}
                    </select>
                </div>
            </Card>

            {/* Status Bar */}
            {selections.assignmentId && selections.bimesterId && (
                <div className={`p-4 rounded-lg flex items-center gap-3 ${isLocked ? 'bg-orange-50 text-orange-800' : 'bg-blue-50 text-blue-800'
                    }`}>
                    {isLocked ? <Lock size={20} /> : <Unlock size={20} />}
                    <span className="font-medium">
                        {isLocked
                            ? 'Este período está cerrado. No se pueden modificar notas.'
                            : 'Período activo. Puede ingresar y modificar calificaciones.'}
                    </span>
                </div>
            )}

            {/* Grades Table */}
            {loading ? (
                <div className="flex justify-center py-12">
                    <Spinner size="lg" />
                </div>
            ) : selections.assignmentId && selections.bimesterId ? (
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <table className="w-full">
                        <thead className="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Carnet</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiante</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Nota</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
                            {students.map(student => (
                                <tr key={student.id} className="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {student.carnet}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {student.name}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <input
                                            type="number"
                                            min="0"
                                            max="100"
                                            disabled={isLocked || student.isLocked}
                                            value={student.score ?? ''}
                                            onChange={(e) => actions.updateScore(student.id, e.target.value)}
                                            className={`w-24 px-3 py-1 border rounded focus:ring-2 focus:ring-blue-500 text-center ${(student.score === null || student.score === '') ? 'bg-gray-50' :
                                                student.score < 60 ? 'text-red-600 border-red-300 bg-red-50' :
                                                    'text-gray-900 border-gray-300'
                                                } dark:bg-gray-700 dark:border-gray-600 dark:text-white`}
                                        />
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm">
                                        {student.score >= 60 ? (
                                            <span className="flex items-center text-green-600 gap-1"><CheckCircle size={14} /> Aprobado</span>
                                        ) : student.score !== null ? (
                                            <span className="flex items-center text-red-600 gap-1"><AlertCircle size={14} /> Reprobado</span>
                                        ) : (
                                            <span className="text-gray-400">-</span>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                    {students.length === 0 && (
                        <div className="p-8 text-center text-gray-500">
                            No hay estudiantes asignados a este curso.
                        </div>
                    )}
                </div>
            ) : (
                <div className="text-center py-12 text-gray-500 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    Seleccione una materia y un bimestre para comenzar.
                </div>
            )}
        </div>
    );
};

export default GradeEntryPage;
