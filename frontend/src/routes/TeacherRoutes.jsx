import { lazy } from 'react';
import { Route } from 'react-router-dom';

const MisAlumnosPage = lazy(() => import('../pages/MisAlumnosPage'));
const CargaNotasPage = lazy(() => import('../pages/CargaNotasPage'));
const AsistenciaPage = lazy(() => import('../pages/CorteDiaPage')); // Assuming usage
const PlanificacionPage = lazy(() => import('../pages/TareasPage')); // Assuming usage

export const teacherRouteElements = (
    <>
        <Route path="teacher/mis-alumnos" element={<MisAlumnosPage />} />
        <Route path="teacher/carga-notas" element={<CargaNotasPage />} />
        {/* Add more teacher routes here */}
    </>
);
