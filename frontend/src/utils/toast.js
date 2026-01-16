/**
 * Sistema Oxford - Toast Notifications
 * =====================================
 * Wrapper around Sonner for consistent toast notifications.
 * 
 * ❌ PROHIBITED: Do not use alert() anywhere in the codebase
 * ✅ USE: toast.success(), toast.error(), toast.promise()
 */
import { toast as sonnerToast } from 'sonner';

/**
 * Toast notification wrapper with consistent styling
 */
export const toast = {
    /**
     * Success notification
     * @param {string} message - Message to display
     * @param {object} options - Additional options
     */
    success: (message, options = {}) => {
        sonnerToast.success(message, {
            duration: 3000,
            ...options,
        });
    },

    /**
     * Error notification
     * @param {string} message - Error message
     * @param {object} options - Additional options
     */
    error: (message, options = {}) => {
        sonnerToast.error(message, {
            duration: 5000,
            ...options,
        });
    },

    /**
     * Info notification
     * @param {string} message - Info message
     * @param {object} options - Additional options
     */
    info: (message, options = {}) => {
        sonnerToast.info(message, {
            duration: 4000,
            ...options,
        });
    },

    /**
     * Warning notification
     * @param {string} message - Warning message
     * @param {object} options - Additional options
     */
    warning: (message, options = {}) => {
        sonnerToast.warning(message, {
            duration: 4000,
            ...options,
        });
    },

    /**
     * Loading toast - returns ID for dismissal
     * @param {string} message - Loading message
     * @returns {string|number} - Toast ID
     */
    loading: (message) => {
        return sonnerToast.loading(message);
    },

    /**
     * Dismiss a specific toast or all toasts
     * @param {string|number} id - Toast ID (optional)
     */
    dismiss: (id) => {
        sonnerToast.dismiss(id);
    },

    /**
     * Promise-based toast for async operations
     * @param {Promise} promise - Promise to track
     * @param {object} messages - { loading, success, error }
     * @returns {Promise} - Same promise
     * 
     * @example
     * toast.promise(createStudent(data), {
     *   loading: 'Creando estudiante...',
     *   success: 'Estudiante creado',
     *   error: (err) => err.message
     * });
     */
    promise: (promise, messages) => {
        return sonnerToast.promise(promise, {
            loading: messages.loading || 'Procesando...',
            success: messages.success || 'Operación exitosa',
            error: messages.error || 'Error en la operación',
        });
    },
};

export default toast;
