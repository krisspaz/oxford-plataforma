import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import GradeEntry from '../../features/academic/components/GradeEntry';

describe('GradeEntry Component', () => {
    it('renders grade inputs', () => {
        render(<GradeEntry />);
        expect(screen.getByPlaceholderText(/Nota/i)).toBeInTheDocument();
    });
});
