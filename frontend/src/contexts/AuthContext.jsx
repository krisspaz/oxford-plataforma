import { createContext, useContext, useState, useEffect } from 'react';
import axios from 'axios';

const AuthContext = createContext(null);

// Decode JWT token without external library
const decodeJWT = (token) => {
    try {
        if (!token || typeof token !== 'string') return null;

        const parts = token.split('.');
        if (parts.length !== 3) {
            console.error('Invalid token format');
            return null;
        }

        const base64Url = parts[1];
        const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
        const jsonPayload = decodeURIComponent(
            atob(base64)
                .split('')
                .map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
                .join('')
        );
        return JSON.parse(jsonPayload);
    } catch (error) {
        console.error('Failed to decode token:', error);
        return null;
    }
};

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    // Parse user from token
    const parseUserFromToken = (token) => {
        const decoded = decodeJWT(token);
        if (decoded) {
            return {
                id: decoded.id || decoded.sub, // Primary User ID
                email: decoded.username || decoded.email,
                roles: decoded.roles || ['ROLE_USER'],
                studentId: decoded.studentId || decoded.student_id || null, // If backend provides this
                teacherId: decoded.teacherId || decoded.teacher_id || null, // If backend provides this
                exp: decoded.exp
            };
        }
        return null;
    };

    const logout = () => {
        console.log("AuthContext: Logging out...");
        localStorage.removeItem('token');
        setUser(null);
        // Optional: Redirect to login or handled by protected routes
        window.location.href = '/login';
    };

    useEffect(() => {
        console.log("AuthContext V2: Initializing...");

        const initAuth = () => {
            try {
                // Check if user is logged in
                const token = localStorage.getItem('token');
                if (token) {
                    const userData = parseUserFromToken(token);
                    if (userData) {
                        // Check if token is expired
                        const now = Math.floor(Date.now() / 1000);
                        if (userData.exp && userData.exp < now) {
                            console.warn("AuthContext: Token expired during init");
                            logout();
                        } else {
                            console.log("AuthContext: User restored from token", userData.email);
                            setUser(userData);
                        }
                    } else {
                        console.warn("AuthContext: Invalid token data");
                        logout();
                    }
                } else {
                    console.log("AuthContext: No token found");
                }
            } catch (error) {
                console.error("AuthContext Error:", error);
                logout();
            } finally {
                // Short delay to ensure state updates propagate if needed
                setTimeout(() => {
                    setLoading(false);
                }, 100);
            }
        };

        initAuth();
    }, []);

    const login = async (email, password) => {
        try {
            const apiUrl = '/api';

            const response = await axios.post(`${apiUrl}/auth/login`, {
                email: email,
                password: password
            });

            const token = response.data.token;

            if (!token) {
                console.error('No se recibió token en la respuesta');
                return false;
            }

            localStorage.setItem('token', token);

            // Decode token to get user info and roles
            const userData = parseUserFromToken(token);

            if (userData) {
                setUser(userData);
                return true;
            }
            return false;
        } catch (error) {
            console.error("Login failed:", error);
            return false;
        }
    };

    // Check if user has a specific role
    const hasRole = (role) => {
        return user?.roles?.includes(role) || false;
    };

    // Get primary role (first non-USER role)
    const getPrimaryRole = () => {
        if (!user?.roles) return null;
        return user.roles.find(r => r !== 'ROLE_USER') || user.roles[0];
    };

    return (
        <AuthContext.Provider value={{
            user,
            login,
            logout,
            loading,
            hasRole,
            getPrimaryRole
        }}>
            {loading ? <div className="p-4 text-center">Iniciando sesión segura...</div> : children}
        </AuthContext.Provider>
    );
};

export const useAuth = () => useContext(AuthContext);
