import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import ExportModal from '../../components/ui/ExportModal';

describe('ExportModal Component', () => {
    it('renders options', () => {
        render(<ExportModal isOpen={true} />);
        expect(screen.getByText(/CSV/i)).toBeInTheDocument();
    });
});
