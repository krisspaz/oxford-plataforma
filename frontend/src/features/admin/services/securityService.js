import { api } from '../../../services/api';

/**
 * Security & Governance Service
 * Connects frontend to enterprise backend modules
 */
export const securityService = {
    // Audit Logs
    getAuditLogs: async (params = {}) => {
        try {
            // Mapping to backend Entity: AuditLog
            const response = await api.get('/audit_logs', { params });

            // Handle Hydra/JSON-LD response or fallback to mock
            if (response['hydra:member']) return response['hydra:member'];
            if (response.member) return response.member;
            if (Array.isArray(response)) return response;

            return [];
        } catch (error) {
            console.warn('Backend AuditLog endpoint missing or offline, returning mock data for demo.');
            return MOCK_AUDIT_LOGS;
        }
    },

    // Institutional Rules
    getRules: async () => {
        try {
            const response = await api.get('/institutional_rules');
            if (response['hydra:member']) return response['hydra:member'];
            if (Array.isArray(response)) return response;
            return [];
        } catch (error) {
            console.warn('Using mock Rules data');
            return MOCK_RULES;
        }
    },

    createRule: async (data) => {
        return api.post('/institutional_rules', data);
    },

    updateRule: async (id, data) => {
        return api.put(`/institutional_rules/${id}`, data);
    },

    deleteRule: async (id) => {
        return api.delete(`/institutional_rules/${id}`);
    },

    // Security Stats (Overview)
    getSecurityStats: async () => {
        // In a real scenario, this might aggregate data from multiple endpoints
        return {
            activeUsers: 142,
            threatsBlocked: 3,
            twoFactorEnabled: 85, // percentage
            systemHealth: '98%'
        };
    }
};

// --- MOCK DATA FOR DEMO/DEV ---
const MOCK_AUDIT_LOGS = [
    { id: 1, action: 'LOGIN_SUCCESS', entityType: 'User', user: { email: 'admin@oxford.edu.gt' }, ipAddress: '192.168.1.10', createdAt: new Date().toISOString() },
    { id: 2, action: 'GRADE_UPDATE', entityType: 'Grade', entityId: 45, user: { email: 'profesor@oxford.edu.gt' }, details: 'Changed Math grade from 80 to 85', createdAt: new Date(Date.now() - 3600000).toISOString() },
    { id: 3, action: 'LOGIN_FAILED', entityType: 'User', user: { email: 'unknown@attacker.com' }, ipAddress: '45.2.1.99', createdAt: new Date(Date.now() - 7200000).toISOString() },
    { id: 4, action: 'RULE_VIOLATION', entityType: 'InstitutionalRule', details: 'Attempted to schedule class during break', user: { email: 'coord@oxford.edu.gt' }, createdAt: new Date(Date.now() - 86400000).toISOString() },
    { id: 5, action: 'PAYMENT_RECEIVED', entityType: 'Payment', entityId: 992, user: { email: 'secretaria@oxford.edu.gt' }, details: 'Q450.00 Tuition', createdAt: new Date(Date.now() - 90000000).toISOString() },
];

const MOCK_RULES = [
    { id: 1, code: 'REG-001', name: 'Horario de Clases', type: 'schedule', priority: 'critical', active: true, description: 'Las clases no pueden solaparse.' },
    { id: 2, code: 'FIN-002', name: 'Bloqueo por Mora', type: 'financial', priority: 'high', active: true, description: 'Bloquear acceso a notas si hay 2 meses de mora.' },
    { id: 3, code: 'SEC-003', name: 'Contraseñas Fuertes', type: 'security', priority: 'medium', active: false, description: 'Requerir cambio de contraseña cada 90 días.' },
];
