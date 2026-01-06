import { lazy } from 'react';
import { Route } from 'react-router-dom';
import ProtectedRoute from '../../components/ProtectedRoute';

// Lazy Imports
const MisHijosPage = lazy(() => import('../../pages/MisHijosPage'));
const ContractsPage = lazy(() => import('../../pages/ContractsPage'));
const MonitoreoPage = lazy(() => import('../../pages/MonitoreoPage'));

export const parentRouteElements = <>
    <Route path="padres/hijos" element={<MisHijosPage />} />
    <Route path="padres/contrato" element={<ContractsPage />} />
</>;

export const standaloneParentRoutes = <>
    <Route path="/mis-hijos" element={<ProtectedRoute><MisHijosPage /></ProtectedRoute>} />
    <Route path="/monitoreo" element={<ProtectedRoute><MonitoreoPage /></ProtectedRoute>} />
</>;
