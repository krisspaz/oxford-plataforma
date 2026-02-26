import { createRoot } from 'react-dom/client'
import { QueryClient } from '@tanstack/react-query'
import * as Sentry from "@sentry/react";
import './index.css'

console.log("React Entry Point: Mounting...");

// Initialize Sentry
Sentry.init({
  dsn: import.meta.env.VITE_SENTRY_DSN, // Will be undefined in dev if not set, that's fine
  integrations: [
    Sentry.browserTracingIntegration(),
    Sentry.replayIntegration(),
  ],
  // Performance Monitoring
  tracesSampleRate: 1.0, // Capture 100% of the transactions
  // Session Replay
  replaysSessionSampleRate: 0.1, // This sets the sample rate at 10%. You may want to change it to 100% while in development and then sample at a lower rate in production.
  replaysOnErrorSampleRate: 1.0, // If you're not already sampling the entire session, change the sample rate to 100% when sampling sessions where errors occur.
  environment: import.meta.env.MODE,
});

// Configure React Query client with sensible defaults
const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 5 * 60 * 1000,      // 5 minutes - data considered fresh
      gcTime: 30 * 60 * 1000,        // 30 minutes (formerly cacheTime)
      retry: 2,
      retryDelay: (attemptIndex) => Math.min(1000 * 2 ** attemptIndex, 30000),
      refetchOnWindowFocus: false,
      refetchOnReconnect: true,
    },
    mutations: {
      retry: 1,
    },
  },
});

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <HelmetProvider>
      <QueryClientProvider client={queryClient}>
        <ErrorBoundary>
          <App />
        </ErrorBoundary>
      </QueryClientProvider>
    </HelmetProvider>
  </StrictMode>,
)

// Service Worker Registration
if ('serviceWorker' in navigator && import.meta.env.PROD) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then((registration) => {
        console.log('SW registered:', registration);
      })
      .catch((error) => {
        console.log('SW registration failed:', error);
      });
  });
}
