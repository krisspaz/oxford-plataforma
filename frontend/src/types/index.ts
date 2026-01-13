/**
 * Type Definitions for Oxford Platform
 * Gradual TypeScript migration - shared types
 */

// User Types
export interface User {
    id: number;
    email: string;
    name: string;
    role: 'admin' | 'teacher' | 'student' | 'parent' | 'secretary' | 'accountant' | 'coordinator' | 'director' | 'it';
    isActive: boolean;
}

// Student Types
export interface Student {
    id: number;
    name: string;
    code: string;
    gradeId?: number;
    sectionId?: number;
    schoolCycleId?: number;
    parentId?: number;
    isActive: boolean;
}

// Grade & Section Types
export interface AcademicLevel {
    id: number;
    name: string;
    code: string;
    sortOrder: number;
    isActive: boolean;
}

export interface Grade {
    id: number;
    name: string;
    code: string;
    academicLevelId: number;
    sortOrder: number;
    isActive: boolean;
}

export interface Section {
    id: number;
    name: string;
    gradeId: number;
    teacherId?: number;
    capacity: number;
    isActive: boolean;
}

// Schedule Types
export interface ScheduleEntry {
    id: number;
    dayOfWeek: number; // 1-5 (Mon-Fri)
    startTime: string;
    endTime: string;
    subjectId: number;
    teacherId: number;
    gradeId: number;
    sectionId: number;
    classroom?: string;
}

// Subject Types
export interface Subject {
    id: number;
    name: string;
    code: string;
    isActive: boolean;
}

// Bimester Types
export interface Bimester {
    id: number;
    name: string;
    number: number;
    startDate: string;
    endDate: string;
    maxScore: number;
    percentage: number;
    schoolCycleId: number;
    isClosed: boolean;
    isActive: boolean;
}

// Task Types
export interface Task {
    id: number;
    title: string;
    description: string;
    subjectId: number;
    teacherId: number;
    gradeId: number;
    dueDate: string;
    points: number;
    type: 'tarea' | 'examen' | 'proyecto';
    bimesterId: number;
}

export interface TaskSubmission {
    id: number;
    taskId: number;
    studentId: number;
    submittedAt: string;
    filePath?: string;
    grade?: number;
    feedback?: string;
    status: 'pending' | 'submitted' | 'graded';
}

// Payment Types
export interface Payment {
    id: number;
    studentId: number;
    amount: number;
    concept: string;
    paymentDate: string;
    status: 'pending' | 'completed' | 'cancelled';
    receiptNumber?: string;
}

// API Response Types
export interface ApiResponse<T> {
    success: boolean;
    data?: T;
    error?: string;
    message?: string;
}

export interface PaginatedResponse<T> {
    items: T[];
    total: number;
    page: number;
    limit: number;
    totalPages: number;
}

// Dashboard Stats Types
export interface DashboardStats {
    admin?: {
        totalStudents: number;
        totalTeachers: number;
        totalUsers: number;
    };
    student?: {
        pendingTasks: number;
        average: number;
        nextClass: string;
    };
    teacher?: {
        totalStudents: number;
        pendingGrades: number;
    };
    secretary?: {
        pendingEnrollments: number;
        pendingPayments: number;
    };
}

// Notification Types
export interface Notification {
    id: string;
    title: string;
    message: string;
    type: 'info' | 'success' | 'warning' | 'error';
    read: boolean;
    createdAt: string;
}
