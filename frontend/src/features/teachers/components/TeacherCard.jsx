import React from 'react';
import { Edit, Eye, Book } from 'lucide-react';
import { Button, Card, Badge } from '../../../components/ui';

export const TeacherCard = ({ teacher, onEdit, onView }) => {
    return (
        <Card className="hover:shadow-md transition-shadow">
            <div className="flex items-start gap-4 mb-4">
                <div className="w-14 h-14 bg-gradient-to-br from-teal-500 to-teal-700 rounded-xl flex items-center justify-center text-white font-bold text-xl shrink-0">
                    {teacher.name.split(' ').slice(-1)[0].charAt(0)}
                </div>
                <div className="flex-1 min-w-0">
                    <div className="flex items-center justify-between">
                        <h3 className="font-bold text-gray-900 dark:text-white truncate pr-2">
                            {teacher.name}
                        </h3>
                        <Badge variant={teacher.status === 'ACTIVO' ? 'success' : 'secondary'}>
                            {teacher.status}
                        </Badge>
                    </div>
                    <p className="text-sm text-teal-600 dark:text-teal-400 font-medium">
                        {teacher.code}
                    </p>
                    <p className="text-sm text-gray-500 dark:text-gray-400 truncate">
                        {teacher.specialization}
                    </p>
                </div>
            </div>

            <div className="grid grid-cols-2 gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
                <div className="truncate">📧 {teacher.email}</div>
                <div className="truncate">📱 {teacher.phone}</div>
            </div>

            <div className="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg mb-4">
                <div className="flex items-center justify-between mb-2">
                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                        <Book size={14} className="inline mr-1" />
                        Carga Académica
                    </span>
                    <Badge variant="info">
                        {teacher.subjectsCount} materias
                    </Badge>
                </div>
                <div className="flex flex-wrap gap-1">
                    {teacher.subjects?.slice(0, 3).map((subj, i) => (
                        <span key={i} className="text-xs px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300">
                            {subj}
                        </span>
                    ))}
                    {teacher.subjects?.length > 3 && (
                        <span className="text-xs text-gray-400 self-center ml-1">
                            +{teacher.subjects.length - 3} más
                        </span>
                    )}
                </div>
            </div>

            <div className="flex gap-2">
                <Button
                    variant="outline"
                    size="sm"
                    className="flex-1"
                    icon={<Edit size={14} />}
                    onClick={() => onEdit(teacher)}
                >
                    Editar
                </Button>
                <Button
                    variant="ghost"
                    size="sm"
                    className="flex-1 text-teal-600 hover:text-teal-700 hover:bg-teal-50 dark:text-teal-400"
                    icon={<Eye size={14} />}
                    onClick={() => onView(teacher)}
                >
                    Ver Carga
                </Button>
            </div>
        </Card>
    );
};
