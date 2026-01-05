import React, { useState, useEffect, useRef } from 'react';
import {
    Send, Bot, User, Sparkles, Brain, Zap, AlertCircle, Check,
    FileText, Download, RotateCcw, Clock, Shield, BarChart
} from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { useTheme } from '../contexts/ThemeContext';
import scheduleService from '../services/scheduleService';

const IAHorariosPage = () => {
    const { darkMode } = useTheme();
    const [messages, setMessages] = useState([
        {
            id: 1,
            sender: 'ai',
            text: "Hola. Soy Oxford AI, tu asistente de planificación académica. Estoy conectado al núcleo del sistema para generar y optimizar horarios.\n\n¿En qué puedo ayudarte hoy?",
            timestamp: new Date()
        }
    ]);
    const [input, setInput] = useState('');
    const [isTyping, setIsTyping] = useState(false);
    const [coreState, setCoreState] = useState('idle'); // idle, listening, processing, success, error
    const messagesEndRef = useRef(null);

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
    };

    useEffect(() => {
        scrollToBottom();
    }, [messages, isTyping]);

    const handleSend = async (text = input) => {
        if (!text.trim()) return;

        // Add User Message
        const userMsg = { id: Date.now(), sender: 'user', text, timestamp: new Date() };
        setMessages(prev => [...prev, userMsg]);
        setInput('');
        setCoreState('processing');
        setIsTyping(true);

        // Process Intent (Simulated NLU)
        // In a real expanded app, this would be a separate service.
        setTimeout(async () => {
            await processIntent(text);
        }, 1500); // Fake "thinking" delay for realism
    };

    const processIntent = async (text) => {
        const lowerText = text.toLowerCase();

        // Intent: GENERATE
        if (lowerText.includes('generar') || lowerText.includes('crear') || lowerText.includes('iniciar')) {
            await runGenerationSequence();
        }
        // Intent: STATUS
        else if (lowerText.includes('estado') || lowerText.includes('cómo vas')) {
            reply("El sistema está operativo. Los últimos horarios fueron generados hace 2 horas.", 'info');
        }
        // Intent: HELP
        else if (lowerText.includes('ayuda') || lowerText.includes('que puedes hacer')) {
            reply("Puedo realizar las siguientes tareas:\n1. Generar horarios desde cero.\n2. Optimizar bloques existentes.\n3. Detectar conflictos de aula o docente.\n\nSolo pídelo.");
        }
        // Fallback
        else {
            reply("Entendido. Aunque para esa solicitud específica necesito más contexto, ¿te gustaría que proceda a **generar los horarios** del ciclo actual?", 'question');
        }
    };

    const runGenerationSequence = async () => {
        // Step 1: Analysis
        setCoreState('processing');
        reply("Iniciando secuencia de generación...", 'process');

        await new Promise(r => setTimeout(r, 1000));
        reply("Analizando disponibilidad de docentes y aulas...", 'process_step');

        await new Promise(r => setTimeout(r, 1000));
        reply("Calculando permutaciones óptimas para minimizar huecos...", 'process_step');

        try {
            // Real API Call
            const cycleId = 1; // Default
            const response = await scheduleService.generateAuto(cycleId);

            if (response.success) {
                setCoreState('success');
                // Rich Response with Stats
                reply(null, 'result_card', response.details);
            } else {
                setCoreState('error');
                reply(`Error en la generación: ${response.error || 'Fallo desconocido'}`, 'error');
            }

        } catch (error) {
            console.error(error);
            setCoreState('error');
            reply("Hubo un error crítico al conectar con el servidor.", 'error');
        } finally {
            setIsTyping(false);
            if (coreState !== 'error') setCoreState('idle');
        }
    };

    const reply = (text, type = 'text', data = null) => {
        setMessages(prev => [...prev, {
            id: Date.now(),
            sender: 'ai',
            text,
            type,
            data,
            timestamp: new Date()
        }]);
        if (type !== 'process' && type !== 'process_step') setIsTyping(false);
    };

    return (
        <div className={`flex flex-col h-[calc(100vh-100px)] rounded-3xl overflow-hidden shadow-2xl transition-all duration-300 ${darkMode ? 'bg-[#0f111a] text-gray-100' : 'bg-white text-gray-900'}`}>

            {/* Header / Neural Core */}
            <div className={`relative p-6 border-b ${darkMode ? 'border-gray-800 bg-[#151923]' : 'border-gray-100 bg-gray-50/50'} flex items-center justify-between z-10`}>
                <div className="flex items-center gap-4">
                    <NeuralCore state={coreState} />
                    <div>
                        <h2 className="text-xl font-bold font-mono tracking-tight flex items-center gap-2">
                            OXFORD AI <span className="text-xs px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-400">BETA 2.0</span>
                        </h2>
                        <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                            {coreState === 'idle' && 'En espera de comandos'}
                            {coreState === 'processing' && 'Procesando lógica...'}
                            {coreState === 'success' && 'Tarea completada'}
                            {coreState === 'error' && 'Error de sistema'}
                        </p>
                    </div>
                </div>
                <div className="flex gap-2">
                    <IconButton icon={RotateCcw} onClick={() => setMessages([{ id: 1, sender: 'ai', text: "Sistema reiniciado. ¿En qué puedo ayudarte?", timestamp: new Date() }])} />
                </div>
            </div>

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
                        <SuggestionChip label="Verificar conflictos" onClick={() => handleSend("Verificar conflictos en el ciclo actual")} darkMode={darkMode} />
                        <SuggestionChip label="Reiniciar todo" onClick={() => handleSend("Reiniciar base de datos de horarios")} darkMode={darkMode} />
                    </div>
                )}

                <form
                    onSubmit={(e) => { e.preventDefault(); handleSend(); }}
                    className={`flex items-center gap-3 p-2 rounded-2xl border transition-all ${darkMode ? 'bg-[#0a0c10] border-gray-700 focus-within:border-indigo-500' : 'bg-gray-50 border-gray-200 focus-within:border-indigo-400'}`}
                >
                    <div className={`p-2 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-white'}`}>
                        <Sparkles size={20} className="text-indigo-500" />
                    </div>
                    <input
                        type="text"
                        value={input}
                        onChange={(e) => setInput(e.target.value)}
                        placeholder="Escribe una instrucción para la IA..."
                        className="flex-1 bg-transparent outline-none p-2 text-sm md:text-base font-medium"
                    />
                    <button
                        type="submit"
                        disabled={!input.trim() || isTyping}
                        className="p-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-700 text-white rounded-xl transition-all hover:scale-105 active:scale-95 shadow-lg shadow-indigo-500/20"
                    >
                        <Send size={20} />
                    </button>
                </form>
            </div>

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
