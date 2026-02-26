
/**
 * Student Card Component
 * Presentational component for displaying student summary
 */
export const StudentCard = ({ student, onEdit, onView }) => {
    return (
        <Card
            className="h-full hover:border-blue-500 transition-colors border border-transparent"
            hoverable
        >
            <div className="flex justify-between items-start mb-4">
                <div>
                    <h3 className="text-lg font-bold text-gray-900 dark:text-white">
                        {student.firstName} {student.lastName}
                    </h3>
                    <p className="text-sm text-gray-500 dark:text-gray-400">
                        {student.code}
                    </p>
                </div>
                <Badge variant={student.status === 'active' ? 'success' : 'default'}>
                    {student.status === 'active' ? 'Activo' : 'Inactivo'}
                </Badge>
            </div>

            <div className="space-y-2 mb-6">
                <div className="flex justify-between text-sm">
                    <span className="text-gray-500">Grado:</span>
                    <span className="font-medium text-gray-900 dark:text-gray-200">
                        {student.grade?.name || 'N/A'}
                    </span>
                </div>
                <div className="flex justify-between text-sm">
                    <span className="text-gray-500">Sección:</span>
                    <span className="font-medium text-gray-900 dark:text-gray-200">
                        {student.section?.name || 'N/A'}
                    </span>
                </div>
            </div>

            <div className="flex gap-2 mt-auto">
                <Button
                    variant="outline"
                    size="sm"
                    className="flex-1"
                    onClick={() => onView(student.id)}
                >
                    Ver
                </Button>
                <Button
                    variant="primary"
                    size="sm"
                    className="flex-1"
                    onClick={() => onEdit(student.id)}
                >
                    Editar
                </Button>
            </div>
        </Card>
    );
};
