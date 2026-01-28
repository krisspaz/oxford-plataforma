/**
 * ErrorState Component
 * Displays error states with retry functionality
 */
import { AlertTriangle, RefreshCw, Home } from 'lucide-react';
import { Button } from './index';

export const ErrorState = ({
    title = 'Error al cargar',
    message = 'Ocurrió un error inesperado. Por favor intenta de nuevo.',
    error, // Optional error object for dev mode
    onRetry,
    onGoHome,
    fullScreen = false,
}) => {
    const Container = fullScreen ? 'div' : 'div';

    return (
        <Container
            className={`flex flex-col items-center justify-center text-center px-4 ${fullScreen ? 'min-h-screen bg-gray-50 dark:bg-gray-900' : 'py-16'
                }`}
        >
            {/* Icon */}
            <div className="w-20 h-20 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mb-6">
                <AlertTriangle className="text-red-600 dark:text-red-400" size={40} />
            </div>

            {/* Title */}
            <h2 className="text-xl font-bold text-gray-900 dark:text-white mb-2">
                {title}
            </h2>

            {/* Message */}
            <p className="text-gray-500 dark:text-gray-400 mb-6 max-w-md">
                {message}
            </p>

            {/* Dev-only error details */}
            {import.meta.env.DEV && error && (
                <details className="mb-6 text-left w-full max-w-md">
                    <summary className="cursor-pointer text-sm text-gray-400 hover:text-gray-600">
                        Detalles técnicos
                    </summary>
                    <pre className="mt-2 p-3 bg-gray-100 dark:bg-gray-800 rounded-lg text-xs overflow-auto max-h-32">
                        {error?.message || JSON.stringify(error, null, 2)}
                    </pre>
                </details>
            )}

            {/* Actions */}
            <div className="flex gap-3">
                {onRetry && (
                    <Button variant="primary" onClick={onRetry} icon={RefreshCw}>
                        Reintentar
                    </Button>
                )}
                {onGoHome && (
                    <Button variant="secondary" onClick={onGoHome} icon={Home}>
                        Ir al inicio
                    </Button>
                )}
            </div>
        </Container>
    );
};

export default ErrorState;
