/**
 * Select Component
 * Dropdown select with loading, error, and empty states
 */
import { forwardRef } from 'react';
import { cn } from '../../utils/cn';

export const Select = forwardRef(({
    label,
    options = [],
    placeholder = 'Seleccionar...',
    loading = false,
    error,
    disabled = false,
    required = false,
    className,
    id,
    ...props
}, ref) => {
    // eslint-disable-next-line react-hooks/purity
    const inputId = id || `select-${Math.random().toString(36).substr(2, 9)}`;
    const isEmpty = !loading && options.length === 0;
    const isDisabled = disabled || loading;

    return (
        <div className={cn('space-y-1.5', className)}>
            {label && (
                <label
                    htmlFor={inputId}
                    className="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    {label}
                    {required && <span className="text-red-500 ml-1">*</span>}
                </label>
            )}

            <div className="relative">
                <select
                    ref={ref}
                    id={inputId}
                    disabled={isDisabled}
                    aria-invalid={error ? 'true' : 'false'}
                    aria-describedby={error ? `${inputId}-error` : undefined}
                    className={cn(
                        'w-full h-11 pl-4 pr-10 rounded-xl border appearance-none',
                        'bg-white dark:bg-gray-800',
                        'text-gray-900 dark:text-white',
                        'transition-all duration-200',
                        'focus:outline-none focus:ring-2 focus:ring-offset-0',
                        error
                            ? 'border-red-500 focus:ring-red-500/30 focus:border-red-500'
                            : 'border-gray-200 dark:border-gray-600 focus:ring-blue-500/30 focus:border-blue-500',
                        isDisabled && 'opacity-50 cursor-not-allowed bg-gray-50 dark:bg-gray-900'
                    )}
                    {...props}
                >
                    {loading ? (
                        <option value="">Cargando...</option>
                    ) : isEmpty ? (
                        <option value="">Sin opciones disponibles</option>
                    ) : (
                        <>
                            <option value="">{placeholder}</option>
                            {options.map((opt) => (
                                <option key={opt.value} value={opt.value}>
                                    {opt.label}
                                </option>
                            ))}
                        </>
                    )}
                </select>

                {/* Icon indicator */}
                <div className="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                    {loading ? (
                        <Loader2 className="animate-spin text-gray-400" size={18} />
                    ) : error ? (
                        <AlertCircle className="text-red-500" size={18} />
                    ) : (
                        <ChevronDown className="text-gray-400" size={18} />
                    )}
                </div>
            </div>

            {/* Error message */}
            {error && (
                <p
                    id={`${inputId}-error`}
                    className="text-sm text-red-500 flex items-center gap-1"
                    role="alert"
                >
                    <AlertCircle size={14} /> {error}
                </p>
            )}
        </div>
    );
});

Select.displayName = 'Select';

export default Select;
