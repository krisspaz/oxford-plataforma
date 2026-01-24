import { api } from '../../../services/api';

/**
 * AI Enterprise Service
 * Interfaces with the Symfony AiService and Python Microservice
 */
export const aiEnterpriseService = {

    // Check System Health (Circuit Breaker Status)
    getHealth: async () => {
        try {
            const response = await api.get('/ai/health');
            return response.data || { status: 'unknown' };
        } catch (error) {
            // Emulate Circuit Breaker Response from backend
            return {
                status: 'degraded',
                service: 'ai_proxy',
                ai_service: 'unreachable',
                circuit_broken: true
            };
        }
    },

    // Predictive Risk Analysis
    getRiskAnalysis: async (studentId) => {
        try {
            // In a real app, this might pass grades array
            const response = await api.post('/ai/risk-analysis', {
                student_id: studentId,
                grades: [65, 70, 58, 80] // Example data
            });
            return response.data;
        } catch (error) {
            return MOCK_RISK_DATA;
        }
    },

    // Get Recommendations/Predictions
    getPredictions: async () => {
        // Simulating aggregate analysis
        return MOCK_PREDICTIONS;
    },

    // Chat with Data
    chat: async (message) => {
        try {
            const response = await api.post('/ai/chat', { message });
            return response.data;
        } catch (error) {
            return {
                success: true,
                data: {
                    response_text: "Modo Offline: No puedo conectar con el cerebro de Python ahora mismo, pero tus datos están seguros.",
                    intent: 'offline_fallback'
                }
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
