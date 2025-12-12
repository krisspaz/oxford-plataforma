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

    const login = async (email, password) => { // Modified to accept email and password
        try {
            const response = await axios.post(`${import.meta.env.VITE_API_URL}/api/login_check`, {
                username: email,
                password: password
            });
            const token = response.data.token; // Assuming the token is in response.data.token
            localStorage.setItem('token', token);
            // In a real app, you'd decode the token or fetch user profile based on the token
            setUser({ email: email, role: 'USER' }); // Simplified user setting after successful login
            return true; // Indicate successful login
        } catch (error) {
            console.error("Login failed:", error);
            // Handle login error (e.g., show error message to user)
            return false; // Indicate failed login
        }
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
