import { lazy } from 'react';
// eslint-disable-next-line unused-imports/no-unused-vars
const Login = lazy(() => import('../pages/Login'));
// eslint-disable-next-line unused-imports/no-unused-vars
const LandingPage = lazy(() => import('../pages/LandingPage'));

// Import Feature Routes
// Import Feature Routes
import { financialRouteElements, standaloneFinancialRoutes } from '../features/financial/routes';
import { academicRouteElements, standaloneAcademicRoutes } from '../features/academic/routes';
import { secretariaRouteElements } from '../features/secretaria/routes';
import { adminRouteElements } from '../features/admin/routes';
import { teacherRouteElements, standaloneTeacherRoutes } from '../features/teacher/routes';
import { studentRouteElements, standaloneStudentRoutes } from '../features/student/routes';
import { parentRouteElements, standaloneParentRoutes } from '../features/parent/routes';

// Shared/General Pages
// eslint-disable-next-line unused-imports/no-unused-vars
const NotFound = () => (
    <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900">
        <h1 className="text-4xl font-bold text-gray-800 dark:text-white mb-4">404</h1>
        <p className="text-gray-600 dark:text-gray-400 mb-8">Página no encontrada</p>
        <a href="/" className="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors">Volver al Inicio</a>
    </div>
);
// eslint-disable-next-line unused-imports/no-unused-vars
const Dashboard = lazy(() => import('../pages/Dashboard'));
// eslint-disable-next-line unused-imports/no-unused-vars
const ReportsPage = lazy(() => import('../pages/ReportsPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const ChatDocentePage = lazy(() => import('../pages/ChatDocentePage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const SugerenciasPage = lazy(() => import('../pages/SugerenciasPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const EvaluacionDocentePage = lazy(() => import('../pages/EvaluacionDocentePage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const CalendarioGlobalPage = lazy(() => import('../pages/CalendarioGlobalPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const CargaNotasPage = lazy(() => import('../pages/CargaNotasPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const HorariosPage = lazy(() => import('../pages/HorariosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const MiHorarioPage = lazy(() => import('../pages/MiHorarioPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const MisAlumnosPage = lazy(() => import('../pages/MisAlumnosPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const GestionTareasPage = lazy(() => import('../pages/GestionTareasPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const CalendarPage = lazy(() => import('../pages/CalendarPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const TareasPage = lazy(() => import('../pages/TareasPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const NotificationsPage = lazy(() => import('../pages/NotificationsPage'));

// Enterprise Components
// eslint-disable-next-line unused-imports/no-unused-vars
const CrisisModeDashboard = lazy(() => import('../components/CrisisModeDashboard'));
// eslint-disable-next-line unused-imports/no-unused-vars
const EconomicDashboard = lazy(() => import('../components/EconomicDashboard'));
// eslint-disable-next-line unused-imports/no-unused-vars
const CalendarView = lazy(() => import('../components/CalendarView'));
// eslint-disable-next-line unused-imports/no-unused-vars
const ExportCenter = lazy(() => import('../components/ExportCenter'));
// eslint-disable-next-line unused-imports/no-unused-vars
const StudentTaskDetailPage = lazy(() => import('../pages/StudentTaskDetailPage'));
// eslint-disable-next-line unused-imports/no-unused-vars
const SystemStatusPage = lazy(() => import('../pages/SystemStatusPage'));
// ... imports continue ...

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
