import { useState, useCallback } from 'react';
import { useAuth } from '../../../contexts/AuthContext';

/**
 * Custom hook for managing student data
 * Follows the Container/Presenter pattern separation
 */
export const useStudents = () => {
    const [students, setStudents] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const { token } = useAuth();

    const fetchStudents = useCallback(async (filters = {}) => {
        setLoading(true);
        setError(null);
        try {
            const queryParams = new URLSearchParams(filters).toString();
            const response = await fetch(`/api/v1/students?${queryParams}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to fetch students');

            const data = await response.json();
            setStudents(data.items || []);
            return data;
        } catch (err) {
            setError(err.message);
            console.error('Error fetching students:', err);
        } finally {
            setLoading(false);
        }
    }, [token]);

    const getStudentInternal = useCallback(async (id) => {
        try {
            const response = await fetch(`/api/v1/students/${id}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            if (!response.ok) throw new Error('Failed to fetch student');
            return await response.json();
        } catch (err) {
            console.error(err);
            return null;
        }
    }, [token]);

    return {
        students,
        loading,
        error,
        fetchStudents,
        getStudent: getStudentInternal
    };
};
