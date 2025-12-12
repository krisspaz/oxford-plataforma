import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Lock, Mail } from 'lucide-react';
import axios from 'axios';
import { useAuth } from '../contexts/AuthContext';

const Login = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const navigate = useNavigate();
    const { login } = useAuth();

    const handleLogin = async (e) => {
        e.preventDefault();
        setError('');
        setLoading(true);

        try {
            const { data } = await axios.post('http://localhost:8000/api/login_check', {
                username: email,
                password
            });

            if (data.token) {
                login(data.token);
                navigate('/');
            }
        } catch (err) {
            setError('Credenciales inválidas. Intente de nuevo.');
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-gray-100 to-gray-300 flex items-center justify-center px-4">
            <div className="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-200 transform transition-all">
                <div className="text-center mb-8">
                    <h1 className="text-3xl font-bold text-oxford-primary">Oxford Plataforma</h1>
                    <p className="text-gray-500 text-sm">Ingrese a su cuenta</p>
                </div>

                {error && (
                    <div className="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm text-center font-medium">
                        {error}
                    </div>
                )}

                <form onSubmit={handleLogin} className="space-y-6">
                    {/* Email */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Correo Electrónico
                        </label>
                        <div className="relative">
                            <Mail className="absolute left-3 top-3 text-gray-400" size={20} />
                            <input
                                type="email"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                placeholder="admin@oxford.edu"
                                required
                                className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg 
                                           focus:ring-2 focus:ring-oxford-primary focus:border-transparent 
                                           text-gray-800 outline-none transition"
                            />
                        </div>
                    </div>

                    {/* Password */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Contraseña
                        </label>
                        <div className="relative">
                            <Lock className="absolute left-3 top-3 text-gray-400" size={20} />
                            <input
                                type="password"
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                placeholder="••••••••"
                                required
                                className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg 
                                           focus:ring-2 focus:ring-oxford-primary focus:border-transparent 
                                           text-gray-800 outline-none transition"
                            />
                        </div>
                    </div>

                    {/* Submit */}
                    <button
                        type="submit"
                        disabled={loading}
                        className={`w-full bg-oxford-primary text-white font-bold py-3 rounded-lg 
                                   transition-all hover:bg-blue-900 
                                   ${loading ? 'opacity-60 cursor-not-allowed' : ''}`}
                    >
                        {loading ? 'Ingresando...' : 'Iniciar Sesión'}
                    </button>
                </form>
            </div>
        </div>
    );
};

export default Login;
