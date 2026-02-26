import { useState } from 'react';
import { Menu, Eye, Home, Users, DollarSign, BookOpen, Settings } from 'lucide-react';
import { ROLE_MENUS, MENU_ITEMS } from '../config/roleMenus';

const GestionMenusPage = () => {
    const [selectedRole, setSelectedRole] = useState('ROLE_ADMIN');
    const [expandedSections, setExpandedSections] = useState({});

    // Iconos para las secciones
    const sectionIcons = {
        'Principal': Home,
        'Finanzas': DollarSign,
        'Inscripciones': Users,
        'Académico': BookOpen,
        'Docente': BookOpen,
        'Alumno': BookOpen,
        'Administración': Settings,
        'Supervisión': Eye,
        'Secretaría': Users
    };

    const toggleSection = (section) => {
        setExpandedSections(prev => ({
            ...prev,
            [section]: !prev[section]
        }));
    };

    const getRoleMenu = (roleId) => {
        return ROLE_MENUS[roleId] || [];
    };

    const currentMenu = getRoleMenu(selectedRole);

    // Lista de roles disponibles
    const availableRoles = [
        { id: 'ROLE_ADMIN', name: 'Administrador', color: 'bg-red-100 text-red-800' },
        { id: 'ROLE_DIRECCION', name: 'Director', color: 'bg-purple-100 text-purple-800' },
        { id: 'ROLE_COORDINACION', name: 'Coordinación', color: 'bg-blue-100 text-blue-800' },
        { id: 'ROLE_SECRETARIA', name: 'Secretaría', color: 'bg-green-100 text-green-800' },
        { id: 'ROLE_DOCENTE', name: 'Docente', color: 'bg-yellow-100 text-yellow-800' },
        { id: 'ROLE_ALUMNO', name: 'Estudiante', color: 'bg-cyan-100 text-cyan-800' },
        { id: 'ROLE_PADRE', name: 'Padre/Tutor', color: 'bg-indigo-100 text-indigo-800' },
        { id: 'ROLE_CONTABILIDAD', name: 'Contabilidad', color: 'bg-emerald-100 text-emerald-800' },
        { id: 'ROLE_INFORMATICA', name: 'Informática', color: 'bg-gray-100 text-gray-800' }
    ];

    const selectedRoleInfo = availableRoles.find(r => r.id === selectedRole);

    return (
        <div className="p-6 space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <Menu className="text-teal-600" size={32} />
                        Gestión de Menús
                    </h1>
                    <p className="text-gray-600 mt-1">Visualización de estructura de menús por rol</p>
                </div>
            </div>

            {/* Role Selector */}
            <div className="bg-white rounded-lg shadow p-6">
                <label className="block text-sm font-medium text-gray-700 mb-3">
                    Seleccionar Rol para Visualizar
                </label>
                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    {availableRoles.map((role) => (
                        <button
                            key={role.id}
                            onClick={() => setSelectedRole(role.id)}
                            className={`px-4 py-3 rounded-lg border-2 transition-all text-sm font-medium
                                ${selectedRole === role.id
                                    ? 'border-teal-500 bg-teal-50'
                                    : 'border-gray-200 hover:border-gray-300 bg-white'
                                }`}
                        >
                            <span className={selectedRole === role.id ? 'text-teal-900' : 'text-gray-700'}>
                                {role.name}
                            </span>
                        </button>
                    ))}
                </div>
            </div>

            {/* Menu Structure */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Menu Tree */}
                <div className="lg:col-span-2 bg-white rounded-lg shadow">
                    <div className="p-6 border-b border-gray-200">
                        <h3 className="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <Layers className="text-teal-600" size={20} />
                            Estructura de Menú - {selectedRoleInfo?.name}
                        </h3>
                        <p className="text-sm text-gray-600 mt-1">
                            {currentMenu.length} {currentMenu.length === 1 ? 'sección' : 'secciones'} disponibles
                        </p>
                    </div>

                    <div className="p-6 space-y-3">
                        {currentMenu.map((menuSection, idx) => {
                            // eslint-disable-next-line unused-imports/no-unused-vars
                            const SectionIcon = sectionIcons[menuSection.section] || Menu;
                            const isExpanded = expandedSections[menuSection.section] !== false;

                            return (
                                <div key={idx} className="border border-gray-200 rounded-lg overflow-hidden">
                                    {/* Section Header */}
                                    <button
                                        onClick={() => toggleSection(menuSection.section)}
                                        className="w-full px-4 py-3 bg-gray-50 hover:bg-gray-100 flex items-center justify-between transition-colors"
                                    >
                                        <div className="flex items-center gap-3">
                                            <SectionIcon className="text-gray-600" size={20} />
                                            <span className="font-medium text-gray-900">
                                                {menuSection.section}
                                            </span>
                                            <span className="text-sm text-gray-500">
                                                ({menuSection.items.length})
                                            </span>
                                        </div>
                                        {isExpanded ? (
                                            <ChevronDown className="text-gray-400" size={20} />
                                        ) : (
                                            <ChevronRight className="text-gray-400" size={20} />
                                        )}
                                    </button>

                                    {/* Menu Items */}
                                    {isExpanded && (
                                        <div className="p-4 space-y-2 bg-white">
                                            {menuSection.items.map((itemId, itemIdx) => {
                                                const menuItem = MENU_ITEMS[itemId];
                                                if (!menuItem) return null;

                                                return (
                                                    <div
                                                        key={itemIdx}
                                                        className="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 text-sm"
                                                    >
                                                        <div className="w-2 h-2 bg-teal-500 rounded-full"></div>
                                                        <span className="font-medium text-gray-700 flex-1">
                                                            {menuItem.label}
                                                        </span>
                                                        <span className="text-xs text-gray-400 font-mono">
                                                            {menuItem.path}
                                                        </span>
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    )}
                                </div>
                            );
                        })}

                        {currentMenu.length === 0 && (
                            <div className="text-center py-12 text-gray-500">
                                <Menu className="mx-auto mb-3 text-gray-300" size={48} />
                                <p>No hay menús configurados para este rol</p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Info Panel */}
                <div className="space-y-6">
                    {/* Current Role Info */}
                    <div className="bg-white rounded-lg shadow p-6">
                        <h3 className="font-semibold text-gray-900 mb-4">Rol Seleccionado</h3>
                        <div className={`px-4 py-3 rounded-lg ${selectedRoleInfo?.color} text-center font-medium mb-4`}>
                            {selectedRoleInfo?.name}
                        </div>
                        <div className="space-y-3 text-sm">
                            <div className="flex justify-between">
                                <span className="text-gray-600">Secciones:</span>
                                <span className="font-medium text-gray-900">{currentMenu.length}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600">Total Items:</span>
                                <span className="font-medium text-gray-900">
                                    {currentMenu.reduce((sum, section) => sum + section.items.length, 0)}
                                </span>
                            </div>
                        </div>
                    </div>

                    {/* Info Note */}
                    <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div className="flex items-start gap-3">
                            <Eye className="text-blue-600 flex-shrink-0 mt-0.5" size={18} />
                            <div className="text-sm text-blue-900">
                                <p className="font-medium mb-1">Modo de Solo Lectura</p>
                                <p className="text-blue-700">
                                    Esta vista muestra la configuración actual de menús por rol.
                                    La configuración se encuentra en <code className="bg-blue-100 px-1 rounded text-xs">roleMenus.js</code>
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Quick Stats */}
                    <div className="bg-white rounded-lg shadow p-6">
                        <h3 className="font-semibold text-gray-900 mb-4">Estadísticas Generales</h3>
                        <div className="space-y-3 text-sm">
                            <div className="flex justify-between">
                                <span className="text-gray-600">Total Roles:</span>
                                <span className="font-medium text-gray-900">{availableRoles.length}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600">Items de Menú:</span>
                                <span className="font-medium text-gray-900">
                                    {Object.keys(MENU_ITEMS).length}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default GestionMenusPage;
