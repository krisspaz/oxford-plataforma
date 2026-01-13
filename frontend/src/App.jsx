import { BrowserRouter } from 'react-router-dom';
import { Suspense, useEffect } from 'react';
import { AuthProvider } from './contexts/AuthContext';
import { ThemeProvider } from './contexts/ThemeContext';
import { AIProvider } from './context/AIContext';
import ErrorBoundary from './components/ErrorBoundary';
import AppRoutes from './routes/AppRoutes';
import VoiceChat from './components/VoiceChat';
import NotificationListener from './components/NotificationListener';

// Client-side version for auto-cache busting
const APP_VERSION = '2025-01-05-v1.1.0';

// Loading component for Suspense fallback
const PageLoader = () => (
  <div className="flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-[#0f111a] dark:to-[#0a0c10]">
    <div className="relative">
      <div className="w-16 h-16 border-4 border-indigo-200/30 border-t-indigo-500 rounded-full animate-spin"></div>
      <div className="absolute inset-0 flex items-center justify-center">
        <div className="w-8 h-8 bg-indigo-500/20 rounded-full animate-pulse"></div>
      </div>
    </div>
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

      // Nuke everything to be safe if version mismatches
      localStorage.clear();
      localStorage.setItem('oxford_app_version', APP_VERSION);
      window.location.reload(true);
      return;
    }

    // 2. Safety Check for Corrupt Data
    try {
      const theme = localStorage.getItem('darkMode');
      if (theme && theme !== 'true' && theme !== 'false') localStorage.removeItem('darkMode');

      const token = localStorage.getItem('token');
      if (token && (token === 'undefined' || token === 'null' || !token.includes('.'))) {
        console.warn("Corrupt token detected, clearing...");
        localStorage.removeItem('token');
      }
    } catch (e) {
      localStorage.clear();
    }
  }, []);

  return (
    <ErrorBoundary>
      <AuthProvider>
        <ThemeProvider>
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
