import { lazy } from 'react';

// Lazy Imports
// eslint-disable-next-line unused-imports/no-unused-vars
const GestionUsuariosPage = lazy(() => import('../../pages/GestionUsuariosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const CatalogosPage = lazy(() => import('../../pages/CatalogosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const SettingsPage = lazy(() => import('../../pages/SettingsPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const ResetNotificacionesPage = lazy(() => import('../../pages/ResetNotificacionesPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const PrivilegiosPage = lazy(() => import('../../pages/PrivilegiosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const GestionMenusPage = lazy(() => import('../../pages/GestionMenusPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const CierreEscolarPage = lazy(() => import('../../pages/CierreEscolarPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const MonitoreoPage = lazy(() => import('../../pages/MonitoreoPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const LogsPage = lazy(() => import('../../pages/LogsPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const AdminChargesPage = lazy(() => import('./pages/AdminChargesPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const SecurityDashboard = lazy(() => import('./pages/SecurityDashboard'));
// eslint-disable-next-line unused-imports/no-unused-vars
const PredictiveDashboard = lazy(() => import('./pages/PredictiveDashboard'));
// eslint-disable-next-line unused-imports/no-unused-vars
const DocumentManager = lazy(() => import('./documents/components/DocumentManager'));
// eslint-disable-next-line unused-imports/no-unused-vars
const HelpTicketsPage = lazy(() => import('./pages/HelpTicketsPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const TeacherRatingsPage = lazy(() => import('../academic/pages/TeacherRatingsPage'));


export const adminRouteElements = <>
    <Route path="admin/usuarios" element={<GestionUsuariosPage />} />
    <Route path="admin/catalogos" element={<CatalogosPage />} />
    <Route path="admin/ajustes" element={<SettingsPage />} />
    <Route path="admin/ajustes-generales" element={<SettingsPage />} />
    <Route path="admin/cierre-escolar" element={<CierreEscolarPage />} />
    <Route path="admin/cargos" element={<AdminChargesPage />} />
    <Route path="admin/notificaciones-reset" element={<ResetNotificacionesPage />} />
    <Route path="admin/privilegios" element={<PrivilegiosPage />} />
    <Route path="admin/menus" element={<GestionMenusPage />} />
    <Route path="admin/estadisticas" element={<MonitoreoPage />} />
    <Route path="admin/logs" element={<LogsPage />} />
    <Route path="admin/security" element={<SecurityDashboard />} />
    <Route path="admin/ai-insights" element={<PredictiveDashboard />} />
    <Route path="admin/documentos" element={<DocumentManager />} />
    <Route path="admin/support" element={<HelpTicketsPage />} />
    <Route path="admin/teacher-ratings" element={<TeacherRatingsPage />} />
    <Route path="settings" element={<SettingsPage />} />
</>;
