import React, { useState, useEffect, useRef } from 'react';
import { Send, Search, MoreVertical, Phone, Video, Paperclip, Smile, ArrowLeft, Check, CheckCheck, Shield } from 'lucide-react';
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

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
    };

    useEffect(() => {
        scrollToBottom();
    }, [messages, selectedTeacher]);

    return (
        <div className={`flex h-[calc(100vh-100px)] rounded-3xl overflow-hidden shadow-2xl ${darkMode ? 'bg-[#0f111a] border border-gray-800' : 'bg-white border border-gray-200'}`}>

            {/* Sidebar List */}
            <div className={`w-full md:w-80 flex flex-col border-r ${darkMode ? 'border-gray-800 bg-[#151923]' : 'border-gray-100 bg-gray-50'} ${selectedStudent ? 'hidden md:flex' : 'flex'}`}>
                <div className="p-4 border-b border-gray-700/10">
                    <h2 className={`font-bold text-lg mb-4 ${darkMode ? 'text-white' : 'text-gray-900'}`}>Mensajes</h2>
                    <div className={`flex items-center gap-2 p-2 rounded-xl border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'}`}>
                        <Search size={18} className="text-gray-400" />
                        <input
                            type="text"
                            placeholder="Buscar estudiante..."
                            className="bg-transparent outline-none text-sm w-full"
                        />
                    </div>
                </div>

                <div className="flex-1 overflow-y-auto">
                    {students.map(student => (
                        <div
                            key={student.id}
                            onClick={() => setSelectedStudent(student)}
                            className={`p-4 flex items-center gap-3 cursor-pointer transition-colors border-b border-gray-700/5
                                ${selectedStudent?.id === student.id
                                    ? (darkMode ? 'bg-indigo-900/30 border-l-4 border-l-indigo-500' : 'bg-indigo-50 border-l-4 border-l-indigo-500')
                                    : (darkMode ? 'hover:bg-gray-800' : 'hover:bg-white')}
                            `}
                        >
                            <div className="relative">
                                <div className={`w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg ${darkMode ? 'bg-indigo-700 text-white' : 'bg-indigo-100 text-indigo-700'}`}>
                                    {student.avatar || student.name?.charAt(0) || 'E'}
                                </div>
                                {student.online && (
                                    <div className="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-900"></div>
                                )}
                            </div>
                            <div className="flex-1 min-w-0">
                                <div className="flex justify-between items-baseline mb-1">
                                    <h3 className={`font-semibold truncate ${darkMode ? 'text-gray-200' : 'text-gray-900'}`}>{student.name}</h3>
                                    <span className="text-xs text-gray-500">10:30</span>
                                </div>
                                <p className="text-xs text-gray-500 truncate">{student.grade || 'Estudiante'}</p>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            {/* Chat Area */}
            {selectedStudent ? (
                <div className={`flex-1 flex flex-col ${darkMode ? 'bg-[#0f111a]' : 'bg-white'}`}>
                    {/* Chat Header */}
                    <div className={`p-4 border-b flex items-center justify-between ${darkMode ? 'border-gray-800 bg-[#151923]' : 'border-gray-100 bg-white'}`}>
                        <div className="flex items-center gap-3">
                            <button onClick={() => setSelectedStudent(null)} className="md:hidden p-2 -ml-2 text-gray-500">
                                <ArrowLeft size={20} />
                            </button>
                            <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold ${darkMode ? 'bg-indigo-700 text-white' : 'bg-indigo-100 text-indigo-700'}`}>
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
                        <div className="flex items-center gap-3 text-gray-400">
                            <Phone size={20} className="hidden sm:block cursor-not-allowed opacity-50" />
                            <Video size={20} className="hidden sm:block cursor-not-allowed opacity-50" />
                            <MoreVertical size={20} />
                        </div>
                    </div>

                    {/* Messages */}
                    <div className={`flex-1 overflow-y-auto p-4 space-y-4 ${darkMode ? 'bg-gradient-to-b from-[#0f111a] to-[#0a0c10]' : 'bg-[#e5e5e5]'}`} style={{ backgroundImage: !darkMode ? "url('https://site-assets.fontawesome.com/releases/v6.1.1/svgs/solid/message-smile.svg')" : 'none' }}>
                        <div className="text-center text-xs text-gray-400 my-4">
                            Hoy
                        </div>
                        {(messages[selectedStudent.id] || []).map((msg) => (
                            <div key={msg.id} className={`flex ${msg.sender === 'me' ? 'justify-end' : 'justify-start'}`}>
                                <div className={`max-w-[80%] rounded-2xl px-4 py-2 shadow-sm text-sm relative
                                    ${msg.sender === 'me'
                                        ? 'bg-indigo-600 text-white rounded-tr-none'
                                        : (darkMode ? 'bg-gray-800 text-gray-200 rounded-tl-none' : 'bg-white text-gray-900 rounded-tl-none')}
                                `}>
                                    {msg.text}
                                    <div className={`text-[10px] mt-1 flex items-center justify-end gap-1 ${msg.sender === 'me' ? 'text-indigo-200' : 'text-gray-400'}`}>
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
                    <div className={`p-4 ${darkMode ? 'bg-[#151923]' : 'bg-gray-100'}`}>
                        <form onSubmit={handleSendMessage} className="flex items-center gap-2">
                            <button type="button" className="p-2 text-gray-400 hover:text-gray-600">
                                <Smile size={24} />
                            </button>
                            <button type="button" className="p-2 text-gray-400 hover:text-gray-600">
                                <Paperclip size={24} />
                            </button>
                            <input
                                type="text"
                                value={input}
                                onChange={(e) => setInput(e.target.value)}
                                placeholder="Escribe un mensaje..."
                                className={`flex-1 p-3 rounded-full outline-none text-sm ${darkMode ? 'bg-gray-800 text-white placeholder-gray-500' : 'bg-white text-gray-900 placeholder-gray-500'} shadow-sm`}
                            />
                            <button
                                type="submit"
                                disabled={!input.trim()}
                                className="p-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
                            >
                                <Send size={20} />
                            </button>
                        </form>
                    </div>

                </div>
            ) : (
                <div className={`hidden md:flex flex-1 flex-col items-center justify-center p-8 text-center ${darkMode ? 'bg-[#0f111a]' : 'bg-[#f0f2f5] border-l border-gray-300'}`}>
                    <div className="w-40 h-40 bg-indigo-100 rounded-full flex items-center justify-center mb-6">
                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/online-chat-4343118-3606700.png" alt="Chat" className="w-32 opacity-80 mix-blend-multiply" />
                    </div>
                    <h2 className={`text-2xl font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Oxford Chat</h2>
                    <p className="text-gray-500 max-w-sm">
                        Comunícate directamente con tus estudiantes. Selecciona un chat para comenzar.
                    </p>
                    <div className="mt-8 flex gap-2 text-xs text-gray-400">
                        <Shield size={14} /> Mensajes encriptados de extremo a extremo (Docente)
                    </div>
                </div>
            )}
        </div>
    );
};

export default ChatDocentePage;
