import { lazy } from 'react';
import { Route } from 'react-router-dom';
import ProtectedRoute from '../../components/ProtectedRoute';

// Lazy Imports
const MyChildrenPage = lazy(() => import('./pages/MyChildrenPage'));
const FamilyContractPage = lazy(() => import('./pages/FamilyContractPage'));
const MonitoreoPage = lazy(() => import('../../pages/MonitoreoPage'));

export const parentRouteElements = <>
    <Route path="padres/hijos" element={<MyChildrenPage />} />
    <Route path="padres/contrato" element={<FamilyContractPage />} />
</>;

export const standaloneParentRoutes = <>
    <Route path="/mis-hijos" element={<ProtectedRoute><MyChildrenPage /></ProtectedRoute>} />
    <Route path="/monitoreo" element={<ProtectedRoute><MonitoreoPage /></ProtectedRoute>} />
</>;
