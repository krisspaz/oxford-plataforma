import { lazy } from 'react';

// eslint-disable-next-line unused-imports/no-unused-vars
const StudentGradesPage = lazy(() => import('../features/student/pages/StudentGradesPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const HorariosPage = lazy(() => import('../pages/HorariosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const TareasPage = lazy(() => import('../pages/TareasPage'));

export const studentRouteElements = (
    <>
        <Route path="student/mis-notas" element={<StudentGradesPage />} />
        <Route path="student/horario" element={<HorariosPage />} />
        <Route path="student/tareas" element={<TareasPage />} />
    </>
);
