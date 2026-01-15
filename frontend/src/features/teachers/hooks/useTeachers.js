import { useState, useEffect, useCallback } from 'react';
import { teacherService } from '../../../services';

export const useTeachers = () => {
    const [teachers, setTeachers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const fetchTeachers = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            const data = await teacherService.getAll();
            setTeachers(data);
        } catch (err) {
            console.error('Error loading teachers:', err);
            setError(err.message);
            setTeachers([]);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchTeachers();
    }, [fetchTeachers]);

    const createTeacher = async (data) => {
        setLoading(true);
        try {
            await teacherService.create(data);
            await fetchTeachers(); // Reload list
            return { success: true };
        } catch (err) {
            console.error('Error creating teacher:', err);
            return { success: false, error: err.message || 'Error al crear docente' };
        } finally {
            setLoading(false);
        }
    };

    return { teachers, loading, error, refetch: fetchTeachers, createTeacher };
};
