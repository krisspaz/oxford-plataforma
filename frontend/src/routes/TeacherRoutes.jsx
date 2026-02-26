import { lazy } from 'react';

// eslint-disable-next-line unused-imports/no-unused-vars
const MisAlumnosPage = lazy(() => import('../pages/MisAlumnosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const CargaNotasPage = lazy(() => import('../pages/CargaNotasPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const AsistenciaPage = lazy(() => import('../pages/CorteDiaPage')); // Assuming usage
// eslint-disable-next-line unused-imports/no-unused-vars
const PlanificacionPage = lazy(() => import('../pages/TareasPage')); // Assuming usage

export const teacherRouteElements = (
    <>
        <Route path="teacher/mis-alumnos" element={<MisAlumnosPage />} />
        <Route path="teacher/carga-notas" element={<CargaNotasPage />} />
        {/* Add more teacher routes here */}
    </>
);
