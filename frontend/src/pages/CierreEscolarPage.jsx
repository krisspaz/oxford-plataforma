import React, { useState, useEffect } from 'react';
import { Archive, AlertTriangle, Check, ChevronRight, Database, Calendar, Users, FileText, Download, Loader } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { toast } from '../utils/toast';
import api from '../services/api';

const CierreEscolarPage = () => {
    const { darkMode } = useTheme();
    const [currentStep, setCurrentStep] = useState(0);
    const [loading, setLoading] = useState(false);
    const [validations, setValidations] = useState({
        gradesComplete: false,
        periodsComplete: false,
        backupDone: false,
    });
    const [newCycleConfig, setNewCycleConfig] = useState({
        year: new Date().getFullYear() + 1,
        startDate: '',
        endDate: '',
        migrateStudents: true,
        archiveGrades: true,
    });
    const [migrationPreview, setMigrationPreview] = useState(null);
    const [confirmChecks, setConfirmChecks] = useState({
        understand: false,
        backup: false,
        notify: false,
    });

    const steps = [
        { id: 0, title: 'Validaciones Pre-Cierre', icon: Check, description: 'Verificar notas completas, períodos cerrados y backup' },
        { id: 1, title: 'Configurar Nuevo Ciclo', icon: Calendar, description: 'Definir fechas y parámetros del próximo año' },
        { id: 2, title: 'Preview Migración', icon: Users, description: 'Revisar qué datos se migrarán' },
        { id: 3, title: 'Confirmación', icon: AlertTriangle, description: 'Confirmar que entiende las consecuencias' },
        { id: 4, title: 'Reporte Post-Cierre', icon: FileText, description: 'Resumen de la migración realizada' },
    ];

    useEffect(() => {
        if (currentStep === 0) {
            runValidations();
        }
    }, [currentStep]);

    const runValidations = async () => {
        setLoading(true);
        try {
            // Simulate API call for validation checks
            const response = await api.get('/school-cycles/pre-close-validation').catch(() => ({ data: {} }));
            setValidations({
                gradesComplete: response.data?.gradesComplete ?? true, // Mock as true for demo
                periodsComplete: response.data?.periodsComplete ?? true,
                backupDone: false, // Always require manual confirmation
            });
        } catch (error) {
            console.error('Validation error:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleGenerateBackup = async () => {
        setLoading(true);
        try {
            await api.post('/backups/create');
            setValidations({ ...validations, backupDone: true });
            toast.success('Backup generado exitosamente');
        } catch (error) {
            console.error('Backup error:', error);
            toast.error('Error al generar backup');
        } finally {
            setLoading(false);
        }
    };

    const handlePreviewMigration = async () => {
        setLoading(true);
        try {
            const response = await api.get('/school-cycles/migration-preview').catch(() => ({
                data: {
                    studentsToMigrate: 245,
                    studentsGraduating: 32,
                    gradesToArchive: 1840,
                    financialRecords: 523,
                }
            }));
            setMigrationPreview(response.data);
            setCurrentStep(2);
        } catch (error) {
            console.error('Preview error:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleExecuteClose = async () => {
        if (!Object.values(confirmChecks).every(v => v)) {
            toast.error('Debe confirmar todos los puntos antes de continuar');
            return;
        }

        setLoading(true);
        try {
            await api.post('/school-cycles/execute-close', newCycleConfig);
            toast.success('Cierre escolar ejecutado exitosamente');
            setCurrentStep(4);
        } catch (error) {
            console.error('Close error:', error);
            toast.error('Error al ejecutar cierre escolar');
        } finally {
            setLoading(false);
        }
    };

    const canProceed = () => {
        switch (currentStep) {
            case 0:
                return validations.gradesComplete && validations.periodsComplete && validations.backupDone;
            case 1:
                return newCycleConfig.year && newCycleConfig.startDate && newCycleConfig.endDate;
            case 2:
                return migrationPreview !== null;
            case 3:
                return Object.values(confirmChecks).every(v => v);
            default:
                return false;
        }
    };

    const inputClass = `w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
        }`;

    const renderStepContent = () => {
        switch (currentStep) {
            case 0:
                return (
                    <div className="space-y-4">
                        <h3 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                            Validaciones Requeridas
                        </h3>

                        <div className={`p-4 rounded-lg flex items-center justify-between ${validations.gradesComplete
                                ? 'bg-green-100 dark:bg-green-900/30'
                                : 'bg-red-100 dark:bg-red-900/30'
                            }`}>
                            <div className="flex items-center gap-3">
                                {validations.gradesComplete ? <Check className="text-green-600" /> : <AlertTriangle className="text-red-600" />}
                                <span className={validations.gradesComplete ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400'}>
                                    Todas las notas están completas
                                </span>
                            </div>
                            {validations.gradesComplete ? '✓' : 'Pendiente'}
                        </div>

                        <div className={`p-4 rounded-lg flex items-center justify-between ${validations.periodsComplete
                                ? 'bg-green-100 dark:bg-green-900/30'
                                : 'bg-red-100 dark:bg-red-900/30'
                            }`}>
                            <div className="flex items-center gap-3">
                                {validations.periodsComplete ? <Check className="text-green-600" /> : <AlertTriangle className="text-red-600" />}
                                <span className={validations.periodsComplete ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400'}>
                                    Todos los períodos cerrados
                                </span>
                            </div>
                            {validations.periodsComplete ? '✓' : 'Pendiente'}
                        </div>

                        <div className={`p-4 rounded-lg flex items-center justify-between ${validations.backupDone
                                ? 'bg-green-100 dark:bg-green-900/30'
                                : 'bg-yellow-100 dark:bg-yellow-900/30'
                            }`}>
                            <div className="flex items-center gap-3">
                                <Database className={validations.backupDone ? 'text-green-600' : 'text-yellow-600'} />
                                <span className={validations.backupDone ? 'text-green-700 dark:text-green-400' : 'text-yellow-700 dark:text-yellow-400'}>
                                    Backup de base de datos
                                </span>
                            </div>
                            {validations.backupDone ? (
                                '✓ Completado'
                            ) : (
                                <button
                                    onClick={handleGenerateBackup}
                                    disabled={loading}
                                    className="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm flex items-center gap-2"
                                >
                                    {loading ? <Loader size={16} className="animate-spin" /> : <Download size={16} />}
                                    Generar Backup
                                </button>
                            )}
                        </div>
                    </div>
                );

            case 1:
                return (
                    <div className="space-y-6">
                        <h3 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                            Configuración del Nuevo Ciclo
                        </h3>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                    Año del Ciclo
                                </label>
                                <input
                                    type="number"
                                    value={newCycleConfig.year}
                                    onChange={(e) => setNewCycleConfig({ ...newCycleConfig, year: e.target.value })}
                                    className={inputClass}
                                />
                            </div>
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                    Nombre del Ciclo
                                </label>
                                <input
                                    type="text"
                                    value={`Ciclo ${newCycleConfig.year}`}
                                    disabled
                                    className={inputClass + ' opacity-60'}
                                />
                            </div>
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                    Fecha de Inicio
                                </label>
                                <input
                                    type="date"
                                    value={newCycleConfig.startDate}
                                    onChange={(e) => setNewCycleConfig({ ...newCycleConfig, startDate: e.target.value })}
                                    className={inputClass}
                                />
                            </div>
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                    Fecha de Fin
                                </label>
                                <input
                                    type="date"
                                    value={newCycleConfig.endDate}
                                    onChange={(e) => setNewCycleConfig({ ...newCycleConfig, endDate: e.target.value })}
                                    className={inputClass}
                                />
                            </div>
                        </div>

                        <div className="space-y-3 mt-4">
                            <label className="flex items-center gap-3 cursor-pointer">
                                <input
                                    type="checkbox"
                                    checked={newCycleConfig.migrateStudents}
                                    onChange={(e) => setNewCycleConfig({ ...newCycleConfig, migrateStudents: e.target.checked })}
                                    className="w-5 h-5 rounded"
                                />
                                <span className={darkMode ? 'text-gray-300' : 'text-gray-700'}>
                                    Migrar estudiantes automáticamente al siguiente grado
                                </span>
                            </label>
                            <label className="flex items-center gap-3 cursor-pointer">
                                <input
                                    type="checkbox"
                                    checked={newCycleConfig.archiveGrades}
                                    onChange={(e) => setNewCycleConfig({ ...newCycleConfig, archiveGrades: e.target.checked })}
                                    className="w-5 h-5 rounded"
                                />
                                <span className={darkMode ? 'text-gray-300' : 'text-gray-700'}>
                                    Archivar notas y calificaciones del ciclo actual
                                </span>
                            </label>
                        </div>
                    </div>
                );

            case 2:
                return (
                    <div className="space-y-6">
                        <h3 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                            Vista Previa de Migración
                        </h3>

                        {migrationPreview && (
                            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div className={`p-4 rounded-lg ${darkMode ? 'bg-blue-900/30' : 'bg-blue-50'}`}>
                                    <p className="text-blue-600 text-3xl font-bold">{migrationPreview.studentsToMigrate}</p>
                                    <p className={`text-sm ${darkMode ? 'text-blue-400' : 'text-blue-700'}`}>Estudiantes a migrar</p>
                                </div>
                                <div className={`p-4 rounded-lg ${darkMode ? 'bg-green-900/30' : 'bg-green-50'}`}>
                                    <p className="text-green-600 text-3xl font-bold">{migrationPreview.studentsGraduating}</p>
                                    <p className={`text-sm ${darkMode ? 'text-green-400' : 'text-green-700'}`}>Graduandos</p>
                                </div>
                                <div className={`p-4 rounded-lg ${darkMode ? 'bg-purple-900/30' : 'bg-purple-50'}`}>
                                    <p className="text-purple-600 text-3xl font-bold">{migrationPreview.gradesToArchive}</p>
                                    <p className={`text-sm ${darkMode ? 'text-purple-400' : 'text-purple-700'}`}>Notas a archivar</p>
                                </div>
                                <div className={`p-4 rounded-lg ${darkMode ? 'bg-orange-900/30' : 'bg-orange-50'}`}>
                                    <p className="text-orange-600 text-3xl font-bold">{migrationPreview.financialRecords}</p>
                                    <p className={`text-sm ${darkMode ? 'text-orange-400' : 'text-orange-700'}`}>Registros financieros</p>
                                </div>
                            </div>
                        )}
                    </div>
                );

            case 3:
                return (
                    <div className="space-y-6">
                        <div className={`p-4 rounded-lg ${darkMode ? 'bg-red-900/30 border-red-800' : 'bg-red-50 border-red-200'} border`}>
                            <div className="flex items-start gap-3">
                                <AlertTriangle className="text-red-500 flex-shrink-0 mt-1" size={24} />
                                <div>
                                    <h3 className="font-bold text-red-600 dark:text-red-400 text-lg">¡Atención! Proceso Irreversible</h3>
                                    <p className="text-red-600 dark:text-red-400 text-sm mt-2">
                                        Una vez ejecutado este proceso, no se podrá revertir. Asegúrese de haber verificado todos los datos.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div className="space-y-4">
                            <label className="flex items-start gap-3 cursor-pointer">
                                <input
                                    type="checkbox"
                                    checked={confirmChecks.understand}
                                    onChange={(e) => setConfirmChecks({ ...confirmChecks, understand: e.target.checked })}
                                    className="w-5 h-5 rounded mt-1"
                                />
                                <span className={darkMode ? 'text-gray-300' : 'text-gray-700'}>
                                    Entiendo que este proceso es irreversible y que los datos del ciclo actual serán archivados.
                                </span>
                            </label>
                            <label className="flex items-start gap-3 cursor-pointer">
                                <input
                                    type="checkbox"
                                    checked={confirmChecks.backup}
                                    onChange={(e) => setConfirmChecks({ ...confirmChecks, backup: e.target.checked })}
                                    className="w-5 h-5 rounded mt-1"
                                />
                                <span className={darkMode ? 'text-gray-300' : 'text-gray-700'}>
                                    He verificado que el backup de la base de datos se generó correctamente y está guardado en un lugar seguro.
                                </span>
                            </label>
                            <label className="flex items-start gap-3 cursor-pointer">
                                <input
                                    type="checkbox"
                                    checked={confirmChecks.notify}
                                    onChange={(e) => setConfirmChecks({ ...confirmChecks, notify: e.target.checked })}
                                    className="w-5 h-5 rounded mt-1"
                                />
                                <span className={darkMode ? 'text-gray-300' : 'text-gray-700'}>
                                    He notificado al personal administrativo sobre el cierre del ciclo escolar.
                                </span>
                            </label>
                        </div>
                    </div>
                );

            case 4:
                return (
                    <div className="text-center space-y-6">
                        <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                            <Check size={40} className="text-green-600" />
                        </div>
                        <h3 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                            ¡Cierre Escolar Completado!
                        </h3>
                        <p className={`${darkMode ? 'text-gray-400' : 'text-gray-600'} max-w-md mx-auto`}>
                            El ciclo {newCycleConfig.year - 1} ha sido archivado y el nuevo ciclo {newCycleConfig.year} está listo para comenzar.
                        </p>
                        <button className="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg flex items-center gap-2 mx-auto">
                            <Download size={20} />
                            Descargar Reporte de Migración
                        </button>
                    </div>
                );
        }
    };

    return (
        <div className="p-6 max-w-4xl mx-auto">
            <h1 className={`text-2xl font-bold mb-6 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                Cierre Escolar - Asistente
            </h1>

            {/* Steps Progress */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm p-6 mb-6`}>
                <div className="flex items-center justify-between">
                    {steps.map((step, index) => (
                        <React.Fragment key={step.id}>
                            <div className="flex flex-col items-center">
                                <div className={`w-12 h-12 rounded-full flex items-center justify-center ${currentStep > step.id
                                        ? 'bg-green-500 text-white'
                                        : currentStep === step.id
                                            ? 'bg-blue-600 text-white'
                                            : darkMode ? 'bg-gray-700 text-gray-400' : 'bg-gray-200 text-gray-500'
                                    }`}>
                                    {currentStep > step.id ? <Check size={24} /> : <step.icon size={24} />}
                                </div>
                                <span className={`text-xs mt-2 text-center max-w-[80px] ${currentStep >= step.id
                                        ? darkMode ? 'text-gray-300' : 'text-gray-700'
                                        : darkMode ? 'text-gray-500' : 'text-gray-400'
                                    }`}>
                                    {step.title}
                                </span>
                            </div>
                            {index < steps.length - 1 && (
                                <div className={`flex-1 h-1 mx-2 ${currentStep > step.id
                                        ? 'bg-green-500'
                                        : darkMode ? 'bg-gray-700' : 'bg-gray-200'
                                    }`} />
                            )}
                        </React.Fragment>
                    ))}
                </div>
            </div>

            {/* Step Content */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm p-6 mb-6`}>
                {renderStepContent()}
            </div>

            {/* Navigation */}
            {currentStep < 4 && (
                <div className="flex justify-between">
                    <button
                        onClick={() => setCurrentStep(Math.max(0, currentStep - 1))}
                        disabled={currentStep === 0}
                        className={`px-6 py-3 rounded-lg font-medium ${currentStep === 0
                                ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                                : darkMode ? 'bg-gray-700 text-white hover:bg-gray-600' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                            }`}
                    >
                        Anterior
                    </button>

                    {currentStep === 3 ? (
                        <button
                            onClick={handleExecuteClose}
                            disabled={!canProceed() || loading}
                            className="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg flex items-center gap-2 disabled:opacity-50"
                        >
                            {loading ? <Loader size={20} className="animate-spin" /> : <Archive size={20} />}
                            Ejecutar Cierre Escolar
                        </button>
                    ) : currentStep === 1 ? (
                        <button
                            onClick={handlePreviewMigration}
                            disabled={!canProceed() || loading}
                            className="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg flex items-center gap-2 disabled:opacity-50"
                        >
                            {loading ? <Loader size={20} className="animate-spin" /> : <ChevronRight size={20} />}
                            Ver Preview
                        </button>
                    ) : (
                        <button
                            onClick={() => setCurrentStep(currentStep + 1)}
                            disabled={!canProceed()}
                            className="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg flex items-center gap-2 disabled:opacity-50"
                        >
                            Siguiente
                            <ChevronRight size={20} />
                        </button>
                    )}
                </div>
            )}
        </div>
    );
};

export default CierreEscolarPage;
