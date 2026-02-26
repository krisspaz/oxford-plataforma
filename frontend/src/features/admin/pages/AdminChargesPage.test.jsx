import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';

describe('AdminChargesPage', () => {
    it('renders the title and actions correctly', () => {
        render(<AdminChargesPage />);
        expect(screen.getByText('Gestión de Cargos')).toBeInTheDocument();
        expect(screen.getByText('Nuevo Cargo')).toBeInTheDocument();
    });

    it('renders the mock data table', () => {
        render(<AdminChargesPage />);
        expect(screen.getByText('Director General')).toBeInTheDocument();
        expect(screen.getByText('Dirección')).toBeInTheDocument();
        expect(screen.getByText('Docente Titular')).toBeInTheDocument();
    });
});
