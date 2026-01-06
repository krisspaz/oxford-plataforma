import { lazy } from 'react';
import { Route } from 'react-router-dom';

const Dashboard = lazy(() => import('../pages/Dashboard'));
const GestionUsuariosPage = lazy(() => import('../pages/GestionUsuariosPage'));
const PrivilegiosPage = lazy(() => import('../pages/PrivilegiosPage'));
const LogsPage = lazy(() => import('../pages/LogsPage'));
const SettingsPage = lazy(() => import('../pages/SettingsPage'));
const CatalogosPage = lazy(() => import('../pages/CatalogosPage'));
const AdminChargesPage = lazy(() => import('../features/admin/pages/AdminChargesPage'));

export const adminRouteElements = (
    <>
        <Route path="admin/usuarios" element={<GestionUsuariosPage />} />
        <Route path="admin/privilegios" element={<PrivilegiosPage />} />
        <Route path="admin/logs" element={<LogsPage />} />
        <Route path="admin/configuracion" element={<SettingsPage />} />
        <Route path="admin/catalogos" element={<CatalogosPage />} />
        <Route path="admin/cargos" element={<AdminChargesPage />} />
    </>
);
