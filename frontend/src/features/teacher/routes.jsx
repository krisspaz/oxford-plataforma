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
const ChatDocentePage = lazy(() => import('../../pages/ChatDocentePage'));

export const teacherRouteElements = <>
    <Route path="docente/notas" element={<GradeEntryPage />} />
    <Route path="docente/horario" element={<MiHorarioPage />} />
    <Route path="docente/alumnos" element={<MisAlumnosPage />} />
    <Route path="docente/tareas" element={<GestionTareasPage viewMode="management" />} />
    <Route path="docente/tareas-calificadas" element={<GestionTareasPage viewMode="grading" />} />
    <Route path="docente/contenido" element={<ContenidoPage />} />
    <Route path="docente/notas-finales" element={<NotasFinalesPage />} />
    <Route path="docente/chat" element={<ChatDocentePage />} />
</>;


export const standaloneTeacherRoutes = <>
    <Route path="/profesor/contenido" element={<ProtectedRoute><ContenidoPage /></ProtectedRoute>} />
    <Route path="/profesor/tareas" element={<ProtectedRoute><GestionTareasPage /></ProtectedRoute>} />
</>;
