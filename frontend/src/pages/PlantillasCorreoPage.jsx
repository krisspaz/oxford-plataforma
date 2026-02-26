import { useState, useEffect } from 'react';
import { useTheme } from '../contexts/ThemeContext';
import { toast } from '../utils/toast';
import api from '../services/api';

const PlantillasCorreoPage = () => {
    const { darkMode } = useTheme();
    const [templates, setTemplates] = useState([]);
    const [selectedTemplate, setSelectedTemplate] = useState(null);
    const [loading, setLoading] = useState(false);
    const [showPreview, setShowPreview] = useState(false);
    const [editMode, setEditMode] = useState(false);
    const [editedContent, setEditedContent] = useState({ subject: '', body: '' });

    // Default templates as defined in spec
    const defaultTemplates = [
        { id: 1, name: 'Bienvenida Inscripción', key: 'WELCOME', description: 'Enviado al inscribir nuevo estudiante', variables: ['{{nombre_estudiante}}', '{{nombre_padre}}', '{{grado}}', '{{fecha_inicio}}'] },
        { id: 2, name: 'Confirmación de Pago', key: 'PAYMENT_CONFIRM', description: 'Confirmación después de registrar pago', variables: ['{{nombre_padre}}', '{{monto}}', '{{concepto}}', '{{fecha}}', '{{saldo}}'] },
        { id: 3, name: 'Recordatorio Cuota Vencida', key: 'PAYMENT_REMINDER', description: 'Notificación de cuota vencida', variables: ['{{nombre_padre}}', '{{estudiante}}', '{{monto}}', '{{dias_mora}}', '{{enlace_pago}}'] },
        { id: 4, name: 'Calificaciones Disponibles', key: 'GRADES_READY', description: 'Notas del bimestre disponibles', variables: ['{{nombre_padre}}', '{{estudiante}}', '{{bimestre}}', '{{promedio}}', '{{enlace_boleta}}'] },
        { id: 5, name: 'Tarea Asignada', key: 'TASK_ASSIGNED', description: 'Nueva tarea asignada', variables: ['{{estudiante}}', '{{materia}}', '{{titulo_tarea}}', '{{fecha_entrega}}'] },
        { id: 6, name: 'Evento Próximo', key: 'EVENT_REMINDER', description: 'Recordatorio de evento escolar', variables: ['{{nombre_evento}}', '{{fecha}}', '{{hora}}', '{{lugar}}', '{{descripcion}}'] },
        { id: 7, name: 'Recuperación de Contraseña', key: 'PASSWORD_RESET', description: 'Enlace para restablecer contraseña', variables: ['{{nombre_usuario}}', '{{enlace_reset}}', '{{expiracion}}'] },
        { id: 8, name: 'Estado de Cuenta', key: 'ACCOUNT_STATEMENT', description: 'Resumen mensual de estado financiero', variables: ['{{nombre_padre}}', '{{mes}}', '{{total_pendiente}}', '{{proximo_vencimiento}}'] },
        { id: 9, name: 'Comprobante Emitido', key: 'INVOICE_ISSUED', description: 'Notificación de nuevo comprobante', variables: ['{{nombre_receptor}}', '{{numero_doc}}', '{{tipo_doc}}', '{{monto}}', '{{enlace_pdf}}'] },
    ];

    useEffect(() => {
        loadTemplates();
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const loadTemplates = async () => {
        setLoading(true);
        try {
            const response = await api.get('/email-templates').catch(() => ({ data: [] }));
            const serverTemplates = response.data['hydra:member'] || response.data || [];

            // Merge server templates with defaults
            const merged = defaultTemplates.map(dt => {
                const serverVersion = serverTemplates.find(st => st.key === dt.key);
                return serverVersion ? { ...dt, ...serverVersion, customized: true } : { ...dt, subject: '', body: '', customized: false };
            });
            setTemplates(merged);
        } catch (error) {
            console.error('Error loading templates:', error);
            setTemplates(defaultTemplates.map(t => ({ ...t, subject: '', body: '', customized: false })));
        } finally {
            setLoading(false);
        }
    };

    const handleEdit = (template) => {
        setSelectedTemplate(template);
        setEditedContent({
            subject: template.subject || `[Colegio Oxford] ${template.name}`,
            body: template.body || getDefaultBody(template),
        });
        setEditMode(true);
    };

    const getDefaultBody = (template) => {
        // Generate a default body based on template type
        switch (template.key) {
            case 'WELCOME':
                return `Estimado(a) {{nombre_padre}},

¡Bienvenido(a) a la familia Oxford!

Nos complace informarle que {{nombre_estudiante}} ha sido inscrito exitosamente en {{grado}} para el presente ciclo escolar.

Las clases inician el {{fecha_inicio}}. Le invitamos a revisar el portal de padres para más información.

Atentamente,
Administración Colegio Oxford`;
            case 'PAYMENT_CONFIRM':
                return `Estimado(a) {{nombre_padre}},

Hemos recibido su pago por Q {{monto}} correspondiente a {{concepto}}.

Fecha: {{fecha}}
Saldo pendiente: Q {{saldo}}

Gracias por su puntualidad.

Atentamente,
Contabilidad Colegio Oxford`;
            default:
                return `Estimado(a) padre de familia,

${template.description}

Atentamente,
Colegio Oxford`;
        }
    };

    const handleSave = async () => {
        setLoading(true);
        try {
            await api.put(`/email-templates/${selectedTemplate.id}`, {
                subject: editedContent.subject,
                body: editedContent.body,
            });

            setTemplates(prev => prev.map(t =>
                t.id === selectedTemplate.id
                    ? { ...t, ...editedContent, customized: true }
                    : t
            ));

            toast.success('Plantilla guardada exitosamente');
            setEditMode(false);
        } catch (error) {
            console.error('Error saving template:', error);
            toast.error('Error al guardar plantilla');
        } finally {
            setLoading(false);
        }
    };

    const handleSendTest = async () => {
        try {
            await api.post(`/email-templates/${selectedTemplate.id}/test`);
            toast.success('Correo de prueba enviado');
        // eslint-disable-next-line unused-imports/no-unused-vars
        } catch (error) {
            toast.error('Error al enviar prueba');
        }
    };

    const insertVariable = (variable) => {
        const textarea = document.getElementById('template-body');
        if (textarea) {
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const newBody = editedContent.body.substring(0, start) + variable + editedContent.body.substring(end);
            setEditedContent({ ...editedContent, body: newBody });
        }
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex justify-between items-center">
                <div>
                    <h1 className={`text-2xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                        <Mail className="text-blue-500" /> Plantillas de Correo
                    </h1>
                    <p className={`${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        Personaliza los correos automáticos del sistema
                    </p>
                </div>
                <button
                    onClick={loadTemplates}
                    disabled={loading}
                    className={`px-4 py-2 rounded-lg flex items-center gap-2 ${darkMode ? 'bg-gray-700 hover:bg-gray-600 text-gray-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'}`}
                >
                    <RefreshCw size={18} className={loading ? 'animate-spin' : ''} /> Actualizar
                </button>
            </div>

            {/* Templates Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {templates.map(template => (
                    <div
                        key={template.id}
                        className={`${darkMode ? 'bg-gray-800 hover:bg-gray-750' : 'bg-white hover:bg-gray-50'} rounded-xl shadow-sm p-4 cursor-pointer transition-colors border-2 ${selectedTemplate?.id === template.id
                                ? 'border-blue-500'
                                : 'border-transparent'
                            }`}
                        onClick={() => setSelectedTemplate(template)}
                    >
                        <div className="flex items-start justify-between mb-3">
                            <div className="flex items-center gap-3">
                                <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${template.customized ? 'bg-green-100 dark:bg-green-900/30' : 'bg-gray-100 dark:bg-gray-700'
                                    }`}>
                                    <Mail size={20} className={template.customized ? 'text-green-600' : 'text-gray-400'} />
                                </div>
                                <div>
                                    <h3 className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                        {template.name}
                                    </h3>
                                    <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        {template.key}
                                    </p>
                                </div>
                            </div>
                            {template.customized && (
                                <span className="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    Personalizado
                                </span>
                            )}
                        </div>
                        <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                            {template.description}
                        </p>
                        <div className="flex gap-2 mt-4">
                            <button
                                onClick={(e) => { e.stopPropagation(); handleEdit(template); }}
                                className="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg flex items-center justify-center gap-1"
                            >
                                <Edit size={14} /> Editar
                            </button>
                            <button
                                onClick={(e) => { e.stopPropagation(); setSelectedTemplate(template); setShowPreview(true); }}
                                className={`px-3 py-2 rounded-lg flex items-center gap-1 text-sm ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-700'}`}
                            >
                                <Eye size={14} /> Ver
                            </button>
                        </div>
                    </div>
                ))}
            </div>

            {/* Edit Modal */}
            {editMode && selectedTemplate && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col`}>
                        {/* Modal Header */}
                        <div className="flex items-center justify-between p-4 border-b dark:border-gray-700">
                            <h2 className={`text-xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                <Edit size={24} /> Editar: {selectedTemplate.name}
                            </h2>
                            <button onClick={() => setEditMode(false)}>
                                <X size={20} />
                            </button>
                        </div>

                        {/* Modal Content */}
                        <div className="flex-1 overflow-y-auto p-6 space-y-4">
                            {/* Subject */}
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                    Asunto del Correo
                                </label>
                                <input
                                    type="text"
                                    value={editedContent.subject}
                                    onChange={(e) => setEditedContent({ ...editedContent, subject: e.target.value })}
                                    className={`w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                                        }`}
                                />
                            </div>

                            {/* Variables Help */}
                            <div className={`p-3 rounded-lg ${darkMode ? 'bg-gray-700' : 'bg-blue-50'}`}>
                                <div className="flex items-center gap-2 mb-2">
                                    <Variable size={16} className="text-blue-500" />
                                    <span className={`text-sm font-medium ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                        Variables Disponibles (click para insertar):
                                    </span>
                                </div>
                                <div className="flex flex-wrap gap-2">
                                    {selectedTemplate.variables.map(v => (
                                        <button
                                            key={v}
                                            onClick={() => insertVariable(v)}
                                            className={`px-2 py-1 rounded text-xs font-mono ${darkMode ? 'bg-gray-600 text-blue-400 hover:bg-gray-500' : 'bg-white text-blue-600 hover:bg-blue-100 border border-blue-200'
                                                }`}
                                        >
                                            {v}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Body */}
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                    Cuerpo del Correo
                                </label>
                                <textarea
                                    id="template-body"
                                    value={editedContent.body}
                                    onChange={(e) => setEditedContent({ ...editedContent, body: e.target.value })}
                                    rows={12}
                                    className={`w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                                        }`}
                                />
                            </div>
                        </div>

                        {/* Modal Footer */}
                        <div className="flex justify-between p-4 border-t dark:border-gray-700">
                            <button
                                onClick={handleSendTest}
                                className={`px-4 py-2 rounded-lg flex items-center gap-2 ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-700'}`}
                            >
                                <Send size={16} /> Enviar Prueba
                            </button>
                            <div className="flex gap-3">
                                <button
                                    onClick={() => setEditMode(false)}
                                    className={`px-4 py-2 rounded-lg ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200 text-gray-700'}`}
                                >
                                    Cancelar
                                </button>
                                <button
                                    onClick={handleSave}
                                    disabled={loading}
                                    className="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2"
                                >
                                    {loading ? <RefreshCw size={16} className="animate-spin" /> : <Save size={16} />}
                                    Guardar Plantilla
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Preview Modal */}
            {showPreview && selectedTemplate && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-2xl max-h-[80vh] overflow-hidden`}>
                        <div className="flex items-center justify-between p-4 border-b dark:border-gray-700">
                            <h2 className={`text-xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                <Eye size={24} /> Vista Previa: {selectedTemplate.name}
                            </h2>
                            <button onClick={() => setShowPreview(false)}>
                                <X size={20} />
                            </button>
                        </div>
                        <div className="p-6">
                            {/* Email Preview */}
                            <div className={`border rounded-lg overflow-hidden ${darkMode ? 'border-gray-700' : 'border-gray-200'}`}>
                                <div className={`p-3 ${darkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                                        <strong>De:</strong> notificaciones@colegiooxford.edu.gt
                                    </p>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>
                                        <strong>Para:</strong> padre@ejemplo.com
                                    </p>
                                    <p className={`text-sm font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                        <strong>Asunto:</strong> {selectedTemplate.subject || `[Colegio Oxford] ${selectedTemplate.name}`}
                                    </p>
                                </div>
                                <div className={`p-4 ${darkMode ? 'bg-gray-900' : 'bg-white'}`}>
                                    <pre className={`whitespace-pre-wrap font-sans text-sm ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                        {selectedTemplate.body || getDefaultBody(selectedTemplate)}
                                    </pre>
                                </div>
                            </div>

                            <div className={`mt-4 p-3 rounded-lg flex items-start gap-2 ${darkMode ? 'bg-yellow-900/20 text-yellow-400' : 'bg-yellow-50 text-yellow-700'}`}>
                                <AlertTriangle size={18} className="flex-shrink-0 mt-0.5" />
                                <p className="text-sm">
                                    Las variables como <code className="font-mono">{'{{nombre}}'}</code> serán reemplazadas con datos reales al enviar el correo.
                                </p>
                            </div>
                        </div>
                        <div className="flex justify-end p-4 border-t dark:border-gray-700">
                            <button
                                onClick={() => { setShowPreview(false); handleEdit(selectedTemplate); }}
                                className="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2"
                            >
                                <Edit size={16} /> Editar Esta Plantilla
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default PlantillasCorreoPage;
