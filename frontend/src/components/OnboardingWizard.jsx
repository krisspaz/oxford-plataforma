import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

/**
 * FASE 16: Onboarding Wizard
 * Zero-training UX - Guía al usuario nuevo por rol
 */
const OnboardingWizard = ({ onComplete }) => {
    const { user } = useAuth();
    const navigate = useNavigate();
    const [currentStep, setCurrentStep] = useState(0);
    const [completed, setCompleted] = useState(false);

    // Determinar rol principal
    const getRole = () => {
        const roles = user?.roles || [];
        if (roles.includes('ROLE_SUPER_ADMIN') || roles.includes('ROLE_ADMIN')) return 'admin';
        if (roles.includes('ROLE_DIRECTOR')) return 'director';
        if (roles.includes('ROLE_COORDINATION')) return 'coordination';
        if (roles.includes('ROLE_TEACHER') || roles.includes('ROLE_DOCENTE')) return 'teacher';
        if (roles.includes('ROLE_SECRETARY')) return 'secretary';
        if (roles.includes('ROLE_STUDENT')) return 'student';
        return 'default';
    };

    const role = getRole();

    // Pasos por rol
    const stepsByRole = {
        admin: [
            { title: '👋 Bienvenido Administrador', description: 'Tienes acceso total al sistema. Te guiaremos por las funciones principales.', icon: '🎯' },
            { title: '📊 Panel de Control', description: 'Desde aquí puedes ver métricas, alertas y el estado general del sistema.', action: '/dashboard', icon: '📈' },
            { title: '👥 Gestión de Usuarios', description: 'Administra docentes, estudiantes y personal. Asigna roles y permisos.', action: '/users', icon: '👤' },
            { title: '📅 Horarios', description: 'Genera y gestiona horarios con ayuda de la IA. Detecta conflictos automáticamente.', action: '/horarios', icon: '🗓️' },
            { title: '🤖 Asistente Personal', description: 'Tu IA está lista para ayudarte. Pregunta cualquier cosa sobre el sistema.', action: '/ia-horarios', icon: '💡' },
        ],
        director: [
            { title: '👋 Bienvenido Director', description: 'Tienes acceso a reportes, métricas y supervisión del sistema.', icon: '🎯' },
            { title: '📊 Reportes Ejecutivos', description: 'Visualiza el desempeño académico, docente y financiero.', action: '/reports', icon: '📈' },
            { title: '⚠️ Alertas y Riesgos', description: 'Recibe alertas de deserción, burnout docente y conflictos.', action: '/alerts', icon: '🔔' },
            { title: '🤖 Asistente IA', description: 'Consulta métricas y toma decisiones informadas con ayuda de la IA.', action: '/ia-horarios', icon: '💡' },
        ],
        teacher: [
            { title: '👋 Bienvenido Docente', description: 'Te guiaremos por las herramientas disponibles para ti.', icon: '🎯' },
            { title: '📅 Mi Horario', description: 'Consulta tu horario semanal y las materias asignadas.', action: '/mi-horario', icon: '🗓️' },
            { title: '📝 Carga de Notas', description: 'Ingresa las calificaciones de tus estudiantes de forma rápida.', action: '/carga-notas', icon: '✏️' },
            { title: '👨‍🎓 Mis Grupos', description: 'Ve la lista de estudiantes en cada uno de tus grupos.', action: '/mis-grupos', icon: '👥' },
            { title: '🤖 Asistente Personal', description: 'Pregunta sobre tus clases, horarios o cualquier duda.', action: '/ia-horarios', icon: '💡' },
        ],
        student: [
            { title: '👋 Bienvenido Estudiante', description: 'Aquí encontrarás todo lo que necesitas para tu éxito académico.', icon: '🎯' },
            { title: '📅 Mi Horario', description: 'Consulta tu horario de clases semanal.', action: '/mi-horario', icon: '🗓️' },
            { title: '📊 Mis Notas', description: 'Revisa tus calificaciones y promedio.', action: '/mis-notas', icon: '📈' },
            { title: '🤖 Asistente', description: 'Pregunta sobre tus clases, tareas o cualquier duda.', action: '/ia-horarios', icon: '💡' },
        ],
        secretary: [
            { title: '👋 Bienvenida Secretaría', description: 'Te guiaremos por las funciones administrativas.', icon: '🎯' },
            { title: '💰 Pagos', description: 'Gestiona cuotas, pagos y estados de cuenta.', action: '/pagos', icon: '💳' },
            { title: '📋 Inscripciones', description: 'Administra inscripciones y documentación.', action: '/inscripciones', icon: '📝' },
            { title: '🤖 Asistente', description: 'Consulta cualquier duda sobre procesos.', action: '/ia-horarios', icon: '💡' },
        ],
        default: [
            { title: '👋 Bienvenido', description: 'Te guiaremos por el sistema.', icon: '🎯' },
            { title: '🤖 Asistente IA', description: 'Tu asistente está listo para ayudarte.', action: '/ia-horarios', icon: '💡' },
        ],
    };

    const steps = stepsByRole[role] || stepsByRole.default;

    const handleNext = () => {
        if (currentStep < steps.length - 1) {
            setCurrentStep(currentStep + 1);
        } else {
            handleComplete();
        }
    };

    const handlePrev = () => {
        if (currentStep > 0) {
            setCurrentStep(currentStep - 1);
        }
    };

    const handleComplete = () => {
        localStorage.setItem(`onboarding_completed_${user?.id}`, 'true');
        setCompleted(true);
        if (onComplete) onComplete();
    };

    const handleSkip = () => {
        handleComplete();
    };

    const handleAction = (action) => {
        if (action) {
            handleComplete();
            navigate(action);
        }
    };

    // Check if already completed
    useEffect(() => {
        const isCompleted = localStorage.getItem(`onboarding_completed_${user?.id}`);
        if (isCompleted) {
            // eslint-disable-next-line react-hooks/set-state-in-effect
            setCompleted(true);
        }
    }, [user?.id]);

    if (completed) return null;

    const step = steps[currentStep];
    const progress = ((currentStep + 1) / steps.length) * 100;

    return (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
                {/* Progress bar */}
                <div className="h-1 bg-gray-200 dark:bg-gray-700">
                    <div
                        className="h-full bg-gradient-to-r from-blue-500 to-purple-600 transition-all duration-500"
                        style={{ width: `${progress}%` }}
                    />
                </div>

                {/* Content */}
                <div className="p-8">
                    {/* Icon */}
                    <div className="text-6xl mb-6 text-center animate-bounce">
                        {step.icon}
                    </div>

                    {/* Title */}
                    <h2 className="text-2xl font-bold text-gray-900 dark:text-white text-center mb-4">
                        {step.title}
                    </h2>

                    {/* Description */}
                    <p className="text-gray-600 dark:text-gray-300 text-center mb-8 text-lg">
                        {step.description}
                    </p>

                    {/* Step indicator */}
                    <div className="flex justify-center gap-2 mb-8">
                        {steps.map((_, idx) => (
                            <div
                                key={idx}
                                className={`w-2 h-2 rounded-full transition-all ${idx === currentStep
                                        ? 'w-8 bg-blue-600'
                                        : idx < currentStep
                                            ? 'bg-blue-400'
                                            : 'bg-gray-300'
                                    }`}
                            />
                        ))}
                    </div>

                    {/* Actions */}
                    <div className="flex gap-3">
                        {currentStep > 0 && (
                            <button
                                onClick={handlePrev}
                                className="flex-1 px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            >
                                ← Anterior
                            </button>
                        )}

                        {step.action && currentStep === steps.length - 1 ? (
                            <button
                                onClick={() => handleAction(step.action)}
                                className="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:opacity-90 transition-opacity font-medium"
                            >
                                🚀 Comenzar
                            </button>
                        ) : (
                            <button
                                onClick={handleNext}
                                className="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:opacity-90 transition-opacity font-medium"
                            >
                                {currentStep === steps.length - 1 ? '✅ Finalizar' : 'Siguiente →'}
                            </button>
                        )}
                    </div>

                    {/* Skip */}
                    <button
                        onClick={handleSkip}
                        className="w-full mt-4 text-gray-500 text-sm hover:text-gray-700 dark:hover:text-gray-300"
                    >
                        Saltar tutorial
                    </button>
                </div>
            </div>
        </div>
    );
};

export default OnboardingWizard;
