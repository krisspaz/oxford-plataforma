import React, { useState } from 'react';
import { LazyMotion, domAnimation, m, AnimatePresence } from 'framer-motion';
import {
    ArrowRight, CheckCircle, Shield, Users, Zap,
    BookOpen, DollarSign, Brain, Star, ChevronDown,
    Menu, X, Play, School
} from 'lucide-react';
import { Link } from 'react-router-dom';

const LandingPage = () => {
    const [activeTab, setActiveTab] = useState('academic');
    const [isMenuOpen, setIsMenuOpen] = useState(false);

    const variants = {
        hidden: { opacity: 0, y: 20 },
        visible: { opacity: 1, y: 0 }
    };

    return (
        <LazyMotion features={domAnimation}>
            <div className="min-h-screen bg-white dark:bg-[#0a0c10] text-gray-900 dark:text-gray-100 font-sans selection:bg-indigo-500 selection:text-white">

                {/* Navbar */}
                <nav className="fixed w-full z-50 bg-white/80 dark:bg-[#0a0c10]/80 backdrop-blur-lg border-b border-gray-100 dark:border-gray-800">
                    <div className="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
                        <div className="flex items-center gap-2">
                            <div className="w-10 h-10 bg-gradient-to-tr from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold shadow-lg shadow-indigo-500/20">
                                <School size={24} />
                            </div>
                            <span className="text-xl font-bold tracking-tight">Oxford System</span>
                        </div>

                        {/* Desktop Menu */}
                        <div className="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600 dark:text-gray-400">
                            <a href="#features" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Características</a>
                            <a href="#pricing" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Precios</a>
                            <a href="#testimonials" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Testimonios</a>
                        </div>

                        <div className="hidden md:flex gap-4">
                            <Link to="/login" className="px-5 py-2.5 rounded-full text-gray-600 dark:text-gray-300 font-bold hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                Ingresar
                            </Link>
                            <Link to="/login" className="px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-black rounded-full font-bold hover:opacity-90 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                                Solicitar Demo
                            </Link>
                        </div>

                        {/* Mobile Toggle */}
                        <button onClick={() => setIsMenuOpen(!isMenuOpen)} className="md:hidden p-2 text-gray-600 dark:text-gray-400">
                            {isMenuOpen ? <X /> : <Menu />}
                        </button>
                    </div>
                </nav>

                {/* Mobile Menu Overlay */}
                <AnimatePresence>
                    {isMenuOpen && (
                        <m.div
                            initial={{ opacity: 0, height: 0 }}
                            animate={{ opacity: 1, height: 'auto' }}
                            exit={{ opacity: 0, height: 0 }}
                            className="md:hidden fixed top-20 left-0 w-full bg-white dark:bg-[#0a0c10] border-b border-gray-100 dark:border-gray-800 z-40 overflow-hidden"
                        >
                            <div className="p-6 space-y-4 flex flex-col items-center">
                                <a href="#features" onClick={() => setIsMenuOpen(false)}>Características</a>
                                <a href="#pricing" onClick={() => setIsMenuOpen(false)}>Precios</a>
                                <Link to="/login" onClick={() => setIsMenuOpen(false)} className="w-full text-center py-3 bg-gray-100 dark:bg-gray-800 rounded-xl">Ingresar</Link>
                            </div>
                        </m.div>
                    )}
                </AnimatePresence>

                {/* Hero Section */}
                <header className="pt-32 pb-20 lg:pt-48 lg:pb-32 relative overflow-hidden">
                    {/* Background Blobs */}
                    <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full max-w-7xl pointer-events-none">
                        <div className="absolute top-20 left-10 w-72 h-72 bg-purple-500/20 rounded-full blur-[100px] animate-pulse"></div>
                        <div className="absolute top-40 right-10 w-96 h-96 bg-indigo-500/20 rounded-full blur-[100px] animate-pulse delay-1000"></div>
                    </div>

                    <div className="max-w-7xl mx-auto px-6 relative z-10 flex flex-col items-center text-center">
                        <m.div
                            initial="hidden" animate="visible" variants={variants} transition={{ duration: 0.5 }}
                            className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-sm font-semibold mb-8 border border-indigo-100 dark:border-indigo-800"
                        >
                            <span className="relative flex h-2 w-2">
                                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                <span className="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                            </span>
                            Nuevo: Reportes con IA Integrada
                        </m.div>

                        <m.h1
                            initial="hidden" animate="visible" variants={variants} transition={{ duration: 0.5, delay: 0.1 }}
                            className="text-5xl md:text-7xl lg:text-8xl font-extrabold tracking-tight leading-[1.1] mb-8"
                        >
                            El sistema operativo <br className="hidden md:block" />
                            de tu <span className="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 animate-gradient">Colegio Premium</span>
                        </m.h1>

                        <m.p
                            initial="hidden" animate="visible" variants={variants} transition={{ duration: 0.5, delay: 0.2 }}
                            className="text-xl md:text-2xl text-gray-500 dark:text-gray-400 max-w-2xl mb-12 leading-relaxed"
                        >
                            Automatiza la gestión académica, recupera cartera vencida y empodera a tus maestros con nuestra plataforma todo en uno.
                        </m.p>

                        <m.div
                            initial="hidden" animate="visible" variants={variants} transition={{ duration: 0.5, delay: 0.3 }}
                            className="flex flex-col sm:flex-row gap-4 w-full justify-center"
                        >
                            <Link to="/login" className="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full font-bold text-lg transition-all shadow-xl shadow-indigo-500/30 flex items-center justify-center gap-2 hover:-translate-y-1">
                                Empezar Gratis <ArrowRight size={20} />
                            </Link>
                            <button className="px-8 py-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-full font-bold text-lg transition-all flex items-center justify-center gap-2">
                                <Play size={20} fill="currentColor" /> Ver Demo (2 min)
                            </button>
                        </m.div>

                        {/* Dashboard Preview */}
                        <m.div
                            initial={{ opacity: 0, y: 100 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.8, delay: 0.5 }}
                            className="mt-20 relative w-full max-w-5xl group"
                        >
                            <div className="absolute -inset-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
                            <div className="relative rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800 shadow-2xl bg-white dark:bg-[#0f111a]">
                                <div className="flex items-center gap-2 px-4 py-3 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-[#0a0c10]">
                                    <div className="flex gap-1.5">
                                        <div className="w-3 h-3 rounded-full bg-red-400"></div>
                                        <div className="w-3 h-3 rounded-full bg-yellow-400"></div>
                                        <div className="w-3 h-3 rounded-full bg-green-400"></div>
                                    </div>
                                    <div className="ml-4 px-3 py-1 bg-gray-200 dark:bg-gray-800 rounded-md text-xs text-gray-500 font-mono w-64 flex justify-between">
                                        <span>app.oxford.edu.gt</span>
                                        <span>🔒</span>
                                    </div>
                                </div>
                                <img
                                    src="https://images.unsplash.com/photo-1497633762265-9d179a990aa6?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80"
                                    alt="Dashboard Interface"
                                    className="w-full h-auto opacity-90 hover:opacity-100 transition-opacity"
                                />
                            </div>
                        </m.div>
                    </div>
                </header>

                {/* Trusted By */}
                <section className="py-10 border-y border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-[#0f111a]/50">
                    <div className="max-w-7xl mx-auto px-6 text-center">
                        <p className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-8">Confían en nosotros</p>
                        <div className="flex flex-wrap justify-center items-center gap-12 grayscale opacity-60">
                            {['Liceo Guatemala', 'Colegio Americano', 'Montessori', 'Tecnológico'].map((school, i) => (
                                <span key={i} className="text-xl font-bold font-serif text-gray-400">{school}</span>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Interactive Modules Section */}
                <section id="features" className="py-32 bg-white dark:bg-[#0a0c10]">
                    <div className="max-w-7xl mx-auto px-6">
                        <div className="text-center mb-20">
                            <h2 className="text-4xl lg:text-5xl font-extrabold mb-6">Todo lo que necesitas.<br />Nada que no.</h2>
                            <p className="text-xl text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                                Deja de usar 5 herramientas diferentes. Centraliza tu colegio en una sola plataforma inteligente.
                            </p>
                        </div>

                        <div className="flex flex-col lg:flex-row gap-16 items-start">
                            {/* Tabs */}
                            <div className="w-full lg:w-1/3 flex flex-col gap-4">
                                {[
                                    { id: 'academic', icon: <BookOpen />, title: "Gestión Académica", desc: "Notas, asistencias y reportes en tiempo real." },
                                    { id: 'financial', icon: <DollarSign />, title: "Finanzas y Cobros", desc: "Facturación automática y control de mora." },
                                    { id: 'ai', icon: <Brain />, title: "Inteligencia Artificial", desc: "Predicción de deserción y tutoría virtual." }
                                ].map((tab) => (
                                    <button
                                        key={tab.id}
                                        onClick={() => setActiveTab(tab.id)}
                                        className={`text-left p-6 rounded-2xl transition-all duration-300 border ${activeTab === tab.id
                                            ? 'bg-indigo-50 dark:bg-indigo-900/10 border-indigo-500 shadow-lg'
                                            : 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 hover:border-gray-300'
                                            }`}
                                    >
                                        <div className={`w-12 h-12 rounded-xl flex items-center justify-center mb-4 ${activeTab === tab.id ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500'
                                            }`}>
                                            {tab.icon}
                                        </div>
                                        <h3 className={`text-xl font-bold mb-2 ${activeTab === tab.id ? 'text-indigo-900 dark:text-indigo-400' : 'text-gray-900 dark:text-gray-100'}`}>
                                            {tab.title}
                                        </h3>
                                        <p className="text-gray-500 text-sm leading-relaxed">{tab.desc}</p>
                                    </button>
                                ))}
                            </div>

                            {/* Content Preview */}
                            <div className="w-full lg:w-2/3 relative h-[500px] bg-gray-100 dark:bg-gray-800 rounded-3xl overflow-hidden shadow-2xl border border-gray-200 dark:border-gray-700">
                                <AnimatePresence mode='wait'>
                                    <motion.div
                                        key={activeTab}
                                        initial={{ opacity: 0, x: 20 }}
                                        animate={{ opacity: 1, x: 0 }}
                                        exit={{ opacity: 0, x: -20 }}
                                        transition={{ duration: 0.3 }}
                                        className="absolute inset-0 p-12 flex flex-col justify-center"
                                    >
                                        {activeTab === 'academic' && (
                                            <>
                                                <div className="absolute top-0 right-0 p-8 opacity-10"><BookOpen size={300} /></div>
                                                <h3 className="text-3xl font-bold mb-6">Académico sin Fricción</h3>
                                                <ul className="space-y-4 text-lg text-gray-600 dark:text-gray-300">
                                                    <li className="flex items-center gap-3"><CheckCircle className="text-green-500" /> Carga de notas masiva estilo Excel</li>
                                                    <li className="flex items-center gap-3"><CheckCircle className="text-green-500" /> Generación de boletas en 1 click</li>
                                                    <li className="flex items-center gap-3"><CheckCircle className="text-green-500" /> Portal para padres y alumnos (App Móvil)</li>
                                                </ul>
                                                <div className="mt-10 p-6 bg-white dark:bg-gray-900 rounded-xl shadow-lg w-fit border border-gray-100 dark:border-gray-700">
                                                    <div className="text-sm text-gray-500 mb-1">Promedio General</div>
                                                    <div className="text-4xl font-bold text-indigo-600">89.4 <span className="text-sm text-green-500 font-normal">↑ 12%</span></div>
                                                </div>
                                            </>
                                        )}
                                        {activeTab === 'financial' && (
                                            <>
                                                <div className="absolute top-0 right-0 p-8 opacity-10"><DollarSign size={300} /></div>
                                                <h3 className="text-3xl font-bold mb-6">Finanzas Saludables</h3>
                                                <ul className="space-y-4 text-lg text-gray-600 dark:text-gray-300">
                                                    <li className="flex items-center gap-3"><CheckCircle className="text-green-500" /> Facturación electrónica (FEL) automática</li>
                                                    <li className="flex items-center gap-3"><CheckCircle className="text-green-500" /> Recordatorios de pago por WhatsApp</li>
                                                    <li className="flex items-center gap-3"><CheckCircle className="text-green-500" /> Dashboard de flujo de caja en tiempo real</li>
                                                </ul>
                                                <div className="mt-10 p-6 bg-white dark:bg-gray-900 rounded-xl shadow-lg w-fit border border-gray-100 dark:border-gray-700">
                                                    <div className="text-sm text-gray-500 mb-1">Recaudado este mes</div>
                                                    <div className="text-4xl font-bold text-green-600">Q 145,200 <span className="text-sm text-gray-400 font-normal">/ Q 150k</span></div>
                                                </div>
                                            </>
                                        )}
                                        {activeTab === 'ai' && (
                                            <>
                                                <div className="absolute top-0 right-0 p-8 opacity-10"><Brain size={300} /></div>
                                                <h3 className="text-3xl font-bold mb-6">El Poder de la IA</h3>
                                                <ul className="space-y-4 text-lg text-gray-600 dark:text-gray-300">
                                                    <li className="flex items-center gap-3"><CheckCircle className="text-indigo-500" /> Detección temprana de alumnos en riesgo</li>
                                                    <li className="flex items-center gap-3"><CheckCircle className="text-indigo-500" /> Generador de horarios sin conflictos</li>
                                                    <li className="flex items-center gap-3"><CheckCircle className="text-indigo-500" /> Chatbot de asistencia 24/7</li>
                                                </ul>
                                                <div className="mt-10 flex gap-4">
                                                    <div className="px-4 py-2 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-lg text-sm font-semibold">Predicción: 98%</div>
                                                    <div className="px-4 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-semibold">AI Assistant ON</div>
                                                </div>
                                            </>
                                        )}
                                    </motion.div>
                                </AnimatePresence>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Pricing Section */}
                <section id="pricing" className="py-32 bg-gray-50 dark:bg-[#0f111a]">
                    <div className="max-w-7xl mx-auto px-6">
                        <div className="text-center mb-20">
                            <h2 className="text-4xl font-extrabold mb-6">Inversión simple y transparente</h2>
                            <p className="text-xl text-gray-500">Sin costos ocultos. Paga solo por alumno activo.</p>
                        </div>

                        <div className="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                            {/* Starter */}
                            <div className="p-8 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                                <h3 className="text-2xl font-bold mb-2">Starter</h3>
                                <div className="text-4xl font-extrabold mb-6">$1.00 <span className="text-lg text-gray-500 font-normal">/alumno/mes</span></div>
                                <p className="text-gray-500 mb-8">Para colegios en crecimiento que necesitan orden.</p>
                                <ul className="space-y-4 mb-8 text-gray-600 dark:text-gray-300">
                                    <li className="flex gap-2"><CheckCircle size={20} className="text-indigo-500" /> Gestión de Notas</li>
                                    <li className="flex gap-2"><CheckCircle size={20} className="text-indigo-500" /> Portal Padres</li>
                                    <li className="flex gap-2"><CheckCircle size={20} className="text-indigo-500" /> App Android</li>
                                </ul>
                                <button className="w-full py-3 border-2 border-indigo-600 text-indigo-600 rounded-xl font-bold hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition-colors">
                                    Elegir Plan
                                </button>
                            </div>

                            {/* Pro */}
                            <div className="p-8 bg-gray-900 text-white rounded-3xl border border-gray-800 shadow-2xl relative transform md:-translate-y-4">
                                <div className="absolute top-0 right-0 bg-gradient-to-r from-pink-500 to-purple-500 text-white text-xs font-bold px-3 py-1 rounded-bl-xl rounded-tr-2xl">MÁS POPULAR</div>
                                <h3 className="text-2xl font-bold mb-2">Pro</h3>
                                <div className="text-4xl font-extrabold mb-6">$2.00 <span className="text-lg text-gray-400 font-normal">/alumno/mes</span></div>
                                <p className="text-gray-400 mb-8">Automatización total para colegios medianos y grandes.</p>
                                <ul className="space-y-4 mb-8 text-gray-300">
                                    <li className="flex gap-2"><CheckCircle size={20} className="text-green-400" /> Todo en Starter</li>
                                    <li className="flex gap-2"><CheckCircle size={20} className="text-green-400" /> Facturación + Cobros</li>
                                    <li className="flex gap-2"><CheckCircle size={20} className="text-green-400" /> Asistente IA Básico</li>
                                    <li className="flex gap-2"><CheckCircle size={20} className="text-green-400" /> App iOS & Android</li>
                                </ul>
                                <button className="w-full py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition-colors">
                                    Empezar Prueba Gratis
                                </button>
                            </div>

                            {/* Enterprise */}
                            <div className="p-8 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                                <h3 className="text-2xl font-bold mb-2">Enterprise</h3>
                                <div className="text-4xl font-extrabold mb-6">A Medida</div>
                                <p className="text-gray-500 mb-8">Para corporaciones educativas y franquicias.</p>
                                <ul className="space-y-4 mb-8 text-gray-600 dark:text-gray-300">
                                    <li className="flex gap-2"><CheckCircle size={20} className="text-indigo-500" /> Todo en Pro</li>
                                    <li className="flex gap-2"><CheckCircle size={20} className="text-indigo-500" /> API Personalizada</li>
                                    <li className="flex gap-2"><CheckCircle size={20} className="text-indigo-500" /> Servidor Dedicado</li>
                                    <li className="flex gap-2"><CheckCircle size={20} className="text-indigo-500" /> Soporte VIP 24/7</li>
                                </ul>
                                <button className="w-full py-3 border-2 border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Contactar Ventas
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Testimonials */}
                <section id="testimonials" className="py-32 bg-white dark:bg-[#0a0c10] border-t border-gray-100 dark:border-gray-800">
                    <div className="max-w-7xl mx-auto px-6">
                        <h2 className="text-4xl font-extrabold text-center mb-16">Lo que dicen los directores</h2>
                        <div className="grid md:grid-cols-2 gap-8">
                            <div className="p-10 bg-gray-50 dark:bg-gray-900 rounded-3xl relative">
                                <div className="absolute top-8 left-8 text-6xl text-indigo-200 dark:text-indigo-900 font-serif">"</div>
                                <p className="text-xl text-gray-700 dark:text-gray-300 italic relative z-10 mb-6">
                                    Recuperamos el 40% de nuestra cartera vencida en los primeros 3 meses gracias a los recordatorios automáticos de Oxford. Es una inversión que se paga sola.
                                </p>
                                <div className="flex items-center gap-4">
                                    <div className="w-12 h-12 bg-gray-300 rounded-full overflow-hidden">
                                        <img src="https://i.pravatar.cc/150?u=a042581f4e29026024d" alt="Director" />
                                    </div>
                                    <div>
                                        <h4 className="font-bold">Lic. Carlos Méndez</h4>
                                        <p className="text-sm text-gray-500">Director General, Colegio San Sebastián</p>
                                    </div>
                                    <div className="ml-auto flex text-yellow-500">
                                        <Star fill="currentColor" size={16} />
                                        <Star fill="currentColor" size={16} />
                                        <Star fill="currentColor" size={16} />
                                        <Star fill="currentColor" size={16} />
                                        <Star fill="currentColor" size={16} />
                                    </div>
                                </div>
                            </div>
                            <div className="p-10 bg-gray-50 dark:bg-gray-900 rounded-3xl relative">
                                <div className="absolute top-8 left-8 text-6xl text-indigo-200 dark:text-indigo-900 font-serif">"</div>
                                <p className="text-xl text-gray-700 dark:text-gray-300 italic relative z-10 mb-6">
                                    La generación de horarios con IA solía tomarnos 2 semanas. Con Oxford lo hicimos en 15 minutos. El personal docente está más feliz que nunca.
                                </p>
                                <div className="flex items-center gap-4">
                                    <div className="w-12 h-12 bg-gray-300 rounded-full overflow-hidden">
                                        <img src="https://i.pravatar.cc/150?u=a042581f4e29026704d" alt="Coordinadora" />
                                    </div>
                                    <div>
                                        <h4 className="font-bold">Mtra. Ana Lucía Cruz</h4>
                                        <p className="text-sm text-gray-500">Coordinadora Académica</p>
                                    </div>
                                    <div className="ml-auto flex text-yellow-500">
                                        <Star fill="currentColor" size={16} />
                                        <Star fill="currentColor" size={16} />
                                        <Star fill="currentColor" size={16} />
                                        <Star fill="currentColor" size={16} />
                                        <Star fill="currentColor" size={16} />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* CTA Final */}
                <section className="py-20 bg-indigo-900 text-white relative overflow-hidden">
                    <div className="absolute top-0 left-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
                    <div className="max-w-4xl mx-auto px-6 text-center relative z-10">
                        <h2 className="text-4xl md:text-5xl font-extrabold mb-8">¿Listo para transformar tu colegio?</h2>
                        <p className="text-xl text-indigo-200 mb-10 max-w-2xl mx-auto">Únete a los colegios de alto rendimiento que ya usan Oxford System.</p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <Link to="/login" className="px-10 py-5 bg-white text-indigo-900 rounded-full font-bold text-xl hover:bg-gray-100 transition-colors shadow-2xl">
                                Solicitar Demo Personalizada
                            </Link>
                        </div>
                        <p className="mt-8 text-indigo-300 text-sm">Sin tarjeta de crédito requerida • Cancelación en cualquier momento</p>
                    </div>
                </section>

                {/* Footer */}
                <footer className="py-12 bg-gray-50 dark:bg-[#0a0c10] border-t border-gray-200 dark:border-gray-800">
                    <div className="max-w-7xl mx-auto px-6 grid md:grid-cols-4 gap-8 mb-12">
                        <div>
                            <div className="flex items-center gap-2 mb-6">
                                <div className="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">O</div>
                                <span className="text-xl font-bold tracking-tight">Oxford</span>
                            </div>
                            <p className="text-gray-500 text-sm">Plataforma integral para la gestión educativa moderna.</p>
                        </div>
                        <div>
                            <h4 className="font-bold mb-4">Producto</h4>
                            <ul className="space-y-2 text-sm text-gray-500">
                                <li><a href="#" className="hover:text-indigo-600">Académico</a></li>
                                <li><a href="#" className="hover:text-indigo-600">Financiero</a></li>
                                <li><a href="#" className="hover:text-indigo-600">App Móvil</a></li>
                                <li><a href="#" className="hover:text-indigo-600">Roadmap</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-bold mb-4">Compañía</h4>
                            <ul className="space-y-2 text-sm text-gray-500">
                                <li><a href="#" className="hover:text-indigo-600">Sobre Nosotros</a></li>
                                <li><a href="#" className="hover:text-indigo-600">Blog</a></li>
                                <li><a href="#" className="hover:text-indigo-600">Carreras</a></li>
                                <li><a href="#" className="hover:text-indigo-600">Contacto</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-bold mb-4">Legal</h4>
                            <ul className="space-y-2 text-sm text-gray-500">
                                <li><a href="#" className="hover:text-indigo-600">Privacidad</a></li>
                                <li><a href="#" className="hover:text-indigo-600">Términos</a></li>
                                <li><a href="#" className="hover:text-indigo-600">Seguridad</a></li>
                            </ul>
                        </div>
                    </div>
                    <div className="text-center text-gray-400 text-sm pt-8 border-t border-gray-200 dark:border-gray-800 mx-6">
                        <p>&copy; 2026 Corpo Oxford. Todos los derechos reservados.</p>
                    </div>
                </footer>
            </div>
        </LazyMotion>
    );
};

export default LandingPage;
