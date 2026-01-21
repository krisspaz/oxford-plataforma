import { lazy } from 'react';
import { Route } from 'react-router-dom';

// Lazy Imports
const GestionUsuariosPage = lazy(() => import('../../pages/GestionUsuariosPage'));
const CatalogosPage = lazy(() => import('../../pages/CatalogosPage'));
const SettingsPage = lazy(() => import('../../pages/SettingsPage'));
const ResetNotificacionesPage = lazy(() => import('../../pages/ResetNotificacionesPage'));
const PrivilegiosPage = lazy(() => import('../../pages/PrivilegiosPage'));
const GestionMenusPage = lazy(() => import('../../pages/GestionMenusPage'));
const CierreEscolarPage = lazy(() => import('../../pages/CierreEscolarPage'));
const MonitoreoPage = lazy(() => import('../../pages/MonitoreoPage'));
const LogsPage = lazy(() => import('../../pages/LogsPage'));
const AdminChargesPage = lazy(() => import('./pages/AdminChargesPage'));
const SecurityDashboard = lazy(() => import('./pages/SecurityDashboard'));
const AiInsightsPage = lazy(() => import('../academic/pages/AiInsightsPage'));
const HelpTicketsPage = lazy(() => import('./pages/HelpTicketsPage'));
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
    <Route path="admin/ai-insights" element={<AiInsightsPage />} />
    <Route path="admin/support" element={<HelpTicketsPage />} />
    <Route path="admin/teacher-ratings" element={<TeacherRatingsPage />} />
    <Route path="settings" element={<SettingsPage />} />
</>;
