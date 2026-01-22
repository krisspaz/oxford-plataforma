import { BrowserRouter } from 'react-router-dom';
import { Suspense, useEffect, lazy } from 'react';
import { AuthProvider } from './contexts/AuthContext';
import { ThemeProvider } from './contexts/ThemeContext';
import { Toaster } from 'sonner';
import { AIProvider } from './contexts/AIContext';
import ErrorBoundary from './components/ErrorBoundary';
import { Helmet } from 'react-helmet-async';
import VoiceChat from './components/VoiceChat';
import NotificationListener from './components/NotificationListener';

// lazy imports
const AppRoutes = lazy(() => import('./routes/AppRoutes'));

const APP_VERSION = '2025-01-05-v1.1.3-FIX';

// Simple, Robust PageLoader
const PageLoader = () => (
  <div className="flex items-center justify-center h-screen bg-gray-900 text-white">
    <div className="text-xl font-mono animate-pulse">Cargando Sistema...</div>
  </div>
);

function App() {

  useEffect(() => {
    // 1. Version Check & Cache Busting
    const currentVersion = localStorage.getItem('oxford_app_version');
    if (currentVersion !== APP_VERSION) {
      console.log(`New version detected: ${APP_VERSION}. Clearing cache...`);
      if ('caches' in window) {
        caches.keys().then((names) => {
          names.forEach((name) => {
            caches.delete(name);
          });
        });
      }
      localStorage.clear();
      localStorage.setItem('oxford_app_version', APP_VERSION);
    }

    // 2. Safety Check for Corrupt Data
    try {
      const theme = localStorage.getItem('darkMode');
      if (theme && theme !== 'true' && theme !== 'false') localStorage.removeItem('darkMode');

      const token = localStorage.getItem('token');
      if (token && (token === 'undefined' || token === 'null' || !token.includes('.'))) {
        localStorage.removeItem('token');
      }
    } catch (e) {
      localStorage.clear();
    }
  }, []);

  return (
    <ErrorBoundary>
      <Helmet>
        <title>Sistema Oxford</title>
        <meta http-equiv="X-Content-Type-Options" content="nosniff" />
        <meta http-equiv="X-XSS-Protection" content="1; mode=block" />
        <meta name="referrer" content="strict-origin-when-cross-origin" />
      </Helmet>
      <AuthProvider>
        <ThemeProvider>
          <Toaster position="top-right" richColors />
          <BrowserRouter>
            <AIProvider>
              <Suspense fallback={<PageLoader />}>
                <AppRoutes />
                <VoiceChat />
                <NotificationListener />
              </Suspense>
            </AIProvider>
          </BrowserRouter>
        </ThemeProvider>
      </AuthProvider>
    </ErrorBoundary>
  );
}

export default App;
