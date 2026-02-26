import { describe, it, expect, vi } from 'vitest';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';

/**
 * Frontend Test Suite
 * Using Vitest + React Testing Library
 */

// Mock components for testing
// eslint-disable-next-line unused-imports/no-unused-vars
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

// Auth tests moved to src/App.test.jsx

// ==========================================
// COMPONENT TESTS
// ==========================================

describe('NotificationCenter', () => {
    it('should render notification bell', async () => {
        // eslint-disable-next-line unused-imports/no-unused-vars
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
        // eslint-disable-next-line unused-imports/no-unused-vars
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
            expect(screen.getByRole('heading', { name: /notificaciones/i })).toBeInTheDocument();
        });
    });
});

describe('ExportCenter', () => {
    it('should render export options', async () => {
        // eslint-disable-next-line unused-imports/no-unused-vars
        const { default: ExportCenter } = await import('../components/ExportCenter');

        render(
            <TestWrapper>
                <ExportCenter />
            </TestWrapper>
        );

        expect(screen.getByText(/centro de exportación/i)).toBeInTheDocument();
        expect(screen.getAllByText(/boleta/i)[0]).toBeInTheDocument();
        expect(screen.getAllByText(/calificaciones/i)[0]).toBeInTheDocument();
    });
});

// ==========================================
// UTILITY TESTS
// ==========================================

vi.mock('jspdf', () => ({
    default: class {
        internal = {
            getNumberOfPages: vi.fn(() => 1),
            pageSize: {
                width: 210,
                height: 297,
                getWidth: () => 210,
                getHeight: () => 297
            }
        };
        constructor() { }
        text() { }
        save() { }
        autoTable() { }
        setFontSize() { }
        setFont() { }
        setTextColor() { }
        setFillColor() { }
        rect() { }
        setPage() { }
    }
}));

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

        // eslint-disable-next-line unused-imports/no-unused-vars
        const { default: Dashboard } = await import('../pages/Dashboard');

        render(
            <TestWrapper>
                <Dashboard />
            </TestWrapper>
        );

        // Dashboard content should render
        await waitFor(() => {
            expect(screen.getByText(/Panel de Administración/i)).toBeInTheDocument();
        });
    });
});
