import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import Sidebar from '../../components/layout/Sidebar';
import { BrowserRouter } from 'react-router-dom';

describe('Sidebar Component', () => {
    it('renders navigation links', () => {
        render(<BrowserRouter><Sidebar /></BrowserRouter>);
        expect(screen.getByRole('navigation')).toBeInTheDocument();
    });
});
