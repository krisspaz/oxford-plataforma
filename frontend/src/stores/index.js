/**
 * Global State Management - Zustand Store
 * =========================================
 * Centralized state management for shared application state
 */

import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';

/**
 * User and Authentication Store
 */
export const useAuthStore = create(
    persist(
        (set, get) => ({
            // State
            user: null,
            isAuthenticated: false,
            loading: true,
            error: null,

            // Actions
            setUser: (user) =>
                set({
                    user,
                    isAuthenticated: !!user,
                    loading: false,
                    error: null,
                }),

            clearUser: () =>
                set({
                    user: null,
                    isAuthenticated: false,
                    loading: false,
                    error: null,
                }),

            setLoading: (loading) => set({ loading }),

            setError: (error) => set({ error, loading: false }),

            // Computed
            getFullName: () => {
                const user = get().user;
                if (!user) return '';
                return `${user.firstName || ''} ${user.lastName || ''}`.trim();
            },

            hasRole: (role) => {
                const user = get().user;
                if (!user) return false;
                return user.role === role || user.roles?.includes(role);
            },
        }),
        {
            name: 'oxford-auth',
            storage: createJSONStorage(() => sessionStorage), // Use sessionStorage, not localStorage
            partialize: (state) => ({
                user: state.user,
                isAuthenticated: state.isAuthenticated,
            }),
        }
    )
);

/**
 * UI State Store
 */
export const useUIStore = create((set) => ({
    // Sidebar
    sidebarOpen: true,
    sidebarCollapsed: false,

    // Modals
    activeModal: null,
    modalData: null,

    // Notifications
    notifications: [],

    // Theme
    theme: 'light',

    // Actions
    toggleSidebar: () =>
        set((state) => ({ sidebarOpen: !state.sidebarOpen })),

    collapseSidebar: () =>
        set((state) => ({ sidebarCollapsed: !state.sidebarCollapsed })),

    openModal: (modalId, data = null) =>
        set({ activeModal: modalId, modalData: data }),

    closeModal: () =>
        set({ activeModal: null, modalData: null }),

    addNotification: (notification) =>
        set((state) => ({
            notifications: [
                ...state.notifications,
                {
                    id: Date.now(),
                    timestamp: new Date(),
                    ...notification,
                },
            ],
        })),

    removeNotification: (id) =>
        set((state) => ({
            notifications: state.notifications.filter((n) => n.id !== id),
        })),

    clearNotifications: () => set({ notifications: [] }),

    setTheme: (theme) => set({ theme }),

    toggleTheme: () =>
        set((state) => ({
            theme: state.theme === 'light' ? 'dark' : 'light',
        })),
}));

/**
 * Schedule Configuration Store
 */
export const useScheduleStore = create((set, get) => ({
    // Schedule config
    config: {
        startTime: '07:30',
        endTime: '14:00',
        classDuration: 45,
        includeRecess: true,
        recessAfterPeriod: 4,
        recessDuration: 30,
    },

    // Current schedule
    schedule: null,
    conflicts: [],

    // Filters
    selectedGrade: null,
    selectedSection: null,
    selectedTeacher: null,

    // Actions
    setConfig: (config) =>
        set((state) => ({
            config: { ...state.config, ...config },
        })),

    resetConfig: () =>
        set({
            config: {
                startTime: '07:30',
                endTime: '14:00',
                classDuration: 45,
                includeRecess: true,
                recessAfterPeriod: 4,
                recessDuration: 30,
            },
        }),

    setSchedule: (schedule, conflicts = []) =>
        set({ schedule, conflicts }),

    clearSchedule: () =>
        set({ schedule: null, conflicts: [] }),

    setSelectedGrade: (grade) => set({ selectedGrade: grade }),

    setSelectedSection: (section) => set({ selectedSection: section }),

    setSelectedTeacher: (teacher) => set({ selectedTeacher: teacher }),

    // Computed
    hasConflicts: () => get().conflicts.length > 0,

    getErrorConflicts: () =>
        get().conflicts.filter((c) => c.severity === 'error'),

    getWarningConflicts: () =>
        get().conflicts.filter((c) => c.severity === 'warning'),
}));

/**
 * Loading/Processing State Store
 */
export const useLoadingStore = create((set) => ({
    // Global loading states
    globalLoading: false,
    loadingMessage: '',

    // Page-specific loading
    pageLoading: {},

    // Actions
    setGlobalLoading: (loading, message = '') =>
        set({ globalLoading: loading, loadingMessage: message }),

    setPageLoading: (page, loading) =>
        set((state) => ({
            pageLoading: {
                ...state.pageLoading,
                [page]: loading,
            },
        })),

    clearAllLoading: () =>
        set({ globalLoading: false, loadingMessage: '', pageLoading: {} }),
}));
