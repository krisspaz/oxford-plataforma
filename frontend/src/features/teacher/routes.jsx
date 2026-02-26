import { lazy } from 'react';

// Lazy Imports
// eslint-disable-next-line unused-imports/no-unused-vars
const GradeEntryPage = lazy(() => import('../grades/pages/GradeEntryPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const MiHorarioPage = lazy(() => import('../../pages/MiHorarioPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const MisAlumnosPage = lazy(() => import('../../pages/MisAlumnosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const GestionTareasPage = lazy(() => import('../../pages/GestionTareasPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const ContenidoPage = lazy(() => import('../../pages/ContenidoPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const NotasFinalesPage = lazy(() => import('../../pages/NotasFinalesPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
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
