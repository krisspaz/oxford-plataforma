import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import StudentList from '../../features/student/components/StudentList'; // Adjust path if needed
import { BrowserRouter } from 'react-router-dom';

describe('StudentList Component', () => {
    it('renders table headers', () => {
        // Mock props if necessary
        const mockStudents = [];
        render(
            <BrowserRouter>
                <StudentList students={mockStudents} />
            </BrowserRouter>
        );
        expect(screen.getByRole('table')).toBeInTheDocument();
    });
});
