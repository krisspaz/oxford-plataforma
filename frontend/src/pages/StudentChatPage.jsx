import React, { useState, useRef, useEffect } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import { studentService } from '../services/studentService';
import { MessageCircle, Send, User, Check, CheckCheck, Search, Image, Smile, Phone, Video, MoreVertical, Paperclip, Volume2 } from 'lucide-react';

const StudentChatPage = () => {
    const { darkMode } = useTheme();
    const [selectedTeacher, setSelectedTeacher] = useState(null);
    const [newMessage, setNewMessage] = useState('');
    const [searchTerm, setSearchTerm] = useState('');
    const messagesEndRef = useRef(null);

    // State for teachers/conversations
    const [teachers, setTeachers] = useState([]);
    const [conversations, setConversations] = useState({});
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadTeachers();
    }, []);

    useEffect(() => {
        if (selectedTeacher) {
            loadChatHistory(selectedTeacher.id);
        }
    }, [selectedTeacher]);

    const loadTeachers = async () => {
        try {
            const response = await studentService.getMyTeachers();
            if (response.data) {
                setTeachers(response.data);
            }
        } catch (error) {
            console.error("Error loading teachers", error);
        } finally {
            setLoading(false);
        }
    };

    const loadChatHistory = async (teacherId) => {
        try {
            const response = await studentService.getChatHistory(null, teacherId);
            if (response.data) {
                setConversations(prev => ({
                    ...prev,
                    [teacherId]: response.data
                }));
            }
        } catch (error) {
            console.error("Error loading chat", error);
        }
    };

    const handleSendMessage = async (e) => {
        e.preventDefault();
        if (!newMessage.trim() || !selectedTeacher) return;

        const messageText = newMessage.trim();
        const tempId = Date.now();
        const now = new Date();
        const timeString = `${now.getHours()}:${now.getMinutes().toString().padStart(2, '0')}`;

        // Optimistic UI update
        const tempMsg = {
            id: tempId,
            from: 'student',
            text: messageText,
            time: timeString,
            read: false
        };

        setConversations(prev => ({
            ...prev,
            [selectedTeacher.id]: [
                ...(prev[selectedTeacher.id] || []),
                tempMsg
            ]
        }));
        setNewMessage('');

        try {
            await studentService.sendMessage({
                teacherId: selectedTeacher.id,
                message: messageText
            });
            // Ideally re-fetch or confirm success here
        } catch (error) {
            console.error("Error sending message", error);
        }
    };

    const filteredTeachers = Array.isArray(teachers) ? teachers.filter(t =>
        (t.name || '').toLowerCase().includes(searchTerm.toLowerCase()) ||
        (t.subject || '').toLowerCase().includes(searchTerm.toLowerCase())
    ) : [];

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    };

    useEffect(() => {
        scrollToBottom();
    }, [selectedTeacher, conversations]);

    const getTotalUnread = () => teachers.reduce((sum, t) => sum + (t.unread || 0), 0);

    return (
        <div className={`flex h-[calc(100vh-100px)] rounded-3xl overflow-hidden shadow-2xl transition-all duration-300 ${darkMode ? 'bg-[#0f111a] border border-gray-800' : 'bg-white border border-gray-200'}`}>

            {/* Sidebar (Teachers List) */}
            <div className={`w-full md:w-80 flex flex-col border-r ${darkMode ? 'border-gray-800 bg-[#151923]' : 'bg-white border-gray-200'} ${selectedTeacher ? 'hidden md:flex' : 'flex'}`}>
                <div className="p-4 border-b border-gray-700/10">
                    <h2 className={`font-bold text-xl mb-4 flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                        <MessageCircle className="text-indigo-500" /> Profesores
                    </h2>
                    <div className={`flex items-center gap-2 p-2.5 rounded-xl border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-gray-50 border-gray-200'}`}>
                        <Search size={18} className="text-gray-400" />
                        <input
                            type="text"
                            placeholder="Buscar profesor..."
                            value={searchTerm}
                            onChange={e => setSearchTerm(e.target.value)}
                            className={`bg-transparent outline-none text-sm w-full ${darkMode ? 'text-white placeholder-gray-500' : 'text-gray-900 placeholder-gray-400'}`}
                        />
                    </div>
                </div>

                <div className="flex-1 overflow-y-auto">
                    {filteredTeachers.map(teacher => (
                        <div
                            key={teacher.id}
                            onClick={() => setSelectedTeacher(teacher)}
                            className={`p-4 flex items-center gap-3 cursor-pointer transition-all border-b border-gray-700/5
                                ${selectedTeacher?.id === teacher.id
                                    ? (darkMode ? 'bg-indigo-900/30 border-l-4 border-l-indigo-500' : 'bg-indigo-50 border-l-4 border-l-indigo-500')
                                    : (darkMode ? 'hover:bg-gray-800' : 'hover:bg-gray-50')}
                            `}
                        >
                            <div className="relative">
                                <div className={`w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg shadow-sm ${darkMode ? 'bg-indigo-600 text-white' : 'bg-gradient-to-br from-indigo-100 to-indigo-200 text-indigo-700'}`}>
                                    {teacher.photo ? <img src={teacher.photo} className="w-full h-full rounded-full object-cover" /> : <User size={20} />}
                                </div>
                                {teacher.online && (
                                    <span className={`absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 rounded-full ${darkMode ? 'border-gray-900' : 'border-white'}`} />
                                )}
                            </div>
                            <div className="flex-1 min-w-0">
                                <div className="flex justify-between items-baseline mb-0.5">
                                    <h3 className={`font-semibold truncate ${darkMode ? 'text-gray-200' : 'text-gray-900'}`}>
                                        {teacher.name}
                                    </h3>
                                    {teacher.unread > 0 && (
                                        <span className="min-w-[20px] h-5 bg-indigo-600 text-white text-xs font-bold rounded-full flex items-center justify-center px-1">
                                            {teacher.unread}
                                        </span>
                                    )}
                                </div>
                                <p className={`text-xs truncate ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                    {teacher.subject}
                                </p>
                            </div>
                        </div>
                    ))}
                    {filteredTeachers.length === 0 && (
                        <div className="p-8 text-center text-gray-500 text-sm">
                            No se encontraron profesores
                        </div>
                    )}
                </div>
            </div>

            {/* Chat Area */}
            {selectedTeacher ? (
                <div className={`flex-1 flex flex-col ${darkMode ? 'bg-[#0f111a]' : 'bg-white'}`}>

                    {/* Header */}
                    <div className={`p-4 border-b flex items-center justify-between ${darkMode ? 'border-gray-800 bg-[#151923]' : 'border-gray-100 bg-white/80 backdrop-blur'}`}>
                        <div className="flex items-center gap-3">
                            <button onClick={() => setSelectedTeacher(null)} className="md:hidden p-2 -ml-2 text-gray-500">
                                <MessagesSquare size={20} />
                            </button>
                            <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg ${darkMode ? 'bg-indigo-600 text-white' : 'bg-gradient-to-br from-indigo-100 to-indigo-200 text-indigo-700'}`}>
                                {selectedTeacher.photo ? <img src={selectedTeacher.photo} className="w-full h-full rounded-full object-cover" /> : <User size={18} />}
                            </div>
                            <div>
                                <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{selectedTeacher.name}</h3>
                                <div className="flex items-center gap-1.5">
                                    <span className={`w-1.5 h-1.5 rounded-full ${selectedTeacher.online ? 'bg-green-500' : 'bg-gray-400'}`}></span>
                                    <span className="text-xs text-gray-500">{selectedTeacher.online ? 'En línea' : 'Desconectado'}</span>
                                </div>
                            </div>
                        </div>
                        <div className="flex items-center gap-2 text-gray-400">
                            <button className="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"><Phone size={20} /></button>
                            <button className="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"><Video size={20} /></button>
                            <button className="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"><MoreVertical size={20} /></button>
                        </div>
                    </div>

                    {/* Messages */}
                    <div className={`flex-1 overflow-y-auto p-4 space-y-4 ${darkMode ? 'bg-gradient-to-b from-[#0f111a] to-[#0a0c10]' : 'bg-gray-50/50'}`}>
                        <div className="text-center text-xs text-gray-400/60 font-medium my-4">Hoy</div>

                        {(conversations[selectedTeacher.id] || []).map((message, idx) => {
                            const isMe = message.from === 'student';
                            return (
                                <div
                                    key={message.id || idx}
                                    className={`flex ${isMe ? 'justify-end' : 'justify-start'} group animate-in slide-in-from-bottom-2 duration-300`}
                                >
                                    <div className={`max-w-[75%] px-5 py-3 rounded-2xl shadow-sm relative text-sm leading-relaxed
                                        ${isMe
                                            ? 'bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-tr-none'
                                            : darkMode ? 'bg-gray-800 text-gray-200 border border-gray-700 rounded-tl-none' : 'bg-white text-gray-800 border border-gray-200 rounded-tl-none'
                                        }`}
                                    >
                                        <p>{message.text}</p>
                                        <div className={`flex items-center justify-end gap-1 mt-1 text-[10px] ${isMe ? 'text-indigo-200' : 'text-gray-400'}`}>
                                            {!isMe && (
                                                <button
                                                    onClick={(e) => {
                                                        e.stopPropagation();
                                                        const u = new SpeechSynthesisUtterance(message.text);
                                                        u.lang = 'es-ES';
                                                        window.speechSynthesis.speak(u);
                                                    }}
                                                    className="opacity-0 group-hover:opacity-100 transition-opacity hover:text-indigo-500 mr-2"
                                                    title="Leer mensaje"
                                                >
                                                    <Volume2 size={14} />
                                                </button>
                                            )}
                                            <span>{message.time || new Date().toLocaleTimeString()}</span>
                                            {isMe && (
                                                message.read ? <CheckCheck size={12} /> : <Check size={12} />
                                            )}
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
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
                                value={newMessage}
                                onChange={e => setNewMessage(e.target.value)}
                                placeholder="Escribe un mensaje..."
                                className={`flex-1 bg-transparent px-2 py-2 outline-none text-sm ${darkMode ? 'text-white placeholder-gray-500' : 'text-gray-900 placeholder-gray-400'}`}
                            />
                            <button
                                type="submit"
                                disabled={!newMessage.trim()}
                                className={`w-10 h-10 rounded-full flex items-center justify-center transition-all ${newMessage.trim()
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
                    <h2 className={`text-2xl font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Bienvenido a tu Chat</h2>
                    <p className={`max-w-xs mb-8 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        Selecciona un profesor de la lista para comenzar a chatear, resolver dudas o enviar tareas.
                    </p>
                    <div className={`inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-medium ${darkMode ? 'bg-gray-800 text-gray-400' : 'bg-white text-gray-500 border border-gray-200'}`}>
                        <span className="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Servidor de mensajería activo
                    </div>
                </div>
            )}
        </div>
    );
};

export default StudentChatPage;
