import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { AuthProvider } from './contexts/AuthContext';
import ProtectedRoute from './components/ProtectedRoute';
import Layout from './components/Layout';
import Dashboard from './pages/Dashboard';
import Students from './pages/Students';
import Academic from './pages/Academic';
import Financial from './pages/Financial';
import CalendarPage from './pages/CalendarPage';
import ExonerationPage from './pages/ExonerationPage';
import ContractsPage from './pages/ContractsPage';
import ReportsPage from './pages/ReportsPage';
import Login from './pages/Login';
import ErrorBoundary from './components/ErrorBoundary';

function App() {
  return (
    <ErrorBoundary>
      <BrowserRouter>
        <AuthProvider>
          <Routes>
            <Route path="/login" element={<Login />} />
            <Route path="/" element={
              <ProtectedRoute>
                <Layout />
              </ProtectedRoute>
            }>
              <Route index element={<Dashboard />} />
              <Route path="students" element={<Students />} />
              <Route path="academic" element={<Academic />} />
              <Route path="financial" element={<Financial />} />
              <Route path="financial" element={<Financial />} />
              <Route path="calendar" element={<CalendarPage />} />
              <Route path="exonerations" element={<ExonerationPage />} />
              <Route path="contracts" element={<ContractsPage />} />
              <Route path="reports" element={<ReportsPage />} />
              {/* Add more routes here as we build them */}
              <Route path="*" element={<div className="p-10">Página no encontrada</div>} />
            </Route>
          </Routes>
        </AuthProvider>
      </BrowserRouter>
    </ErrorBoundary>
  );
}

export default App;
