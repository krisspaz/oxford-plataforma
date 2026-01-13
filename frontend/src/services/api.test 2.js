import { describe, it, expect, vi, beforeEach } from 'vitest';

// Mock localStorage
const localStorageMock = (() => {
    let store = {};
    return {
        getItem: vi.fn((key) => store[key] || null),
        setItem: vi.fn((key, value) => { store[key] = value; }),
        removeItem: vi.fn((key) => { delete store[key]; }),
        clear: vi.fn(() => { store = {}; }),
    };
})();
Object.defineProperty(window, 'localStorage', { value: localStorageMock });

// Mock fetch
global.fetch = vi.fn();

import { api, getToken, setToken, isAuthenticated } from './api';

describe('API Service', () => {
    beforeEach(() => {
        vi.clearAllMocks();
        localStorageMock.clear();
    });

    describe('Token Management', () => {
        it('getToken returns null when no token stored', () => {
            localStorageMock.getItem.mockReturnValue(null);
            expect(getToken()).toBeNull();
        });

        it('getToken returns stored token', () => {
            localStorageMock.getItem.mockReturnValue('test-token');
            expect(getToken()).toBe('test-token');
        });

        it('setToken stores token in localStorage', () => {
            setToken('new-token');
            expect(localStorageMock.setItem).toHaveBeenCalledWith('token', 'new-token');
        });

        it('setToken removes token when null passed', () => {
            setToken(null);
            expect(localStorageMock.removeItem).toHaveBeenCalledWith('token');
        });

        it('isAuthenticated returns true when token exists', () => {
            localStorageMock.getItem.mockReturnValue('valid-token');
            expect(isAuthenticated()).toBe(true);
        });

        it('isAuthenticated returns false when no token', () => {
            localStorageMock.getItem.mockReturnValue(null);
            expect(isAuthenticated()).toBe(false);
        });
    });

    describe('API Methods', () => {
        beforeEach(() => {
            localStorageMock.getItem.mockReturnValue('test-token');
        });

        it('GET request includes Authorization header', async () => {
            global.fetch.mockResolvedValue({
                ok: true,
                status: 200,
                headers: { get: () => 'application/json' },
                json: () => Promise.resolve({ data: 'test' }),
            });

            await api.get('/test');

            expect(global.fetch).toHaveBeenCalledWith(
                '/api/test',
                expect.objectContaining({
                    method: 'GET',
                    headers: expect.objectContaining({
                        Authorization: 'Bearer test-token',
                    }),
                })
            );
        });

        it('POST request sends JSON body', async () => {
            global.fetch.mockResolvedValue({
                ok: true,
                status: 200,
                headers: { get: () => 'application/json' },
                json: () => Promise.resolve({ success: true }),
            });

            await api.post('/test', { name: 'Test' });

            expect(global.fetch).toHaveBeenCalledWith(
                '/api/test',
                expect.objectContaining({
                    method: 'POST',
                    body: '{"name":"Test"}',
                })
            );
        });

        it('handles 404 error', async () => {
            global.fetch.mockResolvedValue({
                ok: false,
                status: 404,
                headers: { get: () => 'application/json' },
                json: () => Promise.resolve({ error: 'Not found' }),
            });

            await expect(api.get('/nonexistent')).rejects.toThrow('Recurso no encontrado');
        });

        it('handles 403 error', async () => {
            global.fetch.mockResolvedValue({
                ok: false,
                status: 403,
                headers: { get: () => 'application/json' },
                json: () => Promise.resolve({}),
            });

            await expect(api.get('/forbidden')).rejects.toThrow('Acceso denegado');
        });

        it('handles 429 rate limit error', async () => {
            global.fetch.mockResolvedValue({
                ok: false,
                status: 429,
                headers: { get: () => 'application/json' },
                json: () => Promise.resolve({}),
            });

            await expect(api.get('/rate-limited')).rejects.toThrow('Demasiadas solicitudes');
        });
    });

    describe('HTTP Methods', () => {
        beforeEach(() => {
            global.fetch.mockResolvedValue({
                ok: true,
                status: 200,
                headers: { get: () => 'application/json' },
                json: () => Promise.resolve({ success: true }),
            });
        });

        it('PUT request uses correct method', async () => {
            await api.put('/test/1', { name: 'Updated' });
            expect(global.fetch).toHaveBeenCalledWith(
                '/api/test/1',
                expect.objectContaining({ method: 'PUT' })
            );
        });

        it('PATCH request uses correct method', async () => {
            await api.patch('/test/1', { name: 'Patched' });
            expect(global.fetch).toHaveBeenCalledWith(
                '/api/test/1',
                expect.objectContaining({ method: 'PATCH' })
            );
        });

        it('DELETE request uses correct method', async () => {
            await api.delete('/test/1');
            expect(global.fetch).toHaveBeenCalledWith(
                '/api/test/1',
                expect.objectContaining({ method: 'DELETE' })
            );
        });
    });
});
