import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import App from './App.jsx'
import ErrorBoundary from './components/ErrorBoundary';

console.log("React Entry Point: Mounting...");

// Fallback removal if stuck
try {
  const version = localStorage.getItem('oxford_app_version');
  if (version !== '2025-01-05-v1.1.0') {
    console.log("Version mismatch in main.jsx, clearing cache");
    if ('caches' in window) caches.keys().then(names => names.forEach(n => caches.delete(n)));
  }
} catch (e) {
  console.error("Cache clear error", e);
}

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <ErrorBoundary>
      <App />
    </ErrorBoundary>
  </StrictMode>,
)
