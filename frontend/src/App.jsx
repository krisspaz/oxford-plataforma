import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { Suspense, lazy } from 'react';
import { AuthProvider } from './contexts/AuthContext';
import { ThemeProvider } from './contexts/ThemeContext';
import ProtectedRoute from './components/ProtectedRoute';
import Layout from './components/Layout';
import ErrorBoundary from './components/ErrorBoundary';

// Loading component for Suspense fallback
const PageLoader = () => (
  <div className="flex items-center justify-center min-h-screen bg-gray-50">
    <div className="text-center">
      <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-600 mx-auto mb-4"></div>
      <p className="text-gray-600">Cargando...</p>
    </div>
  </div>
);

// ========================================
// LAZY LOADED PAGES
// ========================================

// Core pages
const Dashboard = lazy(() => import('./pages/Dashboard'));
const Login = lazy(() => import('./pages/Login'));

// Secretaría
const InscripcionesPage = lazy(() => import('./pages/InscripcionesPage'));
const MatriculacionPage = lazy(() => import('./pages/MatriculacionPage'));
const Students = lazy(() => import('./pages/Students'));
const FamiliasPage = lazy(() => import('./pages/FamiliasPage'));
const ContractsPage = lazy(() => import('./pages/ContractsPage'));

// Finanzas
const Financial = lazy(() => import('./pages/Financial'));
const RegistroPagosPage = lazy(() => import('./pages/RegistroPagosPage'));
const EstadoCuentaPage = lazy(() => import('./pages/EstadoCuentaPage'));
const CorteDiaPage = lazy(() => import('./pages/CorteDiaPage'));
const ComprobantesPage = lazy(() => import('./pages/ComprobantesPage'));
const PaquetesPage = lazy(() => import('./pages/PaquetesPage'));
const ExonerationPage = lazy(() => import('./pages/ExonerationPage'));
const SolicitudesPage = lazy(() => import('./pages/SolicitudesPage'));

// Académico
const Academic = lazy(() => import('./pages/Academic'));
const GradosPage = lazy(() => import('./pages/GradosPage'));
const MateriasPage = lazy(() => import('./pages/MateriasPage'));
const DocentesPage = lazy(() => import('./pages/DocentesPage'));
const BimestresPage = lazy(() => import('./pages/BimestresPage'));
const CargaNotasPage = lazy(() => import('./pages/CargaNotasPage'));
const HorariosPage = lazy(() => import('./pages/HorariosPage'));
const MiHorarioPage = lazy(() => import('./pages/MiHorarioPage'));
const MisAlumnosPage = lazy(() => import('./pages/MisAlumnosPage'));
const GestionTareasPage = lazy(() => import('./pages/GestionTareasPage'));

// Administración
const GestionUsuariosPage = lazy(() => import('./pages/GestionUsuariosPage'));
const CatalogosPage = lazy(() => import('./pages/CatalogosPage'));
const SettingsPage = lazy(() => import('./pages/SettingsPage'));

// Otros
const CalendarPage = lazy(() => import('./pages/CalendarPage'));
const ReportsPage = lazy(() => import('./pages/ReportsPage'));

// 404
const NotFound = () => (
  <div className="p-10 text-center">
    <h1 className="text-4xl font-bold text-gray-800 mb-4">404</h1>
    <p className="text-gray-600">Página no encontrada</p>
  </div>
);

function App() {
  return (
    <ErrorBoundary>
      <ThemeProvider>
        <BrowserRouter>
          <AuthProvider>
            <Suspense fallback={<PageLoader />}>
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
                  <Route path="gestion-tareas" element={<GestionTareasPage />} />

                  {/* Administración */}
                  <Route path="usuarios" element={<GestionUsuariosPage />} />
                  <Route path="catalogos" element={<CatalogosPage />} />
                  <Route path="settings" element={<SettingsPage />} />

                  {/* Otros */}
                  <Route path="calendar" element={<CalendarPage />} />
                  <Route path="reports" element={<ReportsPage />} />

                  <Route path="*" element={<NotFound />} />
                </Route>
              </Routes>
            </Suspense>
          </AuthProvider>
        </BrowserRouter>
      </ThemeProvider>
    </ErrorBoundary>
  );
}

export default App;
