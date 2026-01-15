import { Routes, Route } from 'react-router-dom';
import { lazy } from 'react';
import ProtectedRoute from '../components/ProtectedRoute';
import Layout from '../components/Layout';
import Login from '../pages/Login';
import LandingPage from '../pages/LandingPage';
import PageTransition from '../components/ui/PageTransition';

// Import Feature Routes
import { financialRouteElements, standaloneFinancialRoutes } from '../features/financial/routes';
import { academicRouteElements, standaloneAcademicRoutes } from '../features/academic/routes';
import { secretariaRouteElements } from '../features/secretaria/routes';
import { adminRouteElements } from '../features/admin/routes';
import { teacherRouteElements, standaloneTeacherRoutes } from '../features/teacher/routes';
import { studentRouteElements, standaloneStudentRoutes } from '../features/student/routes';
import { parentRouteElements, standaloneParentRoutes } from '../features/parent/routes';

// Shared/General Pages
const NotFound = () => (
    <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900">
        <h1 className="text-4xl font-bold text-gray-800 dark:text-white mb-4">404</h1>
        <p className="text-gray-600 dark:text-gray-400 mb-8">Página no encontrada</p>
        <a href="/" className="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors">Volver al Inicio</a>
    </div>
);
const Dashboard = lazy(() => import('../pages/Dashboard'));
const ReportsPage = lazy(() => import('../pages/ReportsPage'));
const ChatDocentePage = lazy(() => import('../pages/ChatDocentePage'));
const SugerenciasPage = lazy(() => import('../pages/SugerenciasPage'));
const EvaluacionDocentePage = lazy(() => import('../pages/EvaluacionDocentePage'));
const CalendarioGlobalPage = lazy(() => import('../pages/CalendarioGlobalPage'));
const CargaNotasPage = lazy(() => import('../pages/CargaNotasPage'));
const HorariosPage = lazy(() => import('../pages/HorariosPage'));
const MiHorarioPage = lazy(() => import('../pages/MiHorarioPage'));
const MisAlumnosPage = lazy(() => import('../pages/MisAlumnosPage'));
const GestionTareasPage = lazy(() => import('../pages/GestionTareasPage'));
const CalendarPage = lazy(() => import('../pages/CalendarPage'));
const TareasPage = lazy(() => import('../pages/TareasPage'));
const NotificationsPage = lazy(() => import('../pages/NotificationsPage'));

// Enterprise Components
const CrisisModeDashboard = lazy(() => import('../components/CrisisModeDashboard'));
const EconomicDashboard = lazy(() => import('../components/EconomicDashboard'));
const CalendarView = lazy(() => import('../components/CalendarView'));
const ExportCenter = lazy(() => import('../components/ExportCenter'));
const StudentTaskDetailPage = lazy(() => import('../pages/StudentTaskDetailPage'));
const SystemStatusPage = lazy(() => import('../pages/SystemStatusPage'));

const NotFoundComponent = () => (
    <div className="p-10 text-center">
        <h1 className="text-4xl font-bold text-gray-800 mb-4">404</h1>
        <p className="text-gray-600">Página no encontrada</p>
    </div>
);

const AppRoutes = () => {
    return (
        <Routes>
            <Route path="/welcome" element={<LandingPage />} />
            <Route path="/login" element={<Login />} />

            {/* Standalone Routes (Not under Main Layout) */}
            {standaloneParentRoutes}
            {standaloneFinancialRoutes}
            {standaloneAcademicRoutes}
            {standaloneTeacherRoutes}
            {standaloneStudentRoutes}

            <Route path="/" element={
                <ProtectedRoute>
                    <Layout />
                </ProtectedRoute>
            }>
                {/* Dashboard */}
                <Route index element={<PageTransition><Dashboard /></PageTransition>} />

                {/* Feature Modules */}
                {secretariaRouteElements}
                {financialRouteElements}
                {academicRouteElements}
                {adminRouteElements}
                {teacherRouteElements}
                {studentRouteElements}
                {parentRouteElements}

                {/* Auth Routes (if any needed inside layout) */}
                {/* authRouteElements usually outside layout, but included for completeness if architecture requires */}

                {/* General / Shared */}
                <Route path="mis-notas" element={<ReportsPage />} />
                <Route path="chat" element={<ChatDocentePage />} />
                <Route path="ayuda" element={<SugerenciasPage />} />
                <Route path="evaluacion" element={<EvaluacionDocentePage />} />
                <Route path="calendario" element={<CalendarioGlobalPage />} />
                <Route path="carga-notas" element={<CargaNotasPage />} />
                <Route path="horarios" element={<HorariosPage />} />
                <Route path="mi-horario" element={<MiHorarioPage />} />
                <Route path="mis-alumnos" element={<MisAlumnosPage />} />
                <Route path="gestion-tareas" element={<GestionTareasPage />} />

                {/* IA / Enterprise */}
                <Route path="crisis-mode" element={<CrisisModeDashboard />} />
                <Route path="economic-dashboard" element={<EconomicDashboard />} />
                <Route path="calendario-escolar" element={<CalendarView />} />
                <Route path="exportar" element={<ExportCenter />} />
                <Route path="system-status" element={<SystemStatusPage />} />

                {/* Otros */}
                <Route path="calendar" element={<CalendarPage />} />
                <Route path="reports" element={<ReportsPage />} />
                <Route path="mi-portal/tareas" element={<TareasPage />} />
                <Route path="mis-tareas/:taskId" element={<StudentTaskDetailPage />} />
                <Route path="notificaciones" element={<NotificationsPage />} />

                <Route path="*" element={<NotFound />} />
            </Route>
        </Routes>
    );
};

export default AppRoutes;
