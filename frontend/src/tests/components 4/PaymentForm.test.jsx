import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import PaymentForm from '../../features/financial/components/PaymentForm';

describe('PaymentForm Component', () => {
    it('renders payment options', () => {
        render(<PaymentForm />);
        expect(screen.getByText(/Monto/i)).toBeInTheDocument();
    });
});
