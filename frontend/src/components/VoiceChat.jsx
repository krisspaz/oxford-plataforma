import { useState, useEffect, useRef } from 'react';
import { useNavigate } from 'react-router-dom';
import aiApi from '../services/aiApi';
import { useAuth } from '../contexts/AuthContext';

const VoiceChat = () => {
    // ========== ALL HOOKS MUST BE AT THE TOP ==========
    const [isOpen, setIsOpen] = useState(false);
    const [isListening, setIsListening] = useState(false);
    const [isTyping, setIsTyping] = useState(false);
    const [messages, setMessages] = useState([]);
    const [inputText, setInputText] = useState('');
    const [quickSuggestions, setQuickSuggestions] = useState([]);
    const [contextSuggestions, setContextSuggestions] = useState([]);
    const messagesEndRef = useRef(null);
    const recognitionRef = useRef(null);
    const navigate = useNavigate();
    const { user } = useAuth();

    // Speech Recognition Setup
    useEffect(() => {
        if (!user) return; // Guard inside effect
        if ('webkitSpeechRecognition' in window) {
            const recognition = new window.webkitSpeechRecognition();
            recognition.continuous = false;
            recognition.lang = 'es-ES';
            recognition.interimResults = false;

            recognition.onresult = (event) => {
                const text = event.results[0][0].transcript;
                setInputText(text);
            };

            recognition.onend = () => setIsListening(false);
            recognitionRef.current = recognition;
        }
    }, [user]);

    // Load quick suggestions on mount
    useEffect(() => {
        if (!user) return; // Guard inside effect
        if (isOpen) {
            const loadSuggestions = async () => {
                try {
                    const role = user?.roles?.[0]?.replace('ROLE_', '').toLowerCase() || 'student';
                    const response = await aiApi.get(`/chat/suggestions?role=${role}`);
                    if (response.suggestions) {
                        setQuickSuggestions(response.suggestions);
                    }
                } catch (error) {
                    console.error('Error loading suggestions:', error);
                    setQuickSuggestions([
                        { text: "📊 Mis notas", value: "¿Cuáles son mis notas?" },
                        { text: "📅 Mi horario", value: "Ver mi horario del día" },
                        { text: "📝 Tareas", value: "Ver mis tareas pendientes" },
                    ]);
                }
            };
            loadSuggestions();

            if (messages.length === 0) {
                setMessages([{
                    type: 'ai',
                    text: `¡Hola${user?.firstName ? ` ${user.firstName}` : ''}! 👋\n\nSoy tu **Asistente Oxford AI**. Puedo ayudarte con:\n\n• 📊 Consultar notas y promedios\n• 📅 Ver horarios de clases\n• 📝 Revisar tareas pendientes\n• 💰 Estado de pagos\n• 💡 Tips de estudio\n\n¿En qué te ayudo hoy?`
                }]);
            }
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [isOpen, user]);

    // Scroll to bottom when messages change
    useEffect(() => {
        if (messagesEndRef.current) {
            messagesEndRef.current.scrollIntoView({ behavior: "smooth" });
        }
    }, [messages]);

    // ========== EARLY RETURN AFTER ALL HOOKS ==========
    if (!user) return null;

    const handleSend = async (textOverride = null) => {
        const text = textOverride || inputText;
        if (!text.trim()) return;

        // User Message
        setMessages(prev => [...prev, { type: 'user', text, timestamp: new Date() }]);
        setInputText('');
        setIsOpen(true);
        setIsTyping(true);
        setContextSuggestions([]);

        try {
            const role = user?.roles?.[0]?.replace('ROLE_', '').toLowerCase() || 'student';

            let response;
            try {
                // Endpoint principal: POST /api/ai/chat (Symfony → Python /chat/message)
                response = await aiApi.post('/chat', {
                    message: text,
                    role,
                    user_id: user?.id?.toString(),
                    context: { page: window.location.pathname }
                });
            } catch {
                // Fallback: process-command devuelve response_text y action
                response = await aiApi.post('/process-command', { text, role });
                response = {
                    response: response.response_text ?? response.response ?? '',
                    suggestions: response.suggestions ?? [],
                    action: response.action ?? null,
                    data: response.data ?? null
                };
            }

            setIsTyping(false);

            const responseText = response.response ?? response.response_text ?? '';
            setMessages(prev => [...prev, {
                type: 'ai',
                text: responseText,
                timestamp: new Date(),
                data: response.data
            }]);

            // Update contextual suggestions
            if (response.suggestions && response.suggestions.length > 0) {
                setContextSuggestions(response.suggestions);
            }

            // === EXECUTE ACTIONS ===
            if (response.action) {
                console.log("⚡ AI Action:", response.action);
                executeAction(response.action);
            }

            // eslint-disable-next-line unused-imports/no-unused-vars
        } catch (error) {
            setIsTyping(false);
            setMessages(prev => [...prev, {
                type: 'error',
                text: '❌ No pude conectarme con el servicio de IA. Por favor intenta de nuevo.',
                timestamp: new Date()
            }]);
        }
    };

    const executeAction = (action) => {
        const actionMap = {
            'navigate_grades': '/alumno/notas',
            'fetch_grades': '/alumno/notas',
            'navigate_schedule': '/alumno/horario',
            'fetch_schedule': '/alumno/horario',
            'view_schedule': '/alumno/horario',
            'navigate_tasks': '/alumno/tareas',
            'fetch_tasks': '/alumno/tareas',
            'navigate_payments': '/alumno/pagos',
            'fetch_subjects': '/academico/materias',
            'navigate_attendance': '/alumno/asistencia',
            'show_risk_dashboard': '/',
        };

        const path = actionMap[action];
        if (path) {
            setTimeout(() => navigate(path), 500);
        }
    };

    const handleSuggestionClick = (suggestion) => {
        const text = suggestion.value || suggestion.text || suggestion;
        handleSend(text);
    };

    const toggleListening = () => {
        if (isListening) {
            recognitionRef.current?.stop();
        } else {
            recognitionRef.current?.start();
            setIsListening(true);
        }
    };

    const clearChat = () => {
        setMessages([{
            type: 'ai',
            text: '🔄 Conversación reiniciada. ¿En qué te ayudo?',
            timestamp: new Date()
        }]);
        setContextSuggestions([]);
    };

    return (
        <div className="fixed bottom-6 right-6 z-50 flex flex-col items-end pointer-events-none">

            {/* Chat Interface */}
            {isOpen && (
                <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 w-96 mb-4 overflow-hidden pointer-events-auto flex flex-col transition-all animate-in slide-in-from-bottom-5 fade-in duration-300" style={{ maxHeight: '550px' }}>

                    {/* Header */}
                    <div className="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 p-4 flex justify-between items-center text-white">
                        <div className="flex items-center gap-3">
                            <div className="p-2 bg-white/20 rounded-xl backdrop-blur">
                                <Bot size={20} />
                            </div>
                            <div>
                                <span className="font-bold text-sm block">Oxford AI</span>
                                <span className="text-xs text-white/70">Asistente Personal</span>
                            </div>
                        </div>
                        <div className="flex items-center gap-2">
                            <button onClick={clearChat} className="hover:bg-white/20 p-2 rounded-lg transition" title="Reiniciar chat">
                                <RefreshCw size={16} />
                            </button>
                            <button onClick={() => setIsOpen(false)} className="hover:bg-white/20 p-2 rounded-lg transition">
                                <X size={16} />
                            </button>
                        </div>
                    </div>

                    {/* Messages */}
                    <div className="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900/50" style={{ minHeight: '280px', maxHeight: '350px' }}>
                        {messages.map((msg, idx) => (
                            <div key={idx} className={`flex ${msg.type === 'user' ? 'justify-end' : 'justify-start'} animate-in fade-in slide-in-from-bottom-2`}>
                                <div className={`flex items-start gap-2 max-w-[85%] ${msg.type === 'user' ? 'flex-row-reverse' : ''}`}>
                                    {/* Avatar */}
                                    <div className={`w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 ${msg.type === 'user'
                                        ? 'bg-indigo-100 dark:bg-indigo-900'
                                        : msg.type === 'error'
                                            ? 'bg-red-100 dark:bg-red-900'
                                            : 'bg-gradient-to-br from-indigo-500 to-purple-500'
                                        }`}>
                                        {msg.type === 'user'
                                            ? <User size={14} className="text-indigo-600 dark:text-indigo-400" />
                                            : <Bot size={14} className="text-white" />
                                        }
                                    </div>

                                    {/* Message Bubble */}
                                    <div
                                        className={`rounded-2xl px-4 py-3 text-sm shadow-sm ${msg.type === 'user'
                                            ? 'bg-indigo-600 text-white rounded-tr-md'
                                            : msg.type === 'error'
                                                ? 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300 rounded-tl-md border border-red-200 dark:border-red-800'
                                                : 'bg-white dark:bg-gray-700 dark:text-white text-gray-800 rounded-tl-md border border-gray-100 dark:border-gray-600'
                                            }`}
                                    >
                                        <div className="whitespace-pre-wrap leading-relaxed">
                                            {msg.text}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}

                        {/* Typing Indicator */}
                        {isTyping && (
                            <div className="flex justify-start animate-in fade-in">
                                <div className="flex items-center gap-2">
                                    <div className="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                                        <Bot size={14} className="text-white" />
                                    </div>
                                    <div className="bg-white dark:bg-gray-700 rounded-2xl rounded-tl-md px-4 py-3 text-sm border border-gray-100 dark:border-gray-600">
                                        <div className="flex gap-1">
                                            <span className="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: '0ms' }}></span>
                                            <span className="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: '150ms' }}></span>
                                            <span className="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: '300ms' }}></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}

                        <div ref={messagesEndRef} />
                    </div>

                    {/* Contextual Suggestions */}
                    {contextSuggestions.length > 0 && (
                        <div className="px-3 py-2 bg-gray-100 dark:bg-gray-800 border-t dark:border-gray-700 flex flex-wrap gap-2">
                            {contextSuggestions.map((suggestion, idx) => (
                                <button
                                    key={idx}
                                    onClick={() => handleSuggestionClick(suggestion)}
                                    className="text-xs px-3 py-1.5 bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 rounded-full border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition flex items-center gap-1"
                                >
                                    {suggestion}
                                    <ChevronRight size={12} />
                                </button>
                            ))}
                        </div>
                    )}

                    {/* Quick Suggestions (only if no context suggestions) */}
                    {contextSuggestions.length === 0 && quickSuggestions.length > 0 && messages.length <= 1 && (
                        <div className="px-3 py-2 bg-gray-100 dark:bg-gray-800 border-t dark:border-gray-700 flex flex-wrap gap-2 overflow-x-auto">
                            {quickSuggestions.slice(0, 4).map((suggestion, idx) => (
                                <button
                                    key={idx}
                                    onClick={() => handleSuggestionClick(suggestion)}
                                    className="text-xs px-3 py-1.5 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/30 dark:to-purple-900/30 text-indigo-700 dark:text-indigo-300 rounded-full border border-indigo-200 dark:border-indigo-700 hover:shadow-md transition whitespace-nowrap"
                                >
                                    {suggestion.text}
                                </button>
                            ))}
                        </div>
                    )}

                    {/* Input Area */}
                    <div className="p-3 bg-white dark:bg-gray-800 border-t dark:border-gray-700 flex gap-2">
                        <button
                            onClick={toggleListening}
                            className={`p-2.5 rounded-xl transition-all ${isListening
                                ? 'bg-red-500 text-white animate-pulse shadow-lg shadow-red-500/30'
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'}`}
                            title={isListening ? 'Detener' : 'Hablar'}
                        >
                            {isListening ? <MicOff size={20} /> : <Mic size={20} />}
                        </button>
                        <input
                            type="text"
                            value={inputText}
                            onChange={(e) => setInputText(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && !e.shiftKey && handleSend()}
                            placeholder="Escribe tu mensaje..."
                            className="flex-1 bg-gray-100 dark:bg-gray-700 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-white placeholder-gray-400"
                            disabled={isTyping}
                        />
                        <button
                            onClick={() => handleSend()}
                            className="p-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg hover:shadow-indigo-500/30 disabled:opacity-50 transition-all"
                            disabled={!inputText.trim() || isTyping}
                        >
                            <Send size={18} />
                        </button>
                    </div>
                </div>
            )}

            {/* Floating Toggle Button */}
            <button
                onClick={() => setIsOpen(!isOpen)}
                className={`pointer-events-auto p-4 rounded-2xl shadow-xl transition-all hover:scale-105 active:scale-95 flex items-center gap-2 ${isOpen
                    ? 'bg-indigo-500 hover:bg-indigo-600'
                    : 'bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 hover:shadow-2xl hover:shadow-indigo-500/30'
                    } text-white`}
            >
                {isOpen ? <X size={24} /> : (
                    <>
                        <Sparkles size={24} className="animate-pulse" />
                        <span className="font-bold text-sm hidden sm:block">Asistente</span>
                    </>
                )}
            </button>
        </div>
    );
};

export default VoiceChat;
