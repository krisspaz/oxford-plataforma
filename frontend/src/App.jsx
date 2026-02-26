import { useEffect, lazy } from 'react';

// lazy imports
// eslint-disable-next-line unused-imports/no-unused-vars
const AppRoutes = lazy(() => import('./routes/AppRoutes'));

const APP_VERSION = '2026-01-26-v2.3.0';

// Simple, Robust PageLoader
// eslint-disable-next-line unused-imports/no-unused-vars
const PageLoader = () => (
  <div className="flex items-center justify-center h-screen bg-gray-900 text-white">
    <div className="text-xl font-mono animate-pulse">Cargando Sistema...</div>
  </div>
);

function App() {

  useEffect(() => {
    // 1. Version Check & Cache Busting - BUT PRESERVE AUTH TOKEN!
    const currentVersion = localStorage.getItem('oxford_app_version');
    if (currentVersion !== APP_VERSION) {
      console.log(`New version detected: ${APP_VERSION}. Clearing cache (preserving auth)...`);

      // Save the token BEFORE clearing
      const savedToken = localStorage.getItem('token');
      const savedTheme = localStorage.getItem('darkMode');

      if ('caches' in window) {
        caches.keys().then((names) => {
          names.forEach((name) => {
            caches.delete(name);
          });
        });
      }

      // Only clear non-essential items, NOT the token
      localStorage.setItem('oxford_app_version', APP_VERSION);

      // Restore auth token if it existed
      if (savedToken && savedToken !== 'undefined' && savedToken !== 'null') {
        localStorage.setItem('token', savedToken);
      }
      if (savedTheme) {
        localStorage.setItem('darkMode', savedTheme);
      }
    }

    // 2. Safety Check for Corrupt Data - DON'T clear everything on error
    try {
      const theme = localStorage.getItem('darkMode');
      if (theme && theme !== 'true' && theme !== 'false') localStorage.removeItem('darkMode');

      const token = localStorage.getItem('token');
      if (token && (token === 'undefined' || token === 'null' || !token.includes('.'))) {
        localStorage.removeItem('token');
      }
    } catch (e) {
      console.error('Error checking localStorage:', e);
      // DON'T clear everything - just log the error
    }
  }, []);

  return (
    <CentralizedErrorBoundary>
      <Helmet>
        <title>Sistema Oxford</title>
        <meta http-equiv="X-Content-Type-Options" content="nosniff" />
        <meta http-equiv="X-XSS-Protection" content="1; mode=block" />
        <meta name="referrer" content="strict-origin-when-cross-origin" />
        <meta name="google" content="notranslate" />
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
    </CentralizedErrorBoundary>
  );
}

export default App;
