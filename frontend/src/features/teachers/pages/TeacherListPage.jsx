import React, { useState } from 'react';
import { Users, Plus } from 'lucide-react';
import { useTeachers } from '../hooks/useTeachers';
import { TeacherCard } from '../components/TeacherCard';
import { Button, Input, Spinner, Modal } from '../../../components/ui';
import { useDebounce } from '../../../utils/performanceUtils';

const TeacherListPage = () => {
    const { teachers, loading } = useTeachers();
    const [searchTerm, setSearchTerm] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [selectedTeacher, setSelectedTeacher] = useState(null);

    const debouncedSearch = useDebounce(searchTerm, 300);

    const filteredTeachers = teachers.filter(t =>
        t.name.toLowerCase().includes(debouncedSearch.toLowerCase()) ||
        t.code.toLowerCase().includes(debouncedSearch.toLowerCase()) ||
        t.specialization.toLowerCase().includes(debouncedSearch.toLowerCase())
    );

    const handleEdit = (teacher) => {
        setSelectedTeacher(teacher);
        setShowModal(true);
    };

    const handleView = (teacher) => {
        console.log('View teacher', teacher);
    };

    const handleAddNew = () => {
        setSelectedTeacher(null);
        setShowModal(true);
    };

    if (loading) {
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
                <div className="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
                    <Users size={48} className="mx-auto mb-4 text-gray-300 dark:text-gray-600" />
                    <p className="text-gray-500 dark:text-gray-400">No se encontraron docentes</p>
                </div>
            )}

            <Modal
                isOpen={showModal}
                onClose={() => setShowModal(false)}
                title={selectedTeacher ? 'Editar Docente' : 'Nuevo Docente'}
                size="lg"
            >
                <form className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <Input label="Código" defaultValue={selectedTeacher?.code} placeholder="DOC-XXX" />
                        <div>
                            <label className="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                                Especialización
                            </label>
                            <select className="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option>Matemáticas</option>
                                <option>Lenguaje</option>
                                <option>Ciencias</option>
                            </select>
                        </div>
                    </div>
                    <Input label="Nombre Completo" defaultValue={selectedTeacher?.name} />
                    <div className="grid grid-cols-2 gap-4">
                        <Input label="Email" type="email" defaultValue={selectedTeacher?.email} />
                        <Input label="Teléfono" defaultValue={selectedTeacher?.phone} />
                    </div>

                    <div className="flex justify-end gap-3 mt-6">
                        <Button variant="ghost" onClick={() => setShowModal(false)}>
                            Cancelar
                        </Button>
                        <Button variant="primary" onClick={() => setShowModal(false)}>
                            Guardar
                        </Button>
                    </div>
                </form>
            </Modal>
        </div>
    );
};

export default TeacherListPage;
