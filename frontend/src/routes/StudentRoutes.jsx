import { lazy } from 'react';
import { Route } from 'react-router-dom';

const StudentGradesPage = lazy(() => import('../features/student/pages/StudentGradesPage'));
const HorariosPage = lazy(() => import('../pages/HorariosPage'));
const TareasPage = lazy(() => import('../pages/TareasPage'));

export const studentRouteElements = (
    <>
        <Route path="student/mis-notas" element={<StudentGradesPage />} />
        <Route path="student/horario" element={<HorariosPage />} />
        <Route path="student/tareas" element={<TareasPage />} />
    </>
);
