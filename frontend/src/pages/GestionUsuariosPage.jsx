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

    const inputClass = `px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300'}`;

    useEffect(() => {
        loadUsers();
    }, []);

    const loadUsers = async () => {
        setLoading(true);
        try {
            const response = await userService.getAll();
            if (response.success) {
                setUsers(response.data);
            }
        } catch (error) {
            console.error('Error loading users:', error);
            // Demo data
            setUsers([
                { id: 1, email: 'admin@oxford.edu', name: 'Administrador', roles: ['ROLE_SUPER_ADMIN'], isActive: true, lastLogin: '2025-01-16 10:30' },
                { id: 2, email: 'contabilidad@oxford.edu', name: 'Laura Finanzas', roles: ['ROLE_CONTABILIDAD'], isActive: true, lastLogin: '2025-01-16 09:15' },
                { id: 3, email: 'docente@oxford.edu', name: 'Prof. García', roles: ['ROLE_DOCENTE'], isActive: true, lastLogin: '2025-01-15 16:00' },
                { id: 4, email: 'secretaria@oxford.edu', name: 'María Secretaria', roles: ['ROLE_SECRETARIA'], isActive: false, lastLogin: '2025-01-10 11:00' },
            ]);
        } finally {
            setLoading(false);
        }
    };

    const filteredUsers = users.filter(u =>
        u.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
        u.name?.toLowerCase().includes(searchTerm.toLowerCase())
    );

    const toggleUserStatus = async (id) => {
        setActionLoading(id);
        try {
            await userService.toggleStatus(id);
            setUsers(users.map(u => u.id === id ? { ...u, isActive: !u.isActive } : u));
        } catch (error) {
            console.error('Error toggling user status:', error);
            setUsers(users.map(u => u.id === id ? { ...u, isActive: !u.isActive } : u));
        } finally {
            setActionLoading(null);
        }
    };

    const handleSave = async (formData) => {
        try {
            if (selectedUser) {
                await userService.update(selectedUser.id, formData);
            } else {
                await userService.create(formData);
            }
            loadUsers();
            setShowModal(false);
            setSelectedUser(null);
        } catch (error) {
            console.error('Error saving user:', error);
            setShowModal(false);
        }
    };

    const getRoleColor = (role) => {
        const colors = {
            'ROLE_SUPER_ADMIN': 'bg-red-100 text-red-700',
            'ROLE_DIRECCION': 'bg-purple-100 text-purple-700',
            'ROLE_COORDINACION': 'bg-blue-100 text-blue-700',
            'ROLE_CONTABILIDAD': 'bg-green-100 text-green-700',
            'ROLE_SECRETARIA': 'bg-yellow-100 text-yellow-700',
            'ROLE_INFORMATICA': 'bg-cyan-100 text-cyan-700',
            'ROLE_DOCENTE': 'bg-orange-100 text-orange-700',
            'ROLE_ALUMNO': 'bg-gray-100 text-gray-700',
        };
        return colors[role] || 'bg-gray-100 text-gray-700';
    };

    if (loading) {
        return (
            <div className={`${darkMode ? 'bg-gray-800' : 'bg-white'} rounded-xl p-12 text-center`}>
                <RefreshCw className="animate-spin mx-auto text-teal-500" size={32} />
                <p className={`mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando usuarios...</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Gestión de Usuarios</h1>
                <button onClick={() => { setSelectedUser(null); setShowModal(true); }} className="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
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
                            <th className={`px-4 py-3 text-left font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Último Acceso</th>
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
                                    {user.isActive ? (
                                        <span className="flex items-center justify-center gap-1 text-green-500"><Check size={16} /> Activo</span>
                                    ) : (
                                        <span className="flex items-center justify-center gap-1 text-red-500"><AlertCircle size={16} /> Inactivo</span>
                                    )}
                                </td>
                                <td className={`px-4 py-3 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{user.lastLogin || 'Nunca'}</td>
                                <td className="px-4 py-3">
                                    <div className="flex justify-center gap-1">
                                        <button onClick={() => { setSelectedUser(user); setShowModal(true); }} className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`} title="Editar">
                                            <Edit size={16} className="text-blue-500" />
                                        </button>
                                        <button onClick={() => toggleUserStatus(user.id)} disabled={actionLoading === user.id} className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`} title="Cambiar estado">
                                            {actionLoading === user.id ? (
                                                <RefreshCw size={16} className="animate-spin text-gray-500" />
                                            ) : user.isActive ? (
                                                <Lock size={16} className="text-red-500" />
                                            ) : (
                                                <Unlock size={16} className="text-green-500" />
                                            )}
                                        </button>
                                        <button className={`p-1.5 rounded ${darkMode ? 'hover:bg-gray-600' : 'hover:bg-gray-100'}`} title="Cambiar contraseña">
                                            <Key size={16} className="text-purple-500" />
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
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Nombre</label>
                                <input className={`${inputClass} w-full`} defaultValue={selectedUser?.name} />
                            </div>
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Email</label>
                                <input type="email" className={`${inputClass} w-full`} defaultValue={selectedUser?.email} />
                            </div>
                            <div>
                                <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Rol</label>
                                <select className={`${inputClass} w-full`} defaultValue={selectedUser?.roles?.[0]}>
                                    {roles.map(r => <option key={r.value} value={r.value}>{r.label}</option>)}
                                </select>
                            </div>
                            {!selectedUser && (
                                <div>
                                    <label className={`block text-sm font-medium mb-1 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Contraseña</label>
                                    <input type="password" className={`${inputClass} w-full`} />
                                </div>
                            )}
                        </div>
                        <div className="flex justify-end gap-3 mt-6">
                            <button onClick={() => setShowModal(false)} className={`px-4 py-2 rounded-lg ${darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-100'}`}>Cancelar</button>
                            <button onClick={() => handleSave({})} className="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">Guardar</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default GestionUsuariosPage;
