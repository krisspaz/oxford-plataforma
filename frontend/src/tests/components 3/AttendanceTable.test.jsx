import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import AttendanceTable from '../../features/academic/components/AttendanceTable';

describe('AttendanceTable Component', () => {
    it('renders checklist', () => {
        render(<AttendanceTable />);
        expect(screen.getByRole('table')).toBeInTheDocument();
    });
});
