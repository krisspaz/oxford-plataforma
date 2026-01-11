import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import Header from '../../components/layout/Header';

describe('Header Component', () => {
    it('renders user info', () => {
        render(<Header user={{ name: 'Admin' }} />);
        expect(screen.getByText(/Admin/)).toBeInTheDocument();
    });
});
