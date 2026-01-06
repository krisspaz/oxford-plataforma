import React, { useState, useEffect, useRef } from 'react';
import { Mic, Send, MicOff, Volume2, X } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import aiApi from '../services/aiApi';
import { useAuth } from '../contexts/AuthContext';

const VoiceChat = () => {
    const [isOpen, setIsOpen] = useState(false);
    const [isListening, setIsListening] = useState(false);
    const [messages, setMessages] = useState([
        { type: 'ai', text: 'Hola, soy tu asistente Oxford. ¿En qué te ayudo?' }
    ]);
    const [inputText, setInputText] = useState('');
    const messagesEndRef = useRef(null);
    const navigate = useNavigate();
    const { user } = useAuth();

    // Speech Recognition Setup
    const recognitionRef = useRef(null);

    useEffect(() => {
        if ('webkitSpeechRecognition' in window) {
            const recognition = new window.webkitSpeechRecognition();
            recognition.continuous = false;
            recognition.lang = 'es-ES';
            recognition.interimResults = false;

            recognition.onresult = (event) => {
                const text = event.results[0][0].transcript;
                setInputText(text);
                handleSend(text);
            };

            recognition.onend = () => setIsListening(false);
            recognitionRef.current = recognition;
        }
    }, []);

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
    };

    useEffect(() => scrollToBottom(), [messages]);

    const handleSend = async (textOverride = null) => {
        const text = textOverride || inputText;
        if (!text.trim()) return;

        // User Message
        setMessages(prev => [...prev, { type: 'user', text }]);
        setInputText('');
        setIsOpen(true);

        try {
            // Processing...
            setMessages(prev => [...prev, { type: 'loading' }]);

            // Call Refactored FastAPI Service
            // Note: Since we are calling /process-command, we assume the proxy /ai redirects to correct path
            const response = await aiApi.post('/process-command', {
                text,
                role: user?.roles?.[0] || 'guest'
            });

            // Remove loading
            setMessages(prev => prev.filter(m => m.type !== 'loading'));

            // AI Response
            setMessages(prev => [...prev, { type: 'ai', text: response.response_text }]);

            // === CONTROL TOTAL: EXECUTE ACTIONS ===
            if (response.action) {
                console.log("⚡ AI Action Triggered:", response.action);

                switch (response.action) {
                    case 'navigate_grades':
                    case 'fetch_grades':
                        navigate('/alumno/notas'); // Or /carga-notas depending on role
                        break;
                    case 'fetch_schedule':
                    case 'view_schedule':
                        navigate('/alumno/horario');
                        break;
                    case 'fetch_subjects':
                        navigate('/academico/materias');
                        break;
                    case 'show_risk_dashboard':
                        navigate('/'); // Assume risk dashboard is home for now
                        break;
                    case 'open_feedback_modal':
                        // Could trigger a global modal context here
                        alert("Abriendo formulario de reporte...");
                        break;
                    default:
                        console.warn("Unknown action:", response.action);
                }
            }

        } catch (error) {
            setMessages(prev => prev.filter(m => m.type !== 'loading'));
            setMessages(prev => [...prev, { type: 'error', text: 'Error de conexión con IA.' }]);
        }
    };

    const toggleListening = () => {
        if (isListening) {
            recognitionRef.current?.stop();
        } else {
            recognitionRef.current?.start();
            setIsListening(true);
        }
    };

    return (
        <div className="fixed bottom-6 right-6 z-50 flex flex-col items-end pointer-events-none">

            {/* Chat Interface */}
            {isOpen && (
                <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 w-80 mb-4 overflow-hidden pointer-events-auto flex flex-col transition-all animate-in slide-in-from-bottom-5 fade-in duration-300" style={{ maxHeight: '400px' }}>

                    {/* Header */}
                    <div className="bg-indigo-600 p-4 flex justify-between items-center text-white">
                        <div className="flex items-center gap-2">
                            <div className="p-1 bg-white/20 rounded-full">
                                <Volume2 size={16} />
                            </div>
                            <span className="font-bold text-sm">Asistente Oxford</span>
                        </div>
                        <button onClick={() => setIsOpen(false)} className="hover:bg-white/20 p-1 rounded-full"><X size={16} /></button>
                    </div>

                    {/* Messages */}
                    <div className="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 dark:bg-gray-900/50">
                        {messages.map((msg, idx) => (
                            <div key={idx} className={`flex ${msg.type === 'user' ? 'justify-end' : 'justify-start'}`}>
                                {msg.type === 'loading' ? (
                                    <div className="bg-gray-200 dark:bg-gray-700 rounded-xl rounded-tl-none px-4 py-2 text-sm flex gap-1">
                                        <span className="animate-bounce">.</span>
                                        <span className="animate-bounce delay-100">.</span>
                                        <span className="animate-bounce delay-200">.</span>
                                    </div>
                                ) : (
                                    <div className={`max-w-[85%] rounded-2xl px-4 py-2 text-sm shadow-sm ${msg.type === 'user'
                                            ? 'bg-indigo-600 text-white rounded-tr-none'
                                            : msg.type === 'error'
                                                ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 rounded-tl-none'
                                                : 'bg-white dark:bg-gray-700 dark:text-white text-gray-800 rounded-tl-none border border-gray-100 dark:border-gray-600'
                                        }`}>
                                        {msg.text}
                                    </div>
                                )}
                            </div>
                        ))}
                        <div ref={messagesEndRef} />
                    </div>

                    {/* Input Area */}
                    <div className="p-3 bg-white dark:bg-gray-800 border-t dark:border-gray-700 flex gap-2">
                        <button
                            onClick={toggleListening}
                            className={`p-2 rounded-full transition-all ${isListening ? 'bg-red-500 text-white animate-pulse' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-200'}`}
                        >
                            {isListening ? <MicOff size={20} /> : <Mic size={20} />}
                        </button>
                        <input
                            type="text"
                            value={inputText}
                            onChange={(e) => setInputText(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && handleSend()}
                            placeholder="Escribe o habla..."
                            className="flex-1 bg-gray-100 dark:bg-gray-700 border-none rounded-full px-4 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-white"
                        />
                        <button
                            onClick={() => handleSend()}
                            className="p-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 disabled:opacity-50"
                            disabled={!inputText.trim()}
                        >
                            <Send size={18} />
                        </button>
                    </div>
                </div>
            )}

            {/* Floating Toggle Button */}
            <button
                onClick={() => setIsOpen(!isOpen)}
                className={`pointer-events-auto p-4 rounded-full shadow-xl transition-all hover:scale-105 active:scale-95 flex items-center gap-2 ${isOpen ? 'bg-indigo-500' : 'bg-gradient-to-r from-indigo-600 to-purple-600'} text-white`}
            >
                {isOpen ? <X size={24} /> : <Mic size={24} />}
                {!isOpen && <span className="font-bold text-sm hidden sm:block">Asistente</span>}
            </button>
        </div>
    );
};

export default VoiceChat;
