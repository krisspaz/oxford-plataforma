import React, { useState, useEffect } from 'react';
import { Mail, CheckCircle, Clock, AlertCircle, Filter, Search, User } from 'lucide-react';
import { Card, CardContent } from '@/components/ui/card';
import { helpTicketService } from '@/services';
import { toast } from 'react-hot-toast';

const HelpTicketsPage = () => {
    const [tickets, setTickets] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState('all'); // all, open, closed

    const loadTickets = async () => {
        setLoading(true);
        try {
            const data = await helpTicketService.getAll();
            setTickets(data);
        } catch (error) {
            console.error(error);
            toast.error('Error al cargar tickets');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadTickets();
    }, []);

    const handleCloseTicket = async (id) => {
        try {
            await helpTicketService.update(id, { status: 'closed' });
            toast.success('Ticket cerrado correctamente');
            loadTickets();
        } catch (error) {
            toast.error('Error al actualizar ticket');
        }
    };

    const filteredTickets = tickets.filter(t => {
        if (filter === 'all') return true;
        return t.status === filter;
    });

    const getStatusColor = (status) => {
        return status === 'closed' ? 'text-emerald-500 bg-emerald-500/10 border-emerald-500/30' : 'text-orange-500 bg-orange-500/10 border-orange-500/30';
    };

    return (
        <div className="p-6 space-y-6 bg-slate-950 min-h-screen text-slate-100 font-mono">
            <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 className="text-3xl font-bold flex items-center gap-3 text-emerald-400">
                        <Mail className="w-8 h-8" />
                        BUZÓN DE SOPORTE
                    </h1>
                    <p className="text-slate-400 mt-1">Gestión de Quejas y Sugerencias Estudiantiles</p>
                </div>

                <div className="flex gap-2 bg-slate-900 p-1 rounded-lg border border-slate-800">
                    <button
                        onClick={() => setFilter('all')}
                        className={`px-4 py-2 rounded text-sm transition-all ${filter === 'all' ? 'bg-slate-700 text-white shadow' : 'text-slate-400 hover:text-white'}`}
                    >
                        Todos
                    </button>
                    <button
                        onClick={() => setFilter('open')}
                        className={`px-4 py-2 rounded text-sm transition-all ${filter === 'open' ? 'bg-orange-900/40 text-orange-400 shadow' : 'text-slate-400 hover:text-white'}`}
                    >
                        Pendientes
                    </button>
                    <button
                        onClick={() => setFilter('closed')}
                        className={`px-4 py-2 rounded text-sm transition-all ${filter === 'closed' ? 'bg-emerald-900/40 text-emerald-400 shadow' : 'text-slate-400 hover:text-white'}`}
                    >
                        Cerrados
                    </button>
                </div>
            </div>

            {loading ? (
                <div className="flex justify-center items-center h-64">
                    <div className="w-10 h-10 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            ) : (
                <div className="grid gap-4">
                    {filteredTickets.length === 0 ? (
                        <div className="p-12 text-center text-slate-500 border border-slate-800 border-dashed rounded-xl bg-slate-900/50">
                            <Mail className="w-12 h-12 mx-auto mb-4 opacity-20" />
                            No hay tickets en esta categoría.
                        </div>
                    ) : (
                        filteredTickets.map(ticket => (
                            <div key={ticket.id} className="group relative bg-slate-900/80 backdrop-blur border border-slate-800 hover:border-emerald-500/50 rounded-xl p-6 transition-all duration-300">
                                <div className="flex flex-col md:flex-row justify-between gap-4">
                                    <div className="space-y-3 flex-1">
                                        <div className="flex items-center gap-3">
                                            <span className={`px-3 py-1 rounded text-xs font-bold uppercase border ${getStatusColor(ticket.status)}`}>
                                                {ticket.status === 'open' ? 'Pendiente' : 'Resuelto'}
                                            </span>
                                            <span className="text-xs text-slate-500 flex items-center gap-1">
                                                <Clock className="w-3 h-3" />
                                                {new Date(ticket.createdAt).toLocaleString()}
                                            </span>
                                            <span className="px-2 py-0.5 bg-slate-800 rounded text-xs text-slate-400 uppercase">
                                                {ticket.type}
                                            </span>
                                        </div>

                                        <h3 className="text-lg font-bold text-white group-hover:text-emerald-400 transition-colors">
                                            {ticket.subject}
                                        </h3>

                                        <p className="text-slate-300 bg-slate-950/50 p-4 rounded-lg border border-slate-800/50">
                                            "{ticket.message}"
                                        </p>

                                        <div className="flex items-center gap-2 text-sm text-slate-500">
                                            <User className="w-4 h-4" />
                                            <span>Estudiante ID: {ticket.student?.id || (typeof ticket.student === 'string' ? ticket.student.split('/').pop() : 'Anónimo')}</span>
                                            {/* ApiPlatform might return IRI string or object depending on serialization depth */}
                                        </div>
                                    </div>

                                    <div className="flex flex-col justify-start min-w-[150px]">
                                        {ticket.status === 'open' && (
                                            <button
                                                onClick={() => handleCloseTicket(ticket.id)}
                                                className="flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg transition-colors font-semibold shadow-lg shadow-emerald-900/20"
                                            >
                                                <CheckCircle className="w-4 h-4" />
                                                Marcar Resuelto
                                            </button>
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))
                    )}
                </div>
            )}
        </div>
    );
};

export default HelpTicketsPage;
