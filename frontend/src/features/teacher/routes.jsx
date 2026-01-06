import { lazy } from 'react';
import { Route } from 'react-router-dom';
import ProtectedRoute from '../../components/ProtectedRoute';

// Lazy Imports
const GradeEntryPage = lazy(() => import('../grades/pages/GradeEntryPage'));
const MiHorarioPage = lazy(() => import('../../pages/MiHorarioPage'));
const MisAlumnosPage = lazy(() => import('../../pages/MisAlumnosPage'));
const GestionTareasPage = lazy(() => import('../../pages/GestionTareasPage'));
const ContenidoPage = lazy(() => import('../../pages/ContenidoPage'));
const NotasFinalesPage = lazy(() => import('../../pages/NotasFinalesPage'));

export const teacherRouteElements = <>
    <Route path="docente/notas" element={<GradeEntryPage />} />
    <Route path="docente/horario" element={<MiHorarioPage />} />
    <Route path="docente/alumnos" element={<MisAlumnosPage />} />
    <Route path="docente/tareas" element={<GestionTareasPage />} />
    <Route path="docente/tareas-calificadas" element={<GestionTareasPage />} />
    <Route path="docente/contenido" element={<ContenidoPage />} />
    <Route path="docente/notas-finales" element={<NotasFinalesPage />} />
</>;

export const standaloneTeacherRoutes = <>
    <Route path="/profesor/contenido" element={<ProtectedRoute><ContenidoPage /></ProtectedRoute>} />
    <Route path="/profesor/tareas" element={<ProtectedRoute><GestionTareasPage /></ProtectedRoute>} />
</>;
