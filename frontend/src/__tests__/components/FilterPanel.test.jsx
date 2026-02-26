import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';

describe('FilterPanel Component', () => {
    it('renders inputs', () => {
        render(<FilterPanel />);
        expect(screen.getByRole('searchbox')).toBeInTheDocument();
    });
});
