import React from 'react';
import { motion } from 'framer-motion';
import { ArrowRight, CheckCircle, Shield, Users, Zap } from 'lucide-react';
import { Link } from 'react-router-dom';

const LandingPage = () => {
    return (
        <div className="min-h-screen bg-white dark:bg-[#0a0c10] text-gray-900 dark:text-gray-100 font-sans">
            {/* Navbar */}
            <nav className="flex justify-between items-center p-6 max-w-7xl mx-auto">
                <div className="flex items-center gap-2">
                    <div className="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">O</div>
                    <span className="text-xl font-bold tracking-tight">Oxford System</span>
                </div>
                <div className="flex gap-4">
                    <Link to="/login" className="px-5 py-2.5 rounded-full text-indigo-600 dark:text-indigo-400 font-bold hover:bg-indigo-50 dark:hover:bg-gray-800 transition-colors">
                        Iniciar Sesión
                    </Link>
                    <Link to="/login" className="px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-black rounded-full font-bold hover:opacity-90 transition-opacity">
                        Solicitar Demo
                    </Link>
                </div>
            </nav>

            {/* Hero Section */}
            <header className="max-w-7xl mx-auto px-6 py-20 lg:py-32 flex flex-col lg:flex-row items-center gap-12">
                <div className="lg:w-1/2 space-y-8">
                    <motion.h1
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        className="text-5xl lg:text-7xl font-extrabold tracking-tight leading-tight"
                    >
                        El futuro de la <span className="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">gestión educativa</span>.
                    </motion.h1>
                    <motion.p
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.1 }}
                        className="text-xl text-gray-500 dark:text-gray-400 leading-relaxed max-w-xl"
                    >
                        Plataforma integral para colegios de alto rendimiento. Automatiza calificaciones, finanzas y comunicación en un solo lugar.
                    </motion.p>
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.2 }}
                        className="flex gap-4 flex-col sm:flex-row"
                    >
                        <Link to="/login" className="px-8 py-4 bg-indigo-600 text-white rounded-full font-bold text-lg hover:bg-indigo-700 transition-all shadow-lg hover:shadow-indigo-500/30 flex items-center justify-center gap-2">
                            Empezar Ahora <ArrowRight size={20} />
                        </Link>
                        <button className="px-8 py-4 border border-gray-200 dark:border-gray-800 rounded-full font-bold text-lg hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors flex items-center justify-center gap-2">
                            Ver Video
                        </button>
                    </motion.div>
                </div>
                <div className="lg:w-1/2 relative">
                    <div className="absolute -inset-4 bg-gradient-to-r from-pink-500 to-purple-500 rounded-full blur-3xl opacity-20 animate-pulse"></div>
                    <img
                        src="https://images.unsplash.com/photo-1497633762265-9d179a990aa6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
                        alt="Dashboard Preview"
                        className="relative rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 transform hover:scale-[1.02] transition-transform duration-500"
                    />
                </div>
            </header>

            {/* Features Grid */}
            <section className="py-24 bg-gray-50 dark:bg-[#0f111a]">
                <div className="max-w-7xl mx-auto px-6">
                    <div className="text-center mb-16">
                        <h2 className="text-3xl font-bold mb-4">Todo lo que necesitas para escalar</h2>
                        <p className="text-gray-500">Diseñado para directores, amado por maestros.</p>
                    </div>
                    <div className="grid md:grid-cols-3 gap-8">
                        {[
                            { icon: <Zap className="text-yellow-500" />, title: "Rápido y Fluido", desc: "Interfaz moderna que carga en milisegundos. Basado en React 19." },
                            { icon: <Shield className="text-green-500" />, title: "Seguridad Bancaria", desc: "Protección de datos con encriptación de grado militar y backups automáticos." },
                            { icon: <Users className="text-blue-500" />, title: "Gestión Total", desc: "Controla alumnos, docentes, pagos y notas desde un solo panel." }
                        ].map((feature, i) => (
                            <motion.div
                                whileHover={{ y: -5 }}
                                className="p-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700" key={i}
                            >
                                <div className="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mb-6">
                                    {feature.icon}
                                </div>
                                <h3 className="text-xl font-bold mb-3">{feature.title}</h3>
                                <p className="text-gray-500 dark:text-gray-400">{feature.desc}</p>
                            </motion.div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Footer */}
            <footer className="py-12 text-center text-gray-500 text-sm border-t border-gray-200 dark:border-gray-800">
                <p>&copy; 2026 Corpo Oxford. Todos los derechos reservados.</p>
            </footer>
        </div>
    );
};

export default LandingPage;
