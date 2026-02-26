/**
 * Accessibility Utilities
 * ARIA labels, keyboard navigation, focus management
 */

// ==========================================
// FOCUS MANAGEMENT
// ==========================================

import { useEffect, useRef, useCallback, useState } from 'react';

/**
 * Trap focus within an element (for modals)
 */
export const trapFocus = (element) => {
    const focusableElements = element.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );

    const firstFocusable = focusableElements[0];
    const lastFocusable = focusableElements[focusableElements.length - 1];

    const handleKeyDown = (e) => {
        if (e.key !== 'Tab') return;

        if (e.shiftKey) {
            if (document.activeElement === firstFocusable) {
                e.preventDefault();
                lastFocusable.focus();
            }
        } else {
            if (document.activeElement === lastFocusable) {
                e.preventDefault();
                firstFocusable.focus();
            }
        }
    };

    element.addEventListener('keydown', handleKeyDown);
    firstFocusable?.focus();

    return () => element.removeEventListener('keydown', handleKeyDown);
};

/**
 * Hook for managing focus
 */
export const useFocusManagement = () => {
    const previousFocusRef = useRef(null);

    const saveFocus = useCallback(() => {
        previousFocusRef.current = document.activeElement;
    }, []);

    const restoreFocus = useCallback(() => {
        previousFocusRef.current?.focus();
    }, []);

    return { saveFocus, restoreFocus };
};

/**
 * Hook for focus trap in modals
 */
export const useFocusTrap = (isActive) => {
    const containerRef = useRef(null);

    useEffect(() => {
        if (!isActive || !containerRef.current) return;

        const cleanup = trapFocus(containerRef.current);
        return cleanup;
    }, [isActive]);

    return containerRef;
};

// ==========================================
// KEYBOARD NAVIGATION
// ==========================================

/**
 * Hook for arrow key navigation in lists
 */
export const useArrowKeyNavigation = (itemCount, onSelect) => {
    const [activeIndex, setActiveIndex] = useState(0);

    const handleKeyDown = useCallback((e) => {
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                setActiveIndex(prev => Math.min(prev + 1, itemCount - 1));
                break;
            case 'ArrowUp':
                e.preventDefault();
                setActiveIndex(prev => Math.max(prev - 1, 0));
                break;
            case 'Enter':
            case ' ':
                e.preventDefault();
                onSelect?.(activeIndex);
                break;
            case 'Home':
                e.preventDefault();
                setActiveIndex(0);
                break;
            case 'End':
                e.preventDefault();
                setActiveIndex(itemCount - 1);
                break;
        }
    }, [itemCount, onSelect, activeIndex]);

    return { activeIndex, setActiveIndex, handleKeyDown };
};

/**
 * Hook for escape key to close modals
 */
export const useEscapeKey = (onEscape) => {
    useEffect(() => {
        const handleKeyDown = (e) => {
            if (e.key === 'Escape') {
                onEscape();
            }
        };

        document.addEventListener('keydown', handleKeyDown);
        return () => document.removeEventListener('keydown', handleKeyDown);
    }, [onEscape]);
};

// ==========================================
// SCREEN READER ANNOUNCEMENTS
// ==========================================

/**
 * Hook for announcing messages to screen readers
 */
export const useAnnouncement = () => {
    const announce = useCallback((message, priority = 'polite') => {
        const announcement = document.createElement('div');
        announcement.setAttribute('role', 'status');
        announcement.setAttribute('aria-live', priority);
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = message;

        document.body.appendChild(announcement);

        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    }, []);

    return announce;
};

/**
 * Screen reader only component
 */
export const ScreenReaderOnly = ({ children }) => (
    <span className="sr-only">
        {children}
    </span>
);

// ==========================================
// ARIA HELPERS
// ==========================================

/**
 * Generate unique IDs for ARIA relationships
 */
let idCounter = 0;
export const generateId = (prefix = 'a11y') => {
    return `${prefix}-${++idCounter}`;
};

/* eslint-disable react-hooks/refs */
export const useAriaDescribedBy = () => {
    const id = useRef(generateId('description'));

    const describedById = id.current;
    const descriptionProps = { id: id.current };
    const inputProps = { 'aria-describedby': id.current };

    return {
        describedById,
        descriptionProps,
        inputProps,
    };
};

/**
 * Hook for ARIA labelled by
 */
/* eslint-disable react-hooks/refs */
export const useAriaLabelledBy = () => {
    const id = useRef(generateId('label'));

    const labelledById = id.current;
    const labelProps = { id: id.current };
    const inputProps = { 'aria-labelledby': id.current };

    return {
        labelledById,
        labelProps,
        inputProps,
    };
};

// ==========================================
// SKIP LINK
// ==========================================

/**
 * Skip to main content link
 */
export const SkipLink = ({ href = '#main-content', children = 'Saltar al contenido principal' }) => (
    <a
        href={href}
        className="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 
               focus:z-50 focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white 
               focus:rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
    >
        {children}
    </a>
);

// ==========================================
// REDUCED MOTION
// ==========================================

/**
 * Hook for respecting user's motion preferences
 */
export const usePrefersReducedMotion = () => {
    const [prefersReducedMotion, setPrefersReducedMotion] = useState(false);

    useEffect(() => {
        const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        // eslint-disable-next-line react-hooks/set-state-in-effect
        setPrefersReducedMotion(mediaQuery.matches);

        const handler = (e) => setPrefersReducedMotion(e.matches);
        mediaQuery.addEventListener('change', handler);

        return () => mediaQuery.removeEventListener('change', handler);
    }, []);

    return prefersReducedMotion;
};

export default {
    trapFocus,
    useFocusManagement,
    useFocusTrap,
    useArrowKeyNavigation,
    useEscapeKey,
    useAnnouncement,
    ScreenReaderOnly,
    SkipLink,
    generateId,
    useAriaDescribedBy,
    useAriaLabelledBy,
    usePrefersReducedMotion,
};
