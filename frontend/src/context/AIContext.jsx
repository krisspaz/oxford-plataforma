import { createContext, useContext, useState, useEffect } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { aiService } from '../services/aiApi';

const AIContext = createContext();

export const AIProvider = ({ children }) => {
    const location = useLocation();
    const { user } = useAuth();
    const [contextState, setContextState] = useState({
        currentPath: '/',
        viewingEntity: null, // e.g., { type: 'grade', id: 1 }
        lastAction: null,
        timestamp: Date.now()
    });

    // Omniscience: Update context on route change
    useEffect(() => {
        const newContext = {
            ...contextState,
            currentPath: location.pathname,
            timestamp: Date.now()
        };

        setContextState(newContext);

        // Here we will eventually send this context to the AI Service
        console.log('🧠 [AI Omniscience] User moved to:', location.pathname);

        // Example: If path is /grades/1, we infer viewingEntity
        // This logic can be expanded or moved to a utility

    }, [location]);

    const registerAction = (action, details) => {
        console.log('🧠 [AI Omniscience] Action detected:', action, details);
        setContextState(prev => ({
            ...prev,
            lastAction: { action, details, time: Date.now() }
        }));
    };

    return (
        <AIContext.Provider value={{ contextState, registerAction }}>
            {children}
        </AIContext.Provider>
    );
};

export const useAI = () => useContext(AIContext);
