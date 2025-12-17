import { useState, useCallback, useRef, useEffect } from 'react';
import aiService from '../services/aiService';

/**
 * Custom hook for AI-powered schedule assistant
 * Manages chat history, processing state, and AI interactions
 */
export const useAIAssistant = (onScheduleGenerated = null) => {
    const [messages, setMessages] = useState([
        {
            id: 'welcome',
            type: 'ai',
            content: '¡Hola! Soy tu asistente de horarios. Puedo ayudarte a:\n\n' +
                '📅 Generar horarios automáticamente\n' +
                '⚙️ Configurar restricciones\n' +
                '✅ Validar conflictos\n\n' +
                '¿Qué te gustaría hacer?',
            timestamp: new Date(),
        },
    ]);
    const [isProcessing, setIsProcessing] = useState(false);
    const [config, setConfig] = useState({
        startTime: '07:30',
        endTime: '14:00',
        classDuration: 45,
        includeRecess: true,
        recessAfterPeriod: 4,
        recessDuration: 30,
        teacherRestrictions: {},
        subjectPreferences: {},
    });
    const [aiStatus, setAiStatus] = useState('unknown'); // 'online', 'offline', 'unknown'
    const messageIdCounter = useRef(1);

    // Check AI service health on mount
    useEffect(() => {
        const checkHealth = async () => {
            const isHealthy = await aiService.healthCheck();
            setAiStatus(isHealthy ? 'online' : 'offline');
        };
        checkHealth();

        // Periodic health check
        const interval = setInterval(checkHealth, 60000);
        return () => clearInterval(interval);
    }, []);

    /**
     * Generate unique message ID
     */
    const generateId = useCallback(() => {
        return `msg-${Date.now()}-${messageIdCounter.current++}`;
    }, []);

    /**
     * Add a message to the chat
     */
    const addMessage = useCallback((type, content, extras = {}) => {
        const message = {
            id: generateId(),
            type,
            content,
            timestamp: new Date(),
            ...extras,
        };
        setMessages((prev) => [...prev, message]);
        return message;
    }, [generateId]);

    /**
     * Send a message and get AI response
     */
    const sendMessage = useCallback(async (text) => {
        if (!text.trim() || isProcessing) return;

        // Add user message
        addMessage('user', text);
        setIsProcessing(true);

        try {
            // Process through AI
            const response = await aiService.processCommand(text, config);

            // Handle different intents
            await handleIntent(response, text);

        } catch (error) {
            console.error('AI processing error:', error);
            addMessage('ai', 'Lo siento, hubo un error al procesar tu mensaje. Por favor, intenta de nuevo.', {
                isError: true,
            });
        } finally {
            setIsProcessing(false);
        }
    }, [config, isProcessing, addMessage]);

    /**
     * Handle AI intent response
     */
    const handleIntent = useCallback(async (response, originalText) => {
        const { intent, confidence, response_text, entities, should_generate, config_changes } = response;

        // Apply config changes if any
        if (config_changes) {
            setConfig((prev) => ({ ...prev, ...config_changes }));
            addMessage('ai', `✅ Configuración actualizada: ${response_text}`, {
                configUpdated: true,
            });
            return;
        }

        // Handle schedule generation
        if (should_generate || intent === 'generate_schedule') {
            addMessage('ai', '⏳ Generando horario óptimo...', { isLoading: true });

            try {
                const result = await generateScheduleWithAI(entities);

                if (result.success) {
                    // Remove loading message
                    setMessages((prev) => prev.filter((m) => !m.isLoading));

                    addMessage('ai', `✅ ¡Horario generado exitosamente!\n\n${getScheduleSummary(result)}`, {
                        hasSchedule: true,
                        schedule: result.schedule,
                        conflicts: result.conflicts,
                    });

                    if (onScheduleGenerated) {
                        onScheduleGenerated(result.schedule, result.conflicts);
                    }
                } else {
                    setMessages((prev) => prev.filter((m) => !m.isLoading));
                    addMessage('ai', `❌ ${result.error || 'No se pudo generar el horario.'}`, {
                        isError: true,
                    });
                }
            } catch (error) {
                setMessages((prev) => prev.filter((m) => !m.isLoading));
                addMessage('ai', '❌ Error al generar el horario. Por favor, verifica la configuración.', {
                    isError: true,
                });
            }
            return;
        }

        // Handle help
        if (intent === 'help') {
            addMessage('ai', aiService.getHelpText(), { isHelp: true });
            return;
        }

        // Handle greeting
        if (intent === 'greeting') {
            addMessage('ai', response_text || '¡Hola! ¿En qué puedo ayudarte con los horarios?');
            return;
        }

        // Handle show config/status
        if (intent === 'show_status') {
            addMessage('ai', getConfigSummary(), { isConfig: true });
            return;
        }

        // Handle clear
        if (intent === 'clear') {
            setConfig({
                startTime: '07:30',
                endTime: '14:00',
                classDuration: 45,
                includeRecess: true,
                recessAfterPeriod: 4,
                recessDuration: 30,
                teacherRestrictions: {},
                subjectPreferences: {},
            });
            addMessage('ai', '🗑️ Configuración restablecida a valores predeterminados.');
            return;
        }

        // Default response
        addMessage('ai', response_text || 'Entendido. ¿Hay algo más en lo que pueda ayudarte?', {
            confidence,
        });
    }, [config, addMessage, onScheduleGenerated]);

    /**
     * Generate schedule using AI service
     */
    const generateScheduleWithAI = useCallback(async (entities = {}) => {
        // Extract grade and section from entities if present
        const gradeId = entities.grade?.value || entities.gradeId || 1;
        const sectionId = entities.section?.value || entities.sectionId || null;

        // Mock teachers and subjects for demo (should come from API in real app)
        const teachers = [
            { id: 1, name: 'Prof. García', subjects: [1, 4] },
            { id: 2, name: 'Prof. López', subjects: [2] },
            { id: 3, name: 'Prof. Martínez', subjects: [3, 5] },
            { id: 4, name: 'Prof. Rodríguez', subjects: [6, 7] },
        ];

        const subjects = [
            { id: 1, name: 'Matemáticas', hoursPerWeek: 5 },
            { id: 2, name: 'Español', hoursPerWeek: 5 },
            { id: 3, name: 'Ciencias', hoursPerWeek: 3 },
            { id: 4, name: 'Inglés', hoursPerWeek: 4 },
            { id: 5, name: 'Historia', hoursPerWeek: 2 },
            { id: 6, name: 'Ed. Física', hoursPerWeek: 2 },
            { id: 7, name: 'Arte', hoursPerWeek: 2 },
        ];

        const constraints = {
            teacher_unavailable: config.teacherRestrictions,
            ...entities.constraints,
        };

        return await aiService.generateSchedule(
            { ...config, gradeId, sectionId },
            teachers,
            subjects,
            constraints
        );
    }, [config]);

    /**
     * Get summary of generated schedule
     */
    const getScheduleSummary = (result) => {
        const { schedule, conflicts, attempts } = result;
        const classCount = schedule?.length || 0;
        const warningCount = conflicts?.filter((c) => c.severity === 'warning').length || 0;
        const errorCount = conflicts?.filter((c) => c.severity === 'error').length || 0;

        let summary = `📊 **Resumen:**\n`;
        summary += `- ${classCount} clases programadas\n`;
        summary += `- ${attempts || 1} intentos de optimización\n`;

        if (errorCount > 0) {
            summary += `- ⚠️ ${errorCount} conflictos encontrados\n`;
        }
        if (warningCount > 0) {
            summary += `- ℹ️ ${warningCount} advertencias\n`;
        }
        if (errorCount === 0 && warningCount === 0) {
            summary += `- ✅ Sin conflictos\n`;
        }

        return summary;
    };

    /**
     * Get current config summary
     */
    const getConfigSummary = useCallback(() => {
        return `⚙️ **Configuración Actual:**

📅 **Horario:** ${config.startTime} - ${config.endTime}
⏱️ **Duración de clases:** ${config.classDuration} minutos
☕ **Receso:** ${config.includeRecess ? `Sí, después del período ${config.recessAfterPeriod} (${config.recessDuration} min)` : 'No'}

**Restricciones:** ${Object.keys(config.teacherRestrictions).length > 0 ?
                Object.entries(config.teacherRestrictions).map(([t, r]) => `\n- Profesor ${t}: ${JSON.stringify(r)}`).join('') :
                'Ninguna'}`;
    }, [config]);

    /**
     * Quick command buttons
     */
    const quickCommands = [
        { label: 'Generar Horario', command: 'Genera horario para 1ro primaria' },
        { label: 'Ver Configuración', command: 'Mostrar configuración' },
        { label: 'Ayuda', command: 'Ayuda' },
        { label: 'Limpiar', command: 'Limpiar configuración' },
    ];

    /**
     * Clear chat history (keep welcome message)
     */
    const clearChat = useCallback(() => {
        setMessages((prev) => prev.slice(0, 1));
    }, []);

    return {
        messages,
        isProcessing,
        config,
        setConfig,
        aiStatus,
        sendMessage,
        addMessage,
        quickCommands,
        clearChat,
        getConfigSummary,
    };
};

export default useAIAssistant;
