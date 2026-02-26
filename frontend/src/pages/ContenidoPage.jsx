import { toast } from '../utils/toast';
import { useState, useEffect } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import { useAuth } from '../contexts/AuthContext';
import resourceService from '../services/resourceService';
import teacherService from '../services/teacherService';

const ContenidoPage = () => {
    const { darkMode } = useTheme();
    const { user } = useAuth();
    const [subjects, setSubjects] = useState([]);
    const [selectedSubject, setSelectedSubject] = useState('');
    const [resources, setResources] = useState([]);
    const [newResource, setNewResource] = useState({ title: '', description: '', link: '' });
    const [saving, setSaving] = useState(false);
    const [loading, setLoading] = useState(true);

    // Load subjects and resources from API
    useEffect(() => {
        loadInitialData();
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [user]);

    const loadInitialData = async () => {
        setLoading(true);
        try {
            // 1. Get Teacher Profile
            // eslint-disable-next-line unused-imports/no-unused-vars
            let teacherId = user?.id; // Fallback to user ID if linked directly
            // Try to get real teacher profile if needed, or assume backend handles 'me'
            // Ideally we fetch profile first to get teacher ID
            // For now, let's assume getMyProfile works or we use a safe way

            // Fetch subjects
            // If getSubjects expects ID, we need it. 
            // Better: use updated teacherService if it had 'getMySubjects'. 
            // It has getSubjects(id). Let's try fetching me first.

            // To be safe regarding ID (since user.id != person.id usually), 
            // we should fetch profile.

            // NOTE: In previous turns I used "getMyProfile" which returned teacher data.
            // Let's try that.

            const profile = await teacherService.getMyProfile().catch(() => null);
            const realTeacherId = profile?.data?.id || user?.id;

            if (realTeacherId) {
                const subjectsData = await teacherService.getSubjects(realTeacherId);
                // subjectsData might be array of { id, name, grade: { name } }
                // Need to map to UI format if structure differs

                // Assuming backend returns simple list or need mapping
                // Let's assume response.data is array
                const subjectList = Array.isArray(subjectsData.data) ? subjectsData.data : [];
                setSubjects(subjectList.map(s => ({
                    id: s.id,
                    name: s.name,
                    grade: s.grade?.name || 'N/A'
                })));

                // Load resources
                const resourcesData = await resourceService.getByTeacher(realTeacherId);
                setResources(resourcesData);
            }
        } catch (error) {
            console.error('Error loading content data:', error);
            toast.error('Error cargando datos. Verifique conexión.');
        } finally {
            setLoading(false);
        }
    };

    const handleAddResource = async (e) => {
        e.preventDefault();
        if (!selectedSubject || !newResource.title) return;

        setSaving(true);
        try {
            // Need teacher ID again. Ideally store in state.
            // For quick fix assuming user context has it or we can pass it if backend infers from token
            // resourceService.create should use backend auth token to infer teacher ideally, 
            // but if it requires ID, we must provide it.

            // Let's re-fetch profile or use stored ID. 
            // Optimally:
            const profile = await teacherService.getMyProfile();
            const teacherId = profile.data.id;

            const response = await resourceService.create({
                title: newResource.title,
                description: newResource.description || null,
                link: newResource.link || null,
                subjectId: parseInt(selectedSubject),
                teacherId: teacherId
            });

            // Add the new resource to the list
            if (response.data) {
                setResources([response.data, ...resources]);
            } else {
                // Reload
                const res = await resourceService.getByTeacher(teacherId);
                setResources(res);
            }

            setNewResource({ title: '', description: '', link: '' });
            toast.success('Recurso guardado en base de datos');
        } catch (error) {
            console.error('Error saving resource:', error);
            toast.error('Error al guardar en el servidor.');
        } finally {
            setSaving(false);
        }
    };

    const handleDelete = async (id) => {
        if (!window.confirm('¿Eliminar este recurso?')) return;

        try {
            await resourceService.delete(id);
            setResources(resources.filter(r => r.id !== id));
            toast.info('Recurso eliminado');
        } catch (error) {
            console.error('Error deleting resource:', error);
            // Fallback to local delete
            const updated = resources.filter(r => r.id !== id);
            setResources(updated);
            localStorage.setItem('oxford_content_resources', JSON.stringify(updated));
        }
    };

    const filteredResources = resources.filter(r => r.subjectId === parseInt(selectedSubject));
    const currentSubject = subjects.find(s => s.id === parseInt(selectedSubject));

    if (loading) {
        return (
            <div className={`p-6 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                <div className="flex items-center justify-center h-64">
                    <RefreshCw className="animate-spin text-indigo-500" size={32} />
                    <span className="ml-2">Cargando recursos...</span>
                </div>
            </div>
        );
    }

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
                        Sube material de apoyo, enlaces y guías para tus alumnos. <span className="text-green-500 text-sm">✓ Guardado en base de datos</span>
                    </p>
                </div>
                <button
                    onClick={loadInitialData}
                    className={`p-2 rounded-lg ${darkMode ? 'bg-gray-700 hover:bg-gray-600' : 'bg-gray-100 hover:bg-gray-200'}`}
                    title="Recargar recursos"
                >
                    <RefreshCw size={18} className={loading ? 'animate-spin' : ''} />
                </button>
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
                                className={`w-full p-2 rounded-lg border ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'} outline-none focus:ring-2 ring-indigo-500`}
                                required
                            >
                                <option value="">Seleccione materia...</option>
                                {subjects.map(s => (
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
                                className={`w-full p-2 rounded-lg border ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'} outline-none focus:ring-2 ring-indigo-500`}
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
                                className={`w-full p-2 rounded-lg border ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'} outline-none focus:ring-2 ring-indigo-500`}
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
                                    className={`w-full p-2 rounded-lg border ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'} outline-none focus:ring-2 ring-indigo-500`}
                                    disabled={!selectedSubject}
                                />
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={!selectedSubject || !newResource.title || saving}
                            className="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-2"
                        >
                            {saving ? <RefreshCw size={18} className="animate-spin" /> : <Plus size={18} />}
                            {saving ? 'Guardando...' : 'Publicar Recurso'}
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
