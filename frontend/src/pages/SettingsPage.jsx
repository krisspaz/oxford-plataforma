import { toast } from '../utils/toast';
import { useState, useEffect } from 'react';
import { Settings, User, Bell, Shield, Database, Palette, Image } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { api } from '../services/api';

const SettingsPage = () => {
    const { darkMode, toggleDarkMode } = useTheme();
    const [activeTab, setActiveTab] = useState('general');
    const [saved, setSaved] = useState(false);
    // eslint-disable-next-line unused-imports/no-unused-vars
    const [loading, setLoading] = useState(true);
    const [settings, setSettings] = useState({
        schoolName: 'Colegio Oxford',
        schoolEmail: 'admin@oxford.edu',
        phone: '+502 1234 5678',
        address: 'Ciudad de Guatemala',
        notifications: true,
        emailAlerts: true,
        autoBackup: true,
        language: 'es',
        currency: 'GTQ',
        // Branding
        logoMain: null,
        logoAlt: null,
        favicon: null,
        primaryColor: '#14B8A6',
        secondaryColor: '#0F766E',
        pdfHeader: 'Colegio Oxford - Excelencia Académica',
        pdfFooter: 'www.colegiooxford.edu.gt',
    });

    useEffect(() => {
        loadSettings();
    }, []);

    const loadSettings = async () => {
        try {
            const response = await api.get('/settings');
            if (response && Object.keys(response).length > 0) {
                setSettings(prev => ({ ...prev, ...response }));
            }
        } catch (error) {
            console.error("Error loading settings", error);
        } finally {
            setLoading(false);
        }
    };

    const handleSave = async () => {
        try {
            await api.post('/settings', settings);
            setSaved(true);
            setTimeout(() => setSaved(false), 2000);
        // eslint-disable-next-line unused-imports/no-unused-vars
        } catch (error) {
            toast.info('Error al guardar configuración');
        }
    };

    const tabs = [
        { id: 'general', label: 'General', icon: Settings },
        { id: 'profile', label: 'Perfil', icon: User },
        { id: 'branding', label: 'Branding', icon: Image },
        { id: 'notifications', label: 'Notificaciones', icon: Bell },
        { id: 'security', label: 'Seguridad', icon: Shield },
        { id: 'database', label: 'Base de Datos', icon: Database },
        { id: 'appearance', label: 'Apariencia', icon: Palette },
    ];

    const inputClass = `w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-200 text-gray-900'}`;
    const labelClass = `block text-sm font-medium mb-2 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`;

    // eslint-disable-next-line unused-imports/no-unused-vars
    const GeneralSettings = () => (
        <div className="space-y-6">
            <div>
                <label className={labelClass}>Nombre del Colegio</label>
                <input
                    type="text"
                    value={settings.schoolName}
                    onChange={e => setSettings({ ...settings, schoolName: e.target.value })}
                    className={inputClass}
                />
            </div>
            <div>
                <label className={labelClass}>Email Institucional</label>
                <input
                    type="email"
                    value={settings.schoolEmail}
                    onChange={e => setSettings({ ...settings, schoolEmail: e.target.value })}
                    className={inputClass}
                />
            </div>
            <div className="grid grid-cols-2 gap-4">
                <div>
                    <label className={labelClass}>Teléfono</label>
                    <input
                        type="text"
                        value={settings.phone}
                        onChange={e => setSettings({ ...settings, phone: e.target.value })}
                        className={inputClass}
                    />
                </div>
                <div>
                    <label className={labelClass}>Moneda</label>
                    <select
                        value={settings.currency}
                        onChange={e => setSettings({ ...settings, currency: e.target.value })}
                        className={inputClass}
                    >
                        <option value="GTQ">Quetzales (Q)</option>
                        <option value="USD">Dólares ($)</option>
                        <option value="MXN">Pesos (MXN)</option>
                    </select>
                </div>
            </div>
            <div>
                <label className={labelClass}>Dirección</label>
                <textarea
                    value={settings.address}
                    onChange={e => setSettings({ ...settings, address: e.target.value })}
                    rows={3}
                    className={`${inputClass} resize-none`}
                />
            </div>
        </div>
    );

    // eslint-disable-next-line unused-imports/no-unused-vars
    const NotificationSettings = () => (
        <div className="space-y-6">
            <div className={`flex items-center justify-between p-4 ${darkMode ? 'bg-gray-700' : 'bg-gray-50'} rounded-xl`}>
                <div>
                    <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>Notificaciones Push</p>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Recibir alertas en el navegador</p>
                </div>
                <label className="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        checked={settings.notifications}
                        onChange={e => setSettings({ ...settings, notifications: e.target.checked })}
                        className="sr-only peer"
                    />
                    <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                </label>
            </div>
            <div className={`flex items-center justify-between p-4 ${darkMode ? 'bg-gray-700' : 'bg-gray-50'} rounded-xl`}>
                <div>
                    <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>Alertas por Email</p>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Recibir resúmenes diarios por correo</p>
                </div>
                <label className="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        checked={settings.emailAlerts}
                        onChange={e => setSettings({ ...settings, emailAlerts: e.target.checked })}
                        className="sr-only peer"
                    />
                    <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                </label>
            </div>
            <div className={`flex items-center justify-between p-4 ${darkMode ? 'bg-gray-700' : 'bg-gray-50'} rounded-xl`}>
                <div>
                    <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>Respaldo Automático</p>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Guardar copia de seguridad diaria</p>
                </div>
                <label className="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        checked={settings.autoBackup}
                        onChange={e => setSettings({ ...settings, autoBackup: e.target.checked })}
                        className="sr-only peer"
                    />
                    <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                </label>
            </div>
        </div>
    );

    // eslint-disable-next-line unused-imports/no-unused-vars
    const AppearanceSettings = () => (
        <div className="space-y-6">
            <div className={`flex items-center justify-between p-4 ${darkMode ? 'bg-gray-700' : 'bg-gray-50'} rounded-xl`}>
                <div>
                    <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>Modo Oscuro</p>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cambiar a tema oscuro</p>
                </div>
                <label className="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        checked={darkMode}
                        onChange={toggleDarkMode}
                        className="sr-only peer"
                    />
                    <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                </label>
            </div>
            <div>
                <label className={labelClass}>Idioma</label>
                <select
                    value={settings.language}
                    onChange={e => setSettings({ ...settings, language: e.target.value })}
                    className={inputClass}
                >
                    <option value="es">Español</option>
                    <option value="en">English</option>
                </select>
            </div>
        </div>
    );

    // eslint-disable-next-line unused-imports/no-unused-vars
    const SecuritySettings = () => (
        <div className="space-y-6">
            <div className={`p-4 ${darkMode ? 'bg-yellow-900/30 border-yellow-700' : 'bg-yellow-50 border-yellow-200'} border rounded-xl`}>
                <p className={`font-medium ${darkMode ? 'text-yellow-300' : 'text-yellow-800'}`}>⚠️ Sección sensible</p>
                <p className={`text-sm ${darkMode ? 'text-yellow-200' : 'text-yellow-700'}`}>Los cambios aquí afectan la seguridad del sistema</p>
            </div>
            <button className={`w-full py-3 px-4 ${darkMode ? 'bg-gray-700 text-gray-200 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'} rounded-xl transition-colors font-medium`}>
                Cambiar Contraseña
            </button>
            <button className={`w-full py-3 px-4 ${darkMode ? 'bg-gray-700 text-gray-200 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'} rounded-xl transition-colors font-medium`}>
                Configurar 2FA
            </button>
            <button className={`w-full py-3 px-4 ${darkMode ? 'bg-red-900/50 text-red-300 hover:bg-red-900/70' : 'bg-red-50 text-red-700 hover:bg-red-100'} rounded-xl transition-colors font-medium`}>
                Cerrar Todas las Sesiones
            </button>
        </div>
    );

    // eslint-disable-next-line unused-imports/no-unused-vars
    const DatabaseSettings = () => (
        <div className="space-y-6">
            <div className={`p-4 ${darkMode ? 'bg-green-900/30 border-green-700' : 'bg-green-50 border-green-200'} border rounded-xl`}>
                <p className={`font-medium ${darkMode ? 'text-green-300' : 'text-green-800'}`}>✅ Base de datos conectada</p>
                <p className={`text-sm ${darkMode ? 'text-green-200' : 'text-green-700'}`}>PostgreSQL - Última sincronización: Hace un momento</p>
            </div>
            <button className={`w-full py-3 px-4 ${darkMode ? 'bg-blue-900/50 text-blue-300 hover:bg-blue-900/70' : 'bg-blue-100 text-blue-700 hover:bg-blue-200'} rounded-xl transition-colors font-medium`}>
                Exportar Datos (CSV)
            </button>
            <button className={`w-full py-3 px-4 ${darkMode ? 'bg-blue-900/50 text-blue-300 hover:bg-blue-900/70' : 'bg-blue-100 text-blue-700 hover:bg-blue-200'} rounded-xl transition-colors font-medium`}>
                Crear Respaldo Manual
            </button>
            <button className={`w-full py-3 px-4 ${darkMode ? 'bg-gray-700 text-gray-200 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'} rounded-xl transition-colors font-medium`}>
                Ver Logs del Sistema
            </button>
        </div>
    );

    // eslint-disable-next-line unused-imports/no-unused-vars
    const ProfileSettings = () => (
        <div className="space-y-6">
            <div className={`flex items-center gap-4 p-4 ${darkMode ? 'bg-gray-700' : 'bg-gray-50'} rounded-xl`}>
                <div className="w-20 h-20 bg-gradient-to-br from-teal-500 to-teal-700 rounded-full flex items-center justify-center text-3xl text-white font-bold">
                    {settings.schoolName?.charAt(0) || 'S'}
                </div>
                <div>
                    <p className={`font-bold text-xl ${darkMode ? 'text-white' : 'text-gray-900'}`}>{settings.schoolName}</p>
                    <p className={darkMode ? 'text-gray-400' : 'text-gray-500'}>{settings.schoolEmail}</p>
                </div>
            </div>
            <div>
                <label className={labelClass}>Email de Contacto</label>
                <input
                    type="text"
                    value={settings.schoolEmail}
                    onChange={e => setSettings({ ...settings, schoolEmail: e.target.value })}
                    className={inputClass}
                />
            </div>
        </div>
    );

    // eslint-disable-next-line unused-imports/no-unused-vars
    const BrandingSettings = () => (
        <div className="space-y-6">
            {/* Logo Uploads */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {/* Main Logo */}
                <div className={`p-4 rounded-xl border-2 border-dashed ${darkMode ? 'border-gray-600 bg-gray-700/50' : 'border-gray-300 bg-gray-50'} text-center`}>
                    <div className={`w-16 h-16 mx-auto mb-2 rounded-lg flex items-center justify-center ${darkMode ? 'bg-gray-600' : 'bg-gray-200'}`}>
                        {settings.logoMain ? (
                            <img src={settings.logoMain} alt="Logo" className="w-full h-full object-contain rounded-lg" />
                        ) : (
                            <Image size={32} className="text-gray-400" />
                        )}
                    </div>
                    <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>Logo Principal</p>
                    <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'} mb-2`}>Para encabezados</p>
                    <label className="px-3 py-1 bg-teal-600 hover:bg-teal-700 text-white text-sm rounded-lg cursor-pointer inline-flex items-center gap-1">
                        <Upload size={14} /> Subir
                        // eslint-disable-next-line unused-imports/no-unused-vars
                        <input type="file" className="hidden" accept="image/*" onChange={(_e) => toast.success('Imagen seleccionada')} />
                    </label>
                </div>

                {/* Alt Logo */}
                <div className={`p-4 rounded-xl border-2 border-dashed ${darkMode ? 'border-gray-600 bg-gray-700/50' : 'border-gray-300 bg-gray-50'} text-center`}>
                    <div className={`w-16 h-16 mx-auto mb-2 rounded-lg flex items-center justify-center ${darkMode ? 'bg-gray-600' : 'bg-gray-200'}`}>
                        {settings.logoAlt ? (
                            <img src={settings.logoAlt} alt="Logo Alt" className="w-full h-full object-contain rounded-lg" />
                        ) : (
                            <Image size={32} className="text-gray-400" />
                        )}
                    </div>
                    <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>Logo Alternativo</p>
                    <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'} mb-2`}>Versión reducida</p>
                    <label className="px-3 py-1 bg-teal-600 hover:bg-teal-700 text-white text-sm rounded-lg cursor-pointer inline-flex items-center gap-1">
                        <Upload size={14} /> Subir
                        // eslint-disable-next-line unused-imports/no-unused-vars
                        <input type="file" className="hidden" accept="image/*" onChange={(_e) => toast.success('Imagen seleccionada')} />
                    </label>
                </div>

                {/* Favicon */}
                <div className={`p-4 rounded-xl border-2 border-dashed ${darkMode ? 'border-gray-600 bg-gray-700/50' : 'border-gray-300 bg-gray-50'} text-center`}>
                    <div className={`w-16 h-16 mx-auto mb-2 rounded-lg flex items-center justify-center ${darkMode ? 'bg-gray-600' : 'bg-gray-200'}`}>
                        {settings.favicon ? (
                            <img src={settings.favicon} alt="Favicon" className="w-full h-full object-contain rounded-lg" />
                        ) : (
                            <Image size={32} className="text-gray-400" />
                        )}
                    </div>
                    <p className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>Favicon</p>
                    <p className={`text-xs ${darkMode ? 'text-gray-400' : 'text-gray-500'} mb-2`}>Icono de pestaña</p>
                    <label className="px-3 py-1 bg-teal-600 hover:bg-teal-700 text-white text-sm rounded-lg cursor-pointer inline-flex items-center gap-1">
                        <Upload size={14} /> Subir
                        // eslint-disable-next-line unused-imports/no-unused-vars
                        <input type="file" className="hidden" accept="image/*,.ico" onChange={(_e) => toast.success('Icono seleccionado')} />
                    </label>
                </div>
            </div>

            {/* Colors */}
            <div className="grid grid-cols-2 gap-4">
                <div>
                    <label className={labelClass}>Color Primario</label>
                    <div className="flex items-center gap-3">
                        <input
                            type="color"
                            value={settings.primaryColor}
                            onChange={(e) => setSettings({ ...settings, primaryColor: e.target.value })}
                            className="w-12 h-12 rounded-lg cursor-pointer border-2 border-gray-300"
                        />
                        <input
                            type="text"
                            value={settings.primaryColor}
                            onChange={(e) => setSettings({ ...settings, primaryColor: e.target.value })}
                            className={`flex-1 ${inputClass}`}
                        />
                    </div>
                </div>
                <div>
                    <label className={labelClass}>Color Secundario</label>
                    <div className="flex items-center gap-3">
                        <input
                            type="color"
                            value={settings.secondaryColor}
                            onChange={(e) => setSettings({ ...settings, secondaryColor: e.target.value })}
                            className="w-12 h-12 rounded-lg cursor-pointer border-2 border-gray-300"
                        />
                        <input
                            type="text"
                            value={settings.secondaryColor}
                            onChange={(e) => setSettings({ ...settings, secondaryColor: e.target.value })}
                            className={`flex-1 ${inputClass}`}
                        />
                    </div>
                </div>
            </div>

            {/* PDF Configuration */}
            <div className={`p-4 rounded-xl ${darkMode ? 'bg-gray-700' : 'bg-gray-50'}`}>
                <h3 className={`font-medium mb-4 ${darkMode ? 'text-white' : 'text-gray-800'}`}>Configuración de PDFs</h3>
                <div className="space-y-4">
                    <div>
                        <label className={labelClass}>Encabezado de PDFs</label>
                        <input
                            type="text"
                            value={settings.pdfHeader}
                            onChange={(e) => setSettings({ ...settings, pdfHeader: e.target.value })}
                            className={inputClass}
                            placeholder="Texto que aparece en el encabezado de documentos"
                        />
                    </div>
                    <div>
                        <label className={labelClass}>Pie de Página de PDFs</label>
                        <input
                            type="text"
                            value={settings.pdfFooter}
                            onChange={(e) => setSettings({ ...settings, pdfFooter: e.target.value })}
                            className={inputClass}
                            placeholder="Texto que aparece en el pie de documentos"
                        />
                    </div>
                </div>
            </div>

            {/* Preview Login */}
            <div>
                <button className={`w-full py-3 px-4 flex items-center justify-center gap-2 ${darkMode ? 'bg-blue-900/50 text-blue-300 hover:bg-blue-900/70' : 'bg-blue-100 text-blue-700 hover:bg-blue-200'} rounded-xl transition-colors font-medium`}>
                    <Eye size={20} /> Vista Previa Pantalla de Login
                </button>
            </div>
        </div>
    );

    const renderContent = () => {
        switch (activeTab) {
            case 'general': return <GeneralSettings />;
            case 'profile': return <ProfileSettings />;
            case 'branding': return <BrandingSettings />;
            case 'notifications': return <NotificationSettings />;
            case 'security': return <SecuritySettings />;
            case 'database': return <DatabaseSettings />;
            case 'appearance': return <AppearanceSettings />;
            default: return <GeneralSettings />;
        }
    };

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Configuración</h1>
                <button
                    onClick={handleSave}
                    className={`flex items-center gap-2 px-6 py-3 rounded-xl font-medium transition-all ${saved
                        ? 'bg-green-500 text-white'
                        : 'bg-teal-600 text-white hover:bg-teal-700'
                        }`}
                >
                    {saved ? <Check size={20} /> : <Save size={20} />}
                    {saved ? 'Guardado' : 'Guardar Cambios'}
                </button>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {/* Sidebar Tabs */}
                <div className={`${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} rounded-2xl shadow-sm border p-4`}>
                    <nav className="space-y-2">
                        {tabs.map(tab => (
                            <button
                                key={tab.id}
                                onClick={() => setActiveTab(tab.id)}
                                className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-colors text-left ${activeTab === tab.id
                                    ? darkMode ? 'bg-teal-900/50 text-teal-300 font-medium' : 'bg-teal-50 text-teal-700 font-medium'
                                    : darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-50'
                                    }`}
                            >
                                <tab.icon size={20} />
                                {tab.label}
                            </button>
                        ))}
                    </nav>
                </div>

                {/* Content */}
                <div className={`lg:col-span-3 ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'} rounded-2xl shadow-sm border p-6`}>
                    <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'} mb-6 flex items-center gap-2`}>
                        {tabs.find(t => t.id === activeTab)?.icon &&
                            <span className={`p-2 ${darkMode ? 'bg-teal-900/50' : 'bg-teal-100'} rounded-lg`}>
                                {(() => {
                                    const Icon = tabs.find(t => t.id === activeTab)?.icon;
                                    return Icon ? <Icon size={20} className="text-teal-500" /> : null;
                                })()}
                            </span>
                        }
                        {tabs.find(t => t.id === activeTab)?.label}
                    </h2>
                    {renderContent()}
                </div>
            </div>
        </div>
    );
};

export default SettingsPage;
