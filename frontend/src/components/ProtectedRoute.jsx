import { useLocation } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

/**
 * ProtectedRoute - Guards routes with authentication and role-based access
 * 
 * @param {ReactNode} children - Child components to render
 * @param {string[]} allowedRoles - Optional array of roles that can access this route
 *                                  e.g., ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']
 * @param {string} redirectTo - Where to redirect if not authorized (default: /login)
 */
const ProtectedRoute = ({ children, allowedRoles = null, redirectTo = '/login' }) => {
    const { user, loading, hasRole } = useAuth();
    const location = useLocation();

    // Loading state
    if (loading) {
        return (
            <div className="flex flex-col items-center justify-center h-screen bg-gray-100 dark:bg-gray-900">
                <div className="w-12 h-12 border-4 border-teal-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <div className="text-sm font-mono text-teal-600">Verificando Sesión...</div>
            </div>
        );
    }

    // Not authenticated
    if (!user) {
        return <Navigate to={redirectTo} state={{ from: location }} replace />;
    }

    // Role-based access control
    if (allowedRoles && allowedRoles.length > 0) {
        const hasAccess = allowedRoles.some(role => hasRole(role));

        if (!hasAccess) {
            // User is authenticated but not authorized
            return (
                <div className="flex flex-col items-center justify-center h-screen bg-gray-100 dark:bg-gray-900">
                    <div className="text-center p-8 max-w-md">
                        <ShieldOff className="w-16 h-16 text-red-500 mx-auto mb-4" />
                        <h1 className="text-2xl font-bold text-gray-800 dark:text-white mb-2">
                            Acceso Denegado
                        </h1>
                        <p className="text-gray-600 dark:text-gray-400 mb-6">
                            No tiene permisos para acceder a esta sección.
                            Por favor contacte al administrador si cree que esto es un error.
                        </p>
                        <Navigate to="/dashboard" replace />
                    </div>
                </div>
            );
        }
    }

    return children;
};

export default ProtectedRoute;

