import React from 'react';

const TareasPage = () => {
    const tasks = [
        { id: 1, title: 'Investigación Historia', course: 'Historia', dueDate: '2026-02-15', status: 'pending' },
        { id: 2, title: 'Ejercicios Álgebra', course: 'Matemáticas', dueDate: '2026-02-20', status: 'completed' },
    ];

    return (
        <div className="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
            <h1 className="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Mis Tareas</h1>

            <div className="grid gap-4">
                {tasks.map(task => (
                    <div key={task.id} className="p-4 border rounded hover:shadow-md transition bg-gray-50 dark:bg-gray-700">
                        <div className="flex justify-between items-center">
                            <div>
                                <h3 className="font-semibold text-lg">{task.title}</h3>
                                <p className="text-sm text-gray-600 dark:text-gray-300">{task.course}</p>
                            </div>
                            <div className="text-right">
                                <span className={`px-2 py-1 rounded text-xs ${task.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}`}>
                                    {task.status === 'completed' ? 'Entregada' : 'Pendiente'}
                                </span>
                                <p className="text-xs text-gray-500 mt-1">Vence: {task.dueDate}</p>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default TareasPage;
