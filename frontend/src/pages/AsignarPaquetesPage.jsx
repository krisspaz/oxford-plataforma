import React, { useState } from 'react';
import { Package, User, CheckCircle } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';

const AsignarPaquetesPage = () => {
    const { darkMode } = useTheme();
    const [selectedStudent, setSelectedStudent] = useState('');
    const [selectedPackage, setSelectedPackage] = useState('');

    const handleAssign = () => {
        if (selectedStudent && selectedPackage) {
            alert("Paquete asignado correctamente (Simulación)");
        }
    };

    return (
        <div className="max-w-2xl mx-auto space-y-6">
            <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Asignar Paquetes de Cobro</h1>

            <div className={`p-8 rounded-xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                <div className="space-y-6">
                    <div>
                        <label className={`block text-sm font-medium mb-2 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Seleccionar Estudiante</label>
                        <select
                            className={`w-full p-3 rounded-lg border ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-gray-50 border-gray-300'}`}
                            value={selectedStudent}
                            onChange={(e) => setSelectedStudent(e.target.value)}
                        >
                            <option value="">-- Seleccione --</option>
                            <option value="1">Juan Perez</option>
                            <option value="2">Maria Lopez</option>
                        </select>
                    </div>

                    <div>
                        <label className={`block text-sm font-medium mb-2 ${darkMode ? 'text-gray-300' : 'text-gray-700'}`}>Seleccionar Paquete</label>
                        <div className="grid grid-cols-1 gap-4">
                            {['Inscripción + Mensualidad', 'Solo Mensualidad', 'Graduandos Completo'].map(pkg => (
                                <div
                                    key={pkg}
                                    onClick={() => setSelectedPackage(pkg)}
                                    className={`p-4 rounded-lg border cursor-pointer flex items-center justify-between transition-all ${selectedPackage === pkg
                                        ? 'bg-teal-50 border-teal-500 ring-1 ring-teal-500 dark:bg-teal-900/20'
                                        : (darkMode ? 'bg-gray-700 border-gray-600 hover:bg-gray-600' : 'bg-white border-gray-200 hover:bg-gray-50')}`}
                                >
                                    <div className="flex items-center gap-3">
                                        <Package className={selectedPackage === pkg ? 'text-teal-600' : 'text-gray-400'} />
                                        <span className={darkMode ? 'text-white' : 'text-gray-900'}>{pkg}</span>
                                    </div>
                                    {selectedPackage === pkg && <CheckCircle className="text-teal-500" size={20} />}
                                </div>
                            ))}
                        </div>
                    </div>

                    <button
                        onClick={handleAssign}
                        className="w-full py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-lg shadow-lg transition-transform hover:-translate-y-1"
                    >
                        Asignar Paquete
                    </button>
                </div>
            </div>
        </div>
    );
};

export default AsignarPaquetesPage;
