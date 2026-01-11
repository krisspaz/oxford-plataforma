import React, { useState, useRef, useEffect } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import { studentService } from '../services/studentService';
import { MessageCircle, Send, User, Check, CheckCheck, Search } from 'lucide-react';

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
                // Map API response if necessary, or use directly if matches UI
                // Assuming API returns: { id, name, subject, photo, online, unreadCount }
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

    const filteredTeachers = teachers.filter(t =>
        (t.name || '').toLowerCase().includes(searchTerm.toLowerCase()) ||
        (t.subject || '').toLowerCase().includes(searchTerm.toLowerCase())
    );

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    };

    useEffect(() => {
        scrollToBottom();
    }, [selectedTeacher, conversations]);

    const getTotalUnread = () => teachers.reduce((sum, t) => sum + (t.unread || 0), 0);

    return (
        <div className="space-y-6">
            {/* Header */}
            <div>
                <h1 className={`text-2xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                    <MessageCircle className="text-obs-green" /> Chat con Profesores
                </h1>
                <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>
                    Comunícate directamente con tus profesores
                </p>
            </div>

            {/* Chat Container */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`} style={{ height: 'calc(100vh - 220px)', minHeight: '500px' }}>
                <div className="flex h-full">
                    {/* Teachers List */}
                    <div className={`w-80 border-r ${darkMode ? 'border-gray-700' : 'border-gray-200'} flex flex-col`}>
                        {/* Search */}
                        <div className={`p-3 border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                            <div className={`flex items-center gap-2 px-3 py-2 rounded-lg ${darkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>
                                <Search size={16} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                                <input
                                    type="text"
                                    placeholder="Buscar profesor..."
                                    value={searchTerm}
                                    onChange={e => setSearchTerm(e.target.value)}
                                    className={`flex-1 bg-transparent outline-none text-sm ${darkMode ? 'text-white placeholder-gray-400' : 'text-gray-900 placeholder-gray-500'}`}
                                />
                            </div>
                        </div>

                        {/* Teachers */}
                        <div className="flex-1 overflow-y-auto">
                            {filteredTeachers.map(teacher => (
                                <div
                                    key={teacher.id}
                                    onClick={() => setSelectedTeacher(teacher)}
                                    className={`p-3 cursor-pointer transition-colors flex items-center gap-3 ${selectedTeacher?.id === teacher.id
                                        ? darkMode ? 'bg-obs-green/10' : 'bg-obs-green/5'
                                        : darkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'
                                        }`}
                                >
                                    <div className="relative">
                                        <div className={`w-10 h-10 rounded-full flex items-center justify-center ${darkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>
                                            <User size={20} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                                        </div>
                                        {teacher.online && (
                                            <span className="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-gray-800 dark:border-gray-800 rounded-full" />
                                        )}
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center justify-between">
                                            <p className={`font-medium text-sm truncate ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                                {teacher.name}
                                            </p>
                                            {teacher.unread > 0 && (
                                                <span className="w-5 h-5 bg-obs-pink text-white text-xs font-bold rounded-full flex items-center justify-center">
                                                    {teacher.unread}
                                                </span>
                                            )}
                                        </div>
                                        <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                            {teacher.subject}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Chat Area */}
                    <div className="flex-1 flex flex-col">
                        {selectedTeacher ? (
                            <>
                                {/* Chat Header */}
                                <div className={`p-4 border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'} flex items-center gap-3`}>
                                    <div className="relative">
                                        <div className={`w-10 h-10 rounded-full flex items-center justify-center ${darkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>
                                            <User size={20} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                                        </div>
                                        {selectedTeacher.online && (
                                            <span className="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-gray-700 rounded-full" />
                                        )}
                                    </div>
                                    <div>
                                        <p className={`font-semibold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                            {selectedTeacher.name}
                                        </p>
                                        <p className={`text-xs ${selectedTeacher.online ? 'text-green-500' : darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                            {selectedTeacher.online ? 'En línea' : 'Desconectado'}
                                        </p>
                                    </div>
                                </div>

                                {/* Messages */}
                                <div className="flex-1 overflow-y-auto p-4 space-y-3">
                                    {(conversations[selectedTeacher.id] || []).map(message => (
                                        <div
                                            key={message.id}
                                            className={`flex ${message.from === 'student' ? 'justify-end' : 'justify-start'}`}
                                        >
                                            <div className={`max-w-[70%] px-4 py-2 rounded-2xl ${message.from === 'student'
                                                ? 'bg-obs-green text-white rounded-br-md'
                                                : darkMode ? 'bg-gray-700 text-white rounded-bl-md' : 'bg-gray-100 text-gray-800 rounded-bl-md'
                                                }`}>
                                                <p className="text-sm">{message.text}</p>
                                                <div className={`flex items-center justify-end gap-1 mt-1 ${message.from === 'student' ? 'text-white/60' : darkMode ? 'text-gray-400' : 'text-gray-400'}`}>
                                                    <span className="text-[10px]">{message.time}</span>
                                                    {message.from === 'student' && (
                                                        message.read ? <CheckCheck size={12} /> : <Check size={12} />
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                    <div ref={messagesEndRef} />
                                </div>

                                {/* Message Input */}
                                <form onSubmit={handleSendMessage} className={`p-4 border-t ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                                    <div className="flex items-center gap-2">
                                        <input
                                            type="text"
                                            value={newMessage}
                                            onChange={e => setNewMessage(e.target.value)}
                                            placeholder="Escribe un mensaje..."
                                            className={`flex-1 px-4 py-2 rounded-full border ${darkMode
                                                ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400'
                                                : 'bg-white border-gray-300 placeholder-gray-400'
                                                } focus:ring-2 focus:ring-obs-green focus:border-transparent`}
                                        />
                                        <button
                                            type="submit"
                                            disabled={!newMessage.trim()}
                                            className={`w-10 h-10 rounded-full flex items-center justify-center transition-colors ${newMessage.trim()
                                                ? 'bg-obs-green text-white hover:bg-obs-green/80'
                                                : darkMode ? 'bg-gray-700 text-gray-500' : 'bg-gray-100 text-gray-400'
                                                }`}
                                        >
                                            <Send size={18} />
                                        </button>
                                    </div>
                                </form>
                            </>
                        ) : (
                            <div className="flex-1 flex items-center justify-center">
                                <div className="text-center">
                                    <MessageCircle size={64} className={`mx-auto ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                                    <p className={`mt-4 text-lg font-medium ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        Selecciona un profesor para chatear
                                    </p>
                                    <p className={`text-sm ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                                        {getTotalUnread() > 0 && `Tienes ${getTotalUnread()} mensaje(s) sin leer`}
                                    </p>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentChatPage;
