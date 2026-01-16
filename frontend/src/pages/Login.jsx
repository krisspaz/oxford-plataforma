import { toast } from '../utils/toast';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useAuth } from '../contexts/AuthContext';
import { useNavigate } from 'react-router-dom';
import { Eye, EyeOff, GraduationCap } from 'lucide-react';
import { useState } from 'react';

const loginSchema = z.object({
    email: z.string().email("Formato de email inválido").min(1, "El email es requerido"),
    password: z.string().min(1, "La contraseña es requerida"),
});

const Login = () => {
    const { login } = useAuth();
    const navigate = useNavigate();
    const [showPassword, setShowPassword] = useState(false);

    const { register, handleSubmit, formState: { errors, isSubmitting } } = useForm({
        resolver: zodResolver(loginSchema)
    });

    const onSubmit = async (data) => {
        try {
            const success = await login(data.email, data.password);
            if (success) {
                navigate('/');
            } else {
                toast.info('Credenciales inválidas');
            }
        } catch (error) {
            toast.info('Error iniciando sesión');
        }
    };

    return (
        <div className="min-h-screen flex bg-gradient-to-br from-obs-navy via-[#1a1a2e] to-obs-purple text-white overflow-hidden">
            {/* Left Side - Branding */}
            <div className="hidden lg:flex lg:w-1/2 flex-col justify-center p-16 relative">
                {/* Background Shape */}
                <div className="absolute top-0 right-0 w-96 h-96 bg-obs-blue/20 rounded-bl-full blur-3xl -mr-20 -mt-20"></div>
                <div className="absolute bottom-0 left-0 w-80 h-80 bg-obs-pink/20 rounded-tr-full blur-3xl -ml-20 -mb-20"></div>

                <div className="relative z-10">
                    <div className="flex items-center gap-3 mb-8">
                        <div className="w-16 h-16 bg-white rounded-2xl flex items-center justify-center p-2">
                            <img src="/logo-obs.jpg" alt="Logo OBS" className="w-full h-full object-contain" />
                        </div>
                        <div>
                            <h1 className="text-2xl font-bold leading-none">OBS</h1>
                            <p className="text-xs uppercase tracking-widest text-gray-300">Oxford Bilingual School</p>
                        </div>
                    </div>

                    <h2 className="text-5xl font-bold mb-6 leading-tight">
                        Somos tu colegio,<br />
                        somos tu familia
                    </h2>

                    <p className="text-gray-400 mb-10 max-w-md text-lg">
                        Portal académico integral para la gestión educativa.
                        Accede a calificaciones, pagos y comunicaciones en
                        una plataforma segura.
                    </p>

                    <div className="space-y-6">
                        <div className="flex items-center gap-4">
                            <div className="p-2 bg-obs-green/10 rounded-lg border border-obs-green/20">
                                <svg className="w-6 h-6 text-obs-green" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <span className="font-medium">Educación bilingüe de excelencia</span>
                        </div>
                        <div className="flex items-center gap-4">
                            <div className="p-2 bg-obs-blue/10 rounded-lg border border-obs-blue/20">
                                <svg className="w-6 h-6 text-obs-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <span className="font-medium">Plataforma segura y confiable</span>
                        </div>
                        <div className="flex items-center gap-4">
                            <div className="p-2 bg-obs-orange/10 rounded-lg border border-obs-orange/20">
                                <GraduationCap className="w-6 h-6 text-obs-orange" />
                            </div>
                            <span className="font-medium">Formación integral líder</span>
                        </div>
                    </div>
                </div>
            </div>

            {/* Right Side - Form */}
            <div className="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16">
                <div className="w-full max-w-md p-8 rounded-3xl border border-gray-800 bg-[#111827] shadow-2xl relative">

                    <div className="mb-8">
                        <h2 className="text-2xl font-bold mb-2">Bienvenido de nuevo</h2>
                        <p className="text-gray-400 text-sm">Ingresa tus credenciales para acceder al portal</p>
                    </div>

                    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
                        <div>
                            <label htmlFor="email" className="block text-sm font-medium text-gray-300 mb-2">Correo Electrónico</label>
                            <input
                                id="email"
                                {...register('email')}
                                type="email"
                                className="w-full px-4 py-3 bg-white text-gray-900 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-black"
                                placeholder="ejemplo@oxford.edu.gt"
                            />
                            {errors.email && <p className="text-red-400 text-xs mt-1">{errors.email.message}</p>}
                        </div>

                        <div>
                            <label htmlFor="password" className="block text-sm font-medium text-gray-300 mb-2">Contraseña</label>
                            <div className="relative">
                                <input
                                    id="password"
                                    {...register('password')}
                                    type={showPassword ? "text" : "password"}
                                    className="w-full px-4 py-3 bg-white text-gray-900 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-black"
                                    placeholder="••••••••"
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
                                    className="absolute right-3 top-3 text-gray-500 hover:text-gray-700"
                                >
                                    {showPassword ? <EyeOff size={20} /> : <Eye size={20} />}
                                </button>
                            </div>
                            <div className="flex justify-end mt-1">
                                <button type="button" className="text-xs text-gray-400 hover:text-white">¿Olvidaste tu contraseña?</button>
                            </div>
                            {errors.password && <p className="text-red-400 text-xs mt-1">{errors.password.message}</p>}
                        </div>

                        <button
                            type="submit"
                            disabled={isSubmitting}
                            className="w-full bg-gradient-to-r from-obs-pink to-obs-purple hover:from-pink-600 hover:to-purple-700 border border-transparent text-white py-3 rounded-lg font-bold tracking-wide transition-all transform hover:scale-[1.02] flex justify-between items-center px-6 mt-4 group shadow-lg shadow-obs-pink/25"
                        >
                            <span>Ingresar al Portal</span>
                            <span className="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center group-hover:bg-white/30 transition-colors">
                                <svg className="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </button>

                        <div className="text-center pt-4 border-t border-gray-800">
                            <a href="#" className="text-xs text-gray-400 hover:text-white">¿Necesitas ayuda? Contactar soporte técnico</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default Login;
