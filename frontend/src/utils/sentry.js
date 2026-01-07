/**
 * Sentry Error Tracking Configuration
 * ====================================
 * Real-time error monitoring and performance tracking
 */

import * as Sentry from '@sentry/react';

const SENTRY_DSN = import.meta.env.VITE_SENTRY_DSN;

export const initSentry = () => {
    if (!SENTRY_DSN) {
        console.warn('Sentry DSN not configured. Error tracking disabled.');
        return;
    }

    Sentry.init({
        dsn: SENTRY_DSN,
        environment: import.meta.env.MODE,

        // Performance Monitoring
        tracesSampleRate: import.meta.env.PROD ? 0.1 : 1.0,

        // Session Replay for debugging
        replaysSessionSampleRate: 0.1,
        replaysOnErrorSampleRate: 1.0,

        // Release tracking
        release: `oxford-plataforma@${import.meta.env.VITE_APP_VERSION || '1.0.0'}`,

        // Filter out noise
        ignoreErrors: [
            'ResizeObserver loop limit exceeded',
            'Non-Error promise rejection captured',
            /Loading chunk .* failed/,
        ],

        // Custom tags
        initialScope: {
            tags: {
                component: 'frontend',
            },
        },

        // Before sending, sanitize sensitive data
        beforeSend(event) {
            // Remove sensitive data from breadcrumbs
            if (event.breadcrumbs) {
                event.breadcrumbs = event.breadcrumbs.map(breadcrumb => {
                    if (breadcrumb.data?.password) {
                        breadcrumb.data.password = '[REDACTED]';
                    }
                    if (breadcrumb.data?.token) {
                        breadcrumb.data.token = '[REDACTED]';
                    }
                    return breadcrumb;
                });
            }
            return event;
        },
    });
};

/**
 * Capture custom error with context
 */
export const captureError = (error, context = {}) => {
    Sentry.captureException(error, {
        extra: context,
    });
};

/**
 * Set user context for tracking
 */
export const setUserContext = (user) => {
    if (user) {
        Sentry.setUser({
            id: user.id,
            email: user.email,
            username: user.email?.split('@')[0],
        });
    } else {
        Sentry.setUser(null);
    }
};

/**
 * Track custom event/metric
 */
export const trackEvent = (name, data = {}) => {
    Sentry.addBreadcrumb({
        category: 'custom',
        message: name,
        data,
        level: 'info',
    });
};

/**
 * Performance transaction wrapper
 */
export const startTransaction = (name, op = 'task') => {
    return Sentry.startTransaction({ name, op });
};

export default Sentry;
