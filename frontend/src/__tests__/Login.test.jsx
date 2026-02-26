import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';

describe('Login Component Smoke Test', () => {
    it('renders login form', () => {
        render(
            <BrowserRouter>
                <AuthProvider>
                    <Login />
                </AuthProvider>
            </BrowserRouter>
        );

        // Check for "Bienvenido de nuevo" text which is present in the component
        const loginHeadings = screen.getAllByText(/Bienvenido de nuevo/i);
        expect(loginHeadings.length).toBeGreaterThan(0);

        // Check for email input
        expect(screen.getByPlaceholderText(/ejemplo@oxford.edu.gt/i)).toBeInTheDocument();
    });
});
