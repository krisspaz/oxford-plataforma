import { toast } from '../utils/toast';
import { useState, useEffect } from 'react';
import { helpTicketService } from '../services';
import { useTheme } from '../contexts/ThemeContext';
import { Send, CheckCircle, Eye } from 'lucide-react';

const SuggestionsPage = () => {
    const { darkMode } = useTheme();
    const [activeTab, setActiveTab] = useState('new');
    const [formData, setFormData] = useState({
        category: '',
        title: '',
        description: '',
    });
    const [submitted, setSubmitted] = useState(false);

    const categories = [
        { id: 'teachers', label: '👨‍🏫 Profesores', description: 'Sugerencias para mejorar la enseñanza' },
        { id: 'school', label: '🏫 Colegio', description: 'Instalaciones, horarios, eventos' },
        { id: 'platform', label: '💻 Plataforma', description: 'Mejoras para el portal estudiantil' },
    ];

    // Real Data State
    const [mySuggestions, setMySuggestions] = useState([]);
    // eslint-disable-next-line unused-imports/no-unused-vars
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        if (activeTab === 'history') {
            loadSuggestions();
        }
    }, [activeTab]);

    const loadSuggestions = async () => {
        setLoading(true);
        try {
            // Fetch suggestions (type: sugerencia)
            const data = await helpTicketService.getAll({ type: 'sugerencia' });
            setMySuggestions(Array.isArray(data) ? data : []);
        } catch (error) {
            console.error("Error loading suggestions:", error);
        } finally {
            setLoading(false);
        }
    };

    const statusConfig = {
        open: { label: 'Enviada', color: 'bg-blue-100 text-blue-700', icon: Send },
        closed: { label: 'Revisada', color: 'bg-green-100 text-green-700', icon: CheckCircle },
        // Mapped statuses
        sent: { label: 'Enviada', color: 'bg-blue-100 text-blue-700', icon: Send },
        read: { label: 'Leída', color: 'bg-yellow-100 text-yellow-700', icon: Eye },
        implemented: { label: 'Implementada', color: 'bg-green-100 text-green-700', icon: CheckCircle },
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!formData.category || !formData.title || !formData.description) {
            toast.info('Por favor completa todos los campos');
            return;
        }

        try {
            await helpTicketService.create({
                ...formData,
                subject: formData.title, // Map title to subject
                type: 'sugerencia',
                status: 'open'
            });

            setSubmitted(true);
            setFormData({ category: '', title: '', description: '' });
            setTimeout(() => setSubmitted(false), 3000);
        } catch (error) {
            console.error("Error submitting suggestion:", error);
            toast.error("Error al enviar la sugerencia");
        }
    };

    const getCategoryLabel = (id) => categories.find(c => c.id === id)?.label || id;

    return (
        <div className="space-y-6">
            {/* Header */}
            <div>
                <h1 className={`text-2xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                    <Lightbulb className="text-yellow-500" /> Sugerencias
                </h1>
                <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>
                    Tus ideas nos ayudan a mejorar
                </p>
            </div>

            {/* Tabs */}
            <div className={`flex gap-2 p-1 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-gray-100'}`}>
                <button
                    onClick={() => setActiveTab('new')}
                    className={`flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2 ${activeTab === 'new'
                        ? 'bg-gradient-to-r from-yellow-400 to-orange-500 text-white shadow-lg'
                        : darkMode ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-900'
                        }`}
                >
                    <Lightbulb size={16} /> Nueva Sugerencia
                </button>
                <button
                    onClick={() => setActiveTab('history')}
                    className={`flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2 ${activeTab === 'history'
                        ? 'bg-gradient-to-r from-yellow-400 to-orange-500 text-white shadow-lg'
                        : darkMode ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-900'
                        }`}
                >
                    <Clock size={16} /> Mis Sugerencias ({mySuggestions.length})
                </button>
            </div>

            {/* New Suggestion Form */}
            {activeTab === 'new' && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm p-6`}>
                    {submitted ? (
                        <div className="text-center py-8">
                            <div className="w-16 h-16 mx-auto rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-4">
                                <CheckCircle size={32} className="text-green-500" />
                            </div>
                            <h3 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                ¡Gracias por tu sugerencia!
                            </h3>
                            <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                Tu idea ha sido enviada y será revisada por la administración.
                            </p>
                        </div>
                    ) : (
                        <form onSubmit={handleSubmit} className="space-y-6">
                            {/* Category Selection */}
                            <div>
                                <label className={`block font-medium mb-3 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                    Categoría
                                </label>
                                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    {categories.map(cat => (
                                        <button
                                            key={cat.id}
                                            type="button"
                                            onClick={() => setFormData(prev => ({ ...prev, category: cat.id }))}
                                            className={`p-4 rounded-xl border-2 text-left transition-all ${formData.category === cat.id
                                                ? 'border-obs-orange bg-obs-orange/10'
                                                : darkMode
                                                    ? 'border-gray-700 hover:border-gray-600'
                                                    : 'border-gray-200 hover:border-gray-300'
                                                }`}
                                        >
                                            <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{cat.label}</p>
                                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{cat.description}</p>
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Title */}
                            <div>
                                <label className={`block font-medium mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                    Título de tu sugerencia
                                </label>
                                <input
                                    type="text"
                                    value={formData.title}
                                    onChange={e => setFormData(prev => ({ ...prev, title: e.target.value }))}
                                    placeholder="Ej: Agregar más actividades extracurriculares"
                                    className={`w-full px-4 py-3 rounded-lg border ${darkMode
                                        ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400'
                                        : 'bg-white border-gray-300 placeholder-gray-400'
                                        } focus:ring-2 focus:ring-obs-orange focus:border-transparent`}
                                />
                            </div>

                            {/* Description */}
                            <div>
                                <label className={`block font-medium mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                    Descripción detallada
                                </label>
                                <textarea
                                    rows={4}
                                    value={formData.description}
                                    onChange={e => setFormData(prev => ({ ...prev, description: e.target.value }))}
                                    placeholder="Explica tu sugerencia con más detalle..."
                                    className={`w-full px-4 py-3 rounded-lg border resize-none ${darkMode
                                        ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400'
                                        : 'bg-white border-gray-300 placeholder-gray-400'
                                        } focus:ring-2 focus:ring-obs-orange focus:border-transparent`}
                                />
                            </div>

                            {/* Submit */}
                            <button
                                type="submit"
                                className="w-full py-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-lg font-medium flex items-center justify-center gap-2 hover:opacity-90 transition-opacity"
                            >
                                <Send size={18} /> Enviar Sugerencia
                            </button>
                        </form>
                    )}
                </div>
            )}

            {/* Suggestion History */}
            {activeTab === 'history' && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                    {mySuggestions.length === 0 ? (
                        <div className="p-12 text-center">
                            <Lightbulb size={48} className={`mx-auto ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                            <p className={`mt-4 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                Aún no has enviado sugerencias
                            </p>
                        </div>
                    ) : (
                        <div className="divide-y divide-gray-200 dark:divide-gray-700">
                            {mySuggestions.map(suggestion => {
                                const status = statusConfig[suggestion.status];
                                // eslint-disable-next-line unused-imports/no-unused-vars
                                const StatusIcon = status.icon;

                                return (
                                    <div key={suggestion.id} className="p-4">
                                        <div className="flex items-start justify-between mb-2">
                                            <div className="flex items-center gap-2">
                                                <span className={`px-2 py-1 rounded text-xs font-medium ${status.color}`}>
                                                    <StatusIcon size={12} className="inline mr-1" />
                                                    {status.label}
                                                </span>
                                                <span className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                                                    {getCategoryLabel(suggestion.category)}
                                                </span>
                                            </div>
                                            <span className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                                                {suggestion.date}
                                            </span>
                                        </div>
                                        <h3 className={`font-semibold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                            {suggestion.title || suggestion.subject}
                                        </h3>
                                        <p className={`text-sm mt-1 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                                            {suggestion.description}
                                        </p>
                                        {suggestion.response && (
                                            <div className={`mt-3 p-3 rounded-lg ${darkMode ? 'bg-green-900/20' : 'bg-green-50'}`}>
                                                <p className={`text-sm font-medium ${darkMode ? 'text-green-400' : 'text-green-700'}`}>
                                                    <MessageSquare size={14} className="inline mr-1" /> Respuesta:
                                                </p>
                                                <p className={`text-sm mt-1 ${darkMode ? 'text-green-300' : 'text-green-600'}`}>
                                                    {suggestion.response}
                                                </p>
                                            </div>
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};

export default SuggestionsPage;
