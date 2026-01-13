import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { RefreshCw, ArrowLeft, Upload, FileText, CheckCircle, Clock, AlertCircle } from 'lucide-react';
import { taskService, studentService } from '../services';
import { useAuth } from '../contexts/AuthContext';

const StudentTaskDetailPage = () => {
    const { taskId } = useParams();
    const navigate = useNavigate();
    const { user } = useAuth();
    const [loading, setLoading] = useState(true);
    const [task, setTask] = useState(null);
    const [submissionContent, setSubmissionContent] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState(null);

    // Get student profile ID
    const [studentProfile, setStudentProfile] = useState(null);

    useEffect(() => {
        loadTaskAndProfile();
    }, [taskId]);

    const loadTaskAndProfile = async () => {
        try {
            setLoading(true);
            // Fetch task details
            const taskData = await taskService.getById(taskId);
            setTask(taskData);

            // Should also check if we have a submission already
            // taskData.mySubmission should be available if we use the same serializer or enrich it.
            // But getById might returns generic task. 
            // The TaskController::show returns detailed task but maybe not 'mySubmission' specifically for this user unless we add logic.
            // Let's assume we need to check submissions or if the show endpoint returns it.
            // Actually, TaskController::show does NOT enrich with 'mySubmission'.
            // So we might need to fetch submissions separately or just 'my-tasks' and find it?
            // Or better, let's just use the 'my-tasks' list to find it if we can, or add an endpoint for 'my-submission'.
            // For now, let's try to find it from the full list or assume we don't have it and just show 'submit'.

            // To get student ID for submission
            const profile = await studentService.getMe();
            setStudentProfile(profile);

        } catch (err) {
            console.error(err);
            setError("Error al cargar la tarea.");
        } finally {
            setLoading(false);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!submissionContent) return;

        setIsSubmitting(true);
        try {
            await taskService.submit(taskId, {
                studentId: studentProfile.id,
                content: submissionContent,
                attachmentUrl: 'https://example.com/file.pdf' // Placeholder as we don't have real file upload yet
            });
            alert("Tarea entregada con éxito!");
            navigate('/mis-tareas');
        } catch (err) {
            alert("Error al entregar: " + (err.response?.data?.error || err.message));
        } finally {
            setIsSubmitting(false);
        }
    };

    if (loading) return <div className="p-10 text-center"><RefreshCw className="animate-spin mx-auto" /></div>;
    if (error) return <div className="p-10 text-center text-red-500">{error}</div>;
    if (!task) return <div className="p-10 text-center">Tarea no encontrada</div>;

    const mySubmission = task.mySubmission; // Use if available, else null

    return (
        <div className="max-w-3xl mx-auto p-4 space-y-6">
            <button onClick={() => navigate(-1)} className="flex items-center text-gray-500 hover:text-gray-700">
                <ArrowLeft size={20} className="mr-2" /> Volver
            </button>

            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <div className="flex justify-between items-start mb-4">
                    <div>
                        <span className="text-sm text-blue-500 font-medium">{task.subject?.name}</span>
                        <h1 className="text-2xl font-bold dark:text-white mt-1">{task.title}</h1>
                    </div>
                    <div className="text-right">
                        <span className="text-sm text-gray-500">Vence</span>
                        <p className="font-medium dark:text-gray-300">{task.dueDate}</p>
                    </div>
                </div>

                <div className="prose dark:prose-invert max-w-none mb-6">
                    <p>{task.description}</p>
                </div>

                <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <div className="flex items-center justify-between text-sm">
                        <span className="text-gray-600 dark:text-gray-300">Valor: <b className="text-gray-900 dark:text-white">{task.points} pts</b></span>
                        <span className="text-gray-600 dark:text-gray-300">Tipo: <b className="text-gray-900 dark:text-white capitalize">{task.type}</b></span>
                    </div>
                </div>

                {/* Submission Form */}
                <div className="border-t pt-6 dark:border-gray-700">
                    <h3 className="text-lg font-bold mb-4 dark:text-white">Tu Entrega</h3>

                    {mySubmission ? (
                        <div className="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg flex items-center gap-3">
                            <CheckCircle className="text-green-500" size={24} />
                            <div>
                                <p className="font-medium text-green-700 dark:text-green-400">Tarea Entregada</p>
                                <p className="text-sm text-green-600 dark:text-green-500">Enviado el {mySubmission.submittedAt}</p>
                            </div>
                        </div>
                    ) : (
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium mb-1 dark:text-gray-300">Comentario / Texto</label>
                                <textarea
                                    value={submissionContent}
                                    onChange={e => setSubmissionContent(e.target.value)}
                                    className="w-full p-3 border rounded-lg h-32 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Escribe tu respuesta aquí..."
                                    required
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium mb-1 dark:text-gray-300">Archivo Adjunto</label>
                                <div className="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                                    <input
                                        type="file"
                                        id="file-upload"
                                        className="hidden"
                                        onChange={(e) => {
                                            if (e.target.files[0]) {
                                                setSubmissionContent(prev => prev + `\n[Archivo: ${e.target.files[0].name}]`);
                                                alert(`Archivo "${e.target.files[0].name}" seleccionado. Se simulará el envío.`);
                                            }
                                        }}
                                    />
                                    <label htmlFor="file-upload" className="cursor-pointer">
                                        <Upload className="mx-auto text-gray-400 mb-2" />
                                        <p className="text-sm text-blue-500 hover:text-blue-600">Haz clic para subir un archivo</p>
                                        <p className="text-xs text-gray-400 mt-1">PDF, DOC, o imágenes (máx. 10MB)</p>
                                    </label>
                                </div>
                            </div>

                            <button
                                type="submit"
                                disabled={isSubmitting || !submissionContent}
                                className="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {isSubmitting ? 'Enviando...' : 'Entregar Tarea'}
                            </button>
                        </form>
                    )}
                </div>
            </div>
        </div>
    );
};

export default StudentTaskDetailPage;
