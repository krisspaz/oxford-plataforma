import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import Calendar from '../../components/ui/Calendar';

describe('Calendar Component', () => {
    it('renders days', () => {
        render(<Calendar />);
        expect(screen.getByText(/Dom/i)).toBeInTheDocument();
    });
});
