import React, { useState, useEffect } from 'react';
import { Star, User, AlertCircle, Trash2, Award, TrendingUp } from 'lucide-react';
import { Card, CardContent } from '@/components/ui/card';
import { teacherRatingService, teacherService } from '@/services';
import { toast } from 'react-hot-toast';

const TeacherRatingsPage = () => {
    const [ratings, setRatings] = useState([]);
    const [teachers, setTeachers] = useState([]);
    const [loading, setLoading] = useState(true);

    // Load data
    const loadData = async () => {
        setLoading(true);
        try {
            const [ratingsData, teachersData] = await Promise.all([
                teacherRatingService.getAll(),
                teacherService.getAll()
            ]);
            setRatings(ratingsData);
            setTeachers(teachersData);
        } catch (error) {
            console.error(error);
            toast.error('Error al cargar datos');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadData();
    }, []);

    // Process Data: Group by Teacher
    const processRatings = () => {
        const stats = {};

        // Initialize stats for known teachers
        teachers.forEach(t => {
            stats[t.id] = {
                id: t.id,
                name: `${t.firstName || ''} ${t.lastName || ''}`.trim() || t.fullName || 'Desconocido',
                total: 0,
                sum: 0,
                avg: 0,
                reviews: []
            };
        });

        // Aggregate ratings
        ratings.forEach(r => {
            const teacherId = typeof r.teacher === 'object' ? r.teacher.id : (r.teacher ? r.teacher.split('/').pop() : null);
            if (teacherId && !stats[teacherId]) {
                // Teacher might not be in the list if deleted or mismatch
                stats[teacherId] = { id: teacherId, name: 'Docente #' + teacherId, total: 0, sum: 0, avg: 0, reviews: [] };
            }

            if (teacherId) {
                stats[teacherId].total += 1;
                stats[teacherId].sum += r.rating;
                stats[teacherId].reviews.push(r);
            }
        });

        // Calculate Averages
        Object.values(stats).forEach(s => {
            if (s.total > 0) s.avg = (s.sum / s.total).toFixed(1);
        });

        return Object.values(stats).sort((a, b) => b.avg - a.avg);
    };

    const teacherStats = processRatings();
    const topTeacher = teacherStats.length > 0 ? teacherStats[0] : null;

    const handleDeleteReview = async (id) => {
        if (!confirm('¿Eliminar esta reseña?')) return;
        try {
            await teacherRatingService.delete(id);
            toast.success('Reseña eliminada');
            loadData();
        } catch (e) {
            toast.error('Error al eliminar');
        }
    };

    const renderStars = (score) => {
        return (
            <div className="flex gap-0.5">
                {[1, 2, 3, 4, 5].map(i => (
                    <Star
                        key={i}
                        className={`w-4 h-4 ${i <= score ? 'fill-yellow-400 text-yellow-400' : 'text-slate-600'}`}
                    />
                ))}
            </div>
        );
    };

    return (
        <div className="p-6 space-y-6 bg-slate-950 min-h-screen text-slate-100 font-mono">
            <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 className="text-3xl font-bold flex items-center gap-3 text-yellow-400">
                        <Star className="w-8 h-8 fill-yellow-400" />
                        EVALUACIÓN DOCENTE
                    </h1>
                    <p className="text-slate-400 mt-1">Monitoreo de Calidad Académica</p>
                </div>

                <div className="bg-slate-900 border border-slate-800 rounded-lg p-3 flex gap-4">
                    <div className="text-center">
                        <div className="text-2xl font-bold text-white">{ratings.length}</div>
                        <div className="text-xs text-slate-500 uppercase">Reseñas</div>
                    </div>
                    <div className="w-px bg-slate-800"></div>
                    <div className="text-center">
                        <div className="text-2xl font-bold text-emerald-400">
                            {(ratings.reduce((acc, r) => acc + r.rating, 0) / (ratings.length || 1)).toFixed(1)}
                        </div>
                        <div className="text-xs text-slate-500 uppercase">Promedio Global</div>
                    </div>
                </div>
            </div>

            {/* Top Performer Banner */}
            {topTeacher && topTeacher.total > 0 && (
                <div className="relative overflow-hidden bg-gradient-to-r from-yellow-500/20 to-purple-500/20 border border-yellow-500/30 rounded-xl p-6 flex items-center gap-6">
                    <div className="bg-yellow-500/20 p-4 rounded-full border border-yellow-500/50">
                        <Award className="w-8 h-8 text-yellow-400" />
                    </div>
                    <div>
                        <h3 className="text-yellow-200 text-sm uppercase font-bold tracking-wider">Docente Mejor Evaluado</h3>
                        <h2 className="text-3xl font-bold text-white mt-1">{topTeacher.name}</h2>
                        <div className="flex items-center gap-2 mt-2">
                            {renderStars(Math.round(topTeacher.avg))}
                            <span className="text-lg font-bold text-yellow-500">{topTeacher.avg}</span>
                            <span className="text-slate-400 text-sm">({topTeacher.total} reseñas)</span>
                        </div>
                    </div>
                </div>
            )}

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {teacherStats.filter(t => t.total > 0).map(stat => (
                    <Card key={stat.id} className="bg-slate-900 border-slate-800 hover:border-slate-700 transition-all flex flex-col h-full">
                        <div className="p-4 border-b border-slate-800 flex justify-between items-start bg-slate-900/50">
                            <div>
                                <h3 className="font-bold text-lg text-slate-200">{stat.name}</h3>
                                <div className="flex items-center gap-2 mt-1">
                                    <span className={`text-xl font-bold ${stat.avg >= 4 ? 'text-emerald-400' : stat.avg >= 3 ? 'text-yellow-400' : 'text-red-400'}`}>
                                        {stat.avg}
                                    </span>
                                    {renderStars(Math.round(stat.avg))}
                                </div>
                            </div>
                            <div className="bg-slate-800 px-2 py-1 rounded text-xs text-slate-400">
                                {stat.total} evals
                            </div>
                        </div>

                        <CardContent className="flex-1 p-0 overflow-hidden">
                            <div className="max-h-64 overflow-y-auto custom-scrollbar p-4 space-y-3">
                                {stat.reviews.map(review => (
                                    <div key={review.id} className="text-sm bg-slate-950 p-3 rounded border border-slate-800/50 relative group">
                                        <div className="flex justify-between mb-1">
                                            <div className="flex gap-1">
                                                {[...Array(review.rating)].map((_, i) => (
                                                    <Star key={i} className="w-3 h-3 fill-slate-500 text-slate-500" />
                                                ))}
                                            </div>
                                            <button
                                                onClick={() => handleDeleteReview(review.id)}
                                                className="opacity-0 group-hover:opacity-100 text-red-500 hover:text-red-400 transition-opacity"
                                            >
                                                <Trash2 className="w-3 h-3" />
                                            </button>
                                        </div>
                                        <p className="text-slate-400 italic">"{review.comment || 'Sin comentario'}"</p>
                                        <div className="mt-2 text-[10px] text-slate-600 text-right">
                                            {new Date(review.createdAt).toLocaleDateString()}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                ))}
            </div>
        </div>
    );
};

export default TeacherRatingsPage;
