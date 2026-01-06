import { render, screen, waitFor } from '@testing-library/react';
import { describe, it, expect, vi } from 'vitest';
import StudentGradesPage from './StudentGradesPage';
import { AuthProvider } from '../../../contexts/AuthContext';

// Mock API
vi.mock('../../../services/api', () => ({
    default: {
        get: vi.fn(),
    },
}));

// Mock Auth Context
const MockAuthProvider = ({ children }) => (
    <AuthProvider value={{ user: { name: 'Test Student' } }}>
        {children}
    </AuthProvider>
);

describe('StudentGradesPage', () => {
    it('renders the header correctly', async () => {
        render(
            <MockAuthProvider>
                <StudentGradesPage />
            </MockAuthProvider>
        );

        await waitFor(() => {
            expect(screen.getByText('Mis Calificaciones')).toBeInTheDocument();
        });
    });

    it('displays the grades table after loading', async () => {
        render(
            <MockAuthProvider>
                <StudentGradesPage />
            </MockAuthProvider>
        );

        await waitFor(() => {
            expect(screen.getByText('Matemáticas')).toBeInTheDocument();
            expect(screen.getByText('Ciencias Naturales')).toBeInTheDocument();
        });
    });
});
