import { lazy } from 'react';

// Lazy Imports
// eslint-disable-next-line unused-imports/no-unused-vars
const ReportsPage = lazy(() => import('../../pages/ReportsPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const MiHorarioPage = lazy(() => import('../../pages/MiHorarioPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const EstadoCuentaPage = lazy(() => import('../../pages/EstadoCuentaPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const TareasPage = lazy(() => import('../../pages/TareasPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const ContractsPage = lazy(() => import('../../pages/ContractsPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const StudentGradesPage = lazy(() => import('./pages/StudentGradesPage'));

// New Student Portal Features
// eslint-disable-next-line unused-imports/no-unused-vars
const StudentTasksPage = lazy(() => import('../../pages/StudentTasksPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const TeacherRatingPage = lazy(() => import('../../pages/TeacherRatingPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const SuggestionsPage = lazy(() => import('../../pages/SuggestionsPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const ComplaintsPage = lazy(() => import('../../pages/ComplaintsPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
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
