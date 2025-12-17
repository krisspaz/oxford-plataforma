import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { AuthProvider } from './contexts/AuthContext';
import { ThemeProvider } from './contexts/ThemeContext';
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
import SettingsPage from './pages/SettingsPage';
import Login from './pages/Login';
import ErrorBoundary from './components/ErrorBoundary';

// Nuevas páginas del sistema
import InscripcionesPage from './pages/InscripcionesPage';
import CorteDiaPage from './pages/CorteDiaPage';
import CargaNotasPage from './pages/CargaNotasPage';
import ComprobantesPage from './pages/ComprobantesPage';
import EstadoCuentaPage from './pages/EstadoCuentaPage';
import GestionUsuariosPage from './pages/GestionUsuariosPage';
import PaquetesPage from './pages/PaquetesPage';
import MateriasPage from './pages/MateriasPage';
import BimestresPage from './pages/BimestresPage';
import SolicitudesPage from './pages/SolicitudesPage';
import RegistroPagosPage from './pages/RegistroPagosPage';
import FamiliasPage from './pages/FamiliasPage';
import GradosPage from './pages/GradosPage';
import DocentesPage from './pages/DocentesPage';
import CatalogosPage from './pages/CatalogosPage';
import MatriculacionPage from './pages/MatriculacionPage';
import HorariosPage from './pages/HorariosPage';
import MiHorarioPage from './pages/MiHorarioPage';
import MisAlumnosPage from './pages/MisAlumnosPage';

function App() {
  return (
    <ErrorBoundary>
      <ThemeProvider>
        <BrowserRouter>
          <AuthProvider>
            <Routes>
              <Route path="/login" element={<Login />} />
              <Route path="/" element={
                <ProtectedRoute>
                  <Layout />
                </ProtectedRoute>
              }>
                {/* Dashboard */}
                <Route index element={<Dashboard />} />

                {/* Secretaría */}
                <Route path="inscripciones" element={<InscripcionesPage />} />
                <Route path="matriculacion" element={<MatriculacionPage />} />
                <Route path="students" element={<Students />} />
                <Route path="familias" element={<FamiliasPage />} />
                <Route path="contracts" element={<ContractsPage />} />

                {/* Finanzas */}
                <Route path="financial" element={<Financial />} />
                <Route path="pagos" element={<RegistroPagosPage />} />
                <Route path="estado-cuenta" element={<EstadoCuentaPage />} />
                <Route path="corte-dia" element={<CorteDiaPage />} />
                <Route path="comprobantes" element={<ComprobantesPage />} />
                <Route path="paquetes" element={<PaquetesPage />} />
                <Route path="exonerations" element={<ExonerationPage />} />
                <Route path="solicitudes" element={<SolicitudesPage />} />

                {/* Académico */}
                <Route path="academic" element={<Academic />} />
                <Route path="grados" element={<GradosPage />} />
                <Route path="materias" element={<MateriasPage />} />
                <Route path="docentes" element={<DocentesPage />} />
                <Route path="bimestres" element={<BimestresPage />} />
                <Route path="carga-notas" element={<CargaNotasPage />} />
                <Route path="horarios" element={<HorariosPage />} />
                <Route path="mi-horario" element={<MiHorarioPage />} />
                <Route path="mis-alumnos" element={<MisAlumnosPage />} />

                {/* Administración */}
                <Route path="usuarios" element={<GestionUsuariosPage />} />
                <Route path="catalogos" element={<CatalogosPage />} />
                <Route path="settings" element={<SettingsPage />} />

                {/* Otros */}
                <Route path="calendar" element={<CalendarPage />} />
                <Route path="reports" element={<ReportsPage />} />

                <Route path="*" element={<div className="p-10">Página no encontrada</div>} />
              </Route>
            </Routes>
          </AuthProvider>
        </BrowserRouter>
      </ThemeProvider>
    </ErrorBoundary>
  );
}

export default App;
