import { lazy } from 'react';
import { Route } from 'react-router-dom';
import ProtectedRoute from '../../components/ProtectedRoute';

// Lazy Imports
const Financial = lazy(() => import('../../pages/Financial'));
const RegistroPagosPage = lazy(() => import('../../pages/RegistroPagosPage'));
const EstadoCuentaPage = lazy(() => import('../../pages/EstadoCuentaPage'));
const CorteDiaPage = lazy(() => import('../../pages/CorteDiaPage'));
const ComprobantesPage = lazy(() => import('../../pages/ComprobantesPage'));
const ComprobantesEmitidosPage = lazy(() => import('../../pages/ComprobantesEmitidosPage'));
const PaquetesPage = lazy(() => import('../../pages/PaquetesPage'));
const CostosPage = lazy(() => import('../../pages/CostosPage'));
const ExonerationPage = lazy(() => import('../../pages/ExonerationPage'));
const SolicitudesPage = lazy(() => import('../../pages/SolicitudesPage'));
const InsolventesPage = lazy(() => import('../../pages/InsolventesPage'));
const AsignarPaquetesPage = lazy(() => import('../../pages/AsignarPaquetesPage'));
const ConveniosPage = lazy(() => import('../../pages/ConveniosPage'));
const AsignarConvenioPage = lazy(() => import('../../pages/AsignarConvenioPage'));
const PaquetesSeleccionadosPage = lazy(() => import('../../pages/PaquetesSeleccionadosPage'));
const ScholarshipsPage = lazy(() => import('./pages/ScholarshipsPage'));

const FinancialRoutes = () => {
    return (
        <>
            {/* Main Financial Dashboard */}
            <Route path="finanzas" element={<Financial />} />

            {/* Sub-routes */}
            <Route path="finanzas/pagos" element={<RegistroPagosPage />} />
            <Route path="finanzas/estado-cuenta" element={<EstadoCuentaPage />} />
            <Route path="finanzas/corte-dia" element={<CorteDiaPage />} />
            <Route path="finanzas/comprobantes" element={<ComprobantesPage />} />
            <Route path="finanzas/comprobantes-pendientes" element={<ComprobantesPage />} />
            <Route path="finanzas/comprobantes-emitidos" element={<ComprobantesEmitidosPage />} />
            <Route path="finanzas/paquetes" element={<PaquetesPage />} />
            <Route path="finanzas/costos" element={<CostosPage />} />
            <Route path="finanzas/exonerados" element={<ExonerationPage />} />
            <Route path="finanzas/exoneraciones" element={<ExonerationPage />} />
            <Route path="finanzas/solicitudes" element={<SolicitudesPage />} />
            <Route path="finanzas/insolventes" element={<InsolventesPage />} />
            <Route path="finanzas/paquetes-seleccionados" element={<PaquetesSeleccionadosPage />} />

            <Route path="finanzas/paquetes-seleccionados" element={<PaquetesSeleccionadosPage />} />
            <Route path="finanzas/becas" element={<ScholarshipsPage />} />

            {/* Top Level Protected Financial Routes (if accessed directly not under layout, need check) */}
            {/* Note: The original AppRoutes had some under layout and some outside. 
                Based on structure, these seem to be children of Layout ("/" route). 
                I will export them as a fragment of routes to be placed inside the Layout Route.
            */}
        </>
    );
};

// Export routes specifically for the Layout children
export const financialRouteElements = <>
    <Route path="finanzas" element={<Financial />} />
    <Route path="finanzas/pagos" element={<RegistroPagosPage />} />
    <Route path="finanzas/estado-cuenta" element={<EstadoCuentaPage />} />
    <Route path="finanzas/corte-dia" element={<CorteDiaPage />} />
    <Route path="finanzas/comprobantes" element={<ComprobantesPage />} />
    <Route path="finanzas/comprobantes-pendientes" element={<ComprobantesPage />} />
    <Route path="finanzas/comprobantes-emitidos" element={<ComprobantesEmitidosPage />} />
    <Route path="finanzas/paquetes" element={<PaquetesPage />} />
    <Route path="finanzas/costos" element={<CostosPage />} />
    <Route path="finanzas/exonerados" element={<ExonerationPage />} />
    <Route path="finanzas/exoneraciones" element={<ExonerationPage />} />
    <Route path="finanzas/solicitudes" element={<SolicitudesPage />} />
    <Route path="finanzas/insolventes" element={<InsolventesPage />} />
    <Route path="finanzas/paquetes-seleccionados" element={<PaquetesSeleccionadosPage />} />
    <Route path="finanzas/becas" element={<ScholarshipsPage />} />
</>;

export const standaloneFinancialRoutes = <>
    <Route path="/financiero/comprobantes" element={<ProtectedRoute><ComprobantesEmitidosPage /></ProtectedRoute>} />
    <Route path="/financiero/paquetes" element={<ProtectedRoute><AsignarPaquetesPage /></ProtectedRoute>} />
    <Route path="/financiero/convenios" element={<ProtectedRoute><ConveniosPage /></ProtectedRoute>} />
    <Route path="/financiero/asignar-convenio" element={<ProtectedRoute><AsignarConvenioPage /></ProtectedRoute>} />
    <Route path="/financiero/paquetes-seleccionados" element={<ProtectedRoute><PaquetesSeleccionadosPage /></ProtectedRoute>} />
    <Route path="/financiero/costos" element={<ProtectedRoute><CostosPage /></ProtectedRoute>} />
</>;

export default FinancialRoutes;
