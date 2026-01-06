import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import TaskCard from '../../features/academic/components/TaskCard';

describe('TaskCard Component', () => {
    it('renders task details', () => {
        render(<TaskCard task={{ title: 'Homework' }} />);
        expect(screen.getByText('Homework')).toBeInTheDocument();
    });
});
