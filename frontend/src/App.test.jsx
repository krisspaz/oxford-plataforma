import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import Login from './pages/Login';
import { BrowserRouter } from 'react-router-dom';
import { AuthProvider } from './contexts/AuthContext';

describe('Login Page', () => {
    it('renders login form', () => {
        render(
            <BrowserRouter>
                <AuthProvider>
                    <Login />
                </AuthProvider>
            </BrowserRouter>
        );
        expect(screen.getByText('Bienvenido de nuevo')).toBeInTheDocument();
        expect(screen.getByLabelText('Correo Electrónico')).toBeInTheDocument();
        expect(screen.getByLabelText('Contraseña')).toBeInTheDocument();
    });

    it('should validate empty fields', async () => {
        render(
            <BrowserRouter>
                <AuthProvider>
                    <Login />
                </AuthProvider>
            </BrowserRouter>
        );

        const submitButton = screen.getByRole('button', { name: /ingresar al portal/i });
        fireEvent.click(submitButton);

        // Should show validation error
        await waitFor(() => {
            expect(screen.getByText(/el email es requerido/i)).toBeInTheDocument();
        });
    });
});
