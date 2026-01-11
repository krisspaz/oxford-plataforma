import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import FinancialDashboard from '../../features/financial/components/FinancialDashboard';

describe('FinancialDashboard Component', () => {
    it('renders charts', () => {
        render(<FinancialDashboard />);
        expect(screen.getByText(/Ingresos/i)).toBeInTheDocument();
    });
});
