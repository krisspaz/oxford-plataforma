/**
 * Base API Configuration - Sistema Oxford
 * =========================================
 * JWT Token-based Authentication with Automatic Refresh
 * Uses localStorage for token storage with Authorization header
 */

const API_BASE_URL = '/api';

// Token refresh state
let isRefreshing = false;
let refreshSubscribers = [];

/**
 * Subscribe to token refresh
 */
const subscribeTokenRefresh = (callback) => {
    refreshSubscribers.push(callback);
};

/**
 * Notify all subscribers that token was refreshed
 */
const onTokenRefreshed = (token) => {
    refreshSubscribers.forEach(callback => callback(token));
    refreshSubscribers = [];
};

// Sanitize token retrieval
export const getToken = () => {
    const token = localStorage.getItem('token');
    if (!token || token === 'undefined' || token === 'null') return null;
    return token.replace(/^"(.*)"$/, '$1'); // Remove surrounding quotes if present
};

/**
 * Set JWT token
 */
export const setToken = (token) => {
    if (token && typeof token === 'string') {
        localStorage.setItem('token', token);
    } else {
        localStorage.removeItem('token');
    }
};

/**
 * Check if user is authenticated (has token)
 */
export const isAuthenticated = () => !!getToken();

/**
 * Refresh the access token
 */
const refreshToken = async () => {
    try {
        const response = await fetch(`${API_BASE_URL}/auth/refresh`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include', // For refresh token cookie if used
        });

        if (!response.ok) {
            throw new Error('Refresh failed');
        }

        const data = await response.json();
        if (data.token) {
            setToken(data.token);
            return data.token;
        }
        throw new Error('No token in refresh response');
    } catch (error) {
        // Refresh failed - clear token and redirect to login
        setToken(null);
        if (!window.location.pathname.includes('/login')) {
            window.location.href = '/login';
        }
        throw error;
    }
};

/**
 * Base fetch wrapper with JWT authentication
 */
const apiFetch = async (endpoint, options = {}, retry = true) => {
    const token = getToken();

    // Check if we have a token before even trying, unless it's a login/public endpoint
    const isPublicEndpoint = endpoint === '/login_check' || endpoint.includes('/public');

    if (!token && !isPublicEndpoint) {
        console.warn(`[API] Aborting request to ${endpoint}: No token found.`);
        // Force redirect if trying to access protected resource without token
        if (!window.location.pathname.includes('/login')) {
            window.location.href = '/login';
        }
        throw new Error('Sesión no válida. Redirigiendo...');
    }

    const headers = {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...options.headers,
    };

    // Add Authorization header if token exists
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
        // Redundant header to bypass stripping issues
        headers['X-Auth-Token'] = token;
        if (import.meta.env.DEV) {
            console.log(`[API] 🔐 Attaching Token to ${options.method || 'GET'} ${endpoint}`);
        }
    }

    try {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
            ...options,
            headers,
            credentials: 'include',
        });

        // Parse response body primarily for error checking
        let data;
        const contentType = response.headers.get('content-type');
        if (contentType && (contentType.includes('application/json') || contentType.includes('application/ld+json'))) {
            data = await response.json();
        } else {
            data = await response.text();
        }

        // Handle 401 OR Specific Backend Auth Errors that might return as 500/400
        const isAuthError = response.status === 401 ||
            (typeof data === 'object' && data.message === 'JWT Token not found') ||
            (typeof data === 'object' && data.message === 'Expired JWT Token') ||
            (typeof data === 'string' && data.includes('JWT Token not found'));

        if (isAuthError) {
            console.warn(`[API] Auth Error for ${endpoint}: ${response.status} - ${typeof data === 'object' ? data.message : data}`);

            if (retry && token) {
                if (!isRefreshing) {
                    isRefreshing = true;
                    try {
                        const newToken = await refreshToken();
                        isRefreshing = false;
                        onTokenRefreshed(newToken);
                        // Retry original request with new token
                        return apiFetch(endpoint, options, false);
                    } catch (refreshError) {
                        isRefreshing = false;
                        throw refreshError;
                    }
                } else {
                    // Wait for token refresh and retry
                    return new Promise((resolve, reject) => {
                        subscribeTokenRefresh((newToken) => {
                            headers['Authorization'] = `Bearer ${newToken}`;
                            apiFetch(endpoint, options, false)
                                .then(resolve)
                                .catch(reject);
                        });
                    });
                }
            } else {
                // If 401/AuthError and no retry allowed (or no token), force logout
                setToken(null);
                if (!window.location.pathname.includes('/login')) {
                    window.location.href = '/login';
                }
                throw new Error('Sesión expirada. Por favor inicia sesión nuevamente.');
            }
        }

        // Handle 429 - Rate limited
        if (response.status === 429) {
            throw new Error('Demasiadas solicitudes. Por favor, espera un momento.');
        }

        // Handle 403 - Forbidden
        if (response.status === 403) {
            throw new Error('Acceso denegado.');
        }

        // Handle 404
        if (response.status === 404) {
            throw new Error('Recurso no encontrado.');
        }

        if (!response.ok) {
            const errorMessage =
                (typeof data === 'object' && (data.error || data.message || data.detail)) ||
                `Error ${response.status}`;
            throw new Error(errorMessage);
        }

        return data;
    } catch (error) {
        if (import.meta.env.DEV) {
            console.error(`API Error [${endpoint}]:`, error);
        }
        throw error;
    }
};

/**
 * API methods with JWT authentication
 */
export const api = {
    get: (endpoint, options = {}) =>
        apiFetch(endpoint, { method: 'GET', ...options }),

    post: (endpoint, body, options = {}) =>
        apiFetch(endpoint, {
            method: 'POST',
            body: JSON.stringify(body),
            ...options,
        }),

    put: (endpoint, body, options = {}) =>
        apiFetch(endpoint, {
            method: 'PUT',
            body: JSON.stringify(body),
            ...options,
        }),

    patch: (endpoint, body, options = {}) =>
        apiFetch(endpoint, {
            method: 'PATCH',
            body: JSON.stringify(body),
            ...options,
        }),

    delete: (endpoint, options = {}) =>
        apiFetch(endpoint, { method: 'DELETE', ...options }),
};

export default api;

