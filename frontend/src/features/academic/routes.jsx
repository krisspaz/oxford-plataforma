import { lazy } from 'react';
import { Route } from 'react-router-dom';

// Lazy Imports
const Academic = lazy(() => import('../../pages/Academic'));
const GradosPage = lazy(() => import('../../pages/GradosPage'));
const MateriasPage = lazy(() => import('../../pages/MateriasPage'));
const TeacherListPage = lazy(() => import('../teacher/pages/TeacherListPage'));
const BimestresPage = lazy(() => import('../../pages/BimestresPage'));
const ReportsPage = lazy(() => import('../../pages/ReportsPage'));
const TareasPage = lazy(() => import('../../pages/TareasPage'));
const CursosNivelesPage = lazy(() => import('../../pages/CursosNivelesPage'));
const SeccionesPage = lazy(() => import('../../pages/SeccionesPage'));
const AsignacionMateriasPage = lazy(() => import('../../pages/AsignacionMateriasPage'));
const HorariosPage = lazy(() => import('../../pages/HorariosPage'));
const GestionCursosPage = lazy(() => import('../../pages/GestionCursosPage'));
const IAHorariosPage = lazy(() => import('../../pages/IAHorariosPage'));
const DailyOperationsDashboard = lazy(() => import('./operations/pages/DailyOperationsDashboard'));

export const academicRouteElements = <>
    <Route path="academico" element={<Academic />} />
    <Route path="academico/grados" element={<GradosPage />} />
    <Route path="academico/materias" element={<MateriasPage />} />
    <Route path="academico/docentes" element={<TeacherListPage />} />
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
    <Route path="academico/operaciones" element={<DailyOperationsDashboard />} />
    <Route path="academico/oxford-ai" element={<IAHorariosPage />} />
</>;

// No standalone routes needed - all academic pages render inside Layout
export const standaloneAcademicRoutes = null;

