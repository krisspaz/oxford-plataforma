import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import ScheduleGrid from '../../features/academic/components/ScheduleGrid';

describe('ScheduleGrid Component', () => {
    it('renders grid', () => {
        render(<ScheduleGrid />);
        expect(screen.getByText(/Lunes/i)).toBeInTheDocument();
    });
});
