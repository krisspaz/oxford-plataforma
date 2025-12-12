import { createContext, useContext, useState, useEffect } from 'react';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        // Check if user is logged in (simplified for now)
        const token = localStorage.getItem('token');
        if (token) {
            setUser({ email: 'admin@oxford.edu', role: 'ADMIN' }); // In real app, decode JWT or fetch profile
        }
        setLoading(false);
    }, []);

    const login = (token) => {
        localStorage.setItem('token', token);
        setUser({ email: 'admin@oxford.edu', role: 'ADMIN' });
    };

    const logout = () => {
        localStorage.removeItem('token');
        setUser(null);
    };

    return (
        <AuthContext.Provider value={{ user, login, logout, loading }}>
            {!loading && children}
        </AuthContext.Provider>
    );
};

export const useAuth = () => useContext(AuthContext);
