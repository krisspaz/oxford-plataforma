import { toast } from 'react-hot-toast';

export const handleApiError = (error) => {
    // Log to console until Sentry DSN is available
    console.error('[API Error]', error);

    // Optional: logToSentry(error);

    const userMessage = error.response?.data?.message || error.message || 'Error inesperado';
    toast.error(userMessage);

    // Redirect to login if 401 Unauthorized
    if (error.response?.status === 401) {
        // Prevent infinite redirect loops if already on login
        if (!window.location.pathname.includes('/login')) {
            window.location.href = '/login';
        }
    }
};
