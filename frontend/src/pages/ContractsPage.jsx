import { toast } from 'sonner';
import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useTheme } from '../contexts/ThemeContext';
import { usePdfExport } from '../hooks/usePdfExport';
import { contractService } from '../services';

const ContractsPage = () => {
    const { darkMode } = useTheme();
    // eslint-disable-next-line unused-imports/no-unused-vars
    const { createDoc } = usePdfExport();
    const queryClient = useQueryClient();
    const [activeTab, setActiveTab] = useState('all');
    const [showGenerateModal, setShowGenerateModal] = useState(false);
    const [showUploadModal, setShowUploadModal] = useState(false);
    const [selectedContract, setSelectedContract] = useState(null);
    const [generating, setGenerating] = useState(false);

    // Form state para generar contrato
    const [formData, setFormData] = useState({
        representante: {
            nombres: '', apellidos: '', edad: '', estadoCivil: 'casado(a)',
            nacionalidad: 'guatemalteco(a)', profesion: '', dpi: '', direccion: '',
            telefonoCasa: '', telefonoOficina: '', celular: '', email: ''
        },
        educando: {
            nombres: '', apellidos: '', grado: '', nivel: 'Primaria', jornada: 'Jornada Matutina'
        },
        inscripcion: 600, colegiatura: 600, ciclo: new Date().getFullYear(),
        formaPago: 'anticipada', diasPago: 5
    });

    // === QUERY ===
    const { data: contracts = [], isLoading: loading } = useQuery({
        queryKey: ['contracts'],
        queryFn: async () => {
            const { success, data } = await contractService.getAll();
            if (success && Array.isArray(data)) return data;
            return [];
        },
    });

    // === MUTATION ===
    // eslint-disable-next-line unused-imports/no-unused-vars
    const generateMutation = useMutation({
        mutationFn: async (payload) => contractService.create(payload),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['contracts'] });
            toast.success('Contrato generado exitosamente');
            setShowGenerateModal(false);
        },
        onError: () => toast.error('Error al generar contrato'),
    });

    const handleDownloadPdf = (contract) => {
        // Generar contrato con datos del estudiante
        contractService.downloadPdf(contract.id, {
            representante: {
                nombres: contract.parentName?.split(' ')[0] || '',
                apellidos: contract.parentName?.split(' ').slice(1).join(' ') || ''
            },
            educando: {
                nombres: contract.student.split(' ')[0],
                apellidos: contract.student.split(' ').slice(1).join(' '),
                grado: contract.grade,
                nivel: contract.grade?.includes('Kinder') || contract.grade?.includes('Pre') ? 'Pre-primaria' : 'Primaria'
            },
            ciclo: parseInt(contract.cycle) || new Date().getFullYear(),
            correlativo: String(contract.id).padStart(3, '0')
        });
    };

    const handleGenerateContract = async () => {
        setGenerating(true);
        try {
            // Generar PDF con los datos del formulario
            contractService.downloadPdf(null, formData);

            // Guardar en el sistema
            await contractService.generate({
                studentName: `${formData.educando.nombres} ${formData.educando.apellidos}`,
                parentName: `${formData.representante.nombres} ${formData.representante.apellidos}`,
                grade: formData.educando.grado,
                cycle: formData.ciclo.toString()
            });

            setShowGenerateModal(false);
            queryClient.invalidateQueries({ queryKey: ['contracts'] });

            // Reset form
            setFormData({
                representante: { nombres: '', apellidos: '', edad: '', estadoCivil: 'casado(a)', nacionalidad: 'guatemalteco(a)', profesion: '', dpi: '', direccion: '', telefonoCasa: '', telefonoOficina: '', celular: '', email: '' },
                educando: { nombres: '', apellidos: '', grado: '', nivel: 'Primaria', jornada: 'Jornada Matutina' },
                inscripcion: 600, colegiatura: 600, ciclo: new Date().getFullYear(), formaPago: 'anticipada', diasPago: 5
            });
        } catch (error) {
            console.error("Error generating contract:", error);
            toast.error("Error al generar contrato");
        } finally {
            setGenerating(false);
        }
    };

    const handleUploadSigned = async (e) => {
        const file = e.target.files[0];
        if (!file || !selectedContract) return;

        try {
            await contractService.upload(selectedContract.id, file);
            toast.success("Contrato firmado subido exitosamente");
            setShowUploadModal(false);
            setSelectedContract(null);
            queryClient.invalidateQueries({ queryKey: ['contracts'] });
        } catch (error) {
            console.error("Error uploading signed contract:", error);
            toast.error("Error al subir contrato");
            setShowUploadModal(false);
            setSelectedContract(null);
        }
    };

    const updateFormData = (section, field, value) => {
        if (section) {
            setFormData(prev => ({
                ...prev,
                [section]: { ...prev[section], [field]: value }
            }));
        } else {
            setFormData(prev => ({ ...prev, [field]: value }));
        }
    };

    const filtered = activeTab === 'all'
        ? contracts
        : contracts.filter(c => activeTab === 'pending' ? c.status === 'PENDING' : c.status === 'SIGNED');

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none w-full ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300 bg-white'}`;

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando contratos...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Gestión de Contratos</h1>
                <div className="flex gap-3">
                    <button onClick={() => queryClient.invalidateQueries({ queryKey: ['contracts'] })} className={`px-4 py-2 rounded-lg flex items-center gap-2 ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-700'}`}>
                        <RefreshCw size={18} /> Actualizar
                    </button>
                    <button
                        onClick={() => setShowGenerateModal(true)}
                        className="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 flex items-center gap-2">
                        <Plus size={18} /> Generar Contrato
                    </button>
                </div>
            </div>

            {/* Summary Cards */}
            <div className="grid grid-cols-3 gap-4">
                <div className={`p-4 rounded-xl border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'}`}>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Total Contratos</p>
                    <p className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{contracts.length}</p>
                </div>
                <div className={`p-4 rounded-xl border ${darkMode ? 'bg-yellow-900/30 border-yellow-700' : 'bg-yellow-50 border-yellow-200'}`}>
                    <p className={`text-sm ${darkMode ? 'text-yellow-300' : 'text-yellow-600'}`}>Pendientes de Firma</p>
                    <p className={`text-2xl font-bold ${darkMode ? 'text-yellow-400' : 'text-yellow-700'}`}>{contracts.filter(c => c.status === 'PENDING').length}</p>
                </div>
                <div className={`p-4 rounded-xl border ${darkMode ? 'bg-green-900/30 border-green-700' : 'bg-green-50 border-green-200'}`}>
                    <p className={`text-sm ${darkMode ? 'text-green-300' : 'text-green-600'}`}>Firmados</p>
                    <p className={`text-2xl font-bold ${darkMode ? 'text-green-400' : 'text-green-700'}`}>{contracts.filter(c => c.status === 'SIGNED').length}</p>
                </div>
            </div>

            {/* Tabs */}
            <div className="flex gap-4 border-b dark:border-gray-700">
                {['all', 'pending', 'signed'].map(tab => (
                    <button
                        key={tab}
                        onClick={() => setActiveTab(tab)}
                        className={`pb-2 px-4 font-medium transition-colors ${activeTab === tab ? 'text-teal-600 border-b-2 border-teal-600' : (darkMode ? 'text-gray-400' : 'text-gray-500')}`}
                    >
                        {tab === 'all' ? 'Todos' : tab === 'pending' ? 'Pendientes' : 'Firmados'}
                    </button>
                ))}
            </div>

            {/* Contracts Grid */}
            <div className="grid gap-4">
                {filtered.map(contract => (
                    <div key={contract.id} className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white'} p-4 rounded-xl shadow-sm border flex justify-between items-center transition-all hover:shadow-md`}>
                        <div className="flex items-center gap-4">
                            <div className={`p-3 ${darkMode ? 'bg-blue-900/50' : 'bg-blue-100'} rounded-xl text-blue-500`}>
                                <FileText size={24} />
                            </div>
                            <div>
                                <h3 className={`font-bold ${darkMode ? 'text-white' : 'text-gray-900'}`}>{contract.student}</h3>
                                <div className="flex items-center gap-3 mt-1">
                                    <span className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{contract.carnet}</span>
                                    <span className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>•</span>
                                    <span className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{contract.grade}</span>
                                    <span className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>•</span>
                                    <span className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Ciclo {contract.cycle}</span>
                                </div>
                            </div>
                        </div>
                        <div className="flex items-center gap-4">
                            <span className={`px-3 py-1 text-xs font-semibold rounded-full ${contract.status === 'SIGNED'
                                ? 'bg-green-100 text-green-700 border border-green-200'
                                : 'bg-yellow-100 text-yellow-700 border border-yellow-200'
                                }`}>
                                {contract.status === 'SIGNED' ? 'Firmado' : 'Pendiente de Firma'}
                            </span>
                            <div className="flex gap-2">
                                {/* Descargar contrato para firma */}
                                <button
                                    onClick={() => handleDownloadPdf(contract)}
                                    className={`p-2 rounded-lg transition-colors ${darkMode ? 'text-blue-400 hover:bg-gray-700' : 'text-blue-600 hover:bg-blue-50'}`}
                                    title="Descargar Contrato PDF"
                                >
                                    <Download size={20} />
                                </button>

                                {/* Subir contrato firmado */}
                                {contract.status === 'PENDING' && (
                                    <button
                                        onClick={() => { setSelectedContract(contract); setShowUploadModal(true); }}
                                        className={`p-2 rounded-lg transition-colors ${darkMode ? 'text-green-400 hover:bg-gray-700' : 'text-green-600 hover:bg-green-50'}`}
                                        title="Subir Contrato Firmado"
                                    >
                                        <Upload size={20} />
                                    </button>
                                )}

                                {/* Ver contrato firmado */}
                                {contract.status === 'SIGNED' && (
                                    <button
                                        onClick={() => {
                                            if (contract.signedFile) {
                                                window.open(contract.signedFile, '_blank');
                                            } else {
                                                toast.info('El archivo del contrato no está disponible.');
                                            }
                                        }}
                                        className={`p-2 rounded-lg transition-colors ${darkMode ? 'text-purple-400 hover:bg-gray-700' : 'text-purple-600 hover:bg-purple-50'} ${!contract.signedFile ? 'opacity-50 cursor-not-allowed' : ''}`}
                                        title={contract.signedFile ? "Ver Contrato Firmado" : "Archivo no disponible"}
                                    >
                                        <Eye size={20} />
                                    </button>
                                )}
                            </div>
                        </div>
                    </div>
                ))}

                {filtered.length === 0 && (
                    <div className="p-12 text-center">
                        <FileText size={48} className={`mx-auto mb-4 ${darkMode ? 'text-gray-600' : 'text-gray-300'}`} />
                        <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>No se encontraron contratos en esta sección.</p>
                    </div>
                )}
            </div>

            {/* Modal: Generar Contrato */}
            {showGenerateModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 overflow-y-auto">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto`}>
                        <div className={`sticky top-0 ${darkMode ? 'bg-gray-800' : 'bg-white'} p-6 border-b ${darkMode ? 'border-gray-700' : 'bg-white border-gray-200 text-gray-900'} flex justify-between items-center`}>
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Generar Contrato de Servicios Educativos</h2>
                            <button onClick={() => setShowGenerateModal(false)} className="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                <X size={20} />
                            </button>
                        </div>

                        <div className="p-6 space-y-6">
                            {/* Datos del Representante */}
                            <div className={`p-4 rounded-xl border ${darkMode ? 'border-gray-700 bg-gray-700/30' : 'border-gray-200 bg-gray-50'}`}>
                                <h3 className={`font-bold mb-4 flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                    <User size={18} className="text-blue-500" /> Datos del Representante del Educando
                                </h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Nombres *</label>
                                        <input type="text" value={formData.representante.nombres} onChange={e => updateFormData('representante', 'nombres', e.target.value)} className={inputClass} placeholder="Juan Carlos" />
                                    </div>
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Apellidos *</label>
                                        <input type="text" value={formData.representante.apellidos} onChange={e => updateFormData('representante', 'apellidos', e.target.value)} className={inputClass} placeholder="García López" />
                                    </div>
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>DPI (CUI) *</label>
                                        <input type="text" value={formData.representante.dpi} onChange={e => updateFormData('representante', 'dpi', e.target.value)} className={inputClass} placeholder="1234 56789 0101" />
                                    </div>
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Edad</label>
                                        <input type="number" value={formData.representante.edad} onChange={e => updateFormData('representante', 'edad', e.target.value)} className={inputClass} placeholder="35" />
                                    </div>
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Estado Civil</label>
                                        <select value={formData.representante.estadoCivil} onChange={e => updateFormData('representante', 'estadoCivil', e.target.value)} className={inputClass}>
                                            <option value="soltero(a)">Soltero(a)</option>
                                            <option value="casado(a)">Casado(a)</option>
                                            <option value="unido(a)">Unido(a)</option>
                                            <option value="divorciado(a)">Divorciado(a)</option>
                                            <option value="viudo(a)">Viudo(a)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Profesión</label>
                                        <input type="text" value={formData.representante.profesion} onChange={e => updateFormData('representante', 'profesion', e.target.value)} className={inputClass} placeholder="Ingeniero, Abogado, etc." />
                                    </div>
                                    <div className="md:col-span-2">
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}><MapPin size={14} className="inline mr-1" />Dirección</label>
                                        <input type="text" value={formData.representante.direccion} onChange={e => updateFormData('representante', 'direccion', e.target.value)} className={inputClass} placeholder="Zona 4, Mixco, Guatemala" />
                                    </div>
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}><Phone size={14} className="inline mr-1" />Celular *</label>
                                        <input type="tel" value={formData.representante.celular} onChange={e => updateFormData('representante', 'celular', e.target.value)} className={inputClass} placeholder="5555-1234" />
                                    </div>
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}><Mail size={14} className="inline mr-1" />Correo Electrónico</label>
                                        <input type="email" value={formData.representante.email} onChange={e => updateFormData('representante', 'email', e.target.value)} className={inputClass} placeholder="correo@ejemplo.com" />
                                    </div>
                                </div>
                            </div>

                            {/* Datos del Educando */}
                            <div className={`p-4 rounded-xl border ${darkMode ? 'border-gray-700 bg-gray-700/30' : 'border-gray-200 bg-gray-50'}`}>
                                <h3 className={`font-bold mb-4 flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                    <GraduationCap size={18} className="text-green-500" /> Datos del Educando
                                </h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Nombres *</label>
                                        <input type="text" value={formData.educando.nombres} onChange={e => updateFormData('educando', 'nombres', e.target.value)} className={inputClass} placeholder="María Elena" />
                                    </div>
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Apellidos *</label>
                                        <input type="text" value={formData.educando.apellidos} onChange={e => updateFormData('educando', 'apellidos', e.target.value)} className={inputClass} placeholder="García López" />
                                    </div>
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Nivel *</label>
                                        <select value={formData.educando.nivel} onChange={e => updateFormData('educando', 'nivel', e.target.value)} className={inputClass}>
                                            <option value="Pre-primaria">Pre-primaria</option>
                                            <option value="Primaria">Primaria</option>
                                            <option value="Básicos">Básicos</option>
                                            <option value="Diversificado">Diversificado</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Grado *</label>
                                        <select value={formData.educando.grado} onChange={e => updateFormData('educando', 'grado', e.target.value)} className={inputClass}>
                                            <option value="">Seleccione...</option>
                                            <option value="Kinder">Kinder</option>
                                            <option value="Preparatoria">Preparatoria</option>
                                            <option value="1ro Primaria">1ro Primaria</option>
                                            <option value="2do Primaria">2do Primaria</option>
                                            <option value="3ro Primaria">3ro Primaria</option>
                                            <option value="4to Primaria">4to Primaria</option>
                                            <option value="5to Primaria">5to Primaria</option>
                                            <option value="6to Primaria">6to Primaria</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Jornada</label>
                                        <select value={formData.educando.jornada} onChange={e => updateFormData('educando', 'jornada', e.target.value)} className={inputClass}>
                                            <option value="Jornada Matutina">Jornada Matutina</option>
                                            <option value="Jornada Vespertina">Jornada Vespertina</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {/* Datos de Cuotas */}
                            <div className={`p-4 rounded-xl border ${darkMode ? 'border-gray-700 bg-gray-700/30' : 'border-gray-200 bg-gray-50'}`}>
                                <h3 className={`font-bold mb-4 flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                    <Calendar size={18} className="text-purple-500" /> Cuotas y Ciclo Escolar
                                </h3>
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Ciclo Escolar</label>
                                        <input type="number" value={formData.ciclo} onChange={e => updateFormData(null, 'ciclo', parseInt(e.target.value))} className={inputClass} />
                                    </div>
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Inscripción (Q)</label>
                                        <input type="number" value={formData.inscripcion} onChange={e => updateFormData(null, 'inscripcion', parseFloat(e.target.value))} className={inputClass} />
                                    </div>
                                    <div>
                                        <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Colegiatura Mensual (Q)</label>
                                        <input type="number" value={formData.colegiatura} onChange={e => updateFormData(null, 'colegiatura', parseFloat(e.target.value))} className={inputClass} />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className={`sticky bottom-0 ${darkMode ? 'bg-gray-800' : 'bg-white'} p-6 border-t ${darkMode ? 'border-gray-700' : 'bg-white border-gray-200 text-gray-900'} flex justify-end gap-3`}>
                            <button onClick={() => setShowGenerateModal(false)} className={`px-4 py-2 rounded-lg ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200 text-gray-700'}`}>
                                Cancelar
                            </button>
                            <button
                                onClick={handleGenerateContract}
                                disabled={generating || !formData.representante.nombres || !formData.educando.nombres || !formData.educando.grado}
                                className="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg flex items-center gap-2 disabled:opacity-50"
                            >
                                {generating ? <RefreshCw className="animate-spin" size={18} /> : <Download size={18} />}
                                {generating ? 'Generando...' : 'Generar y Descargar PDF'}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Modal: Subir Contrato Firmado */}
            {showUploadModal && selectedContract && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-2xl w-full max-w-md p-6`}>
                        <div className="flex justify-between items-center mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Subir Contrato Firmado</h2>
                            <button onClick={() => { setShowUploadModal(false); setSelectedContract(null); }} className="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                <X size={20} />
                            </button>
                        </div>

                        <div className={`p-4 rounded-lg mb-4 ${darkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>
                            <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{selectedContract.student}</p>
                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{selectedContract.grade} - Ciclo {selectedContract.cycle}</p>
                        </div>

                        <div className={`border-2 border-dashed rounded-xl p-8 text-center ${darkMode ? 'border-gray-600' : 'bg-white border-gray-300 text-gray-900'}`}>
                            <Upload size={48} className={`mx-auto mb-4 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`} />
                            <p className={`mb-2 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Arrastra el PDF escaneado o</p>
                            <label className="cursor-pointer">
                                <span className="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">Seleccionar Archivo</span>
                                <input type="file" accept=".pdf" onChange={handleUploadSigned} className="hidden" />
                            </label>
                            <p className={`mt-2 text-sm ${darkMode ? 'text-gray-500' : 'text-gray-400'}`}>Solo archivos PDF</p>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default ContractsPage;
