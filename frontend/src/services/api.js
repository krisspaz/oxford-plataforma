/**
 * API Client - Sistema Oxford
 * ============================
 * JWT Auth + Error Handling + Hydra Support
 * VERSIÓN ROBUSTA - No hay toast sin response.ok
 * 
 * Soporta:
 * - API Platform (Hydra format)
 * - Custom controllers ({ success: true, data: [...] })
 */

const API_BASE_URL = '/api';

// Custom API Error class
export class ApiError extends Error {
    constructor(status, data, message) {
        super(message || `API Error ${status}`);
        this.status = status;
        this.data = data;
        this.name = 'ApiError';
    }
}

// Token management
export const getToken = () => {
    const token = localStorage.getItem('token');
    if (!token || token === 'undefined' || token === 'null') return null;
    return token.replace(/^"(.*)"$/, '$1');
};

export const setToken = (token) => {
    if (token && typeof token === 'string') {
        localStorage.setItem('token', token);
    } else {
        localStorage.removeItem('token');
    }
};

export const isAuthenticated = () => !!getToken();

// Token refresh state
let isRefreshing = false;
let refreshSubscribers = [];

const subscribeTokenRefresh = (callback) => refreshSubscribers.push(callback);
const onTokenRefreshed = (token) => {
    refreshSubscribers.forEach(cb => cb(token));
    refreshSubscribers = [];
};

const refreshToken = async () => {
    try {
        const response = await fetch(`${API_BASE_URL}/auth/refresh`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
        });
        if (!response.ok) throw new Error('Refresh failed');
        const data = await response.json();
        if (data.token) {
            setToken(data.token);
            return data.token;
        }
        throw new Error('No token');
    } catch (error) {
        setToken(null);
        if (!window.location.pathname.includes('/login')) {
            window.location.href = '/login';
        }
        throw error;
    }
};

/**
 * Unwrap response data from various formats
 * Handles both API Platform (Hydra) and custom { success, data } responses
 */
const unwrapResponse = (data, options = {}) => {
    if (!data || typeof data !== 'object') return data;

    // If fullResponse requested, return as-is
    if (options.fullResponse) return data;

    // API Platform Hydra format
    if (data['hydra:member']) {
        return data['hydra:member'];
    }

    // Alternative Hydra format
    if (data.member && Array.isArray(data.member)) {
        return data.member;
    }

    // Custom controller format { success: true, data: [...] }
    if (data.success === true && Array.isArray(data.data)) {
        return data.data;
    }

    // Single entity or other response
    return data;
};

/**
 * Core fetch wrapper
 * @param {string} endpoint - API endpoint
 * @param {object} options - Fetch options (method, body, headers, fullResponse, etc.)
 * @param {boolean} retry - Whether to retry on 401
 * @returns {Promise<any>} - Response data (unwrapped if collection)
 * @throws {ApiError} - If response is not ok
 */
const apiFetch = async (endpoint, options = {}, retry = true) => {
    const token = getToken();
    const isPublicEndpoint = endpoint === '/login_check' ||
        endpoint === '/auth/login' ||
        endpoint.includes('/public') ||
        endpoint.includes('/health');

    if (!token && !isPublicEndpoint) {
        if (!window.location.pathname.includes('/login')) {
            window.location.href = '/login';
        }
        throw new ApiError(401, null, 'Sesión no válida');
    }

    // ✅ MERGE HEADERS CORRECTLY - custom headers override defaults
    const headers = {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...options.headers,
    };

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    // Remove Content-Type for FormData (browser sets it with boundary)
    if (options.body instanceof FormData) {
        delete headers['Content-Type'];
    }

    // Build URL with query string if options.params is provided
    let url = `${API_BASE_URL}${endpoint}`;
    const params = options.params;
    if (params != null && typeof params === 'object' && !Array.isArray(params)) {
        const qs = new URLSearchParams();
        Object.entries(params).forEach(([k, v]) => {
            if (v !== undefined && v !== null && v !== '') qs.set(k, String(v));
        });
        const qsStr = qs.toString();
        if (qsStr) url += (endpoint.includes('?') ? '&' : '?') + qsStr;
    }

    try {
        const response = await fetch(url, {
            ...options,
            headers,
            credentials: 'include',
        });

        // Parse response
        let data;
        const contentType = response.headers.get('content-type');
        if (contentType?.includes('application/json') || contentType?.includes('application/ld+json')) {
            try {
                data = await response.json();
            } catch {
                data = null;
            }
        } else {
            data = await response.text();
        }

        // Handle 401 - Auth errors
        const isAuthError = response.status === 401 ||
            (typeof data === 'object' && data?.message === 'JWT Token not found') ||
            (typeof data === 'object' && data?.message === 'Expired JWT Token');

        if (isAuthError) {
            console.warn(`[API] Auth error on ${endpoint}:`, response.status);
            if (retry && token) {
                if (!isRefreshing) {
                    isRefreshing = true;
                    try {
                        const newToken = await refreshToken();
                        isRefreshing = false;
                        onTokenRefreshed(newToken);
                        return apiFetch(endpoint, options, false);
                    } catch {
                        isRefreshing = false;
                        throw new ApiError(401, data, 'Sesión expirada');
                    }
                } else {
                    return new Promise((resolve, reject) => {
                        subscribeTokenRefresh(() => {
                            apiFetch(endpoint, options, false).then(resolve).catch(reject);
                        });
                    });
                }
            } else {
                setToken(null);
                if (!window.location.pathname.includes('/login')) {
                    window.location.href = '/login';
                }
                throw new ApiError(401, data, 'Sesión expirada');
            }
        }

        // ✅ ALWAYS THROW ON NON-2XX
        if (!response.ok) {
            const errorMessage = typeof data === 'object'
                ? (data.error || data.message || data.detail || data['hydra:description'] || `Error ${response.status}`)
                : `Error ${response.status}`;
            throw new ApiError(response.status, data, errorMessage);
        }

        // ✅ UNWRAP RESPONSE (handles both Hydra and {success, data})
        return unwrapResponse(data, options);

    } catch (error) {
        if (error instanceof ApiError) throw error;
        if (import.meta.env.DEV) {
            console.error(`[API] ${options.method || 'GET'} ${endpoint}:`, error);
        }
        throw new ApiError(0, null, error.message || 'Error de conexión');
    }
};

/**
 * API methods - Properly handle options including custom headers
 */
export const api = {
    /**
     * GET request
     * @param {string} endpoint
     * @param {object} options - { headers, fullResponse, ... }
     */
    get: (endpoint, options = {}) =>
        apiFetch(endpoint, { method: 'GET', ...options }),

    /**
     * POST request
     * @param {string} endpoint
     * @param {object|FormData} body
     * @param {object} options - { headers: { 'Content-Type': 'application/ld+json' }, ... }
     */
    post: (endpoint, body, options = {}) =>
        apiFetch(endpoint, {
            method: 'POST',
            body: body instanceof FormData ? body : JSON.stringify(body),
            ...options,
        }),

    /**
     * PUT request
     */
    put: (endpoint, body, options = {}) =>
        apiFetch(endpoint, {
            method: 'PUT',
            body: JSON.stringify(body),
            ...options,
        }),

    /**
     * PATCH request
     */
    patch: (endpoint, body, options = {}) =>
        apiFetch(endpoint, {
            method: 'PATCH',
            body: JSON.stringify(body),
            headers: { 'Content-Type': 'application/merge-patch+json', ...options.headers },
            ...options,
        }),

    /**
     * DELETE request
     */
    delete: (endpoint, options = {}) =>
        apiFetch(endpoint, { method: 'DELETE', ...options }),
};

export default api;
