import { describe, it, expect, vi, beforeEach } from 'vitest';
import api from '../api';

// Mock axios
vi.mock('../api', () => ({
    default: {
        get: vi.fn(),
        post: vi.fn(),
        put: vi.fn(),
        delete: vi.fn(),
    },
}));

import attendanceService from '../attendanceService';
import scheduleService from '../scheduleService';
import taskService from '../taskService';

describe('AttendanceService', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    describe('getBySchedule', () => {
        it('should call correct endpoint with scheduleId', async () => {
            api.get.mockResolvedValue({ data: { attendance: [] } });

            await attendanceService.getBySchedule(123);

            expect(api.get).toHaveBeenCalledWith('/attendance/by-schedule/123', { params: {} });
        });

        it('should include date parameter when provided', async () => {
            api.get.mockResolvedValue({ data: { attendance: [] } });

            await attendanceService.getBySchedule(123, '2024-12-16');

            expect(api.get).toHaveBeenCalledWith('/attendance/by-schedule/123', {
                params: { date: '2024-12-16' }
            });
        });
    });

    describe('saveBatch', () => {
        it('should post attendance data correctly', async () => {
            const attendanceData = [
                { studentId: 1, status: 'present' },
                { studentId: 2, status: 'absent' },
            ];
            api.post.mockResolvedValue({ data: { success: true } });

            await attendanceService.saveBatch(123, '2024-12-16', attendanceData);

            expect(api.post).toHaveBeenCalledWith('/attendance/batch', {
                scheduleId: 123,
                date: '2024-12-16',
                attendances: attendanceData,
            });
        });
    });

    describe('getStudentReport', () => {
        it('should call correct endpoint', async () => {
            api.get.mockResolvedValue({ data: { report: {} } });

            await attendanceService.getStudentReport(1, 2);

            expect(api.get).toHaveBeenCalledWith('/attendance/report/1/bimester/2');
        });
    });
});

describe('ScheduleService', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    describe('getMySchedule', () => {
        it('should call my-schedule endpoint', async () => {
            api.get.mockResolvedValue({ data: { schedule: [] } });

            await scheduleService.getMySchedule();

            expect(api.get).toHaveBeenCalledWith('/schedule/my-schedule', { params: {} });
        });

        it('should include cycleId when provided', async () => {
            api.get.mockResolvedValue({ data: { schedule: [] } });

            await scheduleService.getMySchedule(5);

            expect(api.get).toHaveBeenCalledWith('/schedule/my-schedule', {
                params: { cycleId: 5 }
            });
        });
    });

    describe('getWeeklySchedule', () => {
        it('should call weekly endpoint with teacherId and cycleId', async () => {
            api.get.mockResolvedValue({ data: { weekly: {} } });

            await scheduleService.getWeeklySchedule(1, 2);

            expect(api.get).toHaveBeenCalledWith('/schedule/weekly/1', {
                params: { cycleId: 2 }
            });
        });
    });

    describe('getCurrentClass', () => {
        it('should call current-class endpoint', async () => {
            api.get.mockResolvedValue({ data: { currentClass: null } });

            await scheduleService.getCurrentClass(1);

            expect(api.get).toHaveBeenCalledWith('/schedule/current-class/1');
        });
    });
});

describe('TaskService', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    describe('getAll', () => {
        it('should call tasks endpoint with filters', async () => {
            api.get.mockResolvedValue({ data: [] });

            await taskService.getAll({ teacherId: 1, bimesterId: 2 });

            expect(api.get).toHaveBeenCalledWith('/tasks', {
                params: { teacherId: 1, bimesterId: 2 }
            });
        });
    });

    describe('create', () => {
        it('should post new task', async () => {
            const taskData = {
                title: 'Test Task',
                description: 'Description',
                type: 'tarea',
                dueDate: '2024-12-20',
                points: 100,
            };
            api.post.mockResolvedValue({ data: { id: 1 } });

            await taskService.create(taskData);

            expect(api.post).toHaveBeenCalledWith('/tasks', taskData);
        });
    });

    describe('update', () => {
        it('should put updated task', async () => {
            const updateData = { title: 'Updated Title' };
            api.put.mockResolvedValue({ data: { success: true } });

            await taskService.update(1, updateData);

            expect(api.put).toHaveBeenCalledWith('/tasks/1', updateData);
        });
    });

    describe('delete', () => {
        it('should delete task', async () => {
            api.delete.mockResolvedValue({ data: { success: true } });

            await taskService.delete(1);

            expect(api.delete).toHaveBeenCalledWith('/tasks/1');
        });
    });

    describe('gradeSubmission', () => {
        it('should post grade data', async () => {
            const gradeData = { score: 95, feedback: 'Great work!' };
            api.post.mockResolvedValue({ data: { success: true } });

            await taskService.gradeSubmission(10, gradeData);

            expect(api.post).toHaveBeenCalledWith('/tasks/submissions/10/grade', gradeData);
        });
    });
});
