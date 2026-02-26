import { api } from '../../../services/api';

/**
 * AI Enterprise Service
 * Interfaces with the Symfony AiService and Python Microservice
 */
export const aiEnterpriseService = {

    // Check System Health (Symfony → Python GET /health)
    getHealth: async () => {
        try {
            const data = await api.get('/ai/health');
            return data ?? { status: 'unknown' };
        // eslint-disable-next-line unused-imports/no-unused-vars
        } catch (error) {
            return {
                status: 'degraded',
                service: 'ai_proxy',
                ai_service: 'unreachable',
                circuit_broken: true
            };
        }
    },

    // Predictive Risk Analysis (Symfony → Python POST /analytics/predict-risk)
    getRiskAnalysis: async (studentId) => {
        try {
            const data = await api.post('/ai/risk-analysis', {
                student_id: studentId,
                grades: [65, 70, 58, 80]
            });
            return data ?? MOCK_RISK_DATA;
        // eslint-disable-next-line unused-imports/no-unused-vars
        } catch (error) {
            return MOCK_RISK_DATA;
        }
    },

    getPredictions: async () => MOCK_PREDICTIONS,

    // Chat (Symfony → Python POST /chat/message). Respuesta: { response, suggestions, action, data }
    chat: async (message) => {
        try {
            const data = await api.post('/ai/chat', { message });
            return data;
        // eslint-disable-next-line unused-imports/no-unused-vars
        } catch (error) {
            return {
                response: 'Modo Offline: No puedo conectar con el servicio de IA. Tus datos están seguros.',
                intent: 'offline_fallback',
                suggestions: []
            };
        }
    }
};

const MOCK_RISK_DATA = {
    risk_score: 85,
    risk_level: 'HIGH',
    factors: ['Asistencia irregular', 'Bajo rendimiento en Matemáticas', 'Entrega tardía de tareas']
};

const MOCK_PREDICTIONS = [
    { id: 1, student: 'Ana Garcia', risk: 88, subject: 'Matemáticas', reason: 'Tendencia a la baja últimas 3 semanas' },
    { id: 2, student: 'Carlos Ruiz', risk: 75, subject: 'Física', reason: 'Falta de asistencia crítica' },
    { id: 3, student: 'Sofia Lopez', risk: 45, subject: 'Literatura', reason: 'Comportamiento en clase' },
    { id: 4, student: 'Miguel Angel', risk: 92, subject: 'Historia', reason: 'No ha entregado proyecto final' },
];
