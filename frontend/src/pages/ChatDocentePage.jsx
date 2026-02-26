import { useState, useEffect, useRef } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import teacherService from '../services/teacherService';

const ChatDocentePage = () => {
    const { darkMode } = useTheme();
    const [students, setStudents] = useState([]); // Renamed from teachers
    const [selectedStudent, setSelectedStudent] = useState(null); // Renamed from selectedTeacher
    const [messages, setMessages] = useState({});
    const [input, setInput] = useState('');
    const messagesEndRef = useRef(null);

    useEffect(() => {
        // eslint-disable-next-line react-hooks/immutability
        loadStudents();
    }, []);

    const loadStudents = async () => {
        try {
            // Get current teacher profile first
            const profile = await teacherService.getMyProfile();
            if (profile && profile.id) {
                const data = await teacherService.getStudents(profile.id);
                setStudents(data);
            }
        } catch (error) {
            console.error("Error loading students", error);
        }
    };

    const loadChat = async (studentId) => {
        try {
            const history = await teacherService.getChatHistory(studentId);
            setMessages(prev => ({
                ...prev,
                [studentId]: history
            }));
        } catch (e) { console.error(e); }
    };

    useEffect(() => {
        if (selectedStudent) {
            loadChat(selectedStudent.id);
        }
    }, [selectedStudent]);

    const handleSendMessage = async (e) => {
        e.preventDefault();
        if (!input.trim() || !selectedStudent) return;

        const text = input.trim();
        const newMsg = {
            id: Date.now(),
            text: text,
            sender: 'me',
            time: new Date(),
            status: 'sent'
        };

        setMessages(prev => ({
            ...prev,
            [selectedStudent.id]: [...(prev[selectedStudent.id] || []), newMsg]
        }));

        setInput('');

        try {
            await teacherService.sendMessage({
                studentId: selectedStudent.id,
                message: text
            });
        } catch (error) {
            console.error("Error sending message", error);
        }
    };

    // eslint-disable-next-line unused-imports/no-unused-vars
    const [isTyping, setIsTyping] = useState(false);

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
    };

    useEffect(() => {
        scrollToBottom();
    }, [messages, selectedStudent]);

    return (
        <div className={`flex h-[calc(100vh-100px)] rounded-3xl overflow-hidden shadow-2xl transition-all duration-300 ${darkMode ? 'bg-[#0f111a] border border-gray-800' : 'bg-white border border-gray-200'}`}>

            {/* Sidebar List */}
            <div className={`w-full md:w-80 flex flex-col border-r ${darkMode ? 'border-gray-800 bg-[#151923]' : 'border-gray-200 bg-white'} ${selectedStudent ? 'hidden md:flex' : 'flex'}`}>
                <div className="p-4 border-b border-gray-700/10">
                    <h2 className={`font-bold text-xl mb-4 flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                        <MessageCircle className="text-indigo-500" /> Mensajes
                    </h2>
                    <div className={`flex items-center gap-2 p-2.5 rounded-xl border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-gray-50 border-gray-200'}`}>
                        <Search size={18} className="text-gray-400" />
                        <input
                            type="text"
                            placeholder="Buscar estudiante..."
                            className={`bg-transparent outline-none text-sm w-full ${darkMode ? 'text-white placeholder-gray-500' : 'text-gray-900 placeholder-gray-400'}`}
                        />
                    </div>
                </div>

                <div className="flex-1 overflow-y-auto">
                    {students.map(student => (
                        <div
                            key={student.id}
                            onClick={() => setSelectedStudent(student)}
                            className={`p-4 flex items-center gap-3 cursor-pointer transition-all border-b border-gray-700/5
                                ${selectedStudent?.id === student.id
                                    ? (darkMode ? 'bg-indigo-900/30 border-l-4 border-l-indigo-500' : 'bg-indigo-50 border-l-4 border-l-indigo-500')
                                    : (darkMode ? 'hover:bg-gray-800' : 'hover:bg-white')}
                            `}
                        >
                            <div className="relative">
                                <div className={`w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg shadow-sm ${darkMode ? 'bg-indigo-700 text-white' : 'bg-gradient-to-br from-indigo-100 to-indigo-200 text-indigo-700'}`}>
                                    {student.avatar || student.name?.charAt(0) || 'E'}
                                </div>
                                {student.online && (
                                    <div className={`absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 rounded-full border-2 ${darkMode ? 'border-gray-900' : 'border-white'}`}></div>
                                )}
                            </div>
                            <div className="flex-1 min-w-0">
                                <div className="flex justify-between items-baseline mb-0.5">
                                    <h3 className={`font-semibold truncate ${darkMode ? 'text-gray-200' : 'text-gray-900'}`}>{student.name}</h3>
                                    <span className="text-[10px] text-gray-500 font-medium">10:30</span>
                                </div>
                                <p className="text-xs text-gray-500 truncate">{student.grade || 'Estudiante'}</p>
                            </div>
                        </div>
                    ))}
                    {students.length === 0 && (
                        <div className="p-8 text-center text-gray-500 text-sm">
                            No hay estudiantes asignados
                        </div>
                    )}
                </div>
            </div>

            {/* Chat Area */}
            {selectedStudent ? (
                <div className={`flex-1 flex flex-col ${darkMode ? 'bg-[#0f111a]' : 'bg-white'}`}>
                    {/* Chat Header */}
                    <div className={`p-4 border-b flex items-center justify-between ${darkMode ? 'border-gray-800 bg-[#151923]' : 'border-gray-100 bg-white/80 backdrop-blur'}`}>
                        <div className="flex items-center gap-3">
                            <button onClick={() => setSelectedStudent(null)} className="md:hidden p-2 -ml-2 text-gray-500">
                                <ArrowLeft size={20} />
                            </button>
                            <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg ${darkMode ? 'bg-indigo-700 text-white' : 'bg-gradient-to-br from-indigo-100 to-indigo-200 text-indigo-700'}`}>
                                {selectedStudent.avatar || selectedStudent.name?.charAt(0) || 'E'}
                            </div>
                            <div>
                                <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{selectedStudent.name}</h3>
                                <div className="flex items-center gap-1.5">
                                    <span className={`w-1.5 h-1.5 rounded-full ${selectedStudent.online ? 'bg-green-500' : 'bg-gray-400'}`}></span>
                                    <span className="text-xs text-gray-500">{selectedStudent.online ? 'En línea' : 'Desconectado'}</span>
                                </div>
                            </div>
                        </div>
                        <div className="flex items-center gap-2 text-gray-400">
                            <button className="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors cursor-not-allowed opacity-50"><Phone size={20} /></button>
                            <button className="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors cursor-not-allowed opacity-50"><Video size={20} /></button>
                            <button className="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"><MoreVertical size={20} /></button>
                        </div>
                    </div>

                    {/* Messages */}
                    <div className={`flex-1 overflow-y-auto p-4 space-y-4 ${darkMode ? 'bg-gradient-to-b from-[#0f111a] to-[#0a0c10]' : 'bg-gray-50/50'}`}>
                        <div className="text-center text-xs text-gray-400/60 font-medium my-4">
                            Hoy
                        </div>
                        {(messages[selectedStudent.id] || []).map((msg) => (
                            <div key={msg.id} className={`flex ${msg.sender === 'me' ? 'justify-end' : 'justify-start'} animate-in slide-in-from-bottom-2 duration-300`}>
                                <div className={`max-w-[75%] px-5 py-3 rounded-2xl shadow-sm text-sm relative leading-relaxed
                                    ${msg.sender === 'me'
                                        ? 'bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-tr-none'
                                        : (darkMode ? 'bg-gray-800 text-gray-200 border border-gray-700 rounded-tl-none' : 'bg-white text-gray-800 border border-gray-200 rounded-tl-none')}
                                `}>
                                    {msg.text}
                                    <div className={`text-[10px] mt-1 flex items-center justify-end gap-1 ${msg.sender === 'me' ? 'text-indigo-200' : 'text-gray-400'}`}>
                                        {msg.sender !== 'me' && (
                                            <button
                                                onClick={(e) => {
                                                    e.stopPropagation();
                                                    const u = new SpeechSynthesisUtterance(msg.text);
                                                    u.lang = 'es-ES';
                                                    window.speechSynthesis.speak(u);
                                                }}
                                                className="opacity-0 group-hover:opacity-100 transition-opacity hover:text-indigo-500 mr-2"
                                                title="Leer mensaje"
                                            >
                                                <Volume2 size={12} />
                                            </button>
                                        )}
                                        {msg.time.toLocaleTimeString ? msg.time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'Now'}
                                        {msg.sender === 'me' && (
                                            msg.status === 'read' ? <CheckCheck size={12} /> : <Check size={12} />
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))}
                        <div ref={messagesEndRef} />
                    </div>

                    {/* Input */}
                    <div className={`p-4 border-t ${darkMode ? 'border-gray-800 bg-[#151923]' : 'bg-white border-gray-100'}`}>
                        <form onSubmit={handleSendMessage} className={`flex items-center gap-2 p-1.5 rounded-full border shadow-sm transition-all focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-gray-50 border-gray-200'}`}>
                            <button type="button" className="p-2 text-gray-400 hover:text-indigo-500 transition-colors ml-1">
                                <Smile size={22} />
                            </button>
                            <button type="button" className="p-2 text-gray-400 hover:text-indigo-500 transition-colors">
                                <Paperclip size={22} />
                            </button>
                            <input
                                type="text"
                                value={input}
                                onChange={(e) => setInput(e.target.value)}
                                placeholder="Escribe un mensaje..."
                                className={`flex-1 bg-transparent px-2 py-2 outline-none text-sm ${darkMode ? 'text-white placeholder-gray-500' : 'text-gray-900 placeholder-gray-400'}`}
                            />
                            <button
                                type="submit"
                                disabled={!input.trim()}
                                className={`w-10 h-10 rounded-full flex items-center justify-center transition-all ${input.trim()
                                    ? 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 hover:scale-105 active:scale-95'
                                    : darkMode ? 'bg-gray-700 text-gray-500' : 'bg-gray-200 text-gray-400'
                                    }`}
                            >
                                <Send size={18} />
                            </button>
                        </form>
                    </div>

                </div>
            ) : (
                <div className={`hidden md:flex flex-1 flex-col items-center justify-center p-8 text-center ${darkMode ? 'bg-[#0f111a]' : 'bg-gray-50/50'}`}>
                    <div className={`w-32 h-32 rounded-full flex items-center justify-center mb-6 animate-pulse ${darkMode ? 'bg-indigo-900/20' : 'bg-indigo-50'}`}>
                        <MessageCircle size={64} className="text-indigo-500" />
                    </div>
                    <h2 className={`text-2xl font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Oxford Chat</h2>
                    <p className={`max-w-xs mb-8 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        Comunícate directamente con tus estudiantes. Selecciona un chat para comenzar.
                    </p>
                    <div className={`inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-medium ${darkMode ? 'bg-gray-800 text-gray-400' : 'bg-white text-gray-500 border border-gray-200'}`}>
                        <Shield size={14} className="text-teal-500" />
                        Mensajes encriptados de extremo a extremo
                    </div>
                </div>
            )}
        </div>
    );
};

export default ChatDocentePage;
