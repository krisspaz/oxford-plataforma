import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import StudentForm from '../../features/student/components/StudentForm';

describe('StudentForm Component', () => {
    it('renders inputs', () => {
        render(<StudentForm />);
        expect(screen.getByLabelText(/Nombre/i)).toBeInTheDocument();
    });
});
