import React, { useEffect } from 'react';
import { useStudents } from '../hooks/useStudents';
import { StudentCard } from '../components/StudentCard';
import { Button, Spinner, Input } from '../../../components/ui';
import { useDebounce } from '../../../utils/performanceUtils';

/**
 * Student List Page
 * Main entry point for the Students feature
 */
const StudentListPage = () => {
    const { students, loading, error, fetchStudents } = useStudents();
    const [searchTerm, setSearchTerm] = React.useState('');
    const debouncedSearch = useDebounce(searchTerm, 500);

    useEffect(() => {
        fetchStudents({ search: debouncedSearch });
    }, [debouncedSearch, fetchStudents]);

    const handleSearch = (e) => {
        setSearchTerm(e.target.value);
    };

    return (
        <div className="p-6 space-y-6">
            <div className="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                        Estudiantes
                    </h1>
                    <p className="text-gray-500 dark:text-gray-400">
                        Gestiona el padrón estudiantil
                    </p>
                </div>
                <div className="flex gap-2 w-full md:w-auto">
                    <Button variant="primary" icon={<span>+</span>}>
                        Nuevo Estudiante
                    </Button>
                </div>
            </div>

            <div className="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm">
                <Input
                    placeholder="Buscar por nombre o código..."
                    value={searchTerm}
                    onChange={handleSearch}
                    className="max-w-md"
                />
            </div>

            {error && (
                <div className="bg-red-50 text-red-600 p-4 rounded-xl">
                    Error: {error}
                </div>
            )}

            {loading ? (
                <div className="flex justify-center py-12">
                    <Spinner size="lg" />
                </div>
            ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    {students.map(student => (
                        <StudentCard
                            key={student.id}
                            student={student}
                            onView={(id) => console.log('View', id)}
                            onEdit={(id) => console.log('Edit', id)}
                        />
                    ))}

                    {students.length === 0 && (
                        <div className="col-span-full text-center py-12 text-gray-500">
                            No se encontraron estudiantes
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};

export default StudentListPage;
