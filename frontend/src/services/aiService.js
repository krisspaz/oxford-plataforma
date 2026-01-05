/**
 * AI Service Client for Frontend
 * ================================
 * Robust client for interacting with the AI schedule generator
 * with error handling, retry logic, and caching.
 */

import api from './api';
import axios from 'axios';

// Cache for AI responses to avoid redundant calls
const responseCache = new Map();
const CACHE_TTL = 5 * 60 * 1000; // 5 minutes

/**
 * AI Service configuration
 */
const AI_CONFIG = {
    maxRetries: 3,
    retryDelay: 1000,
    timeout: 30000,
};

/**
 * Generate cache key from request
 */
const getCacheKey = (endpoint, data) => {
    return `${endpoint}:${JSON.stringify(data)}`;
};

/**
 * Check if cached response is still valid
 */
const isValidCache = (cacheEntry) => {
    if (!cacheEntry) return false;
    return Date.now() - cacheEntry.timestamp < CACHE_TTL;
};

/**
 * Sleep utility for retry delay
 */
const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

/**
 * AI Service API client
 */
const aiService = {
    /**
     * Send a chat message to the AI for NLP processing
     * @param {string} message - User message in Spanish
     * @param {object} context - Optional context (current config, schedule, etc)
     * @returns {Promise<object>} AI response with intent, entities, and reply
     */
    async chat(message, context = {}) {
        const cacheKey = getCacheKey('/ai/chat', { message });
        const cached = responseCache.get(cacheKey);

        if (isValidCache(cached)) {
            return cached.data;
        }

        try {
            const response = await api.post('/ai/chat', {
                message,
                context,
            }, { timeout: AI_CONFIG.timeout });

            // Cache successful responses
            responseCache.set(cacheKey, {
                data: response.data,
                timestamp: Date.now(),
            });

            return response.data;
        } catch (error) {
            console.error('AI Chat error:', error);
            return this.getFallbackResponse(message);
        }
    },

    /**
     * Process a command through the NLP engine
     * @param {string} text - Command text
     * @param {object} currentConfig - Current schedule configuration
     * @returns {Promise<object>} Processed command with intent and entities
     */
    async processCommand(text, currentConfig = {}) {
        try {
            // Direct call to Python AI Microservice
            const token = localStorage.getItem('token');
            const response = await axios.post('http://localhost:8001/process-command', {
                text,
                current_config: currentConfig,
            }, {
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                timeout: AI_CONFIG.timeout
            });

            return response.data;
        } catch (error) {
            console.error('AI Process error:', error);
            return {
                intent: 'unknown',
                confidence: 0,
                entities: {},
                response_text: 'No pude procesar tu comando. Intenta ser más específico.',
                should_generate: false,
            };
        }
    },

    /**
     * Generate a schedule using AI
     * @param {object} config - Schedule configuration
     * @param {Array} teachers - Available teachers
     * @param {Array} subjects - Subjects to schedule
     * @param {object} constraints - Additional constraints
     * @returns {Promise<object>} Generated schedule
     */
    async generateSchedule(config, teachers, subjects, constraints = {}) {
        const maxRetries = AI_CONFIG.maxRetries;
        let lastError = null;

        for (let attempt = 1; attempt <= maxRetries; attempt++) {
            try {
                const response = await api.post('/ai/generate-schedule', {
                    config,
                    teachers,
                    subjects,
                    constraints,
                }, { timeout: AI_CONFIG.timeout });

                return {
                    success: true,
                    ...response.data,
                };
            } catch (error) {
                lastError = error;
                console.warn(`Schedule generation attempt ${attempt} failed:`, error.message);

                if (attempt < maxRetries) {
                    await sleep(AI_CONFIG.retryDelay * attempt);
                }
            }
        }

        console.error('Schedule generation failed after all retries:', lastError);
        return {
            success: false,
            error: 'No se pudo generar el horario. Por favor, intenta de nuevo.',
            schedule: null,
        };
    },

    /**
     * Validate a schedule for conflicts
     * @param {Array} schedule - Schedule entries to validate
     * @returns {Promise<object>} Validation result with conflicts
     */
    async validateSchedule(schedule) {
        try {
            const response = await api.post('/ai/validate-schedule', { schedule });
            return response.data;
        } catch (error) {
            console.error('Schedule validation error:', error);
            return {
                valid: false,
                conflicts: [],
                error: 'Error al validar el horario.',
            };
        }
    },

    /**
     * Get AI suggestions for schedule optimization
     * @param {Array} schedule - Current schedule
     * @returns {Promise<Array>} List of suggestions
     */
    async getSuggestions(schedule) {
        try {
            const response = await api.post('/ai/suggestions', { schedule });
            return response.data.suggestions || [];
        } catch (error) {
            console.error('Suggestions error:', error);
            return [];
        }
    },

    /**
     * Check AI service health
     * @returns {Promise<boolean>} True if service is healthy
     */
    async healthCheck() {
        try {
            const response = await api.get('/ai/health', { timeout: 5000 });
            return response.data?.status === 'healthy';
        } catch (error) {
            console.error('AI health check failed:', error);
            return false;
        }
    },

    async predictRisk(studentData) {
        try {
            const response = await api.post('/ai/predict-risk', studentData);
            return response.data;
        } catch (error) {
            console.error('Risk prediction error:', error);
            return { error: 'Failed to predict risk' };
        }
    },

    /**
     * Analyze teacher burnout
     */
    async analyzeTeacherBurnout(teacherId, schedule) {
        try {
            const response = await api.post('/ai/analyze-burnout', { teacher_id: teacherId, schedule });
            return response.data;
        } catch (error) {
            console.error('Burnout analysis error:', error);
            return null;
        }
    },

    /**
     * Get Institutional Health Index (ISA)
     */
    async getInstitutionalHealth() {
        try {
            const response = await api.get('/ai/institutional-health');
            return response.data;
        } catch (error) {
            console.error('ISA error:', error);
            return null;
        }
    },

    async getInstitutionalHealth() {
        try {
            const response = await api.get('/ai/institutional-health');
            return response.data;
        } catch (error) {
            console.error('ISA error:', error);
            return null;
        }
    },

    // --- Enterprise Extensions (Phases 11-14) ---
    async getAuditLog() { return (await api.get('/ai/audit/log')).data; },
    async getLegalDefense(data) { return (await api.post('/ai/legal/defense', data)).data; },
    async validateEthics(command) { return (await api.post('/ai/ethics/validate', { command })).data; },
    async getFutureSimulation() { return (await api.get('/ai/simulation/future')).data; },
    async getMaturityIndex() { return (await api.get('/ai/context/maturity')).data; },

    /**
     * Get fallback response when AI is unavailable
     * @param {string} message - Original message
     * @returns {object} Fallback response
     */
    getFallbackResponse(message) {
        const lowerMessage = message.toLowerCase();

        // Simple pattern matching fallback
        if (/^hola|buenos d[ií]as|buenas tardes/.test(lowerMessage)) {
            return {
                intent: 'greeting',
                confidence: 0.9,
                response: '¡Hola! Soy el asistente de horarios. ¿En qué puedo ayudarte?',
                suggestions: [
                    'Genera horario para 1ro primaria',
                    'Ayuda',
                ],
            };
        }

        if (/ayuda|help|c[oó]mo/.test(lowerMessage)) {
            return {
                intent: 'help',
                confidence: 0.9,
                response: this.getHelpText(),
                suggestions: [],
            };
        }

        if (/genera|crear|horario/.test(lowerMessage)) {
            return {
                intent: 'generate_schedule',
                confidence: 0.6,
                response: 'Para generar un horario, especifica el grado y sección.',
                suggestions: [
                    'Genera horario para 1ro primaria',
                    'Genera horario para 2do básico sección A',
                ],
            };
        }

        return {
            intent: 'unknown',
            confidence: 0,
            response: 'No entendí tu mensaje. Intenta con "ayuda" para ver los comandos disponibles.',
            suggestions: ['Ayuda', 'Genera horario'],
        };
    },

    /**
     * Get help text with available commands
     * @returns {string} Help text
     */
    getHelpText() {
        return `🤖 **Comandos Disponibles:**

**Generar Horarios:**
- "Genera horario para 1ro primaria"
- "Crea horario de 3ro básico sección A"

**Configurar Tiempo:**
- "Clases de 45 minutos"
- "Horario de 7:30 a 14:00"

**Restricciones:**
- "El profesor García no puede los lunes"
- "Matemáticas solo en las mañanas"

**Receso:**
- "Agrega receso después de la 4ta hora"
- "Sin receso"

**Otros:**
- "Mostrar configuración"
- "Limpiar restricciones"`;
    },

    /**
     * Clear the response cache
     */
    clearCache() {
        responseCache.clear();
    },
};

export default aiService;
