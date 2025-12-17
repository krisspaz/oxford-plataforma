import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import taskService from '../services/taskService';
import attendanceService from '../services/attendanceService';
import scheduleService from '../services/scheduleService';

/**
 * React Query hooks for data fetching with caching
 */

// ============================================
// TASK HOOKS
// ============================================

export const useTasksQuery = (filters = {}) => {
    return useQuery({
        queryKey: ['tasks', filters],
        queryFn: () => taskService.getAll(filters),
        staleTime: 5 * 60 * 1000, // 5 minutes
    });
};

export const useTaskQuery = (taskId) => {
    return useQuery({
        queryKey: ['task', taskId],
        queryFn: () => taskService.getById(taskId),
        enabled: !!taskId,
    });
};

export const useTaskSubmissionsQuery = (taskId) => {
    return useQuery({
        queryKey: ['task-submissions', taskId],
        queryFn: () => taskService.getSubmissions(taskId),
        enabled: !!taskId,
    });
};

export const useCreateTaskMutation = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: taskService.create,
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['tasks'] });
        },
    });
};

export const useUpdateTaskMutation = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: ({ id, data }) => taskService.update(id, data),
        onSuccess: (_, { id }) => {
            queryClient.invalidateQueries({ queryKey: ['tasks'] });
            queryClient.invalidateQueries({ queryKey: ['task', id] });
        },
    });
};

export const useDeleteTaskMutation = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: taskService.delete,
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['tasks'] });
        },
    });
};

export const useGradeSubmissionMutation = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: ({ submissionId, data }) => taskService.gradeSubmission(submissionId, data),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['task-submissions'] });
        },
    });
};

// ============================================
// SCHEDULE HOOKS
// ============================================

export const useMyScheduleQuery = (cycleId = null) => {
    return useQuery({
        queryKey: ['my-schedule', cycleId],
        queryFn: () => scheduleService.getMySchedule(cycleId),
        staleTime: 10 * 60 * 1000, // 10 minutes (schedules don't change often)
    });
};

export const useWeeklyScheduleQuery = (teacherId, cycleId) => {
    return useQuery({
        queryKey: ['weekly-schedule', teacherId, cycleId],
        queryFn: () => scheduleService.getWeeklySchedule(teacherId, cycleId),
        enabled: !!teacherId && !!cycleId,
        staleTime: 10 * 60 * 1000,
    });
};

export const useCurrentClassQuery = (teacherId) => {
    return useQuery({
        queryKey: ['current-class', teacherId],
        queryFn: () => scheduleService.getCurrentClass(teacherId),
        enabled: !!teacherId,
        refetchInterval: 60 * 1000, // Refresh every minute
    });
};

// ============================================
// ATTENDANCE HOOKS
// ============================================

export const useAttendanceQuery = (scheduleId, date) => {
    return useQuery({
        queryKey: ['attendance', scheduleId, date],
        queryFn: () => attendanceService.getBySchedule(scheduleId, date),
        enabled: !!scheduleId,
    });
};

export const useStudentAttendanceReportQuery = (studentId, bimesterId) => {
    return useQuery({
        queryKey: ['attendance-report', studentId, bimesterId],
        queryFn: () => attendanceService.getStudentReport(studentId, bimesterId),
        enabled: !!studentId && !!bimesterId,
        staleTime: 5 * 60 * 1000,
    });
};

export const useTeacherAttendanceReportQuery = (teacherId, bimesterId) => {
    return useQuery({
        queryKey: ['teacher-attendance-report', teacherId, bimesterId],
        queryFn: () => attendanceService.getTeacherReport(teacherId, bimesterId),
        enabled: !!teacherId && !!bimesterId,
        staleTime: 5 * 60 * 1000,
    });
};

export const useSaveAttendanceMutation = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: ({ scheduleId, date, attendances }) =>
            attendanceService.saveBatch(scheduleId, date, attendances),
        onSuccess: (_, { scheduleId, date }) => {
            queryClient.invalidateQueries({ queryKey: ['attendance', scheduleId] });
            queryClient.invalidateQueries({ queryKey: ['attendance-report'] });
            queryClient.invalidateQueries({ queryKey: ['teacher-attendance-report'] });
        },
    });
};
