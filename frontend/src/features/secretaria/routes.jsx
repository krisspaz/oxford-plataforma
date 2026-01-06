import { lazy } from 'react';
import { Route } from 'react-router-dom';

// Lazy Imports
const InscripcionesPage = lazy(() => import('../../pages/InscripcionesPage'));
const MatriculacionPage = lazy(() => import('../../pages/MatriculacionPage'));
const Students = lazy(() => import('../../pages/Students'));
const FamiliasPage = lazy(() => import('../../pages/FamiliasPage'));
const ContractsPage = lazy(() => import('../../pages/ContractsPage'));
const AsignarPaquetesPage = lazy(() => import('../../pages/AsignarPaquetesPage'));
const ConveniosPage = lazy(() => import('../../pages/ConveniosPage'));
const AsignarConvenioPage = lazy(() => import('../../pages/AsignarConvenioPage'));

export const secretariaRouteElements = <>
    <Route path="secretaria/inscripciones" element={<InscripcionesPage />} />
    <Route path="secretaria/matriculacion" element={<MatriculacionPage />} />
    <Route path="secretaria/matricular" element={<MatriculacionPage />} />
    <Route path="secretaria/students" element={<Students />} />
    <Route path="secretaria/familias" element={<FamiliasPage />} />
    <Route path="secretaria/contratos" element={<ContractsPage />} />
    <Route path="secretaria/documentos" element={<InscripcionesPage />} />
    <Route path="secretaria/asignar-paquetes" element={<AsignarPaquetesPage />} />
    <Route path="secretaria/convenios" element={<ConveniosPage />} />
    <Route path="secretaria/asignar-convenio" element={<AsignarConvenioPage />} />
</>;
