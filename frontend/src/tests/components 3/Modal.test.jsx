import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import Modal from '../../components/ui/Modal';

describe('Modal Component', () => {
    it('renders content when open', () => {
        render(<Modal isOpen={true}><div>Content</div></Modal>);
        expect(screen.getByText('Content')).toBeInTheDocument();
    });
});
