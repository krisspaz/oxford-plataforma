import { renderHook } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import useFetch from '../../hooks/useFetch';

describe('useFetch Hook', () => {
    it('exists', () => {
        expect(useFetch).toBeDefined();
    });
});
