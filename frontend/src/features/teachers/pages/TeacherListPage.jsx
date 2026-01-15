import React, { useState } from 'react';
import { Users, Plus } from 'lucide-react';
import { useTeachers } from '../hooks/useTeachers';
import { TeacherCard } from '../components/TeacherCard';
import { Button, Input, Spinner, Modal, EmptyState } from '../../../components/ui';
import { useDebounce } from '../../../utils/performanceUtils';

const TeacherListPage = () => {
    const { teachers, loading, refetch, createTeacher } = useTeachers();
    const [searchTerm, setSearchTerm] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [selectedTeacher, setSelectedTeacher] = useState(null);
    const [formData, setFormData] = useState({});
    const [submitting, setSubmitting] = useState(false);

    const debouncedSearch = useDebounce(searchTerm, 300);

    const filteredTeachers = teachers.filter(t =>
        t.fullName?.toLowerCase().includes(debouncedSearch.toLowerCase()) ||
        t.employeeCode?.toLowerCase().includes(debouncedSearch.toLowerCase()) ||
        t.specialization?.toLowerCase().includes(debouncedSearch.toLowerCase())
    );

    const handleEdit = (teacher) => {
        setSelectedTeacher(teacher);
        setFormData(teacher); // Populate form
        setShowModal(true);
    };

    const handleView = (teacher) => {
        console.log('View teacher', teacher);
    };

    const handleAddNew = () => {
        setSelectedTeacher(null);
        setFormData({
            firstName: '',
            lastName: '',
            email: '',
            phone: '',
            employeeCode: '',
            specialization: 'Matemáticas',
            contractType: 'Tiempo Completo',
            hireDate: new Date().toISOString().split('T')[0]
        });
        setShowModal(true);
    };

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);

        // Basic validation
        if (!formData.firstName || !formData.lastName || !formData.email) {
            alert('Por favor complete los campos obligatorios');
            setSubmitting(false);
            return;
        }

        const result = await createTeacher(formData);

        if (result.success) {
            setShowModal(false);
            // Optionally show success toast
        } else {
            alert(result.error);
        }
        setSubmitting(false);
    };

    if (loading && !teachers.length) {
        return (
            <div className="flex justify-center items-center h-64">
                <Spinner size="lg" />
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Docentes</h1>
                    <p className="text-gray-500 dark:text-gray-400">Gestión del personal académico</p>
                </div>
                <Button
                    variant="primary"
                    icon={<Plus size={18} />}
                    onClick={handleAddNew}
                >
                    Nuevo Docente
                </Button>
            </div>

            <div className="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                <Input
                    placeholder="Buscar por nombre, código o especialidad..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="max-w-md"
                />
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {filteredTeachers.map(teacher => (
                    <TeacherCard
                        key={teacher.id}
                        teacher={teacher}
                        onEdit={handleEdit}
                        onView={handleView}
                    />
                ))}
            </div>

            {filteredTeachers.length === 0 && (
                <div className="col-span-full">
                    <EmptyState
                        title="No se encontraron docentes"
                        description={searchTerm ? `No hay resultados para "${searchTerm}"` : "Comienza registrando un nuevo docente en el sistema."}
                        action={
                            !searchTerm && (
                                <Button variant="primary" icon={<Plus size={18} />} onClick={handleAddNew}>
                                    Nuevo Docente
                                </Button>
                            )
                        }
                    />
                </div>
            )}

            <Modal
                isOpen={showModal}
                onClose={() => setShowModal(false)}
                title={selectedTeacher ? 'Editar Docente' : 'Nuevo Docente'}
                size="lg"
            >
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <Input
                            label="Código de Empleado"
                            name="employeeCode"
                            value={formData.employeeCode}
                            onChange={handleInputChange}
                            placeholder="DOC-XXX"
                        />
                        <div>
                            <label className="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                                Especialización
                            </label>
                            <select
                                name="specialization"
                                value={formData.specialization}
                                onChange={handleInputChange}
                                className="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                <option value="Matemáticas">Matemáticas</option>
                                <option value="Lenguaje">Lenguaje</option>
                                <option value="Ciencias">Ciencias</option>
                                <option value="Inglés">Inglés</option>
                                <option value="Computación">Computación</option>
                                <option value="General">General</option>
                            </select>
                        </div>
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <Input
                            label="Nombres *"
                            name="firstName"
                            value={formData.firstName}
                            onChange={handleInputChange}
                            required
                        />
                        <Input
                            label="Apellidos *"
                            name="lastName"
                            value={formData.lastName}
                            onChange={handleInputChange}
                            required
                        />
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <Input
                            label="Email *"
                            type="email"
                            name="email"
                            value={formData.email}
                            onChange={handleInputChange}
                            required
                        />
                        <Input
                            label="Teléfono"
                            name="phone"
                            value={formData.phone}
                            onChange={handleInputChange}
                        />
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                                Fecha Contratación
                            </label>
                            <Input
                                type="date"
                                name="hireDate"
                                value={formData.hireDate}
                                onChange={handleInputChange}
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                                Tipo Contrato
                            </label>
                            <select
                                name="contractType"
                                value={formData.contractType}
                                onChange={handleInputChange}
                                className="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                <option value="Tiempo Completo">Tiempo Completo</option>
                                <option value="Medio Tiempo">Medio Tiempo</option>
                                <option value="Por Horas">Por Horas</option>
                            </select>
                        </div>
                    </div>

                    <div className="flex justify-end gap-3 mt-6">
                        <Button type="button" variant="ghost" onClick={() => setShowModal(false)}>
                            Cancelar
                        </Button>
                        <Button type="submit" variant="primary" disabled={submitting}>
                            {submitting ? <Spinner size="sm" className="mr-2" /> : null}
                            {selectedTeacher ? 'Actualizar' : 'Guardar'}
                        </Button>
                    </div>
                </form>
            </Modal>
        </div>
    );
};

export default TeacherListPage;
