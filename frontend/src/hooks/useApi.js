/**
 * Sistema Oxford - React Query Hooks
 * ===================================
 * Centralized data fetching with caching, invalidation,
 * and automatic refetching.
 */
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../services/api';

// ===================================
// GENERIC HOOKS
// ===================================

/**
 * Generic GET hook with caching
 * @param {string|string[]} queryKey - Unique key for this query
 * @param {string} endpoint - API endpoint
 * @param {object} options - React Query options
 */
export const useApiQuery = (queryKey, endpoint, options = {}) => {
    return useQuery({
        queryKey: Array.isArray(queryKey) ? queryKey : [queryKey],
        queryFn: async () => {
            const response = await api.get(endpoint);
            return response;
        },
        staleTime: 5 * 60 * 1000, // 5 minutes default
        ...options,
    });
};

/**
 * Generic mutation hook (POST/PUT/DELETE)
 * @param {string} endpoint - API endpoint
 * @param {string} method - HTTP method ('post', 'put', 'delete')
 * @param {object} options - Mutation options including invalidates[]
 */
export const useApiMutation = (endpoint, method = 'post', options = {}) => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (data) => {
            if (method === 'delete') {
                return api.delete(endpoint);
            }
            return api[method](endpoint, data);
        },
        onSuccess: (data, variables, context) => {
            // Invalidate related queries
            if (options.invalidates) {
                options.invalidates.forEach(key => {
                    queryClient.invalidateQueries({ queryKey: [key] });
                });
            }
            options.onSuccess?.(data, variables, context);
        },
        onError: options.onError,
    });
};

// ===================================
// DOMAIN HOOKS - STUDENTS
// ===================================

export const useStudents = (params = {}) => {
    const queryString = new URLSearchParams(params).toString();
    return useApiQuery(
        ['students', params],
        `/students${queryString ? `?${queryString}` : ''}`,
        { staleTime: 5 * 60 * 1000 }
    );
};

export const useStudent = (id) => {
    return useApiQuery(
        ['student', id],
        `/students/${id}`,
        { enabled: !!id }
    );
};

export const useCreateStudent = (options = {}) => {
    return useApiMutation('/students', 'post', {
        invalidates: ['students'],
        ...options,
    });
};

export const useUpdateStudent = (id, options = {}) => {
    return useApiMutation(`/students/${id}`, 'put', {
        invalidates: ['students', 'student'],
        ...options,
    });
};

// ===================================
// DOMAIN HOOKS - TEACHERS
// ===================================

export const useTeachers = (params = {}) => {
    const queryString = new URLSearchParams(params).toString();
    return useApiQuery(
        ['teachers', params],
        `/teachers${queryString ? `?${queryString}` : ''}`,
        { staleTime: 5 * 60 * 1000 }
    );
};

export const useTeacher = (id) => {
    return useApiQuery(
        ['teacher', id],
        `/teachers/${id}`,
        { enabled: !!id }
    );
};

// ===================================
// DOMAIN HOOKS - GRADES & SECTIONS
// ===================================

export const useGrades = () => {
    return useApiQuery('grades', '/grades', {
        staleTime: 10 * 60 * 1000, // 10 minutes - rarely changes
    });
};

export const useSections = (gradeId) => {
    return useApiQuery(
        ['sections', gradeId],
        `/grades/${gradeId}/sections`,
        { enabled: !!gradeId }
    );
};

// ===================================
// DOMAIN HOOKS - SUBJECTS
// ===================================

export const useSubjects = (params = {}) => {
    const queryString = new URLSearchParams(params).toString();
    return useApiQuery(
        ['subjects', params],
        `/subjects${queryString ? `?${queryString}` : ''}`,
        { staleTime: 10 * 60 * 1000 }
    );
};

// ===================================
// DOMAIN HOOKS - BIMESTERS
// ===================================

export const useBimesters = () => {
    return useApiQuery('bimesters', '/bimesters', {
        staleTime: 30 * 60 * 1000, // 30 minutes
    });
};

// ===================================
// DOMAIN HOOKS - DASHBOARD
// ===================================

export const useDashboardStats = () => {
    return useApiQuery('dashboard-stats', '/dashboard/stats', {
        staleTime: 2 * 60 * 1000, // 2 minutes - refresh more often
    });
};

// ===================================
// DOMAIN HOOKS - PAYMENTS
// ===================================

export const usePayments = (studentId) => {
    return useApiQuery(
        ['payments', studentId],
        `/payments?studentId=${studentId}`,
        { enabled: !!studentId }
    );
};

export const usePendingQuotas = (studentId) => {
    return useApiQuery(
        ['quotas', studentId],
        `/payments/pending/${studentId}`,
        { enabled: !!studentId }
    );
};

// ===================================
// DOMAIN HOOKS - SCHEDULES
// ===================================

export const useSchedules = (filters = {}) => {
    const queryString = new URLSearchParams(filters).toString();
    return useApiQuery(
        ['schedules', filters],
        `/schedules${queryString ? `?${queryString}` : ''}`
    );
};

// ===================================
// DOMAIN HOOKS - ATTENDANCE
// ===================================

export const useAttendance = (date, sectionId) => {
    return useApiQuery(
        ['attendance', date, sectionId],
        `/attendance?date=${date}&sectionId=${sectionId}`,
        { enabled: !!date && !!sectionId }
    );
};

export const useSaveAttendance = (options = {}) => {
    return useApiMutation('/attendance', 'post', {
        invalidates: ['attendance'],
        ...options,
    });
};
