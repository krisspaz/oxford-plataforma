import React, { useState, useEffect } from 'react';
import { Book, Plus, Trash2, Link as LinkIcon, FileText, CheckCircle, AlertCircle } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';

const ContenidoPage = () => {
    const { darkMode } = useTheme();
    const [mockSubjects, setMockSubjects] = useState([]);
    const [selectedSubject, setSelectedSubject] = useState('');
    const [resources, setResources] = useState([]);
    const [newResource, setNewResource] = useState({ title: '', description: '', link: '' });
    const [saving, setSaving] = useState(false);

    // Load mock subjects and existing data from localStorage
    useEffect(() => {
        // Mock data usually fetched from teacherService.getAssignments()
        const subjects = [
            { id: 1, name: 'Matemáticas', grade: '1ro Básico A' },
            { id: 2, name: 'Ciencias Naturales', grade: '2do Básico B' },
            { id: 3, name: 'Idioma Español', grade: '3ro Primaria A' }
        ];
        setMockSubjects(subjects);

        // Load saved resources
        const saved = localStorage.getItem('oxford_content_resources');
        if (saved) {
            setResources(JSON.parse(saved));
        }
    }, []);

    const handleAddResource = (e) => {
        e.preventDefault();
        if (!selectedSubject || !newResource.title) return;

        const resource = {
            id: Date.now(),
            subjectId: parseInt(selectedSubject),
            ...newResource,
            date: new Date().toISOString()
        };

        const updated = [resource, ...resources];
        setResources(updated);
        localStorage.setItem('oxford_content_resources', JSON.stringify(updated));

        setNewResource({ title: '', description: '', link: '' });
        alert('Recurso añadido correctamente');
    };

    const handleDelete = (id) => {
        if (window.confirm('¿Eliminar este recurso?')) {
            const updated = resources.filter(r => r.id !== id);
            setResources(updated);
            localStorage.setItem('oxford_content_resources', JSON.stringify(updated));
        }
    };

    const filteredResources = resources.filter(r => r.subjectId === parseInt(selectedSubject));
    const currentSubject = mockSubjects.find(s => s.id === parseInt(selectedSubject));

    return (
        <div className={`p-6 ${darkMode ? 'text-white' : 'text-gray-900'} space-y-6`}>

            {/* Header */}
            <div className="flex justify-between items-center">
                <div>
                    <h1 className="text-2xl font-bold flex items-center gap-2">
                        <Book className="text-indigo-500" />
                        Asignar Contenido
                    </h1>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-600'}>
                        Sube material de apoyo, enlaces y guías para tus alumnos.
                    </p>
                </div>
            </div>

            {/* Selection & Form */}
            <div className={`grid grid-cols-1 lg:grid-cols-3 gap-6`}>

                {/* Left: Input Form */}
                <div className={`lg:col-span-1 p-6 rounded-2xl shadow-sm border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                    <h3 className="font-bold mb-4 text-lg">Nuevo Recurso</h3>

                    <form onSubmit={handleAddResource} className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium mb-1">Materia</label>
                            <select
                                value={selectedSubject}
                                onChange={e => setSelectedSubject(e.target.value)}
                                className={`w-full p-2 rounded-lg border ${darkMode ? 'bg-gray-700 border-gray-600' : 'bg-gray-50 border-gray-200'} outline-none focus:ring-2 ring-indigo-500`}
                                required
                            >
                                <option value="">Seleccione materia...</option>
                                {mockSubjects.map(s => (
                                    <option key={s.id} value={s.id}>{s.name} - {s.grade}</option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-1">Título del Recurso</label>
                            <input
                                type="text"
                                value={newResource.title}
                                onChange={e => setNewResource({ ...newResource, title: e.target.value })}
                                placeholder="Ej. Guía de Álgebra"
                                className={`w-full p-2 rounded-lg border ${darkMode ? 'bg-gray-700 border-gray-600' : 'bg-gray-50 border-gray-200'} outline-none focus:ring-2 ring-indigo-500`}
                                required
                                disabled={!selectedSubject}
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-1">Descripción (Opcional)</label>
                            <textarea
                                value={newResource.description}
                                onChange={e => setNewResource({ ...newResource, description: e.target.value })}
                                placeholder="Instrucciones para el alumno..."
                                className={`w-full p-2 rounded-lg border ${darkMode ? 'bg-gray-700 border-gray-600' : 'bg-gray-50 border-gray-200'} outline-none focus:ring-2 ring-indigo-500`}
                                rows="3"
                                disabled={!selectedSubject}
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-1">Enlace / URL</label>
                            <div className="flex gap-2">
                                <div className={`p-2 rounded-lg ${darkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>
                                    <LinkIcon size={20} className="text-gray-500" />
                                </div>
                                <input
                                    type="url"
                                    value={newResource.link}
                                    onChange={e => setNewResource({ ...newResource, link: e.target.value })}
                                    placeholder="https://..."
                                    className={`w-full p-2 rounded-lg border ${darkMode ? 'bg-gray-700 border-gray-600' : 'bg-gray-50 border-gray-200'} outline-none focus:ring-2 ring-indigo-500`}
                                    disabled={!selectedSubject}
                                />
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={!selectedSubject || !newResource.title}
                            className="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-2"
                        >
                            <Plus size={18} /> Publicar Recurso
                        </button>
                    </form>
                </div>

                {/* Right: List */}
                <div className="lg:col-span-2 space-y-4">
                    {!selectedSubject ? (
                        <div className={`h-full flex flex-col items-center justify-center p-12 rounded-2xl border border-dashed ${darkMode ? 'border-gray-700 text-gray-400' : 'border-gray-300 text-gray-500'}`}>
                            <Book size={48} className="mb-4 opacity-20" />
                            <p>Selecciona una materia para ver y gestionar su contenido.</p>
                        </div>
                    ) : (
                        <>
                            <div className="flex justify-between items-center">
                                <h2 className="font-bold text-lg">
                                    Contenido: <span className="text-indigo-500">{currentSubject?.name}</span>
                                </h2>
                                <span className={`text-xs px-2 py-1 rounded-full ${darkMode ? 'bg-indigo-900/40 text-indigo-400' : 'bg-indigo-100 text-indigo-600'}`}>
                                    {filteredResources.length} recursos
                                </span>
                            </div>

                            {filteredResources.length === 0 ? (
                                <div className={`p-8 text-center rounded-xl border ${darkMode ? 'bg-gray-800/50 border-gray-700' : 'bg-gray-50 border-gray-200'}`}>
                                    <p className="text-gray-500">No has publicado contenido para esta clase aún.</p>
                                </div>
                            ) : (
                                <div className="space-y-3">
                                    {filteredResources.map(resource => (
                                        <div key={resource.id} className={`p-4 rounded-xl border flex gap-4 transition-all hover:scale-[1.01] ${darkMode ? 'bg-gray-800 border-gray-700 hover:border-indigo-500/50' : 'bg-white border-gray-100 hover:shadow-md'}`}>
                                            <div className="p-3 bg-indigo-500/10 rounded-lg h-fit text-indigo-500">
                                                {resource.link ? <LinkIcon size={24} /> : <FileText size={24} />}
                                            </div>
                                            <div className="flex-1">
                                                <div className="flex justify-between items-start">
                                                    <h3 className="font-bold">{resource.title}</h3>
                                                    <button onClick={() => handleDelete(resource.id)} className="text-gray-400 hover:text-red-500 transition-colors">
                                                        <Trash2 size={16} />
                                                    </button>
                                                </div>
                                                {resource.description && (
                                                    <p className={`text-sm mt-1 ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>{resource.description}</p>
                                                )}
                                                <div className="flex items-center gap-4 mt-3 text-xs text-gray-500">
                                                    <span>{new Date(resource.date).toLocaleDateString()}</span>
                                                    {resource.link && (
                                                        <a href={resource.link} target="_blank" rel="noopener noreferrer" className="text-indigo-500 hover:underline flex items-center gap-1">
                                                            Abrir enlace <LinkIcon size={12} />
                                                        </a>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </>
                    )}
                </div>
            </div>
        </div>
    );
};

export default ContenidoPage;
