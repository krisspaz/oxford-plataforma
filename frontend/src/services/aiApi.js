import { api } from './api';

/**
 * AI Service Client
 * Todas las llamadas pasan por el backend Symfony (`/api/ai/...`),
 * que se encarga de auth, circuit breaker y headers internos.
 */
export const aiApi = {
    get: async (endpoint, options = {}) => {
        // endpoint debe empezar por '/', por ejemplo '/health'
        return api.get(`/ai${endpoint}`, {
            ...options,
            // Mantener posibilidad de pedir respuesta completa si se necesita
            fullResponse: options.fullResponse ?? false,
        });
    },

    post: async (endpoint, body, options = {}) => {
        return api.post(`/ai${endpoint}`, body, {
            ...options,
            fullResponse: options.fullResponse ?? false,
        });
    },

    // Quick method to get insights (por ahora sigue siendo mock, pero centralizado)
    getInsights: async (role, userId) => {
        // En el futuro se puede cambiar a:
        // return aiApi.get('/analytics/institutional-health');

        return new Promise(resolve =>
            setTimeout(
                () =>
                    resolve({
                        alertLevel: 'low',
                        insight: 'Sistema funcionando correctamente. Análisis de IA iniciando...',
                        recommendation: 'El calendario escolar está actualizado.',
                        positiveTrend: 'Conectividad estable en todos los servicios.',
                        timestamp: new Date().toISOString(),
                    }),
                800,
            ),
        );
    },
};

export default aiApi;
