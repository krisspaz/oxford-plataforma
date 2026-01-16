import { toast } from '../utils/toast';
import React, { useState, useEffect } from 'react';
import { Package, User, CheckCircle, Search, Save, AlertCircle } from 'lucide-react';
import { useTheme } from '../contexts/ThemeContext';
import { studentService, packageService, financialService } from '../services';

const AsignarPaquetesPage = () => {
    const { darkMode } = useTheme();
    const [students, setStudents] = useState([]);
    const [packages, setPackages] = useState([]);

    const [selectedStudent, setSelectedStudent] = useState('');
    const [selectedPackage, setSelectedPackage] = useState('');

    const [loading, setLoading] = useState(false);
    const [loadingData, setLoadingData] = useState(true);
    const [studentSearch, setStudentSearch] = useState('');

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        setLoadingData(true);
        try {
            // Fetch students and packages in parallel
            const [studentsRes, packagesRes] = await Promise.all([
                studentService.getAll({ 'isActive': true }), // Assuming we only want active students
                packageService.getAll({ 'active': true })
            ]);

            if (studentsRes.data) {
                // Adjust based on your API response structure (hydra:member or direct array)
                const studentList = Array.isArray(studentsRes.data) ? studentsRes.data : (studentsRes.data['hydra:member'] || []);
                setStudents(studentList);
            }

            if (packagesRes.data) {
                const packageList = Array.isArray(packagesRes.data) ? packagesRes.data : (packagesRes.data['hydra:member'] || []);
                setPackages(packageList);
            }
        } catch (error) {
            console.error("Error loading data:", error);
        } finally {
            setLoadingData(false);
        }
    };

    const handleAssign = async () => {
        if (!selectedStudent || !selectedPackage) {
            alert("Por favor seleccione un estudiante y un paquete.");
            return;
        }

        if (!confirm("¿Está seguro de asignar este paquete al estudiante seleccionado?")) return;

        setLoading(true);
        try {
            const response = await financialService.assignPackage({
                studentId: selectedStudent,
                packageId: selectedPackage
            });

            if (response.success) {
                alert("Paquete asignado correctamente.");
                setSelectedStudent('');
                setSelectedPackage('');
            } else {
                alert("Error al asignar paquete: " + (response.message || "Error desconocido"));
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Ocurrió un error al procesar la solicitud.");
        } finally {
            setLoading(false);
        }
    };

    // Filter students for the dropdown/search
    const filteredStudents = students.filter(s =>
        (s.name + ' ' + s.lastname).toLowerCase().includes(studentSearch.toLowerCase()) ||
        (s.studentId || '').includes(studentSearch)
    );

    return (
        <div className="max-w-4xl mx-auto space-y-6">
            <h1 className={`text-2xl font-bold ${darkMode ? 'text-white' : 'text-gray-800'}`}>Asignar Paquetes de Cobro</h1>

            <div className={`p-8 rounded-xl shadow-lg border ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
                {loadingData ? (
                    <div className="text-center py-10">
                        <div className="animate-spin rounded-full h-10 w-10 border-b-2 border-teal-500 mx-auto"></div>
                        <p className={`mt-3 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Cargando datos...</p>
                    </div>
                ) : (
                    <div className="space-y-8">
                        {/* Student Selection */}
                        <div>
                            <label className={`block text-lg font-medium mb-3 flex items-center gap-2 ${darkMode ? 'text-gray-200' : 'text-gray-800'}`}>
                                <User size={20} className="text-teal-500" />
                                1. Seleccionar Estudiante
                            </label>

                            {/* Search Box */}
                            <div className="mb-3 relative">
                                <Search className={`absolute left-3 top-3 ${darkMode ? 'text-gray-500' : 'text-gray-400'}`} size={18} />
                                <input
                                    type="text"
                                    placeholder="Buscar por nombre o código..."
                                    value={studentSearch}
                                    onChange={(e) => setStudentSearch(e.target.value)}
                                    className={`w-full pl-10 pr-4 py-2 rounded-lg border focus:ring-2 focus:ring-teal-500 outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-gray-50 border-gray-300'}`}
                                />
                            </div>

                            <select
                                className={`w-full p-3 rounded-lg border text-lg ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300'}`}
                                value={selectedStudent}
                                onChange={(e) => setSelectedStudent(e.target.value)}
                                size={5} // Show multiple lines
                            >
                                <option value="" disabled className="py-2">-- Seleccione un estudiante --</option>
                                {filteredStudents.map(student => (
                                    <option key={student.id} value={student.id} className="py-2 px-2 hover:bg-teal-50/10 cursor-pointer">
                                        {student.name} {student.lastname} ({student.studentId || 'S/C'})
                                    </option>
                                ))}
                                {filteredStudents.length === 0 && (
                                    <option disabled>No se encontraron estudiantes</option>
                                )}
                            </select>
                            <p className={`text-sm mt-2 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>
                                {selectedStudent
                                    ? `Estudiante seleccionado: ${students.find(s => s.id == selectedStudent)?.name}`
                                    : 'Seleccione un estudiante de la lista'}
                            </p>
                        </div>

                        {/* Package Selection */}
                        <div>
                            <label className={`block text-lg font-medium mb-3 flex items-center gap-2 ${darkMode ? 'text-gray-200' : 'text-gray-800'}`}>
                                <Package size={20} className="text-teal-500" />
                                2. Seleccionar Paquete
                            </label>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-2">
                                {packages.map(pkg => (
                                    <div
                                        key={pkg.id}
                                        onClick={() => setSelectedPackage(pkg.id)}
                                        className={`p-4 rounded-lg border cursor-pointer relative transition-all duration-200 ${selectedPackage == pkg.id
                                            ? 'bg-teal-50 border-teal-500 ring-1 ring-teal-500 dark:bg-teal-900/20'
                                            : (darkMode ? 'bg-gray-700 border-gray-600 hover:bg-gray-600' : 'bg-white border-gray-200 hover:bg-gray-50')}`}
                                    >
                                        <div className="flex justify-between items-start mb-2">
                                            <span className={`font-bold text-lg ${darkMode ? 'text-white' : 'text-gray-900'}`}>{pkg.name}</span>
                                            {selectedPackage == pkg.id && <CheckCircle className="text-teal-500" size={24} />}
                                        </div>
                                        <p className={`text-sm mb-3 ${darkMode ? 'text-gray-300' : 'text-gray-600'}`}>{pkg.description || 'Sin descripción'}</p>
                                        <div className="flex justify-between items-center">
                                            <span className={`px-2 py-1 rounded-md text-xs font-semibold ${darkMode ? 'bg-gray-600' : 'bg-gray-100'}`}>
                                                {pkg.cycle?.name || 'Ciclo Actual'}
                                            </span>
                                            <span className="font-bold text-teal-600">
                                                Q {pkg.cost?.toLocaleString() || '0.00'}
                                            </span>
                                        </div>
                                    </div>
                                ))}
                                {packages.length === 0 && (
                                    <div className={`col-span-2 p-6 text-center border rounded-lg border-dashed ${darkMode ? 'border-gray-600 text-gray-400' : 'border-gray-300 text-gray-500'}`}>
                                        <AlertCircle className="mx-auto mb-2 opacity-50" />
                                        No hay paquetes disponibles.
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Action Button */}
                        <div className="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button
                                onClick={handleAssign}
                                disabled={loading || !selectedStudent || !selectedPackage}
                                className={`w-full py-4 rounded-xl font-bold text-lg shadow-lg flex items-center justify-center gap-2 transition-all ${loading
                                        ? 'bg-gray-400 cursor-not-allowed opacity-70'
                                        : 'bg-teal-600 hover:bg-teal-700 hover:shadow-teal-500/30 text-white transform hover:-translate-y-1'
                                    }`}
                            >
                                {loading ? (
                                    <>
                                        <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                                        Procesando...
                                    </>
                                ) : (
                                    <>
                                        <Save size={20} />
                                        Confirmar Asignación
                                    </>
                                )}
                            </button>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default AsignarPaquetesPage;
