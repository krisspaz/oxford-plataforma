import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import NotificationBell from '../../components/ui/NotificationBell';

describe('NotificationBell Component', () => {
    it('renders icon', () => {
        render(<NotificationBell />);
        expect(screen.getByRole('button')).toBeInTheDocument();
    });
});
