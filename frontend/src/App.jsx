import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { AuthProvider } from './contexts/AuthContext';
import ProtectedRoute from './components/ProtectedRoute';
import Layout from './components/Layout';
import Dashboard from './pages/Dashboard';
import Students from './pages/Students';
import Academic from './pages/Academic';
import Financial from './pages/Financial';
import Login from './pages/Login';

function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
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
            {/* Add more routes here as we build them */}
            <Route path="*" element={<div className="p-10">Página no encontrada</div>} />
          </Route>
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  );
}

export default App;
