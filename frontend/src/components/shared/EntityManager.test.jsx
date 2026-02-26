import { describe, it, expect, vi, beforeEach } from 'vitest';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';

// Mock the API
vi.mock('../../services', () => ({
    api: {
        get: vi.fn(),
        post: vi.fn(),
        put: vi.fn(),
        delete: vi.fn(),
    },
}));

import { api } from '../../services';

const mockData = [
    { id: 1, name: 'Test Item 1', status: 'active' },
    { id: 2, name: 'Test Item 2', status: 'inactive' },
];

const defaultProps = {
    title: 'Test Manager',
    subtitle: 'Manage test items',
    endpoint: '/test-items',
    entityName: 'item',
    columns: [
        { key: 'id', label: 'ID' },
        { key: 'name', label: 'Name' },
        { key: 'status', label: 'Status', type: 'badge' },
    ],
    formFields: [
        { key: 'name', label: 'Name', type: 'text', required: true },
        {
            key: 'status', label: 'Status', type: 'select', options: [
                { value: 'active', label: 'Active' },
                { value: 'inactive', label: 'Inactive' },
            ]
        },
    ],
};

const renderWithTheme = (component) => {
    return render(
        <ThemeProvider>
            {component}
        </ThemeProvider>
    );
};

describe('EntityManager', () => {
    beforeEach(() => {
        vi.clearAllMocks();
        api.get.mockResolvedValue({ data: mockData });
    });

    it('renders title and subtitle', async () => {
        renderWithTheme(<EntityManager {...defaultProps} />);

        await waitFor(() => {
            expect(screen.getByText('Test Manager')).toBeInTheDocument();
            expect(screen.getByText('Manage test items')).toBeInTheDocument();
        });
    });

    it('displays loading state initially', () => {
        api.get.mockImplementation(() => new Promise(() => { })); // Never resolves
        renderWithTheme(<EntityManager {...defaultProps} />);

        expect(screen.getByText('Cargando...')).toBeInTheDocument();
    });

    it('loads and displays data from API', async () => {
        renderWithTheme(<EntityManager {...defaultProps} />);

        await waitFor(() => {
            expect(api.get).toHaveBeenCalledWith('/test-items');
            expect(screen.getByText('Test Item 1')).toBeInTheDocument();
            expect(screen.getByText('Test Item 2')).toBeInTheDocument();
        });
    });

    it('filters data based on search term', async () => {
        renderWithTheme(<EntityManager {...defaultProps} />);

        await waitFor(() => {
            expect(screen.getByText('Test Item 1')).toBeInTheDocument();
        });

        const searchInput = screen.getByPlaceholderText('Buscar...');
        fireEvent.change(searchInput, { target: { value: 'Item 1' } });

        expect(screen.getByText('Test Item 1')).toBeInTheDocument();
        expect(screen.queryByText('Test Item 2')).not.toBeInTheDocument();
    });

    it('opens create modal when clicking new button', async () => {
        renderWithTheme(<EntityManager {...defaultProps} />);

        await waitFor(() => {
            expect(screen.getByText('Test Item 1')).toBeInTheDocument();
        });

        const newButton = screen.getByText(/nuevo item/i);
        fireEvent.click(newButton);

        expect(screen.getByText('Nuevo item')).toBeInTheDocument();
        expect(screen.getByLabelText(/name/i)).toBeInTheDocument();
    });

    it('calls API delete when deleting item', async () => {
        api.delete.mockResolvedValue({ success: true });
        window.confirm = vi.fn().mockReturnValue(true);

        renderWithTheme(<EntityManager {...defaultProps} />);

        await waitFor(() => {
            expect(screen.getByText('Test Item 1')).toBeInTheDocument();
        });

        const deleteButtons = screen.getAllByTitle('Eliminar');
        fireEvent.click(deleteButtons[0]);

        await waitFor(() => {
            expect(api.delete).toHaveBeenCalledWith('/test-items/1');
        });
    });

    it('handles empty data state', async () => {
        api.get.mockResolvedValue({ data: [] });

        renderWithTheme(<EntityManager {...defaultProps} />);

        await waitFor(() => {
            expect(screen.getByText('No se encontraron registros')).toBeInTheDocument();
        });
    });

    it('handles API error gracefully', async () => {
        api.get.mockRejectedValue(new Error('Network error'));

        renderWithTheme(<EntityManager {...defaultProps} />);

        await waitFor(() => {
            expect(screen.getByText('No se encontraron registros')).toBeInTheDocument();
        });
    });
});
