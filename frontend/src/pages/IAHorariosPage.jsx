import React, { useState, useEffect, useRef } from 'react';
import {
    Send, Bot, User, Sparkles, Brain, Zap, AlertCircle, Check,
    FileText, Download, RotateCcw, Clock, Shield, BarChart, Mic, MicOff
} from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { useTheme } from '../contexts/ThemeContext';
import { useAuth } from '../contexts/AuthContext';
import scheduleService from '../services/scheduleService';
import taskService from '../services/taskService'; // Integramos servicio de tareas
import teacherService from '../services/teacherService'; // NEW: Import teacherService

import aiService from '../services/AiService';
import studentService from '../services/studentService';
import EnterpriseDashboard from '../components/EnterpriseDashboard'; // Fixed duplicates
import RiskDashboard from '../components/RiskDashboard';

import { useLocation } from 'react-router-dom'; // Context awareness

const IAHorariosPage = () => {
    const { darkMode } = useTheme();
    const { user, getPrimaryRole } = useAuth();
    const activeRole = getPrimaryRole() || 'admin';

    const location = useLocation(); // Context awareness
    const [voiceEnabled, setVoiceEnabled] = useState(false); // Voice state
    const [showRiskDashboard, setShowRiskDashboard] = useState(false);
    const [showEnterpriseDashboard, setShowEnterpriseDashboard] = useState(false);
    const [teacherProfile, setTeacherProfile] = useState(null);
    const [teacherName, setTeacherName] = useState(user?.email?.split('@')[0] || 'Usuario'); // NEW: Name state
    const [coreState, setCoreState] = useState('idle'); // Fixed: Added missing state

    // Load teacher profile if applicable
    useEffect(() => {
        const loadProfile = async () => {
            if (activeRole === 'ROLE_TEACHER' || activeRole === 'ROLE_DOCENTE') {
                try {
                    const profile = await teacherService.getMyProfile();
                    if (profile && profile.firstName) {
                        setTeacherName(`${profile.firstName} ${profile.lastName}`);
                        setTeacherProfile(profile);
                    }
                } catch (e) {
                    console.error("Error loading profile", e);
                }
            }
        };
        loadProfile();
    }, [activeRole]);

    // Helper state for chat
    const [messages, setMessages] = useState([
        { id: 1, sender: 'ai', text: `Hola. Soy tu Asistente Personal Académico. ¿En qué puedo apoyarte hoy?`, timestamp: new Date() }
    ]);
    const [isTyping, setIsTyping] = useState(false);
    const [input, setInput] = useState('');
    const [isListening, setIsListening] = useState(false);
    const messagesEndRef = useRef(null);

    // Auto-scroll to bottom of chat
    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
    };

    useEffect(() => {
        scrollToBottom();
    }, [messages, isTyping]);

    // ... existing refs ...

    // Voice Synthesis Setup
    const speak = (text) => {
        if (!voiceEnabled || !window.speechSynthesis) return;
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'es-ES';
        utterance.rate = 1.1; // Slightly faster for natural feel
        // Try to pick a female voice if available (Google Español or similar)
        const voices = window.speechSynthesis.getVoices();
        const preferredVoice = voices.find(v => v.lang.includes('es') && v.name.includes('Google')) || voices.find(v => v.lang.includes('es'));
        if (preferredVoice) utterance.voice = preferredVoice;
        window.speechSynthesis.speak(utterance);
    };

    // ... existing useEffects

    // Stop speaking when unmounting
    useEffect(() => {
        return () => window.speechSynthesis.cancel();
    }, []);

    // Handlers
    const handleSend = async (textOverride = null) => {
        const text = textOverride || input;
        if (!text.trim()) return;

        // User Message
        const newUserMsg = {
            id: Date.now(),
            sender: 'user',
            text: text,
            timestamp: new Date()
        };

        setMessages(prev => [...prev, newUserMsg]);
        setInput('');
        setIsTyping(true);

        // Process Logic
        await processIntent(text);
    };

    const startListening = () => {
        if (!('webkitSpeechRecognition' in window)) {
            alert("Tu navegador no soporta reconocimiento de voz.");
            return;
        }
        const recognition = new window.webkitSpeechRecognition();
        recognition.lang = 'es-ES';
        recognition.onstart = () => setIsListening(true);
        recognition.onend = () => setIsListening(false);
        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            setInput(transcript);
            handleSend(transcript);
        };
        recognition.start();
    };


    // --- Smart AI Engine with Frontend Fallback ---
    const processIntent = async (text) => {
        const lowerText = text.toLowerCase().trim();
        setCoreState('processing');

        // Simulate slight delay for natural feel
        await new Promise(r => setTimeout(r, 500));

        // --- LOCAL INTENT MATCHING (No Backend Required) ---

        // 1. GREETINGS
        if (/^(hola|ola|buenos d[ií]as|buenas tardes|buenas noches|hey|hi|que tal|qué tal|saludos|que onda)/.test(lowerText)) {
            const greetings = [
                `¡Hola ${teacherName}! 👋 Soy tu asistente. ¿Qué necesitas hoy?`,
                `¡Buenos días, ${teacherName}! ¿En qué te apoyo?`,
                `¡Hola profe ${teacherName}! 🎓 Estoy listo para ayudarte.`
            ];
            setCoreState('idle');
            reply(greetings[Math.floor(Math.random() * greetings.length)], 'text');
            return;
        }

        // 2. HOW ARE YOU / ESTADO
        if (/^(cómo est[aá]s|como estas|como te encuentras)/.test(lowerText)) {
            setCoreState('idle');
            reply("¡Funcionando al 100%! 🤖 ¿En qué te ayudo?", 'text');
            return;
        }

        // 3. HELP / CAPABILITIES - ROLE SPECIFIC
        if (/^(ayuda|help|que puedes hacer|qué puedes hacer|comandos|menu|opciones)/.test(lowerText) ||
            lowerText.includes("puedes hacer") || lowerText.includes("sabes hacer")) {

            let helpText = '';

            // TEACHER/DOCENTE
            if (activeRole === 'ROLE_TEACHER' || activeRole === 'ROLE_DOCENTE') {
                helpText = `🎓 **Asistente para Docentes**

📚 **Consultas:**
• "Ver mis materias" - Lista tus asignaturas
• "Ver mis alumnos" - Lista de estudiantes
• "Ver mi horario" - Tu horario semanal

📝 **Notas:**
• "Cargar notas" - Ir a gestión de notas
• "Estudiantes en riesgo" - Ver alertas

💡 **Bienestar:**
• "Dame un consejo" - Tips pedagógicos
• "Estoy estresado" - Apoyo emocional`;
            }
            // ADMIN / SUPER_ADMIN - FULL ACCESS
            else if (activeRole === 'ROLE_ADMIN' || activeRole === 'ROLE_SUPER_ADMIN') {
                helpText = `⚙️ **Asistente de Administración (Acceso Completo)**

🗓️ **Horarios:**
• "Generar horarios" - Crear con IA
• "Optimizar horarios" - Redistribuir
• "Ver horarios" - Vista general

👥 **Gestión de Personal:**
• "Ver docentes" - Lista de profesores
• "Ver estudiantes" - Lista completa
• "Carga docente" - Análisis de burnout

📊 **Análisis y Reportes:**
• "Salud institucional" - Métricas ISA
• "Estudiantes en riesgo" - Dashboard de alertas
• "Métricas del colegio" - KPIs educativos

📚 **Académico:**
• "Ver materias" - Asignaturas
• "Ver notas" - Calificaciones
• "Cargar notas" - Gestión de notas

📝 **Comunicación:**
• "Contactar padres" - Comunicados
• "Reportar problema" - Soporte

💡 **Bienestar:**
• "Dame un consejo" - Tips
• "Motivación" - Mensajes de ánimo`;
            }
            // DIRECTOR / COORDINATION
            else if (activeRole === 'ROLE_DIRECTOR' || activeRole === 'ROLE_COORDINATION') {
                helpText = `👔 **Asistente de Dirección**

📊 **Análisis:**
• "Salud institucional" - Índice ISA
• "Estudiantes en riesgo" - Alertas
• "Carga docente" - Burnout

🗓️ **Horarios:**
• "Generar horarios" - Crear con IA
• "Ver horarios" - Vista general

📈 **Reportes:**
• "Métricas del colegio" - KPIs
• "Estado general" - Resumen`;
            }
            // SECRETARY / SECRETARIA
            else if (activeRole === 'ROLE_SECRETARY' || activeRole === 'ROLE_SECRETARIA') {
                helpText = `📋 **Asistente de Secretaría**

👥 **Consultas:**
• "Ver estudiantes" - Lista de alumnos
• "Ver docentes" - Lista de profesores

📝 **Gestión:**
• "Ver horarios" - Horarios generales
• "Contactar padres" - Comunicados

❓ **Ayuda:**
• "Dame un consejo" - Tips
• "Reportar problema" - Soporte`;
            }
            // STUDENT / ESTUDIANTE
            else if (activeRole === 'ROLE_STUDENT' || activeRole === 'ROLE_ESTUDIANTE') {
                helpText = `📖 **Asistente Estudiantil**

📊 **Académico:**
• "Mis notas" - Ver calificaciones
• "Mis tareas" - Tareas pendientes
• "Mi horario" - Tu horario semanal

💡 **Estudio:**
• "Tips de estudio" - Consejos
• "Dame motivación" - Ánimo

❓ **Ayuda:**
• "Estoy estresado" - Apoyo
• "Reportar problema" - Soporte`;
            }
            // DEFAULT
            else {
                helpText = `🤖 **Asistente Personal**

📚 **Consultas:**
• "Ver mi horario" - Horario semanal
• "Dame un consejo" - Tips útiles
• "Ayuda" - Ver opciones

Tu rol actual: ${activeRole || 'No identificado'}`;
            }

            helpText += `\n\n¿Qué necesitas, ${teacherName}?`;
            setCoreState('idle');
            reply(helpText, 'text');
            return;
        }

        // 4. VIEW SUBJECTS / MATERIAS - FETCH REAL DATA
        if (lowerText.includes("materia") || lowerText.includes("asignatura") || lowerText.includes("curso") || lowerText.includes("impart")) {
            try {
                reply("📚 Consultando tus materias...", 'text');
                const profile = await teacherService.getMyProfile();
                if (profile && profile.id) {
                    const assignments = await teacherService.getSubjects(profile.id);
                    if (assignments && assignments.length > 0) {
                        const subjectList = assignments.map(a =>
                            `• **${a.subject?.name || a.subjectName || 'Materia'}** - ${a.grade?.name || a.gradeName || ''} ${a.section?.name ? `(${a.section.name})` : ''}`
                        ).join('\n');
                        setCoreState('idle');
                        reply(`📚 **Tus Materias Asignadas:**\n\n${subjectList}\n\n_Total: ${assignments.length} asignaciones_`, 'text');
                    } else {
                        setCoreState('idle');
                        reply("No encontré materias asignadas. Contacta a coordinación.", 'text');
                    }
                } else {
                    setCoreState('idle');
                    reply("No pude obtener tu perfil. Intenta cerrar sesión y entrar de nuevo.", 'text');
                }
            } catch (e) {
                setCoreState('idle');
                reply("Error al consultar materias. Verifica tu conexión.", 'text');
            }
            return;
        }

        // 5. VIEW STUDENTS / ALUMNOS - FETCH REAL DATA
        if (lowerText.includes("alumno") || lowerText.includes("estudiante") || lowerText.includes("patojo")) {
            try {
                reply("👨‍🎓 Consultando tus estudiantes...", 'text');
                const profile = await teacherService.getMyProfile();
                if (profile && profile.id) {
                    const students = await teacherService.getStudents(profile.id);
                    if (students && students.length > 0) {
                        const studentCount = students.length;
                        const sampleList = students.slice(0, 5).map(s =>
                            `• ${s.firstName || ''} ${s.lastName || ''}`
                        ).join('\n');
                        setCoreState('idle');
                        reply(`👨‍🎓 **Tus Estudiantes:**\n\n${sampleList}${studentCount > 5 ? `\n... y ${studentCount - 5} más` : ''}\n\n_Total: ${studentCount} estudiantes_`, 'text');
                    } else {
                        setCoreState('idle');
                        reply("No encontré estudiantes asignados a tus grupos.", 'text');
                    }
                }
            } catch (e) {
                setCoreState('idle');
                reply("Error al consultar estudiantes.", 'text');
            }
            return;
        }

        // 6. VIEW SCHEDULE / HORARIO - SHOW SUBJECTS AS SCHEDULE
        if (lowerText.includes("horario") || lowerText.includes("schedule") || lowerText.includes("clases hoy")) {
            try {
                reply("🗓️ Consultando tu horario...", 'text');
                const profile = await teacherService.getMyProfile();
                if (profile && profile.id) {
                    const assignments = await teacherService.getSubjects(profile.id);
                    if (assignments && assignments.length > 0) {
                        const scheduleText = assignments.map(a =>
                            `📘 **${a.subject?.name || 'Materia'}** → ${a.grade?.name || ''} ${a.section?.name || ''}`
                        ).join('\n');
                        setCoreState('idle');
                        reply(`🗓️ **Tu Horario de Clases:**\n\n${scheduleText}\n\n💡 _Para ver horarios detallados por día, consulta el módulo de Horarios en el menú._`, 'text');
                    } else {
                        setCoreState('idle');
                        reply("No tienes clases asignadas actualmente.", 'text');
                    }
                }
            } catch (e) {
                setCoreState('idle');
                reply("No pude cargar tu horario. Intenta de nuevo.", 'text');
            }
            return;
        }

        // 7. GRADES / NOTAS - NAVIGATE
        if (lowerText.includes("nota") || lowerText.includes("calificacion") || lowerText.includes("cargar nota")) {
            setCoreState('idle');
            reply("📝 Para gestionar notas, ve a **Carga de Notas** en el menú lateral.\n\n¿Quieres que te muestre cómo llegar?", 'text');
            return;
        }

        // 8. MOTIVATION / SUPPORT
        if (lowerText.includes("motivación") || lowerText.includes("ánimo") || lowerText.includes("cansado") || lowerText.includes("estres") || lowerText.includes("abrumad")) {
            const motivations = [
                `🌟 ${teacherName}, recuerda: cada clase transforma una vida. ¡Eres increíble!`,
                `💪 El trabajo de un maestro es el más noble. Tus alumnos te admiran, ${teacherName}.`,
                `🧘 Tómate 5 minutos para respirar. Lo estás haciendo muy bien.`,
                `☀️ Mañana es una nueva oportunidad. Hoy celebra lo que lograste.`
            ];
            setCoreState('idle');
            reply(motivations[Math.floor(Math.random() * motivations.length)], 'text');
            return;
        }

        // 9. TIPS / CONSEJOS
        if (lowerText.includes("consejo") || lowerText.includes("tip") || lowerText.includes("sugerencia")) {
            const tips = [
                "💡 **Tip:** Usa preguntas abiertas para fomentar el pensamiento crítico.",
                "📱 **Tip Tech:** Proyecta un temporizador visual para mantener el ritmo.",
                "🎮 **Gamificación:** Pequeños retos con puntos extra aumentan la participación.",
                "📚 **Lectura:** 10 min de lectura silenciosa al inicio calma y enfoca."
            ];
            setCoreState('idle');
            reply(tips[Math.floor(Math.random() * tips.length)], 'text');
            return;
        }

        // 10. RISK ANALYSIS
        if (lowerText.includes("riesgo") || lowerText.includes("alerta") || lowerText.includes("reprobando")) {
            reply("🔍 Analizando estudiantes...", 'text');
            await new Promise(r => setTimeout(r, 1000));

            const mockStudents = [
                { id: 1, name: "Carlos López", grades: [{ subject: "Matemáticas", score: 45 }, { subject: "Ciencias", score: 50 }] },
                { id: 2, name: "Ana Martínez", grades: [{ subject: "Matemáticas", score: 55 }, { subject: "Historia", score: 48 }] }
            ];
            setCoreState('idle');
            reply({ type: 'risk_dashboard', data: mockStudents }, 'result_card');
            return;
        }

        // 11. THANKS
        if (lowerText.includes("gracia") || lowerText.includes("gracias")) {
            setCoreState('idle');
            reply("¡De nada! 🙏 Aquí estaré cuando me necesites.", 'text');
            return;
        }

        // 12. GOODBYE  
        if (lowerText.includes("adios") || lowerText.includes("chao") || lowerText.includes("bye")) {
            setCoreState('idle');
            reply(`¡Hasta pronto, ${teacherName}! 👋 ¡Éxito en tus clases!`, 'text');
            return;
        }

        // 13. FALLBACK - Try backend
        try {
            const aiResponse = await aiService.processCommand(text, activeRole);
            if (aiResponse && aiResponse.response_text && !aiResponse.response_text.includes("Error")) {
                setCoreState('idle');
                reply(aiResponse.response_text, 'text');
                return;
            }
        } catch (e) {
            console.warn("Backend AI unavailable", e);
        }

        // 14. FINAL FALLBACK
        setCoreState('idle');
        reply(`No entendí "${text}". 🤔\n\nPrueba:\n• "Ver mis materias"\n• "Ver mi horario"\n• "Dame un consejo"\n• "Ayuda"`, 'text');
    };


    const reply = (content, type = 'text') => {
        setMessages(prev => [
            ...prev,
            {
                id: Date.now(),
                sender: 'ai',
                text: type === 'text' ? content : null,
                data: type !== 'text' ? content : null,
                type: type,
                timestamp: new Date()
            }
        ]);
        setIsTyping(false);
    };

    const runGenerationSequence = async () => {
        setIsTyping(true);
        // 1. Analyzing
        await new Promise(r => setTimeout(r, 1000));
        setCoreState('processing');
        reply("Analizando restricciones y preferencias...", 'text');

        // 2. Generating
        await new Promise(r => setTimeout(r, 1500));

        try {
            // Fetch real data (mocked for now or use actual services)
            // In a real scenario we'd pull from context or ask user
            const config = { population_size: 50, generations: 30 };
            const teachers = []; // fetch from context
            const subjects = ["Math_1", "Science_1"]; // fetch from context

            const result = await aiService.generateSchedule(config, teachers, subjects);

            setCoreState('idle');

            if (result.success) {
                // Main Result Card
                reply({
                    generated: 15, // mock count or from result
                    conflicts: result.conflicts || 0,
                    errors: [],
                    schedule: result.schedule
                }, 'result_card');

                // Explainability Card (New!)
                if (result.explanation && result.explanation.length > 0) {
                    // Format explanation as bullet points
                    const explanationText = "🧠 **Por qué elegí este horario:**\n\n" +
                        result.explanation.map(e => `• ${e}`).join("\n");
                    reply(explanationText, 'text');
                    speak("He completado el horario. Te explico mis decisiones abajo.");
                } else {
                    speak("He generado el horario con éxito.");
                }

            } else {
                reply("Hubo un error al generar el horario.", 'error');
            }
        } catch (e) {
            console.error(e);
            setCoreState('error');
            reply("Error crítico en el núcleo de generación.", 'error');
        }
        setIsTyping(false);
    };

    return (
        <div className={`flex flex-col h-[calc(100vh-100px)] rounded-3xl overflow-hidden shadow-2xl transition-all duration-300 ${darkMode ? 'bg-[#0f111a] text-gray-100' : 'bg-white text-gray-900'}`}>

            {/* Header / Neural Core */}
            <div className={`relative p-6 border-b ${darkMode ? 'border-gray-800 bg-[#151923]' : 'border-gray-100 bg-gray-50/50'} flex items-center justify-between z-10`}>
                <div className="flex items-center gap-4">
                    <NeuralCore state={coreState} />
                    <div>
                        <h2 className="text-xl font-bold font-mono tracking-tight flex items-center gap-2">
                            ASISTENTE PERSONAL DE {teacherName.toUpperCase()} <span className="text-xs px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-400">BETA 2.0</span>
                        </h2>
                        {/* Status text */}
                        <div className="flex items-center gap-3 text-xs md:text-sm text-gray-400 font-medium">
                            <span className="flex items-center gap-1.5">
                                <span className={`w-2 h-2 rounded-full ${coreState === 'idle' ? 'bg-green-500 shadow-lg shadow-green-500/50' : coreState === 'processing' ? 'bg-indigo-500 animate-pulse' : 'bg-red-500'}`}></span>
                                {coreState === 'idle' ? 'Red Neuronal Activa' : coreState === 'processing' ? 'Procesando...' : 'Error de Conexión'}
                            </span>

                            {/* ISA Indicator - Interactive */}
                            <button
                                onClick={() => setShowEnterpriseDashboard(true)}
                                className="hidden md:flex items-center gap-1.5 border-l border-gray-700/50 pl-3 hover:bg-gray-800/50 rounded transition-colors cursor-pointer"
                            >
                                <Shield size={12} className="text-teal-400" />
                                <span className="text-teal-400">ISA: 95.0 (Excelente)</span>
                                <span className="text-[10px] text-gray-500 ml-1">v3.1</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div className="flex gap-2">
                    <IconButton
                        icon={voiceEnabled ? Mic : MicOff}
                        onClick={() => {
                            setVoiceEnabled(!voiceEnabled);
                            if (voiceEnabled) window.speechSynthesis.cancel();
                        }}
                        className={voiceEnabled ? "text-indigo-400" : "text-gray-400"}
                    />
                    <IconButton icon={RotateCcw} onClick={() => setMessages([{ id: 1, sender: 'ai', text: "Sistema reiniciado. ¿En qué puedo ayudarte?", timestamp: new Date() }])} />
                </div>
            </div>

            {/* ... Chat Area ... */}

            {/* Chat Area */}
            <div className={`flex-1 overflow-y-auto p-6 space-y-6 ${darkMode ? 'bg-gradient-to-b from-[#0f111a] to-[#0a0c10]' : 'bg-gray-50'}`}>
                <AnimatePresence>
                    {messages.map((msg) => (
                        <MessageBubble key={msg.id} message={msg} darkMode={darkMode} />
                    ))}
                    {isTyping && (
                        <motion.div
                            initial={{ opacity: 0, y: 10 }}
                            animate={{ opacity: 1, y: 0 }}
                            className="flex items-center gap-3 text-gray-500 text-sm ml-2"
                        >
                            <Bot size={18} className="animate-pulse text-indigo-500" />
                            <span className="flex gap-1">
                                <span className="animate-bounce delay-75">.</span>
                                <span className="animate-bounce delay-150">.</span>
                                <span className="animate-bounce delay-300">.</span>
                            </span>
                        </motion.div>
                    )}
                </AnimatePresence>
                <div ref={messagesEndRef} />
            </div>

            {/* Input Area */}
            <div className={`p-4 border-t ${darkMode ? 'border-gray-800 bg-[#151923]' : 'border-gray-100 bg-white'}`}>
                {messages.length < 3 && (
                    <div className="flex gap-2 overflow-x-auto pb-4 mb-2 no-scrollbar">
                        <SuggestionChip label="Generar horarios" onClick={() => handleSend("Generar una nueva versión de horarios")} darkMode={darkMode} />
                        <SuggestionChip label="🚨 Ver Riesgos" onClick={() => handleSend("Analizar riesgos académicos")} darkMode={darkMode} />
                        <SuggestionChip label="Verificar conflictos" onClick={() => handleSend("Verificar conflictos en el ciclo actual")} darkMode={darkMode} />
                    </div>
                )}

                <form
                    onSubmit={(e) => { e.preventDefault(); handleSend(); }}
                    className={`flex items-center gap-3 p-2 rounded-2xl border transition-all ${darkMode ? 'bg-[#0a0c10] border-gray-700 focus-within:border-indigo-500' : 'bg-gray-50 border-gray-200 focus-within:border-indigo-400'}`}
                >
                    <div className={`p-2 rounded-xl transition-all duration-300 ${isListening ? 'bg-red-500 animate-pulse' : (darkMode ? 'bg-gray-800' : 'bg-white')}`}>
                        {isListening ? (
                            <MicOff size={20} className="text-white" onClick={() => {/* Logic to stop if needed */ }} />
                        ) : (
                            <Sparkles size={20} className="text-indigo-500" />
                        )}
                    </div>
                    <input
                        type="text"
                        value={input}
                        onChange={(e) => setInput(e.target.value)}
                        placeholder={isListening ? "Escuchando..." : "Escribe o dicta una instrucción..."}
                        className="flex-1 bg-transparent outline-none p-2 text-sm md:text-base font-medium"
                    />
                    <button
                        type="button"
                        onClick={startListening}
                        className={`p-3 rounded-xl transition-all ${isListening ? 'bg-red-500 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500'}`}
                        title="Dictar por voz"
                    >
                        <Mic size={20} />
                    </button>
                    <button
                        type="submit"
                        disabled={!input.trim() || isTyping}
                        className="p-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-700 text-white rounded-xl transition-all hover:scale-105 active:scale-95 shadow-lg shadow-indigo-500/20"
                    >
                        <Send size={20} />
                    </button>
                </form>
            </div>

            {/* Overlays */}
            {showRiskDashboard && <RiskDashboard onClose={() => setShowRiskDashboard(false)} />}
            <EnterpriseDashboard visible={showEnterpriseDashboard} onClose={() => setShowEnterpriseDashboard(false)} />
        </div>
    );
};

// --- Components ---

const MessageBubble = ({ message, darkMode }) => {
    const isAi = message.sender === 'ai';

    return (
        <motion.div
            initial={{ opacity: 0, x: isAi ? -20 : 20, scale: 0.95 }}
            animate={{ opacity: 1, x: 0, scale: 1 }}
            className={`flex ${isAi ? 'justify-start' : 'justify-end'} group`}
        >
            <div className={`max-w-[85%] md:max-w-[70%] flex gap-4 ${isAi ? 'flex-row' : 'flex-row-reverse'}`}>
                {/* Avatar */}
                <div className={`w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 ${isAi ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-gray-400 text-white'}`}>
                    {isAi ? <Bot size={18} /> : <User size={18} />}
                </div>

                {/* Bubble */}
                <div className={`space-y-1`}>
                    <div className={`p-4 rounded-2xl shadow-sm text-sm md:text-base leading-relaxed whitespace-pre-line
                        ${isAi
                            ? (darkMode ? 'bg-[#1e2330] text-gray-200 border border-gray-700/50' : 'bg-white text-gray-800 border border-gray-100')
                            : 'bg-indigo-600 text-white rounded-tr-none'
                        }
                    `}>
                        {message.type === 'result_card' ? (
                            <ResultCard data={message.data} darkMode={darkMode} />
                        ) : message.type === 'quiz_card' ? (
                            <QuizCard data={message.data} darkMode={darkMode} />
                        ) : message.type === 'grade_card' ? (
                            <GradeCard data={message.data} darkMode={darkMode} />
                        ) : message.type === 'risk_dashboard' ? ( // NEW CASE
                            <div className="min-w-[320px] md:min-w-[500px]">
                                <RiskDashboard students={message.data} />
                            </div>
                        ) : (
                            message.text
                        )}
                    </div>
                    <span className="text-[10px] text-gray-500 block opacity-0 group-hover:opacity-100 transition-opacity">
                        {message.timestamp.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                    </span>
                </div>
            </div>
        </motion.div>
    );
};

const QuizCard = ({ data, darkMode }) => {
    const [answers, setAnswers] = useState({});
    const [score, setScore] = useState(null);

    const checkAnswers = () => {
        let correct = 0;
        data.questions.forEach(q => {
            if (answers[q.id] === q.answer) correct++;
        });
        setScore(correct);
    };

    if (score !== null) {
        return (
            <div className="space-y-3 min-w-[280px]">
                <h3 className="font-bold text-lg mb-2">Resultados 🏆</h3>
                <div className={`text-4xl font-bold text-center py-4 ${score === data.questions.length ? 'text-green-500' : 'text-orange-500'}`}>
                    {score}/{data.questions.length}
                </div>
                <p className="text-center text-sm">{score === data.questions.length ? "¡Perfecto! 🎉" : "Sigue practicando 💪"}</p>
            </div>
        );
    }

    return (
        <div className="space-y-4 min-w-[300px]">
            <h3 className="font-bold border-b pb-2 dark:border-gray-700">{data.title}</h3>
            {data.questions.map((q, idx) => (
                <div key={q.id} className="space-y-2">
                    <p className="font-medium text-sm">{idx + 1}. {q.question}</p>
                    <div className="grid grid-cols-2 gap-2">
                        {q.options.map(opt => (
                            <button
                                key={opt}
                                onClick={() => setAnswers(prev => ({ ...prev, [q.id]: opt }))}
                                className={`text-xs p-2 rounded border transition-colors
                                    ${answers[q.id] === opt
                                        ? 'bg-indigo-500 text-white border-indigo-500'
                                        : (darkMode ? 'hover:bg-gray-700 border-gray-700' : 'hover:bg-gray-100 border-gray-200')}
                                `}
                            >
                                {opt}
                            </button>
                        ))}
                    </div>
                </div>
            ))}
            <button
                onClick={checkAnswers}
                disabled={Object.keys(answers).length < data.questions.length}
                className="w-full py-2 bg-indigo-600 text-white rounded-lg font-bold text-sm disabled:opacity-50"
            >
                Evaluame
            </button>
        </div>
    );
};

const GradeCard = ({ data, darkMode }) => (
    <div className="min-w-[300px]">
        <h3 className="font-bold mb-3 flex items-center gap-2">
            <BarChart size={18} /> Reporte de Notas
        </h3>
        <div className="space-y-2">
            {data.map((g, idx) => (
                <div key={idx} className={`flex justify-between items-center p-2 rounded-lg ${darkMode ? 'bg-gray-800' : 'bg-gray-50'}`}>
                    <span className="text-sm font-medium">{g.subject || g.materia}</span>
                    <span className={`font-bold ${g.grade >= 60 ? 'text-green-500' : 'text-red-500'}`}>
                        {g.grade || g.nota} pts
                    </span>
                </div>
            ))}
        </div>
        <div className="mt-3 text-xs text-gray-500 text-center">
            Promedio General: {Math.round(data.reduce((acc, curr) => acc + (curr.grade || curr.nota), 0) / data.length)} pts
        </div>
    </div>
);

const ResultCard = ({ data, darkMode }) => {
    return (
        <div className="space-y-4 min-w-[300px]">
            <div className="flex items-center gap-2 border-b border-gray-700/30 pb-2 mb-2">
                <Check size={18} className="text-green-500" />
                <span className="font-bold">Generación Exitosa</span>
            </div>

            <div className="grid grid-cols-2 gap-3">
                <StatBox label="Horarios" value={data.generated} darkMode={darkMode} />
                <StatBox label="Conflictos" value={data.conflicts} isWarning={data.conflicts > 0} darkMode={darkMode} />
                <StatBox label="Errores" value={data.errors.length} isError={data.errors.length > 0} darkMode={darkMode} />
                <StatBox label="Eficiencia" value="99%" darkMode={darkMode} />
            </div>

            <div className={`flex gap-2 pt-2`}>
                <button className="flex-1 flex items-center justify-center gap-2 py-2 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 rounded-lg text-xs font-bold transition-colors">
                    <Download size={14} /> Descargar PDF
                </button>
                <button className="flex-1 flex items-center justify-center gap-2 py-2 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 rounded-lg text-xs font-bold transition-colors">
                    <BarChart size={14} /> Ver Gráficas
                </button>
            </div>
        </div>
    );
};

const StatBox = ({ label, value, isWarning, isError, darkMode }) => (
    <div className={`p-3 rounded-xl border ${darkMode ? 'bg-gray-800/50 border-gray-700' : 'bg-gray-50 border-gray-200'}`}>
        <div className={`text-xl font-bold ${isError ? 'text-red-500' : isWarning ? 'text-orange-500' : (darkMode ? 'text-white' : 'text-gray-900')}`}>
            {value}
        </div>
        <div className={`text-[10px] uppercase tracking-wider ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
            {label}
        </div>
    </div>
);

const NeuralCore = ({ state }) => {
    return (
        <div className="relative w-10 h-10 flex items-center justify-center">
            <motion.div
                animate={state === 'processing' ? { rotate: 360, scale: [1, 1.2, 1] } : { rotate: 0 }}
                transition={state === 'processing' ? { repeat: Infinity, duration: 2 } : {}}
                className={`absolute inset-0 rounded-full border-2 border-dashed ${state === 'error' ? 'border-red-500' : 'border-indigo-500'} opacity-50`}
            />
            <div className={`relative z-10 w-8 h-8 rounded-full flex items-center justify-center ${state === 'processing' ? 'bg-indigo-600 animate-pulse' : state === 'error' ? 'bg-red-500' : 'bg-indigo-900'}`}>
                <Brain size={16} className="text-white" />
            </div>
        </div>
    );
};

const SuggestionChip = ({ label, onClick, darkMode }) => (
    <button
        onClick={onClick}
        className={`px-4 py-2 rounded-full text-xs font-medium whitespace-nowrap transition-all border
            ${darkMode
                ? 'bg-[#1e2330] border-gray-700 text-gray-300 hover:bg-gray-800 hover:border-indigo-500'
                : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-indigo-300 shadow-sm'
            }`}
    >
        {label}
    </button>
);

const IconButton = ({ icon: Icon, onClick }) => (
    <button onClick={onClick} className="p-2 rounded-lg hover:bg-white/10 text-gray-400 hover:text-white transition-colors">
        <Icon size={18} />
    </button>
);

export default IAHorariosPage;
