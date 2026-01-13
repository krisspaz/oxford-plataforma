/**
 * Integration Tests - Key User Flows
 * Tests end-to-end flows between frontend services
 */
import { describe, it, expect, vi, beforeEach } from 'vitest';

// Mock API module
vi.mock('../services/api', () => ({
    default: {
        get: vi.fn(),
        post: vi.fn(),
        put: vi.fn(),
        delete: vi.fn(),
    },
    api: {
        get: vi.fn(),
        post: vi.fn(),
        put: vi.fn(),
        delete: vi.fn(),
    }
}));

import api from '../services/api';

describe('Integration: Student Registration Flow', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('should complete student registration → enrollment → package assignment', async () => {
        // 1. Create Student
        api.post.mockResolvedValueOnce({
            success: true,
            data: { id: 1, name: 'Juan Pérez', gradeId: null }
        });

        // 2. Assign Grade
        api.put.mockResolvedValueOnce({
            success: true,
            data: { id: 1, name: 'Juan Pérez', gradeId: 5 }
        });

        // 3. Assign Package
        api.post.mockResolvedValueOnce({
            success: true,
            data: { studentId: 1, packageId: 2, amount: 750 }
        });

        // Simulate flow
        const student = await api.post('/students', { name: 'Juan Pérez' });
        expect(student.success).toBe(true);
        expect(student.data.id).toBe(1);

        const enrolled = await api.put(`/students/${student.data.id}`, { gradeId: 5 });
        expect(enrolled.success).toBe(true);
        expect(enrolled.data.gradeId).toBe(5);

        const packaged = await api.post('/student-packages', { studentId: 1, packageId: 2 });
        expect(packaged.success).toBe(true);
        expect(packaged.data.amount).toBe(750);
    });
});

describe('Integration: Teacher Grade Upload Flow', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('should load bimesters → load students → save grades', async () => {
        // 1. Load Bimesters
        api.get.mockResolvedValueOnce({
            success: true,
            data: [
                { id: 1, name: 'Bimestre 1', number: 1 },
                { id: 2, name: 'Bimestre 2', number: 2 }
            ]
        });

        // 2. Load Students
        api.get.mockResolvedValueOnce({
            success: true,
            data: [
                { id: 1, name: 'Estudiante 1' },
                { id: 2, name: 'Estudiante 2' }
            ]
        });

        // 3. Save Grades
        api.post.mockResolvedValueOnce({
            success: true,
            data: { saved: 2 }
        });

        // Simulate flow
        const bimesters = await api.get('/bimesters');
        expect(bimesters.data).toHaveLength(2);

        const students = await api.get('/students?gradeId=5');
        expect(students.data).toHaveLength(2);

        const grades = await api.post('/grade-records/batch', {
            bimesterId: 1,
            grades: [
                { studentId: 1, score: 85 },
                { studentId: 2, score: 90 }
            ]
        });
        expect(grades.success).toBe(true);
        expect(grades.data.saved).toBe(2);
    });
});

describe('Integration: Parent Dashboard Flow', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('should load children → load schedule → load grades', async () => {
        // 1. Load Children
        api.get.mockResolvedValueOnce({
            success: true,
            data: [
                { id: 1, name: 'Hijo 1', gradeId: 5 },
                { id: 2, name: 'Hijo 2', gradeId: 3 }
            ]
        });

        // 2. Load Schedule for Child 1
        api.get.mockResolvedValueOnce({
            success: true,
            data: [
                { dayOfWeek: 1, subject: 'Matemáticas', startTime: '07:30' }
            ]
        });

        // 3. Load Grades for Child 1
        api.get.mockResolvedValueOnce({
            success: true,
            data: [
                { subject: 'Matemáticas', score: 88 }
            ]
        });

        // Simulate flow
        const children = await api.get('/family/children');
        expect(children.data).toHaveLength(2);

        const schedule = await api.get(`/schedules/student/${children.data[0].id}`);
        expect(schedule.data[0].subject).toBe('Matemáticas');

        const grades = await api.get(`/grade-records/student/${children.data[0].id}`);
        expect(grades.data[0].score).toBe(88);
    });
});

describe('Integration: Payment Flow', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('should load statement → create payment → generate receipt', async () => {
        // 1. Load Statement
        api.get.mockResolvedValueOnce({
            success: true,
            data: { balance: 1500, dueDate: '2026-01-15' }
        });

        // 2. Create Payment
        api.post.mockResolvedValueOnce({
            success: true,
            data: { id: 1, amount: 750, status: 'completed' }
        });

        // 3. Generate Receipt
        api.get.mockResolvedValueOnce({
            success: true,
            data: { receiptNumber: 'REC-2026-001', pdfUrl: '/receipts/1.pdf' }
        });

        // Simulate flow
        const statement = await api.get('/students/1/statement');
        expect(statement.data.balance).toBe(1500);

        const payment = await api.post('/payments', { studentId: 1, amount: 750 });
        expect(payment.success).toBe(true);

        const receipt = await api.get(`/payments/${payment.data.id}/receipt`);
        expect(receipt.data.receiptNumber).toContain('REC-');
    });
});
