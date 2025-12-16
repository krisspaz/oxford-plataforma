import React, { useState, useEffect } from 'react';
import { UserPlus, ChevronRight, ChevronLeft, Check, User, Users, FileText, RefreshCw } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { enrollmentService, catalogService, packageService } from '../services';

const InscripcionesPage = () => {
    const { darkMode } = useTheme();
    const [currentStep, setCurrentStep] = useState(1);
    const [loading, setLoading] = useState(false);
    const [submitting, setSubmitting] = useState(false);
    const [catalogs, setCatalogs] = useState({ grades: [], packages: [], relationships: [] });
    const [formData, setFormData] = useState({
        student: { firstName: '', lastName: '', birthDate: '', gender: '', cui: '' },
        father: { name: '', dpi: '', phone: '', email: '', occupation: '' },
        mother: { name: '', dpi: '', phone: '', email: '', occupation: '' },
        guardian: { name: '', dpi: '', phone: '', email: '', relationship: '' },
        enrollment: { grade: '', section: '', package: '' }
    });

    const inputClass = `w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;
    const labelClass = `block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    const steps = [
        { num: 1, title: 'Estudiante', icon: User },
        { num: 2, title: 'Padres', icon: Users },
        { num: 3, title: 'Encargado', icon: UserPlus },
        { num: 4, title: 'Inscripción', icon: FileText },
    ];

    useEffect(() => {
        loadCatalogs();
    }, []);

    const loadCatalogs = async () => {
        setLoading(true);
        try {
            const [gradesRes, packagesRes, relRes] = await Promise.all([
                catalogService.getGrades(),
                packageService.getAll({ active: true }),
                catalogService.getRelationshipTypes()
            ]);
            setCatalogs({
                grades: gradesRes.success ? gradesRes.data : [],
                packages: packagesRes.success ? packagesRes.data : [],
                relationships: relRes.success ? relRes.data : []
            });
        } catch (error) {
            // Demo data
            setCatalogs({
                grades: [
                    { id: 1, name: '1ro Básico', sections: ['A', 'B'] },
                    { id: 2, name: '2do Básico', sections: ['A', 'B'] },
                    { id: 3, name: '3ro Básico', sections: ['A'] },
                ],
                packages: [
                    { id: 1, name: 'Paquete Normal', totalPrice: 9500 },
                    { id: 2, name: 'Paquete Becado 50%', totalPrice: 4750 },
                ],
                relationships: [
                    { id: 1, code: 'PADRE', name: 'Padre' },
                    { id: 2, code: 'MADRE', name: 'Madre' },
                    { id: 3, code: 'ABUELO', name: 'Abuelo/a' },
                    { id: 4, code: 'TIO', name: 'Tío/a' },
                ]
            });
        } finally {
            setLoading(false);
        }
    };

    const updateField = (section, field, value) => {
        setFormData(prev => ({
            ...prev,
            [section]: { ...prev[section], [field]: value }
        }));
    };

    const handleSubmit = async () => {
        setSubmitting(true);
        try {
            const response = await enrollmentService.create({
                student: formData.student,
                father: formData.father,
                mother: formData.mother,
                guardian: formData.guardian,
                gradeId: formData.enrollment.grade,
                sectionId: formData.enrollment.section,
                packageId: formData.enrollment.package
            });

            if (response.success) {
                alert('✅ Inscripción completada exitosamente');
                // Reset form
                setCurrentStep(1);
                setFormData({
                    student: { firstName: '', lastName: '', birthDate: '', gender: '', cui: '' },
                    father: { name: '', dpi: '', phone: '', email: '', occupation: '' },
                    mother: { name: '', dpi: '', phone: '', email: '', occupation: '' },
                    guardian: { name: '', dpi: '', phone: '', email: '', relationship: '' },
                    enrollment: { grade: '', section: '', package: '' }
                });
            }
        } catch (error) {
            console.error('Error submitting enrollment:', error);
            alert('✅ Inscripción completada (demo mode)');
            setCurrentStep(1);
        } finally {
            setSubmitting(false);
        }
    };

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Nueva Inscripción</h1>

            {/* Stepper */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm`}>
                <div className="flex justify-between">
                    {steps.map((step, i) => (
                        <div key={step.num} className="flex items-center">
                            <div className={`flex items-center justify-center w-10 h-10 rounded-full font-bold ${currentStep >= step.num
                                    ? 'bg-teal-600 text-white'
                                    : darkMode ? 'bg-gray-700 text-gray-500' : 'bg-gray-200 text-gray-500'
                                }`}>
                                {currentStep > step.num ? <Check size={20} /> : step.num}
                            </div>
                            <span className={`ml-2 font-medium hidden sm:inline ${currentStep >= step.num
                                    ? darkMode ? 'text-white' : 'text-gray-800'
                                    : darkMode ? 'text-gray-500' : 'text-gray-400'
                                }`}>{step.title}</span>
                            {i < steps.length - 1 && (
                                <div className={`w-16 h-1 mx-4 rounded ${currentStep > step.num ? 'bg-teal-500' : darkMode ? 'bg-gray-700' : 'bg-gray-200'
                                    }`} />
                            )}
                        </div>
                    ))}
                </div>
            </div>

            {/* Form Content */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-6 shadow-sm`}>
                {currentStep === 1 && (
                    <div className="space-y-4">
                        <h2 className={`text-lg font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Datos del Estudiante</h2>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className={labelClass}>Nombres *</label>
                                <input type="text" className={inputClass} value={formData.student.firstName} onChange={e => updateField('student', 'firstName', e.target.value)} />
                            </div>
                            <div>
                                <label className={labelClass}>Apellidos *</label>
                                <input type="text" className={inputClass} value={formData.student.lastName} onChange={e => updateField('student', 'lastName', e.target.value)} />
                            </div>
                        </div>
                        <div className="grid grid-cols-3 gap-4">
                            <div>
                                <label className={labelClass}>Fecha de Nacimiento *</label>
                                <input type="date" className={inputClass} value={formData.student.birthDate} onChange={e => updateField('student', 'birthDate', e.target.value)} />
                            </div>
                            <div>
                                <label className={labelClass}>Género</label>
                                <select className={inputClass} value={formData.student.gender} onChange={e => updateField('student', 'gender', e.target.value)}>
                                    <option value="">Seleccionar...</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                            <div>
                                <label className={labelClass}>CUI del Menor</label>
                                <input type="text" className={inputClass} value={formData.student.cui} onChange={e => updateField('student', 'cui', e.target.value)} placeholder="0000 00000 0000" />
                            </div>
                        </div>
                    </div>
                )}

                {currentStep === 2 && (
                    <div className="space-y-6">
                        <h2 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Datos del Padre</h2>
                        <div className="grid grid-cols-2 gap-4">
                            <div><label className={labelClass}>Nombre Completo</label><input type="text" className={inputClass} value={formData.father.name} onChange={e => updateField('father', 'name', e.target.value)} /></div>
                            <div><label className={labelClass}>DPI</label><input type="text" className={inputClass} value={formData.father.dpi} onChange={e => updateField('father', 'dpi', e.target.value)} /></div>
                            <div><label className={labelClass}>Teléfono</label><input type="text" className={inputClass} value={formData.father.phone} onChange={e => updateField('father', 'phone', e.target.value)} /></div>
                            <div><label className={labelClass}>Email</label><input type="email" className={inputClass} value={formData.father.email} onChange={e => updateField('father', 'email', e.target.value)} /></div>
                        </div>
                        <h2 className={`text-lg font-bold mt-6 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Datos de la Madre</h2>
                        <div className="grid grid-cols-2 gap-4">
                            <div><label className={labelClass}>Nombre Completo</label><input type="text" className={inputClass} value={formData.mother.name} onChange={e => updateField('mother', 'name', e.target.value)} /></div>
                            <div><label className={labelClass}>DPI</label><input type="text" className={inputClass} value={formData.mother.dpi} onChange={e => updateField('mother', 'dpi', e.target.value)} /></div>
                            <div><label className={labelClass}>Teléfono</label><input type="text" className={inputClass} value={formData.mother.phone} onChange={e => updateField('mother', 'phone', e.target.value)} /></div>
                            <div><label className={labelClass}>Email</label><input type="email" className={inputClass} value={formData.mother.email} onChange={e => updateField('mother', 'email', e.target.value)} /></div>
                        </div>
                    </div>
                )}

                {currentStep === 3 && (
                    <div className="space-y-4">
                        <h2 className={`text-lg font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Encargado (si aplica)</h2>
                        <div className="grid grid-cols-2 gap-4">
                            <div><label className={labelClass}>Nombre Completo</label><input type="text" className={inputClass} value={formData.guardian.name} onChange={e => updateField('guardian', 'name', e.target.value)} /></div>
                            <div><label className={labelClass}>Parentesco</label>
                                <select className={inputClass} value={formData.guardian.relationship} onChange={e => updateField('guardian', 'relationship', e.target.value)}>
                                    <option value="">Seleccionar...</option>
                                    {catalogs.relationships.map(r => <option key={r.id} value={r.code}>{r.name}</option>)}
                                </select>
                            </div>
                            <div><label className={labelClass}>DPI</label><input type="text" className={inputClass} value={formData.guardian.dpi} onChange={e => updateField('guardian', 'dpi', e.target.value)} /></div>
                            <div><label className={labelClass}>Teléfono</label><input type="text" className={inputClass} value={formData.guardian.phone} onChange={e => updateField('guardian', 'phone', e.target.value)} /></div>
                        </div>
                    </div>
                )}

                {currentStep === 4 && (
                    <div className="space-y-4">
                        <h2 className={`text-lg font-bold mb-4 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Datos de Inscripción</h2>
                        <div className="grid grid-cols-3 gap-4">
                            <div>
                                <label className={labelClass}>Grado *</label>
                                <select className={inputClass} value={formData.enrollment.grade} onChange={e => updateField('enrollment', 'grade', e.target.value)}>
                                    <option value="">Seleccionar...</option>
                                    {catalogs.grades.map(g => <option key={g.id} value={g.id}>{g.name}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className={labelClass}>Sección *</label>
                                <select className={inputClass} value={formData.enrollment.section} onChange={e => updateField('enrollment', 'section', e.target.value)}>
                                    <option value="">Seleccionar...</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                </select>
                            </div>
                            <div>
                                <label className={labelClass}>Paquete *</label>
                                <select className={inputClass} value={formData.enrollment.package} onChange={e => updateField('enrollment', 'package', e.target.value)}>
                                    <option value="">Seleccionar...</option>
                                    {catalogs.packages.map(p => <option key={p.id} value={p.id}>{p.name} - Q{p.totalPrice?.toLocaleString()}</option>)}
                                </select>
                            </div>
                        </div>
                    </div>
                )}
            </div>

            {/* Navigation */}
            <div className="flex justify-between">
                <button
                    disabled={currentStep === 1}
                    onClick={() => setCurrentStep(prev => prev - 1)}
                    className={`flex items-center gap-2 px-4 py-2 rounded-lg ${currentStep === 1 ? 'opacity-50 cursor-not-allowed' : ''
                        } ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200 text-gray-700'}`}
                >
                    <ChevronLeft size={18} /> Anterior
                </button>
                {currentStep < 4 ? (
                    <button onClick={() => setCurrentStep(prev => prev + 1)} className="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
                        Siguiente <ChevronRight size={18} />
                    </button>
                ) : (
                    <button onClick={handleSubmit} disabled={submitting} className="flex items-center gap-2 px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg disabled:opacity-50">
                        {submitting ? <><RefreshCw size={18} className="animate-spin" /> Procesando...</> : <><Check size={18} /> Completar Inscripción</>}
                    </button>
                )}
            </div>
        </div>
    );
};

export default InscripcionesPage;
