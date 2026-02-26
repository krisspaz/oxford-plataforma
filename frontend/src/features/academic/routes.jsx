import { lazy } from 'react';

// Lazy Imports
// eslint-disable-next-line unused-imports/no-unused-vars
const Academic = lazy(() => import('../../pages/Academic'));
// eslint-disable-next-line unused-imports/no-unused-vars
const GradosPage = lazy(() => import('../../pages/GradosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const MateriasPage = lazy(() => import('../../pages/MateriasPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const TeacherListPage = lazy(() => import('../teacher/pages/TeacherListPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const BimestresPage = lazy(() => import('../../pages/BimestresPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const ReportsPage = lazy(() => import('../../pages/ReportsPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const TareasPage = lazy(() => import('../../pages/TareasPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const CursosNivelesPage = lazy(() => import('../../pages/CursosNivelesPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const SeccionesPage = lazy(() => import('../../pages/SeccionesPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const AsignacionMateriasPage = lazy(() => import('../../pages/AsignacionMateriasPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const HorariosPage = lazy(() => import('../../pages/HorariosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const GestionCursosPage = lazy(() => import('../../pages/GestionCursosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const IAHorariosPage = lazy(() => import('../../pages/IAHorariosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
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

