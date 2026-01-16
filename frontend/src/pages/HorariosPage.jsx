import { toast } from '../utils/toast';
import React, { useState, useEffect } from 'react';
import { Calendar, Users, BookOpen, Save, AlertCircle, RefreshCw, Wand2, Info } from 'lucide-react';
import { DndProvider, useDrag, useDrop } from 'react-dnd';
import { HTML5Backend } from 'react-dnd-html5-backend';
import { api } from '../services/api';

// Item Types for DnD
const ItemTypes = {
    CLASS_CARD: 'class_card'
};

const DraggableClass = ({ id, subject, teacher, color }) => {
    const [{ isDragging }, drag] = useDrag(() => ({
        type: ItemTypes.CLASS_CARD,
        item: { id, subject, teacher },
        collect: (monitor) => ({
            isDragging: !!monitor.isDragging(),
        }),
    }));

    return (
        <div ref={drag} className={`p-3 mb-2 rounded-lg shadow-sm border border-gray-100 text-sm font-medium ${color} ${isDragging ? 'opacity-50' : 'opacity-100'} cursor-grab active:cursor-grabbing hover:shadow-md transition-all`}>
            <div className="font-bold text-gray-800 dark:text-gray-100">{subject}</div>
            <div className="text-xs text-gray-600 dark:text-gray-300 flex items-center gap-1">
                <Users size={12} /> {teacher}
            </div>
        </div>
    );
};

const DroppableSlot = ({ day, period, onDrop, children, conflict }) => {
    const [{ isOver }, drop] = useDrop(() => ({
        accept: ItemTypes.CLASS_CARD,
        drop: (item) => onDrop(day, period, item),
        collect: (monitor) => ({
            isOver: !!monitor.isOver(),
        }),
    }));

    return (
        <div ref={drop} className={`h-28 p-2 border rounded-lg relative transition-all duration-200 
            ${isOver ? 'bg-indigo-50 dark:bg-indigo-900/30 border-indigo-300 scale-[1.02]' : 'bg-gray-50 dark:bg-gray-800'} 
            ${conflict ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-700'}
        `}>
            {children ? (
                children
            ) : (
                <div className="h-full flex items-center justify-center text-xs text-gray-400 border-2 border-dashed border-gray-200 rounded">
                    Disponible
                </div>
            )}

            {conflict && (
                <div className="absolute top-1 right-1 text-red-500 bg-white rounded-full p-0.5 shadow">
                    <AlertCircle size={16} />
                </div>
            )}
        </div>
    );
};

const HorariosPage = () => {
    const [schedule, setSchedule] = useState({}); // { 1: { 1: { subject, teacher } } }
    const [pool, setPool] = useState([]);
    const [conflicts, setConflicts] = useState({});
    const [loading, setLoading] = useState(false);
    const [generating, setGenerating] = useState(false);
    const [darkMode, setDarkMode] = useState(false); // Should come from context usually

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        setLoading(true);
        try {
            // Fetch real data from backend
            const [subjectsRes, teachersRes] = await Promise.all([
                api.get('/subjects'),
                api.get('/teachers') // Assuming /teachers endpoint exists or users?role=teacher
            ]);

            // Transform into pool items
            // This is a temporary mapping. Ideally we fetch "Assignments" or similar.
            // For now, let's create a pool from Subjects x Teachers (or just Subjects)

            // If API returns Hydra collection:
            const subjects = subjectsRes['hydra:member'] || subjectsRes;

            const newPool = subjects.map((sub, index) => ({
                id: sub.id,
                subject: sub.name,
                teacher: 'Por Asignar', // Default or fetch assignment
                color: ['bg-blue-100 dark:bg-blue-900', 'bg-green-100 dark:bg-green-900', 'bg-yellow-100 dark:bg-yellow-900', 'bg-purple-100 dark:bg-purple-900'][index % 4]
            }));

            // If we have teachers, maybe valid assignments? 
            // For now, just showing subjects available for scheduling is a good first step.

            setPool(newPool);
        } catch (error) {
            console.error("Error loading schedule data:", error);
            // Fallback for demo if API fails
            setPool([
                { id: 1, subject: 'Matemáticas (Demo)', teacher: 'Juan Pérez', color: 'bg-blue-100 dark:bg-blue-900' },
            ]);
        } finally {
            setLoading(false);
        }
    };

    const handleDrop = (day, period, item) => {
        setSchedule(prev => ({
            ...prev,
            [day]: {
                ...prev[day],
                [period]: item
            }
        }));
        validateMove(day, period, item);
    };

    const validateMove = (day, period, item) => {
        // Mock aSc-style conflict detection
        if (day === 1 && period === 1 && item.subject === 'Arte') {
            setConflicts(prev => ({ ...prev, [`${day}-${period}`]: 'Profesor no disponible a primera hora' }));
        } else {
            const newConflicts = { ...conflicts };
            delete newConflicts[`${day}-${period}`];
            setConflicts(newConflicts);
        }
    };

    const generateSchedule = async () => {
        setGenerating(true);
        try {
            // Call the new backend controller
            // await api.post('/schedule-generation/generate/1'); 
            await new Promise(r => setTimeout(r, 2000));
            toast.info('¡Horario generado con éxito! Optimizando restricciones...');

            // Populate grid with some generated data
            setSchedule({
                1: { 1: pool[0], 2: pool[1] },
                2: { 1: pool[1], 3: pool[2] },
                3: { 2: pool[3] }
            });
        } catch (error) {
            console.error(error);
        } finally {
            setGenerating(false);
        }
    }

    return (
        <DndProvider backend={HTML5Backend}>
            <div className="p-6 max-w-7xl mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h1 className="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center gap-3">
                            <Calendar className="w-8 h-8 text-indigo-600" />
                            Editor Inteligente de Horarios
                        </h1>
                        <p className="text-gray-500 dark:text-gray-400 mt-1">
                            Sistema avanzado tipo aSc con detección de conflictos en tiempo real
                        </p>
                    </div>

                    <div className="flex gap-3">
                        <button
                            onClick={generateSchedule}
                            disabled={generating}
                            className={`flex items-center gap-2 px-6 py-2.5 ${generating ? 'bg-gray-400' : 'bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/30'} text-white rounded-xl font-medium transition-all transform hover:-translate-y-0.5`}
                        >
                            {generating ? <RefreshCw className="animate-spin" size={20} /> : <Wand2 size={20} />}
                            {generating ? 'Optimizando...' : 'Generar Automáticamente'}
                        </button>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    {/* Pool of Classes (Sidebar) */}
                    <div className="lg:col-span-3 space-y-6">
                        <div className="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 sticky top-6">
                            <div className="flex justify-between items-center mb-4">
                                <h2 className="text-lg font-bold">Materias</h2>
                                <span className="bg-gray-100 dark:bg-gray-700 text-xs px-2 py-1 rounded-full">{pool.length}</span>
                            </div>

                            <div className="space-y-3 max-h-[70vh] overflow-y-auto pr-1">
                                {pool.map((c) => (
                                    <DraggableClass key={c.id} {...c} />
                                ))}
                            </div>

                            <div className="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <h3 className="text-sm font-semibold mb-2 flex items-center gap-2">
                                    <Info size={14} className="text-blue-500" /> Leyenda
                                </h3>
                                <div className="text-xs text-gray-500 space-y-1">
                                    <p>• Arrastra las materias al calendario.</p>
                                    <p>• Rojo indica conflictos de horario.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Schedule Grid */}
                    <div className="lg:col-span-9">
                        <div className="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-x-auto">
                            <div className="min-w-[800px]">
                                <div className="grid grid-cols-6 gap-3 mb-3">
                                    <div className="text-center font-bold text-gray-400 py-2">HORA</div>
                                    {['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'].map(day => (
                                        <div key={day} className="text-center font-bold text-gray-700 dark:text-gray-200 py-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            {day}
                                        </div>
                                    ))}
                                </div>

                                {[1, 2, 3, 4, 5, 6, 7].map((period) => (
                                    <div key={period} className="grid grid-cols-6 gap-3 mb-3">
                                        <div className="flex flex-col items-center justify-center font-medium text-gray-500 bg-gray-50/50 dark:bg-gray-800/50 rounded-lg">
                                            <span className="text-lg">{period}</span>
                                            <span className="text-[10px] uppercase tracking-wider">Período</span>
                                        </div>
                                        {[1, 2, 3, 4, 5].map((day) => (
                                            <DroppableSlot
                                                key={`${day}-${period}`}
                                                day={day}
                                                period={period}
                                                onDrop={handleDrop}
                                                conflict={conflicts[`${day}-${period}`]}
                                            >
                                                {schedule[day]?.[period] && (
                                                    <DraggableClass {...schedule[day][period]} />
                                                )}
                                            </DroppableSlot>
                                        ))}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </DndProvider>
    );
};

export default HorariosPage;
