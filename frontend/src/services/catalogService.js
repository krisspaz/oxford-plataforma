/**
 * Catalog Service - Sistema Oxford
 * =================================
 * Unified service for all catalog-related API calls.
 * Uses REAL endpoints (API Platform or DB-backed controllers)
 */

import api from './api';

export const catalogService = {
    // ============================================
    // ACADEMIC LEVELS - API Platform (/api/academic_levels)
    // ============================================
    getAcademicLevels: () => api.get('/academic_levels'),

    createAcademicLevel: (data) => api.post('/academic_levels', data, {
        headers: { 'Content-Type': 'application/ld+json' }
    }),

    updateAcademicLevel: (id, data) => api.put(`/academic_levels/${id}`, data, {
        headers: { 'Content-Type': 'application/ld+json' }
    }),

    deleteAcademicLevel: (id) => api.delete(`/academic_levels/${id}`),

    // ============================================
    // GRADES - API Platform (/api/grades)
    // ============================================
    getGrades: () => api.get('/grades'),

    createGrade: (data) => api.post('/grades', data, {
        headers: { 'Content-Type': 'application/ld+json' }
    }),

    updateGrade: (id, data) => api.put(`/grades/${id}`, data, {
        headers: { 'Content-Type': 'application/ld+json' }
    }),

    deleteGrade: (id) => api.delete(`/grades/${id}`),

    // ============================================
    // SECTIONS - API Platform (/api/sections)
    // ============================================
    getSections: (gradeId = null) =>
        api.get(gradeId ? `/sections?grade=${gradeId}` : '/sections'),

    createSection: (data) => api.post('/sections', data, {
        headers: { 'Content-Type': 'application/ld+json' }
    }),

    // ============================================
    // SUBJECTS - API Platform (/api/subjects)
    // ============================================
    getSubjects: () => api.get('/subjects'),

    createSubject: (data) => api.post('/subjects', data, {
        headers: { 'Content-Type': 'application/ld+json' }
    }),

    updateSubject: (id, data) => api.put(`/subjects/${id}`, data, {
        headers: { 'Content-Type': 'application/ld+json' }
    }),

    deleteSubject: (id) => api.delete(`/subjects/${id}`),

    // ============================================
    // TEACHERS - API Platform (/api/teachers)
    // ============================================
    getTeachers: () => api.get('/teachers'),

    getTeacher: (id) => api.get(`/teachers/${id}`),

    // ============================================
    // SCHOOL CYCLES - Custom Controller (/api/cycle)
    // ============================================
    getSchoolCycles: () => api.get('/cycle'),

    getCurrentCycle: () => api.get('/cycle/current'),

    createSchoolCycle: (data) => api.post('/cycle', data),

    updateSchoolCycle: (id, data) => api.put(`/cycle/${id}`, data),

    closeSchoolCycle: () => api.post('/cycle/close'),

    // ============================================
    // SIMPLE CATALOGS - DB-backed (/api/catalogs/*)
    // These are lookup tables stored in the 'catalog' table
    // ============================================
    getPaymentMethods: () => api.get('/catalogs/payment-methods'),

    getDocumentTypes: () => api.get('/catalogs/document-types'),

    getRelationshipTypes: () => api.get('/catalogs/relationships'),

    getStatuses: () => api.get('/catalogs/statuses'),
};

export default catalogService;
