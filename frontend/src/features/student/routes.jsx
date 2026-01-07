import { lazy } from 'react';
import { Route } from 'react-router-dom';
import ProtectedRoute from '../../components/ProtectedRoute';

// Lazy Imports
const ReportsPage = lazy(() => import('../../pages/ReportsPage'));
const MiHorarioPage = lazy(() => import('../../pages/MiHorarioPage'));
const EstadoCuentaPage = lazy(() => import('../../pages/EstadoCuentaPage'));
const TareasPage = lazy(() => import('../../pages/TareasPage'));
const ContractsPage = lazy(() => import('../../pages/ContractsPage'));
const StudentGradesPage = lazy(() => import('./pages/StudentGradesPage'));

// New Student Portal Features
const StudentTasksPage = lazy(() => import('../../pages/StudentTasksPage'));
const TeacherRatingPage = lazy(() => import('../../pages/TeacherRatingPage'));
const SuggestionsPage = lazy(() => import('../../pages/SuggestionsPage'));
const ComplaintsPage = lazy(() => import('../../pages/ComplaintsPage'));
const StudentChatPage = lazy(() => import('../../pages/StudentChatPage'));

export const studentRouteElements = <>
    <Route path="alumno/notas" element={<StudentGradesPage />} />
    <Route path="alumno/horario" element={<MiHorarioPage />} />
    <Route path="alumno/estado-cuenta" element={<EstadoCuentaPage />} />
    <Route path="alumno/tareas" element={<TareasPage />} />

    {/* New Student Portal Features */}
    <Route path="alumno/mis-tareas" element={<StudentTasksPage />} />
    <Route path="alumno/evaluar-profesores" element={<TeacherRatingPage />} />
    <Route path="alumno/sugerencias" element={<SuggestionsPage />} />
    <Route path="alumno/quejas" element={<ComplaintsPage />} />
    <Route path="alumno/chat-profesor" element={<StudentChatPage />} />
</>;

export const standaloneStudentRoutes = <>
    <Route path="/portal/tareas" element={<ProtectedRoute><TareasPage /></ProtectedRoute>} />
    <Route path="/portal/notas" element={<ProtectedRoute><StudentGradesPage /></ProtectedRoute>} />
    <Route path="/portal/horario" element={<ProtectedRoute><MiHorarioPage /></ProtectedRoute>} />
    <Route path="/portal/contrato" element={<ProtectedRoute><ContractsPage /></ProtectedRoute>} />
</>;
