import { toast } from '../utils/toast';
import React, { useState, useEffect } from 'react';
import { Users, Plus, Edit, Lock, Unlock, Key, Search, X, RefreshCw, Check, AlertCircle } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { userService } from '../services';

const GestionUsuariosPage = () => {
    const { darkMode } = useTheme();
    const [searchTerm, setSearchTerm] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [selectedUser, setSelectedUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [actionLoading, setActionLoading] = useState(null);
    const [users, setUsers] = useState([]);

    const roles = [
        { value: 'ROLE_SUPER_ADMIN', label: 'Super Admin' },
        { value: 'ROLE_DIRECCION', label: 'Dirección' },
        { value: 'ROLE_COORDINACION', label: 'Coordinación' },
        { value: 'ROLE_CONTABILIDAD', label: 'Contabilidad' },
        { value: 'ROLE_SECRETARIA', label: 'Secretaría' },
        { value: 'ROLE_INFORMATICA', label: 'Informática' },
        { value: 'ROLE_DOCENTE', label: 'Docente' },
        { value: 'ROLE_ALUMNO', label: 'Alumno' },
    ];

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300 text-gray-900 bg-white'}`;

    // State for form data
    const [formData, setFormData] = useState({
        firstName: '',
        lastName: '',
        email: '',
        role: 'ROLE_USER',
        password: '',
        profileImage: null
    });
    const [showPassword, setShowPassword] = useState(false);

    useEffect(() => {
        loadUsers();
    }, []);

    const loadUsers = async () => {
        setLoading(true);
        try {
            const response = await userService.getAll();
            // Handle custom backend format: { success: true, data: [] }
            if (response && response.data && Array.isArray(response.data)) {
                setUsers(response.data);
            } else if (response && response.member) {
                setUsers(response.member);
            } else if (response && response['hydra:member']) {
                setUsers(response['hydra:member']);
            } else if (Array.isArray(response)) {
                setUsers(response);
            } else {
                console.error('Unexpected response format:', response);
                setUsers([]);
            }
        } catch (error) {
            console.error('Error loading users:', error);
            // Fallback for demo
        } finally {
            setLoading(false);
        }
    };

    const filteredUsers = users.filter(u =>
        u.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
        (u.name && u.name.toLowerCase().includes(searchTerm.toLowerCase()))
    );

    const toggleUserStatus = async (id) => {
        setActionLoading(id);
        try {
            await userService.toggleStatus(id);
            // Optimistic update
            setUsers(users.map(u => u.id === id ? { ...u, active: !u.active } : u));
        } catch (error) {
            console.error('Error toggling status:', error);
        } finally {
            setActionLoading(null);
        }
    };

    const openModal = (user = null) => {
        if (user) {
            const nameParts = (user.name || '').split(' ');
            const firstName = nameParts[0] || '';
            const lastName = nameParts.slice(1).join(' ') || '';

            setSelectedUser(user);
            setFormData({
                firstName,
                lastName,
                email: user.email || '',
                role: user.roles?.[0] || 'ROLE_USER',
                password: '', // Don't show existing password
                profileImage: null
            });
        } else {
            setSelectedUser(null);
            setFormData({
                firstName: '',
                lastName: '',
                email: '',
                role: 'ROLE_DOCENTE', // Default sensible
                password: '',
                profileImage: null
            });
        }
        setShowModal(true);
        setShowPassword(false);
    };

    const generatePassword = () => {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        let pass = "";
        for (let i = 0; i < 12; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        setFormData({ ...formData, password: pass });
    };

    const handleSave = async () => {
        try {
            // Validation
            if (!formData.firstName || !formData.lastName || !formData.email) {
                toast.info('Nombre, Apellido y Email son obligatorios');
                return;
            }
            if (!selectedUser && !formData.password) {
                toast.info('La contraseña es obligatoria para nuevos usuarios');
                return;
            }

            const payload = {
                name: `${formData.firstName} ${formData.lastName}`.trim(),
                email: formData.email,
                roles: [formData.role], // Backend expects 'roles' array, matches User entity properties
                password: formData.password
            };

            if (selectedUser) {
                await userService.update(selectedUser.id, payload);
            } else {
                await userService.create(payload);
            }

            setShowModal(false);
            loadUsers();
        } catch (error) {
            console.error('Error saving user:', error);
            toast.error('Error al guardar usuario: ' + error.message);
        }
    };

    const getRoleColor = (role) => {
        switch (role) {
            case 'ROLE_SUPER_ADMIN': return 'bg-purple-100 text-purple-800';
            case 'ROLE_DIRECCION': return 'bg-blue-100 text-blue-800';
            case 'ROLE_COORDINACION': return 'bg-indigo-100 text-indigo-800';
            case 'ROLE_CONTABILIDAD': return 'bg-emerald-100 text-emerald-800';
            case 'ROLE_SECRETARIA': return 'bg-pink-100 text-pink-800';
            case 'ROLE_INFORMATICA': return 'bg-cyan-100 text-cyan-800';
            case 'ROLE_DOCENTE': return 'bg-orange-100 text-orange-800';
            case 'ROLE_ALUMNO': return 'bg-green-100 text-green-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Gestión de Usuarios</h1>
                <button onClick={() => openModal()} className="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
                    <Plus size={18} /> Nuevo Usuario
                </button>
            </div>

            {/* Search */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-4 shadow-sm`}>
                <div className="relative max-w-md">
                    <Search className={`absolute left-3 top-2.5 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`} size={18} />
                    <input
                        type="text"
                        placeholder="Buscar por nombre o email..."
                        value={searchTerm}
                        onChange={e => setSearchTerm(e.target.value)}
                        className={`${inputClass} w-full pl-10`}
                    />
                </div>
            </div>

            {/* Users Table */}
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-sm overflow-hidden`}>
                <table className="w-full">
                    <thead className={darkMode ? 'bg-gray-700' : 'bg-gray-50'}>
                        <tr>
                            <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Usuario</th>
                            <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Email</th>
                            <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Rol</th>
                            <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Estado</th>
                            <th className={`px-4 py-3 text-center font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Acciones</th>
                        </tr>
                    </thead>
                    <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-100'}`}>
                        {filteredUsers.map(user => (
                            <tr key={user.id} className={darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'}>
                                <td className={`px-4 py-3 font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                                    <div className="flex items-center gap-3">
                                        <div className="w-8 h-8 bg-teal-100 text-teal-700 rounded-full flex items-center justify-center font-bold">
                                            {user.name?.charAt(0) || user.email.charAt(0).toUpperCase()}
                                        </div>
                                        {user.name || 'Sin nombre'}
                                    </div>
                                </td>
                                <td className={`px-4 py-3 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{user.email}</td>
                                <td className="px-4 py-3">
                                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${getRoleColor(user.roles?.[0])}`}>
                                        {roles.find(r => r.value === user.roles?.[0])?.label || user.roles?.[0]}
                                    </span>
                                </td>
                                <td className="px-4 py-3 text-center">
                                    {user.active ? (
                                        <span className="flex items-center justify-center gap-1 text-green-500"><Check size={16} /> Activo</span>
                                    ) : (
                                        <span className="flex items-center justify-center gap-1 text-red-500"><AlertCircle size={16} /> Inactivo</span>
                                    )}
                                </td>
                                <td className="px-4 py-3">
                                    <div className="flex justify-center gap-1">
                                        <button onClick={() => openModal(user)} className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`} title="Editar">
                                            <Edit size={16} className="text-blue-500" />
                                        </button>
                                        <button onClick={() => toggleUserStatus(user.id)} disabled={actionLoading === user.id} className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`} title={user.active ? "Desactivar usuario" : "Activar usuario"}>
                                            {actionLoading === user.id ? <RefreshCw size={16} className="animate-spin text-gray-500" /> : (user.active ? <Lock size={16} className="text-orange-500" /> : <Unlock size={16} className="text-emerald-500" />)}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl shadow-lg w-full max-w-md p-6`}>
                        <div className="flex items-center justify-between mb-4">
                            <h2 className={`text-xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {selectedUser ? 'Editar Usuario' : 'Nuevo Usuario'}
                            </h2>
                            <button onClick={() => setShowModal(false)}><X size={20} /></button>
                        </div>
                        <div className="space-y-4">
                            {/* Profile Image - Mock */}
                            <div className="flex justify-center mb-4">
                                <div className="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center relative cursor-pointer group">
                                    <span className="text-gray-500 text-2xl">{formData.name?.charAt(0) || 'U'}</span>
                                    <div className="absolute inset-0 bg-black/30 rounded-full hidden group-hover:flex items-center justify-center text-white text-xs">Cambiar</div>
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Nombre</label>
                                    <input
                                        className={`${inputClass} w-full`}
                                        value={formData.firstName}
                                        onChange={e => setFormData({ ...formData, firstName: e.target.value })}
                                        placeholder="Ej. Juan"
                                    />
                                </div>
                                <div>
                                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Apellido</label>
                                    <input
                                        className={`${inputClass} w-full`}
                                        value={formData.lastName}
                                        onChange={e => setFormData({ ...formData, lastName: e.target.value })}
                                        placeholder="Ej. Pérez"
                                    />
                                </div>
                            </div>
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Email</label>
                                <input
                                    type="email"
                                    className={`${inputClass} w-full`}
                                    value={formData.email}
                                    onChange={e => setFormData({ ...formData, email: e.target.value })}
                                />
                            </div>
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Rol</label>
                                <select
                                    className={`${inputClass} w-full`}
                                    value={formData.role}
                                    onChange={e => setFormData({ ...formData, role: e.target.value })}
                                >
                                    {roles.map(r => <option key={r.value} value={r.value}>{r.label}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>
                                    {selectedUser ? 'Nueva Contraseña (Opcional)' : 'Contraseña'}
                                </label>
                                <div className="relative">
                                    <input
                                        type={showPassword ? "text" : "password"}
                                        className={`${inputClass} w-full pr-20`} // Space for buttons
                                        value={formData.password}
                                        onChange={e => setFormData({ ...formData, password: e.target.value })}
                                    />
                                    <div className="absolute right-2 top-2 flex gap-1">
                                        <button
                                            type="button"
                                            onClick={() => setShowPassword(!showPassword)}
                                            className="p-1 text-gray-500 hover:text-teal-600"
                                            title="Ver contraseña"
                                        >
                                            {showPassword ? <Unlock size={16} /> : <Lock size={16} />}
                                        </button>
                                        <button
                                            type="button"
                                            onClick={generatePassword}
                                            className="p-1 text-gray-500 hover:text-teal-600"
                                            title="Generar contraseña"
                                        >
                                            <RefreshCw size={16} />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="flex justify-end gap-3 mt-6">
                            <button onClick={() => setShowModal(false)} className={`px-4 py-2 rounded-lg ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}>Cancelar</button>
                            <button onClick={handleSave} className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">Guardar</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default GestionUsuariosPage;
