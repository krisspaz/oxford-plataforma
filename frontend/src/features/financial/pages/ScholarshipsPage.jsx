import React, { useState, useEffect } from 'react';
import { Award, Plus, Trash2, Edit2, UserPlus, CheckCircle, Search, DollarSign, Percent } from 'lucide-react';
import { Card, CardContent } from '@/components/ui/card';
import { scholarshipService, studentService } from '@/services';
import { toast } from '@/utils/toast';

const ScholarshipsPage = () => {
    const [scholarships, setScholarships] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [showAssignModal, setShowAssignModal] = useState(false);
    const [selectedScholarship, setSelectedScholarship] = useState(null);
    const [studentQuery, setStudentQuery] = useState('');

    // Form State
    const [formData, setFormData] = useState({
        name: '',
        type: 'PERCENTAGE', // PERCENTAGE or FIXED
        value: '',
        description: ''
    });

    const loadData = async () => {
        setLoading(true);
        try {
            const res = await scholarshipService.getAll();
            if (res.success) {
                let data = res.data;
                // Normalize Hydra/JSON-LD response
                if (data && !Array.isArray(data)) {
                    if (Array.isArray(data['hydra:member'])) {
                        data = data['hydra:member'];
                    } else if (Array.isArray(data.member)) {
                        data = data.member;
                    } else {
                        // Fallback or empty if structure is unknown
                        data = [];
                    }
                }
                setScholarships(Array.isArray(data) ? data : []);
            }
        } catch (error) {
            console.error(error);
            toast.error('Error al cargar convenios');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadData();
    }, []);

    const handleCreate = async (e) => {
        e.preventDefault();
        try {
            await scholarshipService.create(formData);
            toast.success('Convenio creado exitosamente');
            setShowModal(false);
            setFormData({ name: '', type: 'PERCENTAGE', value: '', description: '' });
            loadData();
        } catch (error) {
            toast.error('Error al crear');
        }
    };

    const handleDelete = async (id) => {
        if (!confirm('¿Desactivar este convenio?')) return;
        try {
            await scholarshipService.delete(id);
            toast.success('Convenio desactivado');
            loadData();
        } catch (error) {
            toast.error('Error al eliminar');
        }
    };

    const handleAssign = async (studentId) => {
        if (!selectedScholarship) return;
        try {
            const res = await scholarshipService.assignToStudent(studentId, selectedScholarship.id);
            if (res.success) {
                toast.success(`Asignado a estudiante #${studentId}`);
                setShowAssignModal(false);
            } else {
                toast.error(res.message || 'Error al asignar');
            }
        } catch (error) {
            toast.error('Error de conexión');
        }
    };

    // Placeholder for student search
    const StudentSearch = () => {
        const [idInput, setIdInput] = useState('');

        return (
            <div className="space-y-4">
                <div className="flex gap-2">
                    <input
                        type="number"
                        placeholder="ID Estudiante (Temporal)"
                        className="flex-1 bg-slate-800 border-slate-700 rounded p-2 text-white"
                        value={idInput}
                        onChange={(e) => setIdInput(e.target.value)}
                    />
                    <button
                        onClick={() => handleAssign(idInput)}
                        className="bg-purple-600 hover:bg-purple-500 text-white px-4 rounded font-bold"
                    >
                        Asignar
                    </button>
                </div>
                <p className="text-xs text-slate-500">* En el futuro se implementará búsqueda por nombre.</p>
            </div>
        );
    };

    return (
        <div className="p-6 space-y-6 bg-slate-950 min-h-screen text-slate-100 font-mono">
            <div className="flex justify-between items-center">
                <div>
                    <h1 className="text-3xl font-bold flex items-center gap-3 text-blue-400">
                        <Award className="w-8 h-8" />
                        BECAS Y CONVENIOS
                    </h1>
                    <p className="text-slate-400 mt-1">Gestión Financiera de Descuentos</p>
                </div>
                <button
                    onClick={() => setShowModal(true)}
                    className="flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg shadow-blue-900/20 transition-all"
                >
                    <Plus className="w-5 h-5" />
                    NUEVO CONVENIO
                </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {scholarships.map(s => (
                    <Card key={s.id} className="bg-slate-900 border-slate-800 hover:border-blue-500/50 transition-all group relative overflow-hidden">
                        <div className="absolute top-0 right-0 p-4 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                            <button onClick={() => handleDelete(s.id)} className="p-2 bg-red-500/20 text-red-400 rounded hover:bg-red-500 hover:text-white transition-colors">
                                <Trash2 className="w-4 h-4" />
                            </button>
                        </div>

                        <CardContent className="p-6 space-y-4">
                            <div className="flex justify-between items-start">
                                <div className={`p-3 rounded-xl ${s.type === 'PERCENTAGE' ? 'bg-purple-500/20 text-purple-400' : 'bg-emerald-500/20 text-emerald-400'}`}>
                                    {s.type === 'PERCENTAGE' ? <Percent className="w-6 h-6" /> : <DollarSign className="w-6 h-6" />}
                                </div>
                                <div className="text-right">
                                    <span className="text-2xl font-bold text-white">
                                        {s.type === 'PERCENTAGE' ? `${s.value}%` : `Q${s.value}`}
                                    </span>
                                    <div className="text-xs text-slate-500 uppercase">Descuento</div>
                                </div>
                            </div>

                            <div>
                                <h3 className="text-xl font-bold text-slate-200">{s.name}</h3>
                                <p className="text-sm text-slate-500 mt-1 line-clamp-2">{s.description || 'Sin descripción'}</p>
                            </div>

                            <button
                                onClick={() => { setSelectedScholarship(s); setShowAssignModal(true); }}
                                className="w-full mt-4 py-2 border border-slate-700 rounded text-slate-300 hover:bg-slate-800 hover:text-white transition-colors flex items-center justify-center gap-2"
                            >
                                <UserPlus className="w-4 h-4" />
                                Asignar a Estudiante
                            </button>
                        </CardContent>
                    </Card>
                ))}
            </div>

            {/* CREATE MODAL */}
            {showModal && (
                <div className="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                    <div className="bg-slate-900 border border-slate-800 rounded-xl p-6 w-full max-w-md space-y-4">
                        <h2 className="text-xl font-bold text-white">Nuevo Convenio</h2>
                        <form onSubmit={handleCreate} className="space-y-4">
                            <div>
                                <label className="block text-sm text-slate-400 mb-1">Nombre</label>
                                <input required className="w-full bg-slate-800 border-slate-700 rounded p-2 text-white"
                                    value={formData.name} onChange={e => setFormData({ ...formData, name: e.target.value })} />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm text-slate-400 mb-1">Tipo</label>
                                    <select className="w-full bg-slate-800 border-slate-700 rounded p-2 text-white"
                                        value={formData.type} onChange={e => setFormData({ ...formData, type: e.target.value })}>
                                        <option value="PERCENTAGE">Porcentaje (%)</option>
                                        <option value="FIXED">Monto Fijo (Q)</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-sm text-slate-400 mb-1">Valor</label>
                                    <input type="number" step="0.01" required className="w-full bg-slate-800 border-slate-700 rounded p-2 text-white"
                                        value={formData.value} onChange={e => setFormData({ ...formData, value: e.target.value })} />
                                </div>
                            </div>
                            <div>
                                <label className="block text-sm text-slate-400 mb-1">Descripción</label>
                                <textarea className="w-full bg-slate-800 border-slate-700 rounded p-2 text-white h-24"
                                    value={formData.description} onChange={e => setFormData({ ...formData, description: e.target.value })} />
                            </div>
                            <div className="flex justify-end gap-2 pt-2">
                                <button type="button" onClick={() => setShowModal(false)} className="px-4 py-2 text-slate-400 hover:text-white">Cancelar</button>
                                <button type="submit" className="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded font-bold">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* ASSIGN MODAL */}
            {showAssignModal && (
                <div className="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                    <div className="bg-slate-900 border border-slate-800 rounded-xl p-6 w-full max-w-md space-y-4">
                        <h2 className="text-xl font-bold text-white">Asignar Beca</h2>
                        <p className="text-sm text-slate-400">Convenio: <span className="text-blue-400 font-bold">{selectedScholarship?.name}</span></p>

                        <StudentSearch />

                        <div className="flex justify-end pt-2">
                            <button onClick={() => setShowAssignModal(false)} className="px-4 py-2 text-slate-400 hover:text-white">Cerrar</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default ScholarshipsPage;
