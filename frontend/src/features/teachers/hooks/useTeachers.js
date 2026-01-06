import { useState, useEffect, useCallback } from 'react';
import { catalogService } from '../../../services';

export const useTeachers = () => {
    const [teachers, setTeachers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const fetchTeachers = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            const response = await catalogService.getTeachers();
            if (response.success) {
                setTeachers(response.data);
            } else {
                // Fallback for demo if backend not ready
                throw new Error('Failed to fetch from API');
            }
        } catch (err) {
            console.error('Error loading teachers:', err);
            // Demo data fallback
            setTeachers([
                {
                    id: 1,
                    code: 'DOC-001',
                    name: 'Prof. Roberto García',
                    email: 'rgarcia@oxford.edu',
                    phone: '5512-3456',
                    specialization: 'Matemáticas',
                    hireDate: '2020-01-15',
                    status: 'ACTIVO',
                    subjectsCount: 4,
                    subjects: ['Matemáticas 1ro A', 'Matemáticas 1ro B', 'Matemáticas 2do A', 'Matemáticas 2do B']
                },
                {
                    id: 2,
                    code: 'DOC-002',
                    name: 'Profa. María López',
                    email: 'mlopez@oxford.edu',
                    phone: '5534-5678',
                    specialization: 'Comunicación y Lenguaje',
                    hireDate: '2019-03-10',
                    status: 'ACTIVO',
                    subjectsCount: 3,
                    subjects: ['Comunicación 1ro A', 'Comunicación 1ro B', 'Comunicación 2do A']
                },
                {
                    id: 3,
                    code: 'DOC-003',
                    name: 'Prof. Carlos Hernández',
                    email: 'chernandez@oxford.edu',
                    phone: '5556-7890',
                    specialization: 'Ciencias Naturales',
                    hireDate: '2021-06-01',
                    status: 'ACTIVO',
                    subjectsCount: 2,
                    subjects: ['Ciencias 2do A', 'Ciencias 2do B']
                },
                {
                    id: 4,
                    code: 'DOC-004',
                    name: 'Profa. Ana Martínez',
                    email: 'amartinez@oxford.edu',
                    phone: '5578-9012',
                    specialization: 'Inglés',
                    hireDate: '2022-01-10',
                    status: 'ACTIVO',
                    subjectsCount: 5,
                    subjects: ['Inglés 1ro A', 'Inglés 1ro B', 'Inglés 2do A', 'Inglés 2do B', 'Inglés 3ro A']
                },
            ]);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchTeachers();
    }, [fetchTeachers]);

    return { teachers, loading, error, refetch: fetchTeachers };
};
