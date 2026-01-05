import React, { useState } from 'react';
import { MessageSquare, ThumbsUp, AlertTriangle, Send, CheckCircle, HelpCircle } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import studentService from '../services/studentService';

const SugerenciasPage = () => {
    const { darkMode } = useTheme();
    const [type, setType] = useState('sugerencia'); // sugerencia, queja, felicitacion
    const [formData, setFormData] = useState({ subject: '', message: '', area: 'academico' });
    const [status, setStatus] = useState('idle'); // idle, submitting, success

    const handleSubmit = async (e) => {
        e.preventDefault();
        setStatus('submitting');
        try {
            await studentService.submitFeedback({ type, ...formData });
            setStatus('success');
            setFormData({ subject: '', message: '', area: 'academico' });
        } catch (error) {
            console.error(error);
            setStatus('idle'); // In real app handle error
        }
    };

    const options = [
        { id: 'sugerencia', icon: HelpCircle, label: 'Sugerencia', color: 'text-blue-500', bg: 'bg-blue-500/10' },
        { id: 'queja', icon: AlertTriangle, label: 'Reporte/Queja', color: 'text-red-500', bg: 'bg-red-500/10' },
        { id: 'felicitacion', icon: ThumbsUp, label: 'Felicitación', color: 'text-green-500', bg: 'bg-green-500/10' },
    ];

    if (status === 'success') {
        return (
            <div className={`max-w-2xl mx-auto mt-10 p-8 rounded-3xl text-center shadow-xl ${darkMode ? 'bg-[#151923] border border-gray-800' : 'bg-white border border-gray-100'}`}>
                <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <CheckCircle size={40} className="text-green-600" />
                </div>
                <h2 className={`text-2xl font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>¡Mensaje Enviado!</h2>
                <p className="text-gray-500 mb-8">
                    Gracias por tu feedback. Tu voz es muy importante para nosotros. Hemos generado el ticket <strong>#TK-8923</strong>.
                </p>
                <button
                    onClick={() => setStatus('idle')}
                    className="px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition hover:scale-105"
                >
                    Enviar otro mensaje
                </button>
            </div>
        );
    }

    return (
        <div className="max-w-4xl mx-auto p-4 md:p-8">
            <div className={`rounded-3xl shadow-xl overflow-hidden ${darkMode ? 'bg-[#151923] border border-gray-800' : 'bg-white'}`}>

                {/* Header */}
                <div className="p-8 pb-0">
                    <h1 className={`text-3xl font-bold mb-2 ${darkMode ? 'text-white' : 'text-gray-900'}`}>Buzón de Voz Estudiantil</h1>
                    <p className="text-gray-500">
                        ¿Tienes una idea brillante? ¿Algo nos salió mal? ¿O quieres felicitar a alguien?
                        <br />Este es tu espacio seguro. Tu opinión nos ayuda a construir un mejor colegio.
                    </p>
                </div>

                <div className="p-8 grid md:grid-cols-3 gap-8">

                    {/* Sidebar Type Selection */}
                    <div className="space-y-3">
                        <label className={`text-xs font-bold uppercase tracking-wider ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                            Tipo de Mensaje
                        </label>
                        {options.map((opt) => (
                            <button
                                key={opt.id}
                                onClick={() => setType(opt.id)}
                                className={`w-full flex items-center gap-3 p-4 rounded-xl transition-all border
                                    ${type === opt.id
                                        ? (darkMode ? 'bg-indigo-900/20 border-indigo-500/50 shadow-lg' : 'bg-indigo-50 border-indigo-200 shadow-md')
                                        : (darkMode ? 'bg-gray-800/50 border-transparent hover:bg-gray-800' : 'bg-gray-50 border-transparent hover:bg-gray-100')
                                    }
                                `}
                            >
                                <div className={`p-2 rounded-lg ${opt.bg}`}>
                                    <opt.icon size={20} className={opt.color} />
                                </div>
                                <span className={`font-semibold ${darkMode ? 'text-gray-200' : 'text-gray-700'}`}>
                                    {opt.label}
                                </span>
                                {type === opt.id && <div className="ml-auto w-2 h-2 rounded-full bg-indigo-500"></div>}
                            </button>
                        ))}
                    </div>

                    {/* Form Area */}
                    <form onSubmit={handleSubmit} className="md:col-span-2 space-y-6">

                        <div>
                            <label className={`block text-sm font-bold mb-2 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                Asunto
                            </label>
                            <input
                                type="text"
                                value={formData.subject}
                                onChange={e => setFormData({ ...formData, subject: e.target.value })}
                                placeholder="Ej: Mejorar la comida de la cafetería..."
                                className={`w-full p-4 rounded-xl outline-none transition-all border
                                    ${darkMode
                                        ? 'bg-[#0f111a] border-gray-700 focus:border-indigo-500 text-white'
                                        : 'bg-gray-50 border-gray-200 focus:border-indigo-500 text-gray-900'
                                    }
                                `}
                                required
                            />
                        </div>

                        <div>
                            <label className={`block text-sm font-bold mb-2 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                Área Relacionada
                            </label>
                            <select
                                value={formData.area}
                                onChange={e => setFormData({ ...formData, area: e.target.value })}
                                className={`w-full p-4 rounded-xl outline-none transition-all border
                                    ${darkMode
                                        ? 'bg-[#0f111a] border-gray-700 focus:border-indigo-500 text-white'
                                        : 'bg-gray-50 border-gray-200 focus:border-indigo-500 text-gray-900'
                                    }
                                `}
                            >
                                <option value="academico">Académico / Clases</option>
                                <option value="infraestructura">Instalaciones / Baños / Aulas</option>
                                <option value="convivencia">Convivencia / Bullying</option>
                                <option value="cafeteria">Cafetería / Comida</option>
                                <option value="deportes">Deportes / Eventos</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>

                        <div>
                            <label className={`block text-sm font-bold mb-2 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                Tu Mensaje
                            </label>
                            <textarea
                                value={formData.message}
                                onChange={e => setFormData({ ...formData, message: e.target.value })}
                                placeholder="Cuéntanos los detalles..."
                                rows={5}
                                className={`w-full p-4 rounded-xl outline-none transition-all border resize-none
                                    ${darkMode
                                        ? 'bg-[#0f111a] border-gray-700 focus:border-indigo-500 text-white'
                                        : 'bg-gray-50 border-gray-200 focus:border-indigo-500 text-gray-900'
                                    }
                                `}
                                required
                            />
                        </div>

                        <div className="pt-4 flex items-center justify-between">
                            <div className="flex items-center gap-2 text-xs text-gray-500">
                                <AlertTriangle size={14} /> Tu reporte puede ser anónimo si no incluyes tu nombre.
                            </div>
                            <button
                                type="submit"
                                disabled={status === 'submitting'}
                                className="flex items-center gap-2 px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition-all hover:scale-105 disabled:opacity-50"
                            >
                                {status === 'submitting' ? 'Enviando...' : (
                                    <>Enviar <Send size={18} /></>
                                )}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    );
};

export default SugerenciasPage;
