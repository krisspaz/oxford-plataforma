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

import aiService from '../services/AiService';
import studentService from '../services/studentService';
import RiskDashboard from '../components/RiskDashboard';

// ... imports
import { useLocation } from 'react-router-dom'; // Added useLocation

// ...

const IAHorariosPage = () => {
    const { darkMode } = useTheme();
    const location = useLocation(); // Context awareness
    const [voiceEnabled, setVoiceEnabled] = useState(false); // Voice state

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


    // --- Advanced AI Engine (Python Connected) ---
    const processIntent = async (text) => {
        try {
            // 1. Send to Python Brain
            reply(null, 'process_step');

            // Specialized Handling for Risk
            if (text.toLowerCase().includes("riesgo") || text.toLowerCase().includes("risk")) {
                reply("Analizando base de datos de estudiantes...", 'text');

                // MOCK FETCH STUDENTS for Demo
                const mockStudents = [
                    { id: 1, name: "Juan Pérez", grades: [{ subject: "Math", score: 55 }, { subject: "Science", score: 60 }] },
                    { id: 2, name: "Maria Garcia", grades: [{ subject: "Math", score: 90 }, { subject: "Science", score: 95 }] },
                    { id: 3, name: "Carlos López", grades: [{ subject: "Math", score: 40 }, { subject: "Science", score: 45 }, { subject: "History", score: 50 }] }
                ];

                reply({ type: 'risk_dashboard', data: mockStudents }, 'result_card');
                speak("He detectado algunos estudiantes en riesgo.");
                return;
            }

            // ... (rest of logic) ...

            // Add context to the request (simulated here as we send 'text' mostly)
            const contextEnhancedText = `[Context: ${location.pathname}] ${text}`;

            const aiResponse = await aiService.processCommand(text); // Using original text for now, but context is ready

            // ... (rest of logic) ...

            // 3. Default Text Response
            const responseText = aiResponse.response_text || "No estoy segura de qué decir.";
            reply(responseText, 'text');
            speak(responseText); // Speak the response

        } catch (error) {
            console.error("AI Brain Error:", error);
            const errorMsg = "He perdido conexión con mi núcleo neuronal (Python API). 🔌 Intenta de nuevo.";
            reply(errorMsg, 'error');
            speak("Error de conexión.");
        }
    };

    // ... runGenerationSequence (no changes needed)

    // ... reply function (no changes needed)

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
                        {/* Status text */}
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
// ... rest of code

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
