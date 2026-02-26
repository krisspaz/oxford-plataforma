import { lazy } from 'react';

// eslint-disable-next-line unused-imports/no-unused-vars
const Dashboard = lazy(() => import('../pages/Dashboard'));
// eslint-disable-next-line unused-imports/no-unused-vars
const GestionUsuariosPage = lazy(() => import('../pages/GestionUsuariosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const PrivilegiosPage = lazy(() => import('../pages/PrivilegiosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const LogsPage = lazy(() => import('../pages/LogsPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const SettingsPage = lazy(() => import('../pages/SettingsPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const CatalogosPage = lazy(() => import('../pages/CatalogosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const AdminChargesPage = lazy(() => import('../features/admin/pages/AdminChargesPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const CentroAjustesPage = lazy(() => import('../pages/CentroAjustesPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const PlantillasCorreoPage = lazy(() => import('../pages/PlantillasCorreoPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const CierreEscolarPage = lazy(() => import('../pages/CierreEscolarPage'));

export const adminRouteElements = (
    <>
        <Route path="admin/usuarios" element={<GestionUsuariosPage />} />
        <Route path="admin/privilegios" element={<PrivilegiosPage />} />
        <Route path="admin/logs" element={<LogsPage />} />
        <Route path="admin/configuracion" element={<SettingsPage />} />
        <Route path="admin/catalogos" element={<CatalogosPage />} />
        <Route path="admin/cargos" element={<AdminChargesPage />} />
        <Route path="admin/ajustes" element={<CentroAjustesPage />} />
        <Route path="admin/plantillas-correo" element={<PlantillasCorreoPage />} />
        <Route path="admin/cierre-escolar" element={<CierreEscolarPage />} />
    </>
);
