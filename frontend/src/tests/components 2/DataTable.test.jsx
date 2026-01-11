import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import DataTable from '../../components/ui/DataTable';

describe('DataTable Component', () => {
    it('renders rows', () => {
        render(<DataTable data={[{ id: 1, name: 'Test' }]} columns={[{ accessor: 'name' }]} />);
        expect(screen.getByText('Test')).toBeInTheDocument();
    });
});
