import React, { Component } from 'react';
import { Alert, Button } from './ui';

/**
 * Centralized Error Boundary
 * 
 * Catches errors in child components and displays fallback UI.
 * Logs errors to monitoring service.
 */
class CentralizedErrorBoundary extends Component {
    constructor(props) {
        super(props);
        this.state = {
            hasError: false,
            error: null,
            errorInfo: null,
            errorId: null,
        };
    }

    static getDerivedStateFromError(error) {
        return {
            hasError: true,
            error,
            errorId: `ERR-${Date.now().toString(36)}`,
        };
    }

    componentDidCatch(error, errorInfo) {
        this.setState({ errorInfo });

        // Log to monitoring service
        this.logErrorToService(error, errorInfo);

        // Log to console in development
        if (process.env.NODE_ENV === 'development') {
            console.error('Error caught by boundary:', error);
            console.error('Component stack:', errorInfo.componentStack);
        }
    }

    logErrorToService = async (error, errorInfo) => {
        try {
            // Send to error monitoring (Sentry, LogRocket, etc.)
            const errorData = {
                message: error.message,
                stack: error.stack,
                componentStack: errorInfo.componentStack,
                url: window.location.href,
                timestamp: new Date().toISOString(),
                userAgent: navigator.userAgent,
                errorId: this.state.errorId,
            };

            // In production, send to monitoring service
            if (process.env.NODE_ENV === 'production') {
                await fetch('/api/log-error', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(errorData),
                });
            }
        } catch (e) {
            console.error('Failed to log error:', e);
        }
    };

    handleRetry = () => {
        this.setState({
            hasError: false,
            error: null,
            errorInfo: null,
            errorId: null,
        });
    };

    handleGoHome = () => {
        window.location.href = '/';
    };

    handleReload = () => {
        window.location.reload();
    };

    render() {
        const { hasError, error, errorId } = this.state;
        const { fallback, children } = this.props;

        if (hasError) {
            // Custom fallback provided
            if (fallback) {
                return typeof fallback === 'function'
                    ? fallback({ error, errorId, retry: this.handleRetry })
                    : fallback;
            }

            // Default error UI
            return (
                <div className="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 p-6">
                    <div className="max-w-lg w-full text-center">
                        {/* Error Icon */}
                        <div className="text-6xl mb-6">💥</div>

                        {/* Title */}
                        <h1 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            ¡Ups! Algo salió mal
                        </h1>

                        {/* Description */}
                        <p className="text-gray-600 dark:text-gray-400 mb-6">
                            Ha ocurrido un error inesperado. Nuestro equipo ha sido notificado
                            y estamos trabajando para solucionarlo.
                        </p>

                        {/* Error ID */}
                        {errorId && (
                            <div className="mb-6 p-4 bg-gray-100 dark:bg-gray-800 rounded-xl">
                                <p className="text-sm text-gray-500 dark:text-gray-400">
                                    Código de error:
                                </p>
                                <code className="text-sm font-mono text-gray-700 dark:text-gray-300">
                                    {errorId}
                                </code>
                            </div>
                        )}

                        {/* Error details in development */}
                        {process.env.NODE_ENV === 'development' && error && (
                            <div className="mb-6 text-left p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800">
                                <p className="font-mono text-sm text-red-700 dark:text-red-300 break-all">
                                    {error.message}
                                </p>
                                {error.stack && (
                                    <pre className="mt-2 text-xs text-red-600 dark:text-red-400 overflow-auto max-h-40">
                                        {error.stack}
                                    </pre>
                                )}
                            </div>
                        )}

                        {/* Actions */}
                        <div className="flex flex-col sm:flex-row gap-3 justify-center">
                            <Button variant="primary" onClick={this.handleRetry}>
                                🔄 Reintentar
                            </Button>
                            <Button variant="secondary" onClick={this.handleGoHome}>
                                🏠 Ir al inicio
                            </Button>
                            <Button variant="ghost" onClick={this.handleReload}>
                                ♻️ Recargar página
                            </Button>
                        </div>

                        {/* Support link */}
                        <p className="mt-8 text-sm text-gray-500 dark:text-gray-400">
                            ¿Persiste el problema?{' '}
                            <a
                                href="mailto:soporte@oxford.edu.gt"
                                className="text-blue-600 hover:underline"
                            >
                                Contactar soporte
                            </a>
                        </p>
                    </div>
                </div>
            );
        }

        return children;
    }
}

/**
 * Hook for programmatic error handling
 */
export const useErrorHandler = () => {
    const handleError = (error, context = {}) => {
        console.error('Handled error:', error, context);

        // Show toast notification
        if (typeof window !== 'undefined' && window.toast) {
            window.toast.error(error.message || 'Ha ocurrido un error');
        }

        // Log to service
        if (process.env.NODE_ENV === 'production') {
            fetch('/api/log-error', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    message: error.message,
                    stack: error.stack,
                    context,
                    timestamp: new Date().toISOString(),
                }),
            }).catch(() => { });
        }
    };

    return { handleError };
};

/**
 * HOC for adding error boundary to components
 */
export const withErrorBoundary = (WrappedComponent, fallback = null) => {
    return function WithErrorBoundaryWrapper(props) {
        return (
            <CentralizedErrorBoundary fallback={fallback}>
                <WrappedComponent {...props} />
            </CentralizedErrorBoundary>
        );
    };
};

export default CentralizedErrorBoundary;
