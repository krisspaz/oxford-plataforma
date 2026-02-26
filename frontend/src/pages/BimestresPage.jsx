import { toast } from 'sonner';
import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useTheme } from '../contexts/ThemeContext';
import { bimesterService } from '../services';

const BimestresPage = () => {
    const { darkMode } = useTheme();
    const queryClient = useQueryClient();
    const [showModal, setShowModal] = useState(false);
    const [selectedBimester, setSelectedBimester] = useState(null);
    const [actionLoading, setActionLoading] = useState(null);
    const [formData, setFormData] = useState({
        number: 1,
        year: new Date().getFullYear(),
        name: '',
        startDate: '',
        endDate: '',
        maxScore: 100,
        percentage: 25
    });

    // === QUERY ===
    // eslint-disable-next-line unused-imports/no-unused-vars
    const { data: bimesters = [], isLoading: loading, refetch } = useQuery({
        queryKey: ['bimesters'],
        queryFn: async () => {
            const response = await bimesterService.getAll();
            return response.success ? response.data : (response || []);
        },
    });

    // === MUTATIONS ===
    const toggleMutation = useMutation({
        mutationFn: async ({ id, isClosed }) => {
            return isClosed ? bimesterService.open(id) : bimesterService.close(id);
        },
        onMutate: (vars) => setActionLoading(vars.id),
        onSettled: () => setActionLoading(null),
        onSuccess: () => queryClient.invalidateQueries({ queryKey: ['bimesters'] }),
        onError: (err) => toast.error('Error: ' + err.message),
    });

    const saveMutation = useMutation({
        mutationFn: async ({ id, data }) => {
            return id ? bimesterService.update(id, data) : bimesterService.create(data);
        },
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['bimesters'] });
            toast.success('Bimestre guardado correctamente');
            setShowModal(false);
            setSelectedBimester(null);
        },
        onError: () => toast.error('Error al guardar bimestre'),
    });

    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    const toggleClosed = (id) => {
        const bimester = bimesters.find(b => b.id === id);
        toggleMutation.mutate({ id, isClosed: bimester?.isClosed });
    };

    const openModal = (bimester = null) => {
        if (bimester) {
            setSelectedBimester(bimester);
            setFormData({
                number: bimester.number,
                year: bimester.year || new Date().getFullYear(),
                name: bimester.name,
                startDate: bimester.startDate,
                endDate: bimester.endDate,
                maxScore: bimester.maxScore,
                percentage: bimester.percentage
            });
        } else {
            setSelectedBimester(null);
            setFormData({
                number: bimesters.length + 1,
                year: new Date().getFullYear(),
                name: `Bimestre ${bimesters.length + 1}`,
                startDate: new Date().toISOString().split('T')[0],
                endDate: new Date().toISOString().split('T')[0],
                maxScore: 100,
                percentage: 25
            });
        }
        setShowModal(true);
    };

    const handleSave = () => {
        saveMutation.mutate({
            id: selectedBimester?.id,
            data: formData
        });
    };

    const isCurrentBimester = (b) => {
        const now = new Date();
        return new Date(b.startDate) <= now && now <= new Date(b.endDate);
    };

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando bimestres...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Gestión Bimestral</h1>
                <button onClick={() => openModal()} className="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
                    <Plus size={18} /> Nuevo Bimestre
                </button>
            </div>

            {/* Info Banner */}
            <div className={`${darkMode ? 'bg-blue-900/30 border-blue-700' : 'bg-blue-50 border-blue-200'} border rounded-xl p-4 flex items-start gap-3`}>
                <AlertTriangle className="text-blue-500 mt-0.5" size={20} />
                <div>
                    <p className={`font-medium ${darkMode ? 'text-blue-300' : 'text-blue-700'}`}>Cierre Automático</p>
                    <p className={`text-sm ${darkMode ? 'text-blue-200' : 'text-blue-600'}`}>
                        Los bimestres se cierran automáticamente al finalizar su fecha de cierre.
                        Los docentes no podrán modificar notas después del cierre.
                    </p>
                </div>
            </div>

            {/* Bimesters Grid */}
            <div className="grid grid-cols-2 gap-4">
                {bimesters.map(bimester => (
                    <div
                        key={bimester.id}
                        className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden ${isCurrentBimester(bimester) ? 'ring-2 ring-teal-500' : ''
                            }`}
                    >
                        <div className={`p-4 flex items-center justify-between ${bimester.isClosed ? darkMode ? 'bg-red-900/20' : 'bg-red-50' : ''}`}>
                            <div className="flex items-center gap-3">
                                <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${bimester.isClosed ? 'bg-red-100 text-red-600' : 'bg-teal-100 text-teal-600'
                                    }`}>
                                    {bimester.isClosed ? <Lock size={24} /> : <Unlock size={24} />}
                                </div>
                                <div>
                                    <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{bimester.name}</h3>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Ciclo {bimester.year || 2026}</p>
                                </div>
                            </div>
                            {isCurrentBimester(bimester) && (
                                <span className="px-2 py-1 bg-teal-100 text-teal-700 rounded-full text-xs font-medium">ACTUAL</span>
                            )}
                        </div>

                        <div className={`p-4 border-t ${darkMode ? 'border-gray-700' : 'bg-white border-gray-200 text-gray-900'}`}>
                            <div className="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Fecha Inicio</p>
                                    <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{bimester.startDate}</p>
                                </div>
                                <div>
                                    <p className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Fecha Fin</p>
                                    <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{bimester.endDate}</p>
                                </div>
                                <div>
                                    <p className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Punteo Máximo</p>
                                    <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{bimester.maxScore} pts</p>
                                </div>
                                <div>
                                    <p className={`text-xs ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Porcentaje Anual</p>
                                    <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{bimester.percentage}%</p>
                                </div>
                            </div>

                            <div className="flex gap-2">
                                <button
                                    onClick={() => openModal(bimester)}
                                    className={`flex-1 flex items-center justify-center gap-1 px-3 py-2 rounded-lg text-sm ${darkMode ? 'bg-gray-700 hover:bg-gray-600 text-gray-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'
                                        }`}
                                >
                                    <Edit size={14} /> Editar
                                </button>
                                <button
                                    onClick={() => toggleClosed(bimester.id)}
                                    disabled={actionLoading === bimester.id}
                                    className={`flex-1 flex items-center justify-center gap-1 px-3 py-2 rounded-lg text-sm ${bimester.isClosed
                                        ? 'bg-green-100 hover:bg-green-200 text-green-700'
                                        : 'bg-red-100 hover:bg-red-200 text-red-700'
                                        }`}
                                >
                                    {actionLoading === bimester.id ? (
                                        <RefreshCw size={14} className="animate-spin" />
                                    ) : (
                                        bimester.isClosed ? <><Unlock size={14} /> Abrir</> : <><Lock size={14} /> Cerrar</>
                                    )}
                                </button>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-md p-6`}>
                        <div className="flex items-center justify-between mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {selectedBimester ? 'Editar Bimestre' : 'Nuevo Bimestre'}
                            </h2>
                            <button onClick={() => { setShowModal(false); setSelectedBimester(null); }}><X size={20} /></button>
                        </div>
                        <div className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className={labelClass}>Número</label>
                                    <input type="number" className={inputClass} value={formData.number} onChange={e => setFormData({ ...formData, number: parseInt(e.target.value) })} />
                                </div>
                                <div>
                                    <label className={labelClass}>Ciclo</label>
                                    <select className={inputClass} value={formData.year} onChange={e => setFormData({ ...formData, year: parseInt(e.target.value) })}>
                                        <option value={2026}>2026</option>
                                        <option value={2025}>2025</option>
                                        <option value={2024}>2024</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label className={labelClass}>Nombre</label>
                                <input type="text" className={inputClass} value={formData.name} onChange={e => setFormData({ ...formData, name: e.target.value })} placeholder="Ej: Primer Bimestre" />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className={labelClass}>Fecha Inicio</label>
                                    <input type="date" className={inputClass} value={formData.startDate} onChange={e => setFormData({ ...formData, startDate: e.target.value })} />
                                </div>
                                <div>
                                    <label className={labelClass}>Fecha Fin</label>
                                    <input type="date" className={inputClass} value={formData.endDate} onChange={e => setFormData({ ...formData, endDate: e.target.value })} />
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className={labelClass}>Punteo Máximo</label>
                                    <input type="number" className={inputClass} value={formData.maxScore} onChange={e => setFormData({ ...formData, maxScore: parseInt(e.target.value) })} />
                                </div>
                                <div>
                                    <label className={labelClass}>Porcentaje Anual</label>
                                    <input type="number" className={inputClass} value={formData.percentage} onChange={e => setFormData({ ...formData, percentage: parseInt(e.target.value) })} />
                                </div>
                            </div>
                        </div>
                        <div className="flex justify-end gap-3 mt-6">
                            <button onClick={() => { setShowModal(false); setSelectedBimester(null); }} className={`px-4 py-2 rounded-lg ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}>Cancelar</button>
                            <button onClick={handleSave} className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">Guardar</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default BimestresPage;
