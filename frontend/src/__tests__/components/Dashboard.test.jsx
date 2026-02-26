import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';

describe('Dashboard Component', () => {
    it('renders dashboard overview', () => {
        render(
            <BrowserRouter>
                <Dashboard />
            </BrowserRouter>
        );
        expect(screen.getByText(/Dashboard/i)).toBeInTheDocument();
    });
});
