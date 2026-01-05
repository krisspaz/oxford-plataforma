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
const ComingSoonPage = lazy(() => import('./pages/ComingSoonPage'));

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
const CostosPage = lazy(() => import('./pages/CostosPage'));
const ExonerationPage = lazy(() => import('./pages/ExonerationPage'));
const SolicitudesPage = lazy(() => import('./pages/SolicitudesPage'));
const InsolventesPage = lazy(() => import('./pages/InsolventesPage'));
const ComprobantesEmitidosPage = lazy(() => import('./pages/ComprobantesEmitidosPage'));
const AsignarPaquetesPage = lazy(() => import('./pages/AsignarPaquetesPage'));
const ConveniosPage = lazy(() => import('./pages/ConveniosPage'));
const AsignarConvenioPage = lazy(() => import('./pages/AsignarConvenioPage'));
const PaquetesSeleccionadosPage = lazy(() => import('./pages/PaquetesSeleccionadosPage'));

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
const CursosNivelesPage = lazy(() => import('./pages/CursosNivelesPage'));
const SeccionesPage = lazy(() => import('./pages/SeccionesPage'));
const GestionCursosPage = lazy(() => import('./pages/GestionCursosPage'));
const IAHorariosPage = lazy(() => import('./pages/IAHorariosPage'));
const AsignacionMateriasPage = lazy(() => import('./pages/AsignacionMateriasPage'));

// Administración
const GestionUsuariosPage = lazy(() => import('./pages/GestionUsuariosPage'));
const NotasFinalesPage = lazy(() => import('./pages/NotasFinalesPage'));
const CatalogosPage = lazy(() => import('./pages/CatalogosPage'));
const SettingsPage = lazy(() => import('./pages/SettingsPage'));
const PrivilegiosPage = lazy(() => import('./pages/PrivilegiosPage'));
const GestionMenusPage = lazy(() => import('./pages/GestionMenusPage'));
const CierreEscolarPage = lazy(() => import('./pages/CierreEscolarPage'));

// Otros
const CalendarPage = lazy(() => import('./pages/CalendarPage'));
const ReportsPage = lazy(() => import('./pages/ReportsPage'));
const TareasPage = lazy(() => import('./pages/TareasPage'));
const MisHijosPage = lazy(() => import('./pages/MisHijosPage'));
const MonitoreoPage = lazy(() => import('./pages/MonitoreoPage'));
const LogsPage = lazy(() => import('./pages/LogsPage'));

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
                <Route path="/mis-hijos" element={
                  <ProtectedRoute>
                    <MisHijosPage />
                  </ProtectedRoute>
                } />
                <Route path="/monitoreo" element={
                  <ProtectedRoute>
                    <MonitoreoPage />
                  </ProtectedRoute>
                } />

                {/* Financial Routes */}
                <Route path="/financiero/comprobantes" element={<ProtectedRoute><ComprobantesEmitidosPage /></ProtectedRoute>} />
                <Route path="/financiero/paquetes" element={<ProtectedRoute><AsignarPaquetesPage /></ProtectedRoute>} />
                <Route path="/financiero/convenios" element={<ProtectedRoute><ConveniosPage /></ProtectedRoute>} />
                <Route path="/financiero/asignar-convenio" element={<ProtectedRoute><AsignarConvenioPage /></ProtectedRoute>} />
                <Route path="/financiero/paquetes-seleccionados" element={<ProtectedRoute><PaquetesSeleccionadosPage /></ProtectedRoute>} />
                <Route path="/financiero/costos" element={<ProtectedRoute><CostosPage /></ProtectedRoute>} />

                {/* Academic Routes (Director/Coordination) */}
                <Route path="/academico/cursos" element={<ProtectedRoute><CursosNivelesPage /></ProtectedRoute>} />
                <Route path="/academico/secciones" element={<ProtectedRoute><SeccionesPage /></ProtectedRoute>} />
                <Route path="/academico/gestion-cursos" element={<ProtectedRoute><GestionCursosPage /></ProtectedRoute>} />
                <Route path="/academico/horarios-ia" element={<ProtectedRoute><IAHorariosPage /></ProtectedRoute>} />
                <Route path="/academico/asignacion-materias" element={<ProtectedRoute><AsignacionMateriasPage /></ProtectedRoute>} />

                {/* Teacher Routes */}
                <Route path="/profesor/contenido" element={<ProtectedRoute><ComingSoonPage title="Asignar Contenido" /></ProtectedRoute>} />
                <Route path="/profesor/tareas" element={<ProtectedRoute><GestionTareasPage /></ProtectedRoute>} />

                {/* Student/Parent Portal Routes */}
                <Route path="/portal/tareas" element={<ProtectedRoute><TareasPage /></ProtectedRoute>} />
                <Route path="/portal/notas" element={<ProtectedRoute><ComingSoonPage title="Mis Notas" /></ProtectedRoute>} />
                <Route path="/portal/horario" element={<ProtectedRoute><MiHorarioPage /></ProtectedRoute>} />
                <Route path="/portal/contrato" element={<ProtectedRoute><ContractsPage /></ProtectedRoute>} />

                <Route path="/" element={
                  <ProtectedRoute>
                    <Layout />
                  </ProtectedRoute>
                }>
                  {/* Dashboard */}
                  <Route index element={<Dashboard />} />

                  {/* Secretaría */}
                  <Route path="secretaria/inscripciones" element={<InscripcionesPage />} />
                  <Route path="secretaria/matriculacion" element={<MatriculacionPage />} />
                  <Route path="secretaria/matricular" element={<MatriculacionPage />} />
                  <Route path="secretaria/students" element={<Students />} />
                  <Route path="secretaria/familias" element={<FamiliasPage />} />
                  <Route path="secretaria/contratos" element={<ContractsPage />} />
                  <Route path="secretaria/documentos" element={<InscripcionesPage />} />
                  <Route path="secretaria/asignar-paquetes" element={<AsignarPaquetesPage />} />
                  <Route path="secretaria/convenios" element={<ConveniosPage />} />
                  <Route path="secretaria/asignar-convenio" element={<AsignarConvenioPage />} />

                  {/* Finanzas */}
                  <Route path="finanzas" element={<Financial />} />
                  <Route path="finanzas/pagos" element={<RegistroPagosPage />} />
                  <Route path="finanzas/estado-cuenta" element={<EstadoCuentaPage />} />
                  <Route path="finanzas/corte-dia" element={<CorteDiaPage />} />
                  <Route path="finanzas/comprobantes" element={<ComprobantesPage />} />
                  <Route path="finanzas/comprobantes-pendientes" element={<ComprobantesPage />} />
                  <Route path="finanzas/comprobantes-emitidos" element={<ComprobantesEmitidosPage />} />
                  <Route path="finanzas/paquetes" element={<PaquetesPage />} />
                  <Route path="finanzas/costos" element={<CostosPage />} />
                  <Route path="finanzas/exonerados" element={<ExonerationPage />} />
                  <Route path="finanzas/exoneraciones" element={<ExonerationPage />} />
                  <Route path="finanzas/solicitudes" element={<SolicitudesPage />} />
                  <Route path="finanzas/insolventes" element={<InsolventesPage />} />
                  <Route path="finanzas/paquetes-seleccionados" element={<PaquetesSeleccionadosPage />} />

                  {/* Académico */}
                  <Route path="academico" element={<Academic />} />
                  <Route path="academico/grados" element={<GradosPage />} />
                  <Route path="academico/materias" element={<MateriasPage />} />
                  <Route path="academico/docentes" element={<DocentesPage />} />
                  <Route path="academico/bimestres" element={<BimestresPage />} />
                  <Route path="academico/cierre-notas" element={<BimestresPage />} />
                  <Route path="academico/boletas" element={<ReportsPage />} />
                  <Route path="academico/cuadros" element={<ReportsPage />} />
                  <Route path="academico/cronograma" element={<TareasPage />} />
                  <Route path="academico/cursos" element={<CursosNivelesPage />} />
                  <Route path="academico/secciones" element={<SeccionesPage />} />
                  <Route path="academico/asignacion-materias" element={<AsignacionMateriasPage />} />
                  <Route path="academico/horarios" element={<HorariosPage />} />
                  <Route path="academico/gestion-cursos" element={<GestionCursosPage />} />

                  {/* Docente */}
                  <Route path="docente/notas" element={<CargaNotasPage />} />
                  <Route path="docente/horario" element={<MiHorarioPage />} />
                  <Route path="docente/alumnos" element={<MisAlumnosPage />} />
                  <Route path="docente/tareas" element={<GestionTareasPage />} />
                  <Route path="docente/tareas-calificadas" element={<GestionTareasPage />} />
                  <Route path="docente/contenido" element={<ComingSoonPage title="Asignar Contenido" />} />
                  <Route path="docente/notas-finales" element={<NotasFinalesPage />} />

                  {/* Alumno */}
                  <Route path="alumno/notas" element={<ReportsPage />} />
                  <Route path="alumno/horario" element={<MiHorarioPage />} />
                  <Route path="alumno/estado-cuenta" element={<EstadoCuentaPage />} />
                  <Route path="alumno/tareas" element={<TareasPage />} />

                  {/* Padres */}
                  <Route path="padres/hijos" element={<MisHijosPage />} />
                  <Route path="padres/contrato" element={<ContractsPage />} />

                  {/* General / Shared */}
                  <Route path="carga-notas" element={<CargaNotasPage />} />
                  <Route path="horarios" element={<HorariosPage />} />
                  <Route path="mi-horario" element={<MiHorarioPage />} />
                  <Route path="mis-alumnos" element={<MisAlumnosPage />} />
                  <Route path="gestion-tareas" element={<GestionTareasPage />} />

                  {/* Administración */}
                  <Route path="admin/usuarios" element={<GestionUsuariosPage />} />
                  <Route path="admin/catalogos" element={<CatalogosPage />} />
                  <Route path="admin/ajustes" element={<SettingsPage />} />
                  <Route path="admin/ajustes-generales" element={<SettingsPage />} />
                  <Route path="admin/cierre-escolar" element={<CierreEscolarPage />} />
                  <Route path="admin/cargos" element={<ComingSoonPage title="Gestión de Cargos" />} />
                  <Route path="admin/notificaciones-reset" element={<ComingSoonPage title="Reset de Notificaciones" />} />
                  <Route path="admin/privilegios" element={<PrivilegiosPage />} />
                  <Route path="admin/menus" element={<GestionMenusPage />} />
                  <Route path="admin/estadisticas" element={<MonitoreoPage />} />
                  <Route path="admin/logs" element={<LogsPage />} />
                  <Route path="settings" element={<SettingsPage />} />

                  {/* Otros */}
                  <Route path="calendar" element={<CalendarPage />} />
                  <Route path="reports" element={<ReportsPage />} />
                  <Route path="mi-portal/tareas" element={<TareasPage />} />

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
