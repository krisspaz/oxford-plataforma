import { useState } from 'react';
import { Shield, Users, Eye, Edit2, Check, X } from 'lucide-react';

const PrivilegiosPage = () => {
    const [selectedRole, setSelectedRole] = useState(null);

    // Roles del sistema (extraídos de roleMenus.js)
    const systemRoles = [
        {
            id: 'ROLE_ADMIN',
            name: 'Administrador',
            description: 'Acceso completo al sistema',
            color: 'bg-red-100 text-red-800',
            userCount: 1,
            permissions: ['Usuarios', 'Roles', 'Configuración', 'Reportes', 'Finanzas', 'Académico', 'Secretaría']
        },
        {
            id: 'ROLE_DIRECCION',
            name: 'Director',
            description: 'Supervisión académica y administrativa',
            color: 'bg-purple-100 text-purple-800',
            userCount: 1,
            permissions: ['Académico', 'Reportes Académicos', 'Horarios', 'Personal Docente', 'Cierre de Notas']
        },
        {
            id: 'ROLE_COORDINACION',
            name: 'Coordinación',
            description: 'Coordinación académica',
            color: 'bg-blue-100 text-blue-800',
            userCount: 1,
            permissions: ['Académico', 'Horarios', 'Asignación Materias', 'Reportes Académicos']
        },
        {
            id: 'ROLE_SECRETARIA',
            name: 'Secretaría',
            description: 'Gestión de inscripciones y matrículas',
            color: 'bg-green-100 text-green-800',
            userCount: 1,
            permissions: ['Inscripciones', 'Matrículas', 'Familias', 'Contratos', 'Documentos']
        },
        {
            id: 'ROLE_DOCENTE',
            name: 'Docente',
            description: 'Gestión de clases y calificaciones',
            color: 'bg-yellow-100 text-yellow-800',
            userCount: 1,
            permissions: ['Mis Alumnos', 'Asignar Notas', 'Tareas', 'Mi Horario', 'Contenido']
        },
        {
            id: 'ROLE_ALUMNO',
            name: 'Estudiante',
            description: 'Acceso a información académica personal',
            color: 'bg-cyan-100 text-cyan-800',
            userCount: 1,
            permissions: ['Mis Notas', 'Mi Horario', 'Tareas', 'Estado de Cuenta']
        },
        {
            id: 'ROLE_PADRE',
            name: 'Padre/Tutor',
            description: 'Información de hijos y pagos',
            color: 'bg-indigo-100 text-indigo-800',
            userCount: 1,
            permissions: ['Información Hijos', 'Estado de Cuenta', 'Contrato']
        },
        {
            id: 'ROLE_CONTABILIDAD',
            name: 'Contabilidad',
            description: 'Gestión financiera y contable',
            color: 'bg-emerald-100 text-emerald-800',
            userCount: 1,
            permissions: ['Finanzas', 'Pagos', 'Comprobantes', 'Insolventes', 'Reportes Financieros']
        },
        {
            id: 'ROLE_INFORMATICA',
            name: 'Informática',
            description: 'Soporte técnico y sistemas',
            color: 'bg-gray-100 text-gray-800',
            userCount: 1,
            permissions: ['Usuarios', 'Configuración', 'Logs', 'Respaldos']
        }
    ];

    return (
        <div className="p-6 space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <Shield className="text-teal-600" size={32} />
                        Privilegios y Roles
                    </h1>
                    <p className="text-gray-600 mt-1">Gestión de roles del sistema y sus permisos</p>
                </div>
            </div>

            {/* Stats Cards */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-white rounded-lg shadow p-6 border-l-4 border-teal-500">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-sm text-gray-600">Total Roles</p>
                            <p className="text-3xl font-bold text-gray-900">{systemRoles.length}</p>
                        </div>
                        <Shield className="text-teal-500" size={40} />
                    </div>
                </div>
                <div className="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-sm text-gray-600">Total Usuarios</p>
                            <p className="text-3xl font-bold text-gray-900">
                                {systemRoles.reduce((sum, role) => sum + role.userCount, 0)}
                            </p>
                        </div>
                        <Users className="text-blue-500" size={40} />
                    </div>
                </div>
                <div className="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-sm text-gray-600">Modo</p>
                            <p className="text-lg font-semibold text-gray-900">Solo Lectura</p>
                        </div>
                        <Eye className="text-purple-500" size={40} />
                    </div>
                </div>
            </div>

            {/* Roles Grid */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {systemRoles.map((role) => (
                    <div
                        key={role.id}
                        className="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow cursor-pointer border border-gray-200"
                        onClick={() => setSelectedRole(selectedRole?.id === role.id ? null : role)}
                    >
                        <div className="p-6">
                            {/* Role Header */}
                            <div className="flex items-start justify-between mb-4">
                                <div className="flex-1">
                                    <div className="flex items-center gap-3 mb-2">
                                        <span className={`px-3 py-1 rounded-full text-sm font-medium ${role.color}`}>
                                            {role.name}
                                        </span>
                                        <span className="text-sm text-gray-500">
                                            {role.userCount} {role.userCount === 1 ? 'usuario' : 'usuarios'}
                                        </span>
                                    </div>
                                    <p className="text-gray-600 text-sm">{role.description}</p>
                                    <p className="text-xs text-gray-400 mt-1 font-mono">{role.id}</p>
                                </div>
                                <Shield className="text-gray-400" size={24} />
                            </div>

                            {/* Permissions Preview */}
                            <div className="mt-4 pt-4 border-t border-gray-200">
                                <p className="text-sm font-medium text-gray-700 mb-2">
                                    Permisos ({role.permissions.length})
                                </p>

                                {selectedRole?.id === role.id ? (
                                    <div className="space-y-2">
                                        {role.permissions.map((permission, idx) => (
                                            <div key={idx} className="flex items-center gap-2 text-sm">
                                                <Check className="text-green-500" size={16} />
                                                <span className="text-gray-700">{permission}</span>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="flex flex-wrap gap-2">
                                        {role.permissions.slice(0, 3).map((permission, idx) => (
                                            <span key={idx} className="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                                {permission}
                                            </span>
                                        ))}
                                        {role.permissions.length > 3 && (
                                            <span className="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">
                                                +{role.permissions.length - 3} más
                                            </span>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {/* Info Note */}
            <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start gap-3">
                <Eye className="text-blue-600 flex-shrink-0" size={20} />
                <div className="text-sm text-blue-900">
                    <p className="font-medium mb-1">Modo de Solo Lectura</p>
                    <p className="text-blue-700">
                        Esta página muestra la configuración actual de roles y permisos del sistema.
                        Para modificar roles o permisos, contacte al administrador del sistema.
                    </p>
                </div>
            </div>
        </div>
    );
};

export default PrivilegiosPage;
