/**
 * useMutationWithToast Hook
 * Wraps React Query's useMutation with automatic toast notifications
 * and query invalidation pattern
 */
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { toast } from 'sonner';

/**
 * @param {Object} options
 * @param {Function} options.mutationFn - The mutation function
 * @param {Array} options.invalidateKeys - Array of query keys to invalidate on success
 * @param {string|Function} options.successMessage - Success toast message (can be function that receives response)
 * @param {string} options.errorMessage - Default error message
 * @param {Function} options.onSuccess - Additional success callback
 * @param {Function} options.onError - Additional error callback
 * @returns {Object} - useMutation result
 */
export const useMutationWithToast = ({
    mutationFn,
    invalidateKeys = [],
    successMessage,
    errorMessage = 'Ocurrió un error',
    onSuccess: customOnSuccess,
    onError: customOnError,
    ...options
}) => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn,
        onSuccess: (data, variables, context) => {
            // Invalidate specified queries
            invalidateKeys.forEach((key) => {
                queryClient.invalidateQueries({ queryKey: Array.isArray(key) ? key : [key] });
            });

            // Show success toast
            if (successMessage) {
                const message = typeof successMessage === 'function'
                    ? successMessage(data, variables)
                    : successMessage;
                toast.success(message);
            }

            // Call custom onSuccess if provided
            customOnSuccess?.(data, variables, context);
        },
        onError: (error, variables, context) => {
            // Extract error message from response
            const message = error?.response?.data?.message
                || error?.response?.data?.error
                || error?.message
                || errorMessage;

            toast.error(message);

            // Call custom onError if provided
            customOnError?.(error, variables, context);
        },
        ...options,
    });
};

export default useMutationWithToast;
