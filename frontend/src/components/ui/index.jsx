import React from 'react';

/**
 * Design System - Reusable Components
 * Includes: Button, Input, Card, Modal, Badge, Spinner
 */

// ==========================================
// BUTTON
// ==========================================

export const Button = ({
    children,
    variant = 'primary',
    size = 'md',
    disabled = false,
    loading = false,
    icon = null,
    className = '',
    onClick,
    type = 'button',
    ...props
}) => {
    const variants = {
        primary: 'bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:opacity-90',
        secondary: 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600',
        danger: 'bg-red-600 text-white hover:bg-red-700',
        success: 'bg-green-600 text-white hover:bg-green-700',
        ghost: 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700',
        outline: 'border-2 border-blue-600 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20',
    };

    const sizes = {
        sm: 'px-3 py-1.5 text-sm',
        md: 'px-4 py-2',
        lg: 'px-6 py-3 text-lg',
    };

    return (
        <button
            type={type}
            disabled={disabled || loading}
            onClick={onClick}
            className={`
        inline-flex items-center justify-center gap-2 font-medium rounded-xl
        transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed
        focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
        ${variants[variant]} ${sizes[size]} ${className}
      `}
            {...props}
        >
            {loading ? <Spinner size="sm" /> : icon}
            {children}
        </button>
    );
};

// ==========================================
// INPUT
// ==========================================

export const Input = ({
    label,
    error,
    icon = null,
    className = '',
    id,
    type = 'text',
    ...props
}) => {
    const inputId = id || `input-${Math.random().toString(36).substr(2, 9)}`;

    return (
        <div className={className}>
            {label && (
                <label
                    htmlFor={inputId}
                    className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                >
                    {label}
                </label>
            )}
            <div className="relative">
                {icon && (
                    <div className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        {icon}
                    </div>
                )}
                <input
                    id={inputId}
                    type={type}
                    className={`
            w-full rounded-xl border transition-colors
            ${icon ? 'pl-10' : 'pl-4'} pr-4 py-3
            ${error
                            ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                            : 'border-gray-200 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500'
                        }
            bg-white dark:bg-gray-800 text-gray-900 dark:text-white
            focus:outline-none focus:ring-2
          `}
                    aria-invalid={error ? 'true' : 'false'}
                    aria-describedby={error ? `${inputId}-error` : undefined}
                    {...props}
                />
            </div>
            {error && (
                <p id={`${inputId}-error`} className="mt-1 text-sm text-red-500" role="alert">
                    {error}
                </p>
            )}
        </div>
    );
};

// ==========================================
// CARD
// ==========================================

export const Card = ({
    children,
    title,
    subtitle,
    icon,
    actions,
    className = '',
    hoverable = false,
    ...props
}) => {
    return (
        <div
            className={`
        bg-white dark:bg-gray-800 rounded-2xl shadow-lg
        ${hoverable ? 'hover:shadow-xl transition-shadow cursor-pointer' : ''}
        ${className}
      `}
            {...props}
        >
            {(title || actions) && (
                <div className="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700">
                    <div className="flex items-center gap-3">
                        {icon && <div className="text-2xl">{icon}</div>}
                        <div>
                            {title && <h3 className="font-semibold text-gray-900 dark:text-white">{title}</h3>}
                            {subtitle && <p className="text-sm text-gray-500 dark:text-gray-400">{subtitle}</p>}
                        </div>
                    </div>
                    {actions && <div className="flex gap-2">{actions}</div>}
                </div>
            )}
            <div className="p-6">{children}</div>
        </div>
    );
};

// ==========================================
// MODAL
// ==========================================

export const Modal = ({
    isOpen,
    onClose,
    title,
    children,
    footer,
    size = 'md',
}) => {
    if (!isOpen) return null;

    const sizes = {
        sm: 'max-w-sm',
        md: 'max-w-md',
        lg: 'max-w-lg',
        xl: 'max-w-xl',
        full: 'max-w-4xl',
    };

    return (
        <div
            className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            onClick={onClose}
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-title"
        >
            <div
                className={`bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full ${sizes[size]} overflow-hidden`}
                onClick={(e) => e.stopPropagation()}
            >
                {/* Header */}
                <div className="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700">
                    <h2 id="modal-title" className="text-xl font-bold text-gray-900 dark:text-white">
                        {title}
                    </h2>
                    <button
                        onClick={onClose}
                        className="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                        aria-label="Cerrar modal"
                    >
                        ✕
                    </button>
                </div>

                {/* Body */}
                <div className="p-6">{children}</div>

                {/* Footer */}
                {footer && (
                    <div className="flex justify-end gap-3 p-6 border-t border-gray-100 dark:border-gray-700">
                        {footer}
                    </div>
                )}
            </div>
        </div>
    );
};

// ==========================================
// BADGE
// ==========================================

export const Badge = ({
    children,
    variant = 'default',
    size = 'md',
    className = '',
}) => {
    const variants = {
        default: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        primary: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        success: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        warning: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
        danger: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    };

    const sizes = {
        sm: 'px-2 py-0.5 text-xs',
        md: 'px-2.5 py-1 text-sm',
        lg: 'px-3 py-1.5',
    };

    return (
        <span
            className={`
        inline-flex items-center font-medium rounded-full
        ${variants[variant]} ${sizes[size]} ${className}
      `}
        >
            {children}
        </span>
    );
};

// ==========================================
// SPINNER
// ==========================================

export const Spinner = ({ size = 'md', className = '' }) => {
    const sizes = {
        sm: 'w-4 h-4',
        md: 'w-6 h-6',
        lg: 'w-10 h-10',
    };

    return (
        <div
            className={`
        border-2 border-current border-t-transparent rounded-full animate-spin
        ${sizes[size]} ${className}
      `}
            role="status"
            aria-label="Cargando"
        >
            <span className="sr-only">Cargando...</span>
        </div>
    );
};

// ==========================================
// SKELETON
// ==========================================

export const Skeleton = ({ className = '', variant = 'rect' }) => {
    const variants = {
        rect: 'rounded-lg',
        circle: 'rounded-full',
        text: 'rounded h-4',
    };

    return (
        <div
            className={`
        animate-pulse bg-gray-200 dark:bg-gray-700
        ${variants[variant]} ${className}
      `}
        />
    );
};

// ==========================================
// ALERT
// ==========================================

export const Alert = ({
    children,
    variant = 'info',
    title,
    onClose,
    className = '',
}) => {
    const variants = {
        info: 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-300',
        success: 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300',
        warning: 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-300',
        error: 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300',
    };

    const icons = {
        info: 'ℹ️',
        success: '✅',
        warning: '⚠️',
        error: '❌',
    };

    return (
        <div
            className={`
        flex gap-3 p-4 border rounded-xl
        ${variants[variant]} ${className}
      `}
            role="alert"
        >
            <span className="text-xl">{icons[variant]}</span>
            <div className="flex-1">
                {title && <p className="font-semibold">{title}</p>}
                <div className="text-sm">{children}</div>
            </div>
            {onClose && (
                <button
                    onClick={onClose}
                    className="opacity-50 hover:opacity-100 transition-opacity"
                    aria-label="Cerrar alerta"
                >
                    ✕
                </button>
            )}
        </div>
    );
};

// ==========================================
// TOOLTIP
// ==========================================

export const Tooltip = ({ children, content, position = 'top' }) => {
    const positions = {
        top: 'bottom-full mb-2 left-1/2 -translate-x-1/2',
        bottom: 'top-full mt-2 left-1/2 -translate-x-1/2',
        left: 'right-full mr-2 top-1/2 -translate-y-1/2',
        right: 'left-full ml-2 top-1/2 -translate-y-1/2',
    };

    return (
        <div className="relative group inline-block">
            {children}
            <div
                className={`
          absolute ${positions[position]} z-50
          px-2 py-1 text-sm text-white bg-gray-900 rounded-lg
          opacity-0 invisible group-hover:opacity-100 group-hover:visible
          transition-all duration-200 whitespace-nowrap
        `}
                role="tooltip"
            >
                {content}
            </div>
        </div>
    );
};

export default {
    Button,
    Input,
    Card,
    Modal,
    Badge,
    Spinner,
    Skeleton,
    Alert,
    Tooltip,
};
