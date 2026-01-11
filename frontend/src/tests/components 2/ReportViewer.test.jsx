import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import ReportViewer from '../../components/reports/ReportViewer';

describe('ReportViewer Component', () => {
    it('renders preview', () => {
        render(<ReportViewer />);
        expect(screen.getByRole('document')).toBeInTheDocument();
    });
});
