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

export const studentRouteElements = <>
    <Route path="alumno/notas" element={<StudentGradesPage />} />
    <Route path="alumno/horario" element={<MiHorarioPage />} />
    <Route path="alumno/estado-cuenta" element={<EstadoCuentaPage />} />
    <Route path="alumno/tareas" element={<TareasPage />} />
</>;

export const standaloneStudentRoutes = <>
    <Route path="/portal/tareas" element={<ProtectedRoute><TareasPage /></ProtectedRoute>} />
    <Route path="/portal/notas" element={<ProtectedRoute><StudentGradesPage /></ProtectedRoute>} />
    <Route path="/portal/horario" element={<ProtectedRoute><MiHorarioPage /></ProtectedRoute>} />
    <Route path="/portal/contrato" element={<ProtectedRoute><ContractsPage /></ProtectedRoute>} />
</>;
