/**
 * Sistema Oxford - Debounce Hook
 * ===============================
 * Delays value updates to reduce API calls on rapid input changes.
 */
import { useState, useEffect } from 'react';

/**
 * Debounce a value by a specified delay
 * @param {any} value - Value to debounce
 * @param {number} delay - Delay in milliseconds (default: 500)
 * @returns {any} - Debounced value
 * 
 * @example
 * const [search, setSearch] = useState('');
 * const debouncedSearch = useDebounce(search, 500);
 * 
 * // API call only fires when user stops typing for 500ms
 * const { data } = useStudents({ search: debouncedSearch });
 */
export const useDebounce = (value, delay = 500) => {
    const [debouncedValue, setDebouncedValue] = useState(value);

    useEffect(() => {
        // Set up timer to update debounced value
        const handler = setTimeout(() => {
            setDebouncedValue(value);
        }, delay);

        // Cleanup: cancel timer on value change or unmount
        return () => {
            clearTimeout(handler);
        };
    }, [value, delay]);

    return debouncedValue;
};

export default useDebounce;
