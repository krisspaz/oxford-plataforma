import api from './api';

export const catalogService = {
    // Get all grades (academic levels structure) - DEPRECATED, use getGrades from gradeService
    getGrades: () => api.get('/grades'),

    // Get all sections - DEPRECATED, use gradeService
    getSections: (gradeId) => api.get(`/grades/${gradeId}/sections`),

    // Get all subjects
    // Subjects CRUD
    getSubjects: () => api.get('/subjects'),
    createSubject: (data) => api.post('/subjects', data, {
        headers: { 'Content-Type': 'application/ld+json' }
    }),
    updateSubject: (id, data) => api.put(`/subjects/${id}`, data, {
        headers: { 'Content-Type': 'application/ld+json' }
    }),

    // Get all teachers
    getTeachers: () => api.get('/catalogs/teachers'),

    // Get all teachers
    getTeachers: () => api.get('/catalogs/teachers'),

    // School Cycles CRUD
    getSchoolCycles: () => api.get('/cycle'),
    createSchoolCycle: (data) => api.post('/cycle', data),
    updateSchoolCycle: (id, data) => api.put(`/cycle/${id}`, data),
    closeSchoolCycle: () => api.post('/cycle/close'),

    // Academic Levels CRUD
    getAcademicLevels: () => api.get('/academic_levels'),
    createAcademicLevel: (data) => api.post('/academic_levels', data, {
        headers: { 'Content-Type': 'application/ld+json' }
    }),
    updateAcademicLevel: (id, data) => api.put(`/academic_levels/${id}`, data, {
        headers: { 'Content-Type': 'application/ld+json' }
    }),
    deleteAcademicLevel: (id) => api.delete(`/academic_levels/${id}`),

    // Get payment methods
    getPaymentMethods: () => api.get('/catalogs/payment-methods'),

    // Get document types
    getDocumentTypes: () => api.get('/catalogs/document-types'),

    // Get relationship types
    getRelationshipTypes: () => api.get('/catalogs/relationships'),

    // Get statuses
    getStatuses: () => api.get('/catalogs/statuses'),
};

export default catalogService;
