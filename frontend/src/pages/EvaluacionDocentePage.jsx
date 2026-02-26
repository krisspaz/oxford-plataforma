import { toast } from '../utils/toast';
import { useState, useEffect } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import studentService from '../services/studentService';

const EvaluacionDocentePage = () => {
    const { darkMode } = useTheme();
    const [teachers, setTeachers] = useState([]);

    useEffect(() => {
        const load = async () => {
            try {
                const user = JSON.parse(localStorage.getItem('user') || '{}');
                const studentId = user.id || user.sub; // Fallback depending on auth structure

                if (!studentId) {
                    console.error("No student ID found");
                    return;
                }

                const data = await studentService.getMyTeachers(studentId);
                // Add local state for review
                if (Array.isArray(data)) {
                    setTeachers(data.map(t => ({ ...t, rating: 0, comment: '', submitted: false })));
                }
            } catch (e) {
                console.error("Error loading teachers", e);
            }
        };
        load();
    }, []);

    const handleRate = (id, stars) => {
        setTeachers(prev => prev.map(t => t.id === id ? { ...t, rating: stars } : t));
    };

    const handleComment = (id, text) => {
        setTeachers(prev => prev.map(t => t.id === id ? { ...t, comment: text } : t));
    };

    const handleSubmit = async (id) => {
        const teacher = teachers.find(t => t.id === id);
        if (teacher.rating === 0) return toast.warning("Por favor selecciona una calificación");

        await studentService.submitTeacherRating({ teacherId: id, rating: teacher.rating, comment: teacher.comment });

        setTeachers(prev => prev.map(t => t.id === id ? { ...t, submitted: true } : t));
    };

    return (
        <div className="max-w-5xl mx-auto p-4 md:p-8">
            <div className="text-center mb-10">
                <h1 className={`text-3xl font-bold mb-3 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                    Evaluación Docente Bimestral
                </h1>
                <p className="text-gray-500 max-w-2xl mx-auto">
                    Tu opinión ayuda a mejorar la calidad educativa. Tus respuestas son <strong>anónimas</strong> para tus profesores y solo visibles para la dirección.
                </p>
            </div>

            <div className="grid md:grid-cols-2 lg:grid-cols-2 gap-6">
                {(Array.isArray(teachers) ? teachers : []).map((teacher, idx) => (
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: idx * 0.1 }}
                        key={teacher.id}
                        className={`p-6 rounded-2xl relative overflow-hidden transition-all
                            ${darkMode ? 'bg-[#151923] border border-gray-800' : 'bg-white border border-gray-100 shadow-lg shadow-gray-200/50'}
                            ${teacher.submitted ? 'opacity-70 grayscale' : ''}
                        `}
                    >
                        {teacher.submitted && (
                            <div className="absolute inset-0 z-10 bg-white/50 dark:bg-black/50 backdrop-blur-sm flex items-center justify-center">
                                <div className="bg-green-500 text-white px-4 py-2 rounded-full font-bold shadow-lg flex items-center gap-2">
                                    <Award size={20} /> Evaluado
                                </div>
                            </div>
                        )}

                        <div className="flex items-center gap-4 mb-6">
                            <div className={`w-16 h-16 rounded-2xl flex items-center justify-center text-xl font-bold ${darkMode ? 'bg-gray-800 text-white' : 'bg-indigo-50 text-indigo-700'}`}>
                                {teacher.avatar}
                            </div>
                            <div>
                                <h3 className={`font-bold text-lg ${darkMode ? 'text-white' : 'text-gray-900'}`}>{teacher.name}</h3>
                                <p className="text-indigo-500 text-sm font-medium">{teacher.subject}</p>
                            </div>
                        </div>

                        <div className="space-y-6">
                            {/* Star Rating */}
                            <div>
                                <label className={`text-xs font-bold uppercase tracking-wider mb-2 block ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                    Calificación General
                                </label>
                                <div className="flex gap-2">
                                    {[1, 2, 3, 4, 5].map((star) => (
                                        <button
                                            key={star}
                                            onClick={() => handleRate(teacher.id, star)}
                                            className={`transition-transform hover:scale-110 focus:outline-none`}
                                        >
                                            <Star
                                                size={32}
                                                className={`
                                                    ${teacher.rating >= star
                                                        ? 'fill-yellow-400 text-yellow-400 drop-shadow-md'
                                                        : (darkMode ? 'text-gray-700' : 'text-gray-200')}
                                                `}
                                            />
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Comment */}
                            <div>
                                <label className={`text-xs font-bold uppercase tracking-wider mb-2 block ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                    ¿Algún comentario? (Opcional)
                                </label>
                                <div className={`relative rounded-xl border transition-all ${darkMode ? 'bg-gray-800 border-gray-700 focus-within:border-indigo-500' : 'bg-gray-50 border-gray-200 focus-within:border-indigo-400'}`}>
                                    <MessageSquare size={18} className="absolute top-3 left-3 text-gray-400" />
                                    <textarea
                                        value={teacher.comment}
                                        onChange={(e) => handleComment(teacher.id, e.target.value)}
                                        placeholder="Escribe aquí tu opinión..."
                                        rows={2}
                                        className="w-full bg-transparent pl-10 p-3 outline-none text-sm resize-none"
                                    />
                                </div>
                            </div>

                            <button
                                onClick={() => handleSubmit(teacher.id)}
                                disabled={teacher.rating === 0}
                                className={`w-full py-3 rounded-xl font-bold transition-all
                                    ${teacher.rating > 0
                                        ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-500/30'
                                        : 'bg-gray-200 dark:bg-gray-800 text-gray-400 cursor-not-allowed'}
                                `}
                            >
                                Enviar Evaluación
                            </button>
                        </div>
                    </motion.div>
                ))}
            </div>
        </div>
    );
};

export default EvaluacionDocentePage;
