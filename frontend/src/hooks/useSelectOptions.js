/**
 * useSelectOptions Hook
 * Provides standardized loading/error/empty states for select dropdowns
 */
import { useQuery } from '@tanstack/react-query';

/**
 * @param {Array|string} queryKey - React Query key
 * @param {Function} fetchFn - Function that fetches the options
 * @param {Object} options - Additional options
 * @param {Function} options.transform - Transform function for options
 * @param {string} options.labelKey - Key to use for label (default: 'name')
 * @param {string} options.valueKey - Key to use for value (default: 'id')
 * @param {boolean} options.enabled - Whether the query is enabled
 * @returns {Object} - { options, loading, error, refetch, isEmpty }
 */
export const useSelectOptions = (queryKey, fetchFn, {
    transform,
    labelKey = 'name',
    valueKey = 'id',
    enabled = true,
    ...queryOptions
} = {}) => {
    const { data, isLoading, error, refetch, isFetching } = useQuery({
        queryKey: Array.isArray(queryKey) ? queryKey : [queryKey],
        queryFn: fetchFn,
        staleTime: 5 * 60 * 1000, // 5 minutes
        retry: 2,
        enabled,
        ...queryOptions,
    });

    // Transform data to select options format
    const transformedOptions = (data || []).map((item) => {
        if (transform) {
            return transform(item);
        }
        return {
            value: item[valueKey],
            label: item[labelKey] || item.nombre || item.title || `ID: ${item[valueKey]}`,
            ...item, // Include rest of item for reference
        };
    });

    return {
        options: transformedOptions,
        loading: isLoading || isFetching,
        error: error?.message || null,
        refetch,
        isEmpty: !isLoading && transformedOptions.length === 0,
        raw: data, // Original data if needed
    };
};

export default useSelectOptions;
