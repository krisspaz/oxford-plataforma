import { describe, it, expect, vi } from 'vitest';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { BrowserRouter } from 'react-router-dom';
import { AuthProvider } from '../contexts/AuthContext';
import { ThemeProvider } from '../contexts/ThemeContext';

/**
 * Frontend Test Suite
 * Using Vitest + React Testing Library
 */

// Mock components for testing
const TestWrapper = ({ children }) => (
    <BrowserRouter>
        <ThemeProvider>
            <AuthProvider>
                {children}
            </AuthProvider>
        </ThemeProvider>
    </BrowserRouter>
);

// ==========================================
// AUTH TESTS
// ==========================================

describe('Authentication', () => {
    it('should show login form', async () => {
        // Import dynamically to avoid circular deps
        const { default: Login } = await import('../pages/Login');

        render(
            <TestWrapper>
                <Login />
            </TestWrapper>
        );

        expect(screen.getByPlaceholderText(/email/i)).toBeInTheDocument();
        expect(screen.getByPlaceholderText(/contraseña/i)).toBeInTheDocument();
    });

    it('should validate empty fields', async () => {
        const { default: Login } = await import('../pages/Login');

        render(
            <TestWrapper>
                <Login />
            </TestWrapper>
        );

        const submitButton = screen.getByRole('button', { name: /iniciar/i });
        fireEvent.click(submitButton);

        // Should show validation error
        await waitFor(() => {
            expect(screen.getByText(/requerido/i)).toBeInTheDocument();
        });
    });
});

// ==========================================
// COMPONENT TESTS
// ==========================================

describe('NotificationCenter', () => {
    it('should render notification bell', async () => {
        const { default: NotificationCenter } = await import('../components/NotificationCenter');

        render(
            <TestWrapper>
                <NotificationCenter />
            </TestWrapper>
        );

        // Bell icon should exist
        expect(screen.getByLabelText(/notificaciones/i)).toBeInTheDocument();
    });

    it('should toggle dropdown on click', async () => {
        const { default: NotificationCenter } = await import('../components/NotificationCenter');

        render(
            <TestWrapper>
                <NotificationCenter />
            </TestWrapper>
        );

        const bellButton = screen.getByLabelText(/notificaciones/i);
        fireEvent.click(bellButton);

        // Dropdown should appear
        await waitFor(() => {
            expect(screen.getByText(/notificaciones/i)).toBeInTheDocument();
        });
    });
});

describe('ExportCenter', () => {
    it('should render export options', async () => {
        const { default: ExportCenter } = await import('../components/ExportCenter');

        render(
            <TestWrapper>
                <ExportCenter />
            </TestWrapper>
        );

        expect(screen.getByText(/centro de exportación/i)).toBeInTheDocument();
        expect(screen.getByText(/boleta/i)).toBeInTheDocument();
        expect(screen.getByText(/calificaciones/i)).toBeInTheDocument();
    });
});

// ==========================================
// UTILITY TESTS
// ==========================================

describe('Export Utilities', () => {
    it('should export to PDF', async () => {
        const { exportToPDF } = await import('../utils/exportUtils');

        const data = [
            { name: 'Juan', grade: 85 },
            { name: 'María', grade: 92 },
        ];

        const columns = [
            { header: 'Nombre', key: 'name' },
            { header: 'Nota', key: 'grade' },
        ];

        // Should not throw
        expect(() => exportToPDF(data, columns, 'test', 'Test Report')).not.toThrow();
    });

    it('should export to Excel', async () => {
        const { exportToExcel } = await import('../utils/exportUtils');

        const data = [
            { name: 'Juan', grade: 85 },
            { name: 'María', grade: 92 },
        ];

        const columns = [
            { header: 'Nombre', key: 'name' },
            { header: 'Nota', key: 'grade' },
        ];

        // Should not throw
        expect(() => exportToExcel(data, columns, 'test')).not.toThrow();
    });
});

// ==========================================
// INTEGRATION TESTS
// ==========================================

describe('Dashboard Integration', () => {
    it('should render dashboard for authenticated user', async () => {
        // Mock auth
        vi.mock('../contexts/AuthContext', () => ({
            useAuth: () => ({
                user: { email: 'admin@oxford.edu', roles: ['ROLE_ADMIN'] },
                isAuthenticated: true,
            }),
            AuthProvider: ({ children }) => children,
        }));

        const { default: Dashboard } = await import('../pages/Dashboard');

        render(
            <TestWrapper>
                <Dashboard />
            </TestWrapper>
        );

        // Dashboard content should render
        await waitFor(() => {
            expect(screen.getByText(/dashboard/i)).toBeInTheDocument();
        });
    });
});
