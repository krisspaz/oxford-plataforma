import React, { useState, useEffect } from 'react';
import { RefreshCw, AlertCircle, CheckCircle, Clock, FileText } from 'lucide-react';
import { taskService } from '../services';

const TareasPage = () => {
    const [tasks, setTasks] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchTasks();
    }, []);

    const fetchTasks = async () => {
        try {
            const data = await taskService.getMyTasks();
            setTasks(data || []);
        } catch (error) {
            console.error("Error loading tasks", error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <div className="p-12 text-center text-gray-500">
                <RefreshCw className="animate-spin mx-auto mb-2" />
                Cargando mis tareas...
            </div>
        );
    }

    return (
        <div className="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
            <h1 className="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Mis Tareas</h1>

            {tasks.length === 0 ? (
                <div className="text-center py-10 text-gray-500">
                    <FileText size={48} className="mx-auto mb-3 opacity-50" />
                    <p>No tienes tareas pendientes.</p>
                </div>
            ) : (
                <div className="grid gap-4">
                    {tasks.map(task => {
                        const isCompleted = task.mySubmission && task.mySubmission.status !== 'pending'; // Adjust based on statuses
                        // Actually mySubmission might be null if not submitted.
                        // status in submission: 'graded', 'submitted', 'late', etc.
                        const submissionStatus = task.mySubmission ? task.mySubmission.status : 'pending';

                        return (
                            <div
                                key={task.id}
                                onClick={() => window.location.href = `/mis-tareas/${task.id}`} // Using simple navigation for now or import useNavigate
                                className="p-4 border rounded hover:shadow-md transition bg-gray-50 dark:bg-gray-700 cursor-pointer"
                            >
                                <div className="flex justify-between items-center">
                                    <div>
                                        <h3 className="font-semibold text-lg dark:text-gray-200">{task.title}</h3>
                                        <p className="text-sm text-gray-600 dark:text-gray-400">{task.subject?.name} • {task.type}</p>
                                    </div>
                                    <div className="text-right">
                                        <span className={`px-2 py-1 rounded text-xs inline-flex items-center gap-1
                                            ${submissionStatus !== 'pending' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}`}>
                                            {submissionStatus !== 'pending' ? <CheckCircle size={12} /> : <Clock size={12} />}
                                            {submissionStatus === 'pending' ? 'Pendiente' : 'Entregada'}
                                        </span>
                                        <p className="text-xs text-gray-500 mt-1">Vence: {task.dueDate}</p>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
};

export default TareasPage;
