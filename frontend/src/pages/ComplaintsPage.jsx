import React, { useState } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import { AlertTriangle, Send, CheckCircle, Shield, Eye, Clock, MessageSquare, Lock } from 'lucide-react';

const ComplaintsPage = () => {
    const { darkMode } = useTheme();
    const [activeTab, setActiveTab] = useState('new');
    const [formData, setFormData] = useState({
        category: '',
        subject: '',
        description: '',
        isAnonymous: true,
    });
    const [submitted, setSubmitted] = useState(false);

    const categories = [
        { id: 'academic', label: '📚 Académico', description: 'Problemas con clases, evaluaciones, calificaciones' },
        { id: 'infrastructure', label: '🏗️ Infraestructura', description: 'Instalaciones, equipos, mantenimiento' },
        { id: 'staff', label: '👥 Personal', description: 'Trato inadecuado, comportamientos' },
        { id: 'bullying', label: '🚫 Acoso/Bullying', description: 'Situaciones de acoso entre estudiantes' },
        { id: 'other', label: '📋 Otros', description: 'Otros problemas no listados' },
    ];

    const myComplaints = [
        {
            id: 1,
            category: 'infrastructure',
            subject: 'Aire acondicionado no funciona en Aula 5',
            description: 'Desde hace 2 semanas el aire acondicionado del aula 5 no funciona y hace mucho calor',
            status: 'resolved',
            date: '2024-12-20',
            isAnonymous: false,
            response: 'El equipo de mantenimiento reparó el aire acondicionado el 23 de diciembre. Gracias por reportar.',
            resolvedDate: '2024-12-23'
        },
        {
            id: 2,
            category: 'academic',
            subject: 'Falta de material para laboratorio',
            description: 'En la clase de Química no hay suficientes equipos de laboratorio para todos',
            status: 'in_review',
            date: '2025-01-03',
            isAnonymous: true,
            response: null
        },
        {
            id: 3,
            category: 'other',
            subject: 'Horario del transporte escolar',
            description: 'El bus llega muy tarde por las mañanas',
            status: 'sent',
            date: '2025-01-05',
            isAnonymous: true,
            response: null
        },
    ];

    const statusConfig = {
        sent: { label: 'Enviada', color: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', icon: Send },
        in_review: { label: 'En Revisión', color: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400', icon: Eye },
        resolved: { label: 'Resuelta', color: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', icon: CheckCircle },
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!formData.category || !formData.subject || !formData.description) {
            alert('Por favor completa todos los campos');
            return;
        }
        console.log('Submitting complaint:', formData);
        setSubmitted(true);
        setFormData({ category: '', subject: '', description: '', isAnonymous: true });
        setTimeout(() => setSubmitted(false), 3000);
    };

    const getCategoryLabel = (id) => categories.find(c => c.id === id)?.label || id;

    return (
        <div className="space-y-6">
            {/* Header */}
            <div>
                <h1 className={`text-2xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                    <AlertTriangle className="text-orange-500" /> Reportar Problema
                </h1>
                <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>
                    Tu reporte es confidencial y será atendido
                </p>
            </div>

            {/* Confidentiality Notice */}
            <div className={`p-4 rounded-xl flex items-start gap-3 ${darkMode ? 'bg-obs-blue/10 border border-obs-blue/30' : 'bg-blue-50 border border-blue-200'}`}>
                <Shield className="text-obs-blue flex-shrink-0 mt-0.5" size={20} />
                <div>
                    <p className={`font-medium ${darkMode ? 'text-obs-blue' : 'text-blue-700'}`}>
                        Tu privacidad está protegida
                    </p>
                    <p className={`text-sm ${darkMode ? 'text-obs-blue/70' : 'text-blue-600'}`}>
                        Puedes enviar reportes de forma anónima. Solo coordinación académica tiene acceso a estos reportes.
                    </p>
                </div>
            </div>

            {/* Tabs */}
            <div className={`flex gap-2 p-1 rounded-xl ${darkMode ? 'bg-gray-800' : 'bg-gray-100'}`}>
                <button
                    onClick={() => setActiveTab('new')}
                    className={`flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2 ${activeTab === 'new'
                            ? 'bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-lg'
                            : darkMode ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-900'
                        }`}
                >
                    <AlertTriangle size={16} /> Nuevo Reporte
                </button>
                <button
                    onClick={() => setActiveTab('history')}
                    className={`flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2 ${activeTab === 'history'
                            ? 'bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-lg'
                            : darkMode ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-900'
                        }`}
                >
                    <Clock size={16} /> Mis Reportes ({myComplaints.length})
                </button>
            </div>

            {/* New Complaint Form */}
            {activeTab === 'new' && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm p-6`}>
                    {submitted ? (
                        <div className="text-center py-8">
                            <div className="w-16 h-16 mx-auto rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-4">
                                <CheckCircle size={32} className="text-green-500" />
                            </div>
                            <h3 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                Reporte Enviado
                            </h3>
                            <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                Tu reporte ha sido recibido. Recibirás una notificación cuando sea atendido.
                            </p>
                        </div>
                    ) : (
                        <form onSubmit={handleSubmit} className="space-y-6">
                            {/* Category Selection */}
                            <div>
                                <label className={`block font-medium mb-3 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                    Tipo de Problema
                                </label>
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    {categories.map(cat => (
                                        <button
                                            key={cat.id}
                                            type="button"
                                            onClick={() => setFormData(prev => ({ ...prev, category: cat.id }))}
                                            className={`p-3 rounded-xl border-2 text-left transition-all ${formData.category === cat.id
                                                    ? 'border-orange-500 bg-orange-500/10'
                                                    : darkMode
                                                        ? 'border-gray-700 hover:border-gray-600'
                                                        : 'border-gray-200 hover:border-gray-300'
                                                }`}
                                        >
                                            <p className={`font-medium text-sm ${darkMode ? 'text-white' : 'text-gray-800'}`}>{cat.label}</p>
                                            <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{cat.description}</p>
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Subject */}
                            <div>
                                <label className={`block font-medium mb-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                    Asunto
                                </label>
                                <input
                                    type="text"
                                    value={formData.subject}
                                    onChange={e => setFormData(prev => ({ ...prev, subject: e.target.value }))}
                                    placeholder="Resumen breve del problema"
                                    className={`w-full px-4 py-3 rounded-lg border ${darkMode
                                            ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400'
                                            : 'bg-white border-gray-300 placeholder-gray-400'
                                        } focus:ring-2 focus:ring-orange-500 focus:border-transparent`}
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
                                    placeholder="Describe el problema con todos los detalles posibles: cuándo ocurrió, dónde, personas involucradas, etc."
                                    className={`w-full px-4 py-3 rounded-lg border resize-none ${darkMode
                                            ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400'
                                            : 'bg-white border-gray-300 placeholder-gray-400'
                                        } focus:ring-2 focus:ring-orange-500 focus:border-transparent`}
                                />
                            </div>

                            {/* Anonymous Toggle */}
                            <label className={`flex items-center gap-3 cursor-pointer p-3 rounded-lg ${darkMode ? 'bg-gray-700' : 'bg-gray-50'}`}>
                                <input
                                    type="checkbox"
                                    checked={formData.isAnonymous}
                                    onChange={e => setFormData(prev => ({ ...prev, isAnonymous: e.target.checked }))}
                                    className="w-5 h-5 rounded text-orange-500 focus:ring-orange-500"
                                />
                                <div>
                                    <span className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                        <Lock size={14} className="inline mr-1" /> Enviar de forma anónima
                                    </span>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        Tu identidad no será revelada al revisar el reporte
                                    </p>
                                </div>
                            </label>

                            {/* Submit */}
                            <button
                                type="submit"
                                className="w-full py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg font-medium flex items-center justify-center gap-2 hover:opacity-90 transition-opacity"
                            >
                                <Send size={18} /> Enviar Reporte
                            </button>
                        </form>
                    )}
                </div>
            )}

            {/* Complaints History */}
            {activeTab === 'history' && (
                <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                    {myComplaints.length === 0 ? (
                        <div className="p-12 text-center">
                            <AlertTriangle size={48} className={`mx-auto ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                            <p className={`mt-4 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                No has enviado reportes
                            </p>
                        </div>
                    ) : (
                        <div className="divide-y divide-gray-200 dark:divide-gray-700">
                            {myComplaints.map(complaint => {
                                const status = statusConfig[complaint.status];
                                const StatusIcon = status.icon;

                                return (
                                    <div key={complaint.id} className="p-4">
                                        <div className="flex items-start justify-between mb-2">
                                            <div className="flex items-center gap-2 flex-wrap">
                                                <span className={`px-2 py-1 rounded text-xs font-medium ${status.color}`}>
                                                    <StatusIcon size={12} className="inline mr-1" />
                                                    {status.label}
                                                </span>
                                                <span className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                                                    {getCategoryLabel(complaint.category)}
                                                </span>
                                                {complaint.isAnonymous && (
                                                    <span className={`text-xs flex items-center gap-1 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                                                        <Lock size={10} /> Anónimo
                                                    </span>
                                                )}
                                            </div>
                                            <span className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>
                                                {complaint.date}
                                            </span>
                                        </div>
                                        <h3 className={`font-semibold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                            {complaint.subject}
                                        </h3>
                                        <p className={`text-sm mt-1 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                                            {complaint.description}
                                        </p>
                                        {complaint.response && (
                                            <div className={`mt-3 p-3 rounded-lg ${darkMode ? 'bg-green-900/20' : 'bg-green-50'}`}>
                                                <p className={`text-sm font-medium ${darkMode ? 'text-green-400' : 'text-green-700'}`}>
                                                    <MessageSquare size={14} className="inline mr-1" />
                                                    Respuesta ({complaint.resolvedDate}):
                                                </p>
                                                <p className={`text-sm mt-1 ${darkMode ? 'text-green-300' : 'text-green-600'}`}>
                                                    {complaint.response}
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

export default ComplaintsPage;
