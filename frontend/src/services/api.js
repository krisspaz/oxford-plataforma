/**
 * Base API Configuration - Sistema Oxford
 * =========================================
 * Uses HttpOnly cookies for authentication (set by backend)
 * CSRF token is read from non-HttpOnly cookie for state-changing requests
 */

const API_BASE_URL = import.meta.env.VITE_API_URL || '/api';

/**
 * Get CSRF token from cookie (set by backend)
 * CSRF token is NOT HttpOnly so we can read it
 */
const getCsrfToken = () => {
    const cookies = document.cookie.split(';');
    for (const cookie of cookies) {
        const [name, value] = cookie.trim().split('=');
        if (name === 'csrf_token') {
            return value;
        }
    }
    return null;
};

/**
 * Check if user is likely authenticated (has csrf_token cookie)
 * Note: Actual auth is verified by backend via HttpOnly access_token cookie
 */
export const isAuthenticated = () => {
    return getCsrfToken() !== null;
};

/**
 * Base fetch wrapper with cookie-based auth and CSRF protection
 * 
 * - Uses credentials: 'include' to send HttpOnly cookies
 * - Adds X-CSRF-Token header for state-changing requests
 * - Adds X-Requested-With header for AJAX detection
 */
const apiFetch = async (endpoint, options = {}) => {
    const isStateChanging = ['POST', 'PUT', 'PATCH', 'DELETE'].includes(
        options.method?.toUpperCase()
    );

    const headers = {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest', // Helps backend detect AJAX
        ...options.headers,
    };

    // Add CSRF token for state-changing requests
    if (isStateChanging) {
        const csrfToken = getCsrfToken();
        if (csrfToken) {
            headers['X-CSRF-Token'] = csrfToken;
        }
    }

    try {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
            ...options,
            headers,
            credentials: 'include', // CRITICAL: Send cookies with request
        });

        // Handle 401 - redirect to login
        if (response.status === 401) {
            // Clear any stored user data
            localStorage.removeItem('user');

            // Redirect to login if not already there
            if (!window.location.pathname.includes('/login')) {
                window.location.href = '/login';
            }
            throw new Error('Session expired');
        }

        // Handle 429 - rate limited
        if (response.status === 429) {
            throw new Error('Demasiadas solicitudes. Por favor, espera un momento.');
        }

        // Handle 403 - CSRF or permission error
        if (response.status === 403) {
            throw new Error('Acceso denegado. Por favor, recarga la página.');
        }

        // Parse response
        let data;
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            data = await response.json();
        } else {
            data = await response.text();
        }

        if (!response.ok) {
            const errorMessage =
                (typeof data === 'object' && (data.error || data.message)) ||
                'Error en la solicitud';
            throw new Error(errorMessage);
        }

        return data;
    } catch (error) {
        // Log error in development
        if (import.meta.env.DEV) {
            console.error('API Error:', error);
        }
        throw error;
    }
};

/**
 * API methods object
 * All methods automatically include cookies and CSRF protection
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
