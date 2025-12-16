import api from './api';

export const catalogService = {
    // Get all grades (academic levels structure)
    getGrades: () => api.get('/catalogs/grades'),

    // Get all sections
    getSections: (gradeId) => api.get(`/catalogs/sections${gradeId ? `?grade=${gradeId}` : ''}`),

    // Get all subjects
    getSubjects: () => api.get('/catalogs/subjects'),

    // Get all teachers
    getTeachers: () => api.get('/catalogs/teachers'),

    // Get school cycles
    getSchoolCycles: () => api.get('/catalogs/school-cycles'),

    // Get academic levels
    getAcademicLevels: () => api.get('/catalogs/academic-levels'),

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
