import React, { useState } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import { Star, User, Send, CheckCircle, MessageSquare } from 'lucide-react';

const TeacherRatingPage = () => {
    const { darkMode } = useTheme();
    const [selectedTeacher, setSelectedTeacher] = useState(null);
    const [ratings, setRatings] = useState({
        clarity: 0,
        punctuality: 0,
        treatment: 0,
        expertise: 0,
    });
    const [comment, setComment] = useState('');
    const [isAnonymous, setIsAnonymous] = useState(true);
    const [submitted, setSubmitted] = useState(false);

    // Mock data - conectar con backend real
    const teachers = [
        { id: 1, name: 'Prof. María García', subject: 'Matemáticas', photo: null, rated: false },
        { id: 2, name: 'Prof. Carlos Rodríguez', subject: 'Física', photo: null, rated: true },
        { id: 3, name: 'Prof. Ana Martínez', subject: 'Química', photo: null, rated: false },
        { id: 4, name: 'Prof. Luis Hernández', subject: 'Historia', photo: null, rated: false },
        { id: 5, name: 'Prof. Sandra López', subject: 'Inglés', photo: null, rated: true },
    ];

    const criteria = [
        { key: 'clarity', label: 'Claridad al Explicar', description: '¿Explica de forma clara y comprensible?' },
        { key: 'punctuality', label: 'Puntualidad', description: '¿Llega a tiempo y cumple con el horario?' },
        { key: 'treatment', label: 'Trato con Estudiantes', description: '¿Es respetuoso y accesible?' },
        { key: 'expertise', label: 'Dominio del Tema', description: '¿Demuestra conocimiento profundo?' },
    ];

    const handleRating = (criteriaKey, value) => {
        setRatings(prev => ({ ...prev, [criteriaKey]: value }));
    };

    const handleSubmit = () => {
        // Aquí enviar al backend
        console.log('Submitting rating:', { teacherId: selectedTeacher.id, ratings, comment, isAnonymous });
        setSubmitted(true);
        setTimeout(() => {
            setSelectedTeacher(null);
            setRatings({ clarity: 0, punctuality: 0, treatment: 0, expertise: 0 });
            setComment('');
            setSubmitted(false);
        }, 2000);
    };

    const averageRating = Object.values(ratings).filter(r => r > 0).length > 0
        ? (Object.values(ratings).reduce((a, b) => a + b, 0) / Object.values(ratings).filter(r => r > 0).length).toFixed(1)
        : 0;

    const StarRating = ({ value, onChange, size = 24 }) => (
        <div className="flex gap-1">
            {[1, 2, 3, 4, 5].map(star => (
                <button
                    key={star}
                    onClick={() => onChange(star)}
                    className="focus:outline-none transition-transform hover:scale-110"
                >
                    <Star
                        size={size}
                        className={star <= value ? 'text-yellow-400 fill-yellow-400' : darkMode ? 'text-gray-600' : 'text-gray-300'}
                    />
                </button>
            ))}
        </div>
    );

    return (
        <div className="space-y-6">
            {/* Header */}
            <div>
                <h1 className={`text-2xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                    <Star className="text-yellow-500" /> Evaluar Profesores
                </h1>
                <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>
                    Tu opinión ayuda a mejorar la calidad educativa
                </p>
            </div>

            {/* Info Card */}
            <div className={`p-4 rounded-xl ${darkMode ? 'bg-blue-900/20 border border-blue-800' : 'bg-blue-50 border border-blue-200'}`}>
                <p className={`text-sm ${darkMode ? 'text-blue-300' : 'text-blue-700'}`}>
                    📋 Puedes evaluar a cada profesor <strong>una vez por bimestre</strong>. Tus respuestas son {isAnonymous ? 'anónimas' : 'identificadas'} y se usan para mejorar la enseñanza.
                </p>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Teachers List */}
                <div className={`lg:col-span-1 ${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                    <div className={`p-4 border-b ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                        <h2 className={`font-semibold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Mis Profesores</h2>
                    </div>
                    <div className="divide-y divide-gray-200 dark:divide-gray-700">
                        {teachers.map(teacher => (
                            <div
                                key={teacher.id}
                                onClick={() => !teacher.rated && setSelectedTeacher(teacher)}
                                className={`p-4 cursor-pointer transition-colors ${teacher.rated
                                        ? 'opacity-60 cursor-not-allowed'
                                        : selectedTeacher?.id === teacher.id
                                            ? darkMode ? 'bg-obs-pink/10' : 'bg-obs-pink/5'
                                            : darkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'
                                    }`}
                            >
                                <div className="flex items-center gap-3">
                                    <div className={`w-10 h-10 rounded-full flex items-center justify-center ${darkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>
                                        <User size={20} className={darkMode ? 'text-gray-400' : 'text-gray-500'} />
                                    </div>
                                    <div className="flex-1">
                                        <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{teacher.name}</p>
                                        <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{teacher.subject}</p>
                                    </div>
                                    {teacher.rated && (
                                        <span className="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 flex items-center gap-1">
                                            <CheckCircle size={12} /> Evaluado
                                        </span>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Rating Form */}
                <div className={`lg:col-span-2 ${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                    {submitted ? (
                        <div className="p-12 text-center">
                            <div className="w-16 h-16 mx-auto rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-4">
                                <CheckCircle size={32} className="text-green-500" />
                            </div>
                            <h3 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>¡Gracias por tu evaluación!</h3>
                            <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                Tu opinión ha sido registrada exitosamente.
                            </p>
                        </div>
                    ) : selectedTeacher ? (
                        <>
                            {/* Header */}
                            <div className="p-4 bg-gradient-to-r from-obs-pink to-obs-purple text-white">
                                <h2 className="font-bold text-lg">Evaluar a {selectedTeacher.name}</h2>
                                <p className="text-sm opacity-75">{selectedTeacher.subject}</p>
                            </div>

                            {/* Rating Criteria */}
                            <div className="p-6 space-y-6">
                                {criteria.map(c => (
                                    <div key={c.key} className="space-y-2">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{c.label}</p>
                                                <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{c.description}</p>
                                            </div>
                                            <StarRating value={ratings[c.key]} onChange={(v) => handleRating(c.key, v)} />
                                        </div>
                                    </div>
                                ))}

                                {/* Average */}
                                <div className={`p-4 rounded-lg ${darkMode ? 'bg-gray-700' : 'bg-gray-100'} flex items-center justify-between`}>
                                    <span className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>Promedio General</span>
                                    <div className="flex items-center gap-2">
                                        <Star size={24} className="text-yellow-400 fill-yellow-400" />
                                        <span className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{averageRating}</span>
                                        <span className={darkMode ? 'text-gray-400' : 'text-gray-500'}>/5</span>
                                    </div>
                                </div>

                                {/* Comment */}
                                <div>
                                    <label className={`block font-medium mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                        <MessageSquare size={16} className="inline mr-2" />
                                        Comentario (opcional)
                                    </label>
                                    <textarea
                                        value={comment}
                                        onChange={e => setComment(e.target.value)}
                                        placeholder="Escribe aquí tus sugerencias o comentarios..."
                                        rows={3}
                                        className={`w-full px-4 py-3 rounded-lg border resize-none ${darkMode
                                                ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400'
                                                : 'bg-white border-gray-300 placeholder-gray-400'
                                            } focus:ring-2 focus:ring-obs-pink focus:border-transparent`}
                                    />
                                </div>

                                {/* Anonymous Toggle */}
                                <label className="flex items-center gap-3 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        checked={isAnonymous}
                                        onChange={e => setIsAnonymous(e.target.checked)}
                                        className="w-5 h-5 rounded text-obs-pink focus:ring-obs-pink"
                                    />
                                    <span className={darkMode ? 'text-gray-300' : 'text-gray-700'}>
                                        Enviar evaluación de forma anónima
                                    </span>
                                </label>

                                {/* Submit Button */}
                                <button
                                    onClick={handleSubmit}
                                    disabled={Object.values(ratings).some(r => r === 0)}
                                    className={`w-full py-3 rounded-lg font-medium flex items-center justify-center gap-2 transition-all ${Object.values(ratings).some(r => r === 0)
                                            ? 'bg-gray-300 dark:bg-gray-700 text-gray-500 cursor-not-allowed'
                                            : 'bg-gradient-to-r from-obs-pink to-obs-purple text-white hover:opacity-90'
                                        }`}
                                >
                                    <Send size={18} /> Enviar Evaluación
                                </button>
                            </div>
                        </>
                    ) : (
                        <div className="p-12 text-center">
                            <Star size={48} className={`mx-auto ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                            <p className={`mt-4 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                Selecciona un profesor para evaluar
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default TeacherRatingPage;
