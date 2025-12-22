import React, { useState, useEffect } from 'react';
import { UserPlus, Search, Handshake, Check, X, RefreshCw } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { studentService } from '../services';

const AsignarConvenioPage = () => {
    const { darkMode } = useTheme();
    const [searchTerm, setSearchTerm] = useState('');
    const [students, setStudents] = useState([]);
    const [selectedStudent, setSelectedStudent] = useState(null);
    const [selectedConvenio, setSelectedConvenio] = useState(null);
    const [loading, setLoading] = useState(false);
    const [assigning, setAssigning] = useState(false);
    const [showSuccess, setShowSuccess] = useState(false);

    // Mock convenios data
    const convenios = [
        { id: 1, name: 'Beca Excelencia Académica', discount: 50, type: 'Porcentual', description: 'Para estudiantes con promedio mayor a 90 puntos' },
        { id: 2, name: 'Beca Deportiva', discount: 30, type: 'Porcentual', description: 'Para atletas destacados' },
        { id: 3, name: 'Descuento Hermanos', discount: 15, type: 'Porcentual', description: 'Aplica a partir del segundo hermano' },
        { id: 4, name: 'Convenio Empleados', discount: 40, type: 'Porcentual', description: 'Para hijos de empleados de la institución' },
        { id: 5, name: 'Beca Situación Económica', discount: 25, type: 'Porcentual', description: 'Previa evaluación socioeconómica' },
    ];

    // Search students
    useEffect(() => {
        const searchStudents = async () => {
            if (searchTerm.length < 3) {
                setStudents([]);
                return;
            }
            setLoading(true);
            try {
                const response = await studentService.search(searchTerm);
                if (response.success) {
                    setStudents(response.data);
                }
            } catch (error) {
                console.error("Error searching students:", error);
                // Mock data fallback
                setStudents([
                    { id: 1, fullName: 'Juan García López', carnet: '2025-001', grade: '1ro Primaria' },
                    { id: 2, fullName: 'María Fernández', carnet: '2025-002', grade: '2do Primaria' },
                    { id: 3, fullName: 'Carlos Martínez', carnet: '2025-003', grade: '3ro Primaria' },
                ].filter(s => s.fullName.toLowerCase().includes(searchTerm.toLowerCase())));
            } finally {
                setLoading(false);
            }
        };
        const timeoutId = setTimeout(searchStudents, 500);
        return () => clearTimeout(timeoutId);
    }, [searchTerm]);

    const handleAssign = async () => {
        if (!selectedStudent || !selectedConvenio) return;

        setAssigning(true);
        try {
            // TODO: Call actual API endpoint
            await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate API call
            setShowSuccess(true);
            setTimeout(() => {
                setShowSuccess(false);
                setSelectedStudent(null);
                setSelectedConvenio(null);
            }, 3000);
        } catch (error) {
            console.error("Error assigning convenio:", error);
            alert("Error al asignar convenio");
        } finally {
            setAssigning(false);
        }
    };

    const inputClass = `px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'border-gray-300 bg-white'}`;

    return (
        <div className="max-w-4xl mx-auto space-y-6">
            <div className="flex justify-between items-center">
                <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Asignar Convenio a Estudiante</h1>
            </div>

            {/* Success Message */}
            {showSuccess && (
                <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
                    <Check className="text-green-600" size={24} />
                    <div>
                        <p className="font-bold">¡Convenio Asignado Exitosamente!</p>
                        <p className="text-sm">El convenio ha sido aplicado a la cuenta del estudiante.</p>
                    </div>
                </div>
            )}

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Step 1: Student Selection */}
                <div className={`p-6 rounded-xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                    <div className="flex items-center gap-3 mb-4">
                        <div className={`p-2 rounded-lg ${darkMode ? 'bg-blue-900/50 text-blue-400' : 'bg-blue-100 text-blue-600'}`}>
                            <UserPlus size={20} />
                        </div>
                        <h2 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>1. Seleccionar Estudiante</h2>
                    </div>

                    <div className="relative mb-4">
                        <Search className={`absolute left-3 top-3.5 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`} size={18} />
                        <input
                            type="text"
                            placeholder="Buscar por nombre o carnet..."
                            value={searchTerm}
                            onChange={e => setSearchTerm(e.target.value)}
                            className={`${inputClass} w-full pl-10`}
                        />
                    </div>

                    {loading && (
                        <div className="text-center py-4">
                            <RefreshCw className="animate-spin mx-auto text-blue-500" size={24} />
                        </div>
                    )}

                    {students.length > 0 && (
                        <div className="space-y-2 max-h-60 overflow-y-auto">
                            {students.map(s => (
                                <div
                                    key={s.id}
                                    onClick={() => { setSelectedStudent(s); setStudents([]); setSearchTerm(''); }}
                                    className={`p-3 rounded-lg cursor-pointer transition-all ${selectedStudent?.id === s.id
                                            ? 'bg-blue-100 border-blue-500 border-2'
                                            : (darkMode ? 'hover:bg-gray-700 border border-gray-600' : 'hover:bg-gray-100 border border-gray-200')
                                        }`}
                                >
                                    <span className={`font-medium ${darkMode ? 'text-white' : 'text-gray-800'}`}>{s.fullName}</span>
                                    <div className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                        {s.carnet} • {s.grade}
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}

                    {selectedStudent && (
                        <div className={`mt-4 p-4 rounded-lg border-2 border-blue-500 ${darkMode ? 'bg-blue-900/20' : 'bg-blue-50'}`}>
                            <div className="flex justify-between items-center">
                                <div>
                                    <p className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{selectedStudent.fullName}</p>
                                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{selectedStudent.carnet} • {selectedStudent.grade}</p>
                                </div>
                                <button onClick={() => setSelectedStudent(null)} className="text-red-500 hover:text-red-600">
                                    <X size={20} />
                                </button>
                            </div>
                        </div>
                    )}
                </div>

                {/* Step 2: Convenio Selection */}
                <div className={`p-6 rounded-xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                    <div className="flex items-center gap-3 mb-4">
                        <div className={`p-2 rounded-lg ${darkMode ? 'bg-purple-900/50 text-purple-400' : 'bg-purple-100 text-purple-600'}`}>
                            <Handshake size={20} />
                        </div>
                        <h2 className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>2. Seleccionar Convenio</h2>
                    </div>

                    <div className="space-y-3 max-h-80 overflow-y-auto">
                        {convenios.map(conv => (
                            <div
                                key={conv.id}
                                onClick={() => setSelectedConvenio(conv)}
                                className={`p-4 rounded-lg cursor-pointer transition-all ${selectedConvenio?.id === conv.id
                                        ? 'bg-purple-100 border-purple-500 border-2'
                                        : (darkMode ? 'hover:bg-gray-700 border border-gray-600' : 'hover:bg-gray-100 border border-gray-200')
                                    }`}
                            >
                                <div className="flex justify-between items-start">
                                    <div>
                                        <p className={`font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>{conv.name}</p>
                                        <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>{conv.description}</p>
                                    </div>
                                    <span className="text-lg font-bold text-purple-600">{conv.discount}%</span>
                                </div>
                                {selectedConvenio?.id === conv.id && (
                                    <div className="mt-2 flex items-center gap-2 text-purple-600">
                                        <Check size={16} />
                                        <span className="text-sm font-medium">Seleccionado</span>
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Assign Button */}
            <div className={`p-6 rounded-xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                {selectedStudent && selectedConvenio ? (
                    <div className="flex items-center justify-between">
                        <div>
                            <p className={`font-medium ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>Asignar</p>
                            <p className={`text-lg font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>
                                {selectedConvenio.name} ({selectedConvenio.discount}%)
                            </p>
                            <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                a {selectedStudent.fullName}
                            </p>
                        </div>
                        <button
                            onClick={handleAssign}
                            disabled={assigning}
                            className="px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold rounded-xl shadow-lg transition-all hover:-translate-y-1 disabled:opacity-50"
                        >
                            {assigning ? (
                                <span className="flex items-center gap-2">
                                    <RefreshCw className="animate-spin" size={18} />
                                    Procesando...
                                </span>
                            ) : (
                                'Confirmar Asignación'
                            )}
                        </button>
                    </div>
                ) : (
                    <p className={`text-center ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                        Selecciona un estudiante y un convenio para continuar
                    </p>
                )}
            </div>
        </div>
    );
};

export default AsignarConvenioPage;
