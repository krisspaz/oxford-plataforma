/* eslint-disable react-refresh/only-export-components */
import { useMemo, useCallback, memo, useState, useEffect, useRef, createContext, useContext } from 'react';

/**
 * Performance optimization utilities
 * React.memo, useMemo, useCallback patterns
 */

// ==========================================
// MEMOIZED COMPONENTS
// ==========================================

/**
 * Memoized list item component
 */
export const MemoizedListItem = memo(function ListItem({
    item,
    onSelect,
    isSelected
}) {
    const handleClick = useCallback(() => {
        onSelect(item.id);
    }, [item.id, onSelect]);

    return (
        <div
            onClick={handleClick}
            className={`p-4 cursor-pointer transition-colors ${isSelected ? 'bg-blue-100 dark:bg-blue-900/30' : 'hover:bg-gray-100 dark:hover:bg-gray-800'
                }`}
            role="option"
            aria-selected={isSelected}
        >
            {item.name}
        </div>
    );
});

/**
 * Memoized table row
 */
export const MemoizedTableRow = memo(function TableRow({
    row,
    columns,
    onRowClick
}) {
    return (
        <tr
            onClick={() => onRowClick?.(row)}
            className="hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer"
        >
            {columns.map((col, idx) => (
                <td key={idx} className="px-4 py-3 text-sm">
                    {row[col.key]}
                </td>
            ))}
        </tr>
    );
});

// ==========================================
// HOOKS FOR OPTIMIZATION
// ==========================================

/**
 * Hook for memoized filtering
 */
export const useFilteredData = (data, filters) => {
    return useMemo(() => {
        if (!filters || Object.keys(filters).length === 0) {
            return data;
        }

        return data.filter(item => {
            return Object.entries(filters).every(([key, value]) => {
                if (!value) return true;
                const itemValue = String(item[key]).toLowerCase();
                return itemValue.includes(String(value).toLowerCase());
            });
        });
    }, [data, filters]);
};

/**
 * Hook for memoized sorting
 */
export const useSortedData = (data, sortConfig) => {
    return useMemo(() => {
        if (!sortConfig || !sortConfig.key) {
            return data;
        }

        return [...data].sort((a, b) => {
            const aVal = a[sortConfig.key];
            const bVal = b[sortConfig.key];

            if (aVal < bVal) return sortConfig.direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return sortConfig.direction === 'asc' ? 1 : -1;
            return 0;
        });
    }, [data, sortConfig]);
};

/**
 * Hook for memoized pagination
 */
export const usePaginatedData = (data, page, pageSize = 10) => {
    return useMemo(() => {
        const start = (page - 1) * pageSize;
        const end = start + pageSize;

        return {
            items: data.slice(start, end),
            totalPages: Math.ceil(data.length / pageSize),
            total: data.length,
            hasNext: end < data.length,
            hasPrev: page > 1,
        };
    }, [data, page, pageSize]);
};

/**
 * Hook for debounced value
 */
export const useDebounce = (value, delay = 300) => {
    const [debouncedValue, setDebouncedValue] = useState(value);

    useEffect(() => {
        const timer = setTimeout(() => {
            setDebouncedValue(value);
        }, delay);

        return () => clearTimeout(timer);
    }, [value, delay]);

    return debouncedValue;
};

/**
 * Hook for throttled callback
 */
export const useThrottle = (callback, delay = 300) => {
    // eslint-disable-next-line react-hooks/purity
    const lastRun = useRef(Date.now());

    return useCallback((...args) => {
        if (Date.now() - lastRun.current >= delay) {
            callback(...args);
            lastRun.current = Date.now();
        }
    }, [callback, delay]);
};

/**
 * Hook for stable callback
 */
export const useStableCallback = (callback) => {
    const callbackRef = useRef(callback);

    useEffect(() => {
        callbackRef.current = callback;
    }, [callback]);

    return useCallback((...args) => {
        return callbackRef.current(...args);
    }, []);
};

// ==========================================
// CONTEXT OPTIMIZATION
// ==========================================

const DefaultContext = createContext(null);

/**
 * Create optimized context with selector
 * Prevents unnecessary re-renders
 */
export const createOptimizedContext = (defaultValue) => {
    const Context = createContext(defaultValue);

    const useContextSelector = (selector) => {
        const value = useContext(Context);
        return useMemo(() => selector(value), [value, selector]);
    };

    return { Context, useContextSelector };
};

/**
 * Memoized context provider pattern
 */
export const createMemoizedProvider = (useProviderValue, DerivedContext) => {
    // eslint-disable-next-line unused-imports/no-unused-vars
    const TargetContext = DerivedContext || DefaultContext;

    return memo(function Provider({ children }) {
        const value = useProviderValue();
        // eslint-disable-next-line react-hooks/exhaustive-deps
        const memoizedValue = useMemo(() => value, [JSON.stringify(value)]);

        return (
            <TargetContext.Provider value={memoizedValue}>
                {children}
            </TargetContext.Provider>
        );
    });
};

// ==========================================
// VIRTUAL LIST FOR LARGE DATA
// ==========================================

/**
 * Virtual list hook for rendering large lists efficiently
 */
export const useVirtualList = (items, itemHeight, containerHeight) => {
    const [scrollTop, setScrollTop] = useState(0);

    const startIndex = Math.floor(scrollTop / itemHeight);
    const endIndex = Math.min(
        startIndex + Math.ceil(containerHeight / itemHeight) + 1,
        items.length
    );

    const visibleItems = useMemo(() => {
        return items.slice(startIndex, endIndex).map((item, index) => ({
            ...item,
            index: startIndex + index,
            style: {
                position: 'absolute',
                top: (startIndex + index) * itemHeight,
                height: itemHeight,
                width: '100%',
            },
        }));
    }, [items, startIndex, endIndex, itemHeight]);

    const totalHeight = items.length * itemHeight;

    const handleScroll = useCallback((e) => {
        setScrollTop(e.target.scrollTop);
    }, []);

    return {
        visibleItems,
        totalHeight,
        handleScroll,
        startIndex,
        endIndex,
    };
};

export default {
    MemoizedListItem,
    MemoizedTableRow,
    useFilteredData,
    useSortedData,
    usePaginatedData,
    useDebounce,
    useThrottle,
    useStableCallback,
    useVirtualList,
};
