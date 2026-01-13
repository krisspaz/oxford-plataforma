import '@testing-library/jest-dom';
import { vi } from 'vitest';

// Mock localStorage
const localStorageMock = {
    getItem: vi.fn(),
    setItem: vi.fn(),
    removeItem: vi.fn(),
    clear: vi.fn(),
    length: 0,
    key: vi.fn(),
};
global.localStorage = localStorageMock;

// Mock matchMedia
Object.defineProperty(window, 'matchMedia', {
    writable: true,
    value: vi.fn().mockImplementation(query => ({
        matches: false,
        media: query,
        onchange: null,
        addListener: vi.fn(), // deprecated
        removeListener: vi.fn(), // deprecated
        addEventListener: vi.fn(),
        removeEventListener: vi.fn(),
        dispatchEvent: vi.fn(),
    })),
});

// Mock API Service
vi.mock('../services/api', () => {
    const apiMock = {
        get: vi.fn(() => Promise.resolve([])),
        post: vi.fn(() => Promise.resolve({})),
        put: vi.fn(() => Promise.resolve({})),
        patch: vi.fn(() => Promise.resolve({})),
        delete: vi.fn(() => Promise.resolve({})),
    };
    return {
        default: apiMock,
        api: apiMock,
        getToken: vi.fn(() => 'mock-token'),
        setToken: vi.fn(),
        isAuthenticated: vi.fn(() => true),
    };
});
