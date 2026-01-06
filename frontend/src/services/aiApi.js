import { api } from './api';

const AI_BASE_URL = '/ai'; // Proxied to localhost:8001

/**
 * AI Service Client
 * Uses the same auth cookies as the main API (assuming shared domain or proxy handling)
 */
export const aiApi = {
    get: async (endpoint, options = {}) => {
        const response = await fetch(`${AI_BASE_URL}${endpoint}`, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers,
            },
            credentials: 'include',
        });
        if (!response.ok) throw new Error('AI Service Error');
        return response.json();
    },

    post: async (endpoint, body, options = {}) => {
        const response = await fetch(`${AI_BASE_URL}${endpoint}`, {
            method: 'POST',
            body: JSON.stringify(body),
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers,
            },
            credentials: 'include',
        });
        if (!response.ok) throw new Error('AI Service Error');
        return response.json();
    },

    // Quick method to get insights
    getInsights: async (role, userId) => {
        // Mocking the call if endpoint doesn't exist yet, but planned for /analytics
        // return aiApi.get('/analytics/institutional-health'); 

        // For now, return mock data to demonstrate the UI
        return new Promise(resolve => setTimeout(() => resolve({
            alertLevel: 'medium', // low, medium, high
            insight: 'Se detecta una baja en el promedio de 5to Bachillerato (-15% vs mes pasado).',
            recommendation: 'Sugerimos programar una reunión de refuerzo.',
            positiveTrend: '2do Primaria aumentó su asistencia un 10%.',
            timestamp: new Date().toISOString()
        }), 800));
    }
};

export default aiApi;
