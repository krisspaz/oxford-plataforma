import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import ConfirmDialog from '../../components/ui/ConfirmDialog';

describe('ConfirmDialog Component', () => {
    it('renders message', () => {
        render(<ConfirmDialog isOpen={true} message="Sure?" />);
        expect(screen.getByText('Sure?')).toBeInTheDocument();
    });
});
