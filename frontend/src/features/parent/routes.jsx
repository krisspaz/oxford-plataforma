import { lazy } from 'react';

// Lazy Imports
// eslint-disable-next-line unused-imports/no-unused-vars
const MyChildrenPage = lazy(() => import('./pages/MyChildrenPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const FamilyContractPage = lazy(() => import('./pages/FamilyContractPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const MonitoreoPage = lazy(() => import('../../pages/MonitoreoPage'));

export const parentRouteElements = <>
    <Route path="padres/hijos" element={<MyChildrenPage />} />
    <Route path="padres/contrato" element={<FamilyContractPage />} />
</>;

export const standaloneParentRoutes = <>
    <Route path="/mis-hijos" element={<ProtectedRoute><MyChildrenPage /></ProtectedRoute>} />
    <Route path="/monitoreo" element={<ProtectedRoute><MonitoreoPage /></ProtectedRoute>} />
</>;
