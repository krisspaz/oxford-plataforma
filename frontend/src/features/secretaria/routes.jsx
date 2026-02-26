import { lazy } from 'react';

// Lazy Imports
// eslint-disable-next-line unused-imports/no-unused-vars
const InscripcionesPage = lazy(() => import('../../pages/InscripcionesPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const MatriculacionPage = lazy(() => import('../../pages/MatriculacionPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const Students = lazy(() => import('../../pages/Students'));
// eslint-disable-next-line unused-imports/no-unused-vars
const FamiliasPage = lazy(() => import('../../pages/FamiliasPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const ContractsPage = lazy(() => import('../../pages/ContractsPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const AsignarPaquetesPage = lazy(() => import('../../pages/AsignarPaquetesPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const ConveniosPage = lazy(() => import('../../pages/ConveniosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
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
