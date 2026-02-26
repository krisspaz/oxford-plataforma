import { render, screen, fireEvent } from '@testing-library/react';
import { describe, it, expect } from 'vitest';

describe('Login Component', () => {
    it('renders login form', () => {
        render(
            <BrowserRouter>
                <Login />
            </BrowserRouter>
        );
        expect(screen.getByPlaceholderText(/email/i)).toBeInTheDocument();
        expect(screen.getByPlaceholderText(/password/i)).toBeInTheDocument();
    });

    it('handles input changes', () => {
        render(
            <BrowserRouter>
                <Login />
            </BrowserRouter>
        );
        const emailInput = screen.getByPlaceholderText(/email/i);
        fireEvent.change(emailInput, { target: { value: 'test@oxford.edu' } });
        expect(emailInput.value).toBe('test@oxford.edu');
    });
});
