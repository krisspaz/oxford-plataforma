import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import Dashboard from '../../pages/Dashboard';
import { BrowserRouter } from 'react-router-dom';

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
