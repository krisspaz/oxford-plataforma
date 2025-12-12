<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CmsMenusSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['id' => 1, 'name' => 'Ciclo Academico', 'type' => 'Route', 'path' => 'AdminGestionesacademicasControllerGetIndex', 'color' => 'normal', 'icon' => 'fa fa-university', 'parent_id' => 58, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 14, 'created_at' => '2024-11-14 13:09:27', 'updated_at' => '2025-03-06 08:55:25'],
            ['id' => 2, 'name' => 'Estados', 'type' => 'Route', 'path' => 'AdminTbEstadosControllerGetIndex', 'color' => null, 'icon' => 'fa fa-check', 'parent_id' => 58, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 10, 'created_at' => '2024-11-14 13:12:14', 'updated_at' => null],
            ['id' => 4, 'name' => 'Niveles', 'type' => 'Route', 'path' => 'AdminNiveles15ControllerGetIndex', 'color' => 'normal', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 56, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 6, 'created_at' => '2024-11-14 07:17:13', 'updated_at' => '2025-03-06 02:54:26'],
            ['id' => 5, 'name' => 'Cursos', 'type' => 'Route', 'path' => 'AdminCursosControllerGetIndex', 'color' => 'normal', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 56, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 1, 'created_at' => '2024-11-14 07:53:01', 'updated_at' => '2025-03-06 02:54:55'],
            ['id' => 6, 'name' => 'Grados', 'type' => 'Route', 'path' => 'AdminTbGradosControllerGetIndex', 'color' => 'normal', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 56, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 11, 'created_at' => '2024-11-14 08:52:18', 'updated_at' => '2025-03-06 02:54:39'],
            ['id' => 7, 'name' => 'Secciones', 'type' => 'Route', 'path' => 'AdminSeccionesControllerGetIndex', 'color' => 'normal', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 56, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 2, 'created_at' => '2024-11-14 09:30:12', 'updated_at' => '2025-03-06 02:55:09'],
            ['id' => 8, 'name' => 'Parentescos', 'type' => 'Route', 'path' => 'AdminTbParentescosControllerGetIndex', 'color' => null, 'icon' => 'fa fa-user-plus', 'parent_id' => 58, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 11, 'created_at' => '2024-11-14 09:52:01', 'updated_at' => null],
            ['id' => 9, 'name' => 'Jornadas y Horarios', 'type' => 'Route', 'path' => 'AdminPvJornadaDiaHorariosControllerGetIndex', 'color' => null, 'icon' => 'fa fa-times-circle', 'parent_id' => 51, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 4, 'created_at' => '2024-11-22 08:12:32', 'updated_at' => null],
            ['id' => 11, 'name' => 'Inscripciones', 'type' => 'URL', 'path' => '/admin/inscripcion/', 'color' => 'normal', 'icon' => 'fa fa-hacker-news', 'parent_id' => 51, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 2, 'created_at' => '2024-11-29 12:18:24', 'updated_at' => '2025-03-11 23:04:19'],
            ['id' => 14, 'name' => 'Documentos de Identificación', 'type' => 'Route', 'path' => 'AdminTbIdentificacionDocumentosControllerGetIndex', 'color' => null, 'icon' => 'fa fa-user-plus', 'parent_id' => 58, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 13, 'created_at' => '2024-12-05 06:34:04', 'updated_at' => null],
            ['id' => 15, 'name' => 'Costos por Nivel', 'type' => 'Route', 'path' => 'AdminTbCostosNivelesControllerGetIndex', 'color' => null, 'icon' => 'fa fa-money', 'parent_id' => 55, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 6, 'created_at' => '2024-12-10 05:35:57', 'updated_at' => null],
                  ['id' => 17, 'name' => 'Materias', 'type' => 'URL', 'path' => '/admin/materias', 'color' => 'normal', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 56, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 9, 'created_at' => '2024-12-16 16:51:11', 'updated_at' => '2025-03-06 02:51:20'],
            ['id' => 19, 'name' => 'Docentes', 'type' => 'URL', 'path' => '/admin/docentes', 'color' => 'normal', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 56, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 7, 'created_at' => '2024-12-20 01:34:26', 'updated_at' => '2024-12-31 10:45:04'],
            ['id' => 23, 'name' => 'Registro de Pagos', 'type' => 'URL', 'path' => '/admin/pagos/buscar', 'color' => 'normal', 'icon' => 'fa fa-money', 'parent_id' => 51, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 8, 'created_at' => '2024-12-24 03:59:34', 'updated_at' => '2024-12-31 02:09:23'],
            ['id' => 24, 'name' => 'Asignar Convenio', 'type' => 'URL', 'path' => '/admin/convenios', 'color' => 'normal', 'icon' => 'fa fa-legal', 'parent_id' => 51, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 6, 'created_at' => '2024-12-24 04:00:44', 'updated_at' => '2025-02-26 23:18:55'],
            ['id' => 25, 'name' => 'Gestión de Tareas', 'type' => 'URL', 'path' => '/admin/docentes-tareas/tareas', 'color' => 'normal', 'icon' => 'fa fa-cog', 'parent_id' => 59, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 3, 'created_at' => '2024-12-30 15:07:11', 'updated_at' => '2025-01-08 01:38:39'],
            ['id' => 26, 'name' => 'Asignacion de Materias', 'type' => 'URL', 'path' => '/admin/materiascursos/', 'color' => 'normal', 'icon' => 'fa fa-cog', 'parent_id' => 56, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 10, 'created_at' => '2024-12-30 15:12:27', 'updated_at' => '2025-03-06 02:51:34'],
            ['id' => 28, 'name' => 'Tareas Asignadas', 'type' => 'URL', 'path' => '/admin/estudiantes/tareas', 'color' => 'normal', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 60, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 3, 'created_at' => '2024-12-31 01:19:23', 'updated_at' => '2025-08-09 22:01:46'],
            ['id' => 30, 'name' => 'Contrato', 'type' => 'URL', 'path' => '/admin/estudiante_contratos', 'color' => 'normal', 'icon' => 'fa fa-legal', 'parent_id' => 51, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 9, 'created_at' => '2024-12-31 04:40:47', 'updated_at' => '2025-02-19 23:08:38'],
            ['id' => 32, 'name' => 'Cuadros', 'type' => 'URL', 'path' => '/admin/reporte/filtros', 'color' => 'normal', 'icon' => 'fa fa-glass', 'parent_id' => 56, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 5, 'created_at' => '2024-12-31 11:14:09', 'updated_at' => '2025-04-29 15:12:03'],
            ['id' => 33, 'name' => 'Users', 'type' => 'Statistic', 'path' => 'statistic_builder/show/das', 'color' => 'green', 'icon' => 'fa fa-user', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 1, 'id_cms_privileges' => 1, 'sorting' => 17, 'created_at' => '2024-12-31 11:44:36', 'updated_at' => '2025-01-04 11:21:27'],
            ['id' => 34, 'name' => 'Detalles de Paquetes', 'type' => 'URL', 'path' => '/admin/paquete_detalles', 'color' => 'normal', 'icon' => 'fa fa-file-zip-o', 'parent_id' => 55, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 2, 'created_at' => '2025-01-02 15:14:16', 'updated_at' => null],
            ['id' => 35, 'name' => 'Paquetes', 'type' => 'URL', 'path' => '/admin/paquetes', 'color' => 'normal', 'icon' => 'fa fa-paste', 'parent_id' => 55, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 5, 'created_at' => '2025-01-02 15:15:17', 'updated_at' => null],
            ['id' => 37, 'name' => 'Productos', 'type' => 'URL', 'path' => '/admin/productos', 'color' => 'normal', 'icon' => 'fa fa-product-hunt', 'parent_id' => 55, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 3, 'created_at' => '2025-01-02 16:18:08', 'updated_at' => null],
            ['id' => 38, 'name' => 'Paquetes y Productos', 'type' => 'URL', 'path' => '/admin/detalle_productos', 'color' => 'normal', 'icon' => 'fa fa-calendar-minus-o', 'parent_id' => 55, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 4, 'created_at' => '2025-01-02 17:15:30', 'updated_at' => null],
            ['id' => 39, 'name' => 'Paquetes Seleccionados', 'type' => 'URL', 'path' => '/admin/productos_seleccionados', 'color' => 'normal', 'icon' => 'fa fa-tags', 'parent_id' => 51, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 5, 'created_at' => '2025-01-05 09:02:16', 'updated_at' => null],
            ['id' => 40, 'name' => 'Asignación de Paquetes de', 'type' => 'URL', 'path' => '/admin/asignador_de_paquetes', 'color' => 'normal', 'icon' => 'fa fa-tags', 'parent_id' => 51, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 3, 'created_at' => '2025-01-05 10:53:14', 'updated_at' => '2025-03-11 23:23:03'],
            ['id' => 41, 'name' => 'Convenios y Cuotas', 'type' => 'URL', 'path' => '/admin/convenios_detalles', 'color' => 'normal', 'icon' => 'fa fa-tags', 'parent_id' => 51, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 7, 'created_at' => '2025-01-06 08:33:00', 'updated_at' => null],
            ['id' => 43, 'name' => 'Listado', 'type' => 'URL', 'path' => '/admin/docentes-tareas/listado', 'color' => 'normal', 'icon' => 'fa fa-user', 'parent_id' => 59, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 4, 'created_at' => '2025-01-08 01:39:42', 'updated_at' => '2025-02-19 23:29:05'],
                        ['id' => 44, 'name' => 'Asignar nota (Tareas)', 'type' => 'URL', 'path' => '/admin/calificaciones', 'color' => 'normal', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 59, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 5, 'created_at' => '2025-01-08 07:42:03', 'updated_at' => null],
            ['id' => 46, 'name' => 'Gestion Bimestral', 'type' => 'Route', 'path' => 'AdminBimestresControllerGetIndex', 'color' => 'normal', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 58, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 12, 'created_at' => '2025-01-20 13:29:03', 'updated_at' => '2025-03-06 02:55:44'],
            ['id' => 47, 'name' => 'Asignar Contenido', 'type' => 'URL', 'path' => '/admin/contenido_materias', 'color' => 'normal', 'icon' => 'fa fa-odnoklassniki', 'parent_id' => 59, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 2, 'created_at' => '2025-01-29 10:57:47', 'updated_at' => null],
            ['id' => 48, 'name' => 'Corte del Día', 'type' => 'URL', 'path' => '/admin/filtrar-pagos', 'color' => 'normal', 'icon' => 'fa fa-money', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 5, 'created_at' => '2025-01-30 08:50:32', 'updated_at' => '2025-03-06 02:53:22'],
            ['id' => 49, 'name' => 'Gestión de Materias', 'type' => 'Route', 'path' => 'AdminGestionMateriasControllerGetIndex', 'color' => 'normal', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 56, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 8, 'created_at' => '2025-02-06 01:06:00', 'updated_at' => '2025-03-06 02:50:56'],
            ['id' => 50, 'name' => 'Listado de Alumnos', 'type' => 'URL', 'path' => '/admin/reporte/filtrar', 'color' => 'normal', 'icon' => 'fa fa-university', 'parent_id' => 56, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 4, 'created_at' => '2025-02-19 21:11:25', 'updated_at' => null],
            ['id' => 51, 'name' => 'Secretaria', 'type' => 'URL', 'path' => '#', 'color' => 'light-blue', 'icon' => 'fa fa-university', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 16, 'created_at' => '2025-02-26 23:14:43', 'updated_at' => '2025-03-24 18:35:02'],
            ['id' => 52, 'name' => 'Revision de Tareas', 'type' => 'URL', 'path' => '/admin/tareas/familiaalumnos', 'color' => 'normal', 'icon' => 'fa fa-check', 'parent_id' => 61, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 3, 'created_at' => '2025-03-11 07:08:49', 'updated_at' => '2025-03-14 13:49:49'],
            ['id' => 55, 'name' => 'Finanzas', 'type' => 'URL', 'path' => '#', 'color' => 'yellow', 'icon' => 'fa fa-home', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 11, 'created_at' => '2025-03-21 18:23:14', 'updated_at' => '2025-03-21 18:50:20'],
            ['id' => 56, 'name' => 'Academico', 'type' => 'URL', 'path' => '#', 'color' => 'normal', 'icon' => 'fa fa-book', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 14, 'created_at' => '2025-03-21 18:29:45', 'updated_at' => '2025-03-24 18:35:39'],
            ['id' => 58, 'name' => 'Generales', 'type' => 'URL', 'path' => '#', 'color' => 'normal', 'icon' => 'fa fa-globe', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 10, 'created_at' => '2025-03-21 18:39:54', 'updated_at' => null],
            ['id' => 59, 'name' => 'Docente', 'type' => 'URL', 'path' => '#', 'color' => 'red', 'icon' => 'fa fa-table', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 12, 'created_at' => '2025-03-21 18:42:08', 'updated_at' => '2025-03-21 20:07:50'],
            ['id' => 60, 'name' => 'Estudiante', 'type' => 'URL', 'path' => '#', 'color' => 'normal', 'icon' => 'fa fa-user', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 9, 'created_at' => '2025-03-21 22:06:42', 'updated_at' => '2025-03-21 22:15:46'],
            ['id' => 61, 'name' => 'Padres', 'type' => 'URL', 'path' => '#', 'color' => 'normal', 'icon' => 'fa fa-users', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 8, 'created_at' => '2025-03-21 22:07:53', 'updated_at' => '2025-03-21 22:15:15'],
            ['id' => 65, 'name' => 'Personal Administrativo', 'type' => 'URL', 'path' => '/admin/administrativos', 'color' => 'normal', 'icon' => 'fa fa-users', 'parent_id' => 58, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 4, 'created_at' => '2025-04-21 17:06:30', 'updated_at' => null],
            ['id' => 66, 'name' => 'Notas Finales', 'type' => 'URL', 'path' => '/admin/docentes/cuadro-notas', 'color' => 'normal', 'icon' => 'fa fa-hacker-news', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 13, 'created_at' => '2025-04-21 17:10:00', 'updated_at' => null],
            ['id' => 68, 'name' => 'Documentos Inscripción', 'type' => 'URL', 'path' => '/admin/documentos', 'color' => 'normal', 'icon' => 'fa fa-file-o', 'parent_id' => 51, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 1, 'created_at' => '2025-04-21 17:13:36', 'updated_at' => null],
            ['id' => 69, 'name' => 'Estado de Cuenta', 'type' => 'URL', 'path' => '/admin/pagos/estudiante', 'color' => 'normal', 'icon' => 'fa fa-money', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 15, 'created_at' => '2025-04-21 17:14:49', 'updated_at' => null],
            ['id' => 70, 'name' => 'Solicitudes', 'type' => 'URL', 'path' => '#', 'color' => 'green', 'icon' => 'fa fa-check-circle', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 7, 'created_at' => '2025-04-21 17:15:39', 'updated_at' => '2025-11-07 19:09:15'],
            ['id' => 71, 'name' => 'Anulación de Facturas SAT', 'type' => 'URL', 'path' => '/admin/reportes/facturas/anuladas', 'color' => 'normal', 'icon' => 'fa fa-remove', 'parent_id' => 70, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 1, 'created_at' => '2025-04-21 17:16:17', 'updated_at' => '2025-04-21 17:17:58'],
            [  'id' => 72, 'name' => 'Anulación Recibos SAT', 'type' => 'URL', 'path' => '/admin/reportes/recibossat/anuladas',
    'color' => 'normal', 'icon' => 'fa fa-remove', 'parent_id' => 70, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 2, 'created_at' => '2025-04-21 17:17:41', 'updated_at' => null
],
[
    'id' => 73, 'name' => 'Comprobantes Emitidos', 'type' => 'URL', 'path' => '#',
    'color' => 'normal', 'icon' => 'fa fa-th-large', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 6, 'created_at' => '2025-04-21 17:18:49', 'updated_at' => '2025-04-21 17:19:04'
],
[
    'id' => 74, 'name' => 'Facturas SAT Emitidas', 'type' => 'URL', 'path' => '/admin/reportes/facturas',
    'color' => 'normal', 'icon' => 'fa fa-file-photo-o', 'parent_id' => 73, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 1, 'created_at' => '2025-04-21 17:20:46', 'updated_at' => null
],
[
    'id' => 75, 'name' => 'Recibos SAT Emitidos', 'type' => 'URL', 'path' => '/admin/reportes/recibossat',
    'color' => 'normal', 'icon' => 'fa fa-file-text-o', 'parent_id' => 73, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 2, 'created_at' => '2025-04-21 17:22:01', 'updated_at' => null
],
[
    'id' => 76, 'name' => 'Anular Factura (SAT) / Recibo (SAT)', 'type' => 'URL', 'path' => '/admin/anular/documento',
    'color' => 'normal', 'icon' => 'fa fa-arrows-alt', 'parent_id' => 58, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 5, 'created_at' => '2025-04-21 17:25:16', 'updated_at' => '2025-04-24 18:55:03'
],
[
    'id' => 77, 'name' => 'Ajuste en Familias', 'type' => 'URL', 'path' => '/admin/ajuste-familiar',
    'color' => 'normal', 'icon' => 'fa fa-users', 'parent_id' => 58, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 6, 'created_at' => '2025-04-21 17:26:39', 'updated_at' => null
],
[
    'id' => 78, 'name' => 'Ajuste en Usuarios', 'type' => 'URL', 'path' => '/admin/usuarios',
    'color' => 'normal', 'icon' => 'fa fa-user-plus', 'parent_id' => 58, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 7, 'created_at' => '2025-04-21 17:28:04', 'updated_at' => null
],
[
    'id' => 79, 'name' => 'Ajuste de Personas', 'type' => 'URL', 'path' => '/admin/ajuste-persona',
    'color' => 'normal', 'icon' => 'fa fa-user', 'parent_id' => 58, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 8, 'created_at' => '2025-04-21 17:30:34', 'updated_at' => null
],
[
    'id' => 80, 'name' => 'Cargos Administrativos', 'type' => 'Route', 'path' => 'AdminCargos1ControllerGetIndex',
    'color' => null, 'icon' => 'fa fa-newspaper-o', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 18, 'created_at' => '2025-04-22 18:47:21', 'updated_at' => null
],
[
    'id' => 81, 'name' => 'Ajuste P. Administrativo', 'type' => 'URL', 'path' => '/admin/ajuste-administrativos',
    'color' => 'normal', 'icon' => 'fa fa-user', 'parent_id' => 58, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 9, 'created_at' => '2025-04-22 18:51:36', 'updated_at' => null
],
[
    'id' => 82, 'name' => 'Recibos Internos Emitidos', 'type' => 'URL', 'path' => '/admin/reportes/recibosinternos',
    'color' => 'normal', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 55, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 1, 'created_at' => '2025-04-23 00:52:26', 'updated_at' => null
],
[
    'id' => 83, 'name' => 'Ajustes de Inscripcion', 'type' => 'URL', 'path' => '/admin/ajustes_inscripciones',
    'color' => 'normal', 'icon' => null, 'parent_id' => 58, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 3, 'created_at' => '2025-04-25 22:35:10', 'updated_at' => null
],
[
    'id' => 85, 'name' => 'Recibos Internos Emitidos', 'type' => 'URL', 'path' => '/admin/reportes/recibosinternos',
    'color' => 'normal', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 73, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 3, 'created_at' => '2025-04-26 00:12:27', 'updated_at' => null
],
[
    'id' => 86, 'name' => 'Anulación Recibos Internos', 'type' => 'URL', 'path' => '/admin/reportes/recibosinternos/anuladas',
    'color' => 'normal', 'icon' => 'fa fa-remove', 'parent_id' => 70, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 3, 'created_at' => '2025-04-26 06:29:48', 'updated_at' => '2025-04-26 06:30:49'
],
[
    'id' => 88, 'name' => 'Ajustes en Estudiantes', 'type' => 'URL', 'path' => '/admin/ajustes_estudiantes',
    'color' => 'normal', 'icon' => 'fa fa-user', 'parent_id' => 58, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 1, 'created_at' => '2025-04-28 16:40:48', 'updated_at' => null
],
[
    'id' => 89, 'name' => 'Ajuste en Pagos', 'type' => 'URL', 'path' => '/admin/reporte_pagos',
    'color' => 'normal', 'icon' => 'fa fa-money', 'parent_id' => 58, 'is_active' => 1, 'is_dashboard' => 0,
    'id_cms_privileges' => 1, 'sorting' => 2, 'created_at' => '2025-04-28 16:42:49', 'updated_at' => null
],
['id' => 91, 'name' => 'Materias', 'type' => 'URL', 'path' => '/admin/estudiantes/panel-materias','color' => 'normal', 'icon' => 'fa fa-delicious', 'parent_id' => 60, 'is_active' => 1, 'is_dashboard' => 0,'id_cms_privileges' => 1, 'sorting' => 1, 'created_at' => '2025-04-29 16:55:24', 'updated_at' => null],
['id' => 92, 'name' => 'Cierre de Notas Bimestrales', 'type' => 'URL', 'path' => '/admin/cuadro-notas/cierre', 'color' => 'normal', 'icon' => 'fa fa-rotate-right', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 4, 'created_at' => '2025-04-29 16:58:11', 'updated_at' => null],
    ['id' => 93, 'name' => 'Contratos Generados', 'type' => 'URL', 'path' => '/admin/ajustes_contrato', 'color' => 'normal', 'icon' => 'fa fa-legal', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 3, 'created_at' => '2025-04-29 16:59:02', 'updated_at' => null],
    ['id' => 94, 'name' => 'Comproabantes Pendientes', 'type' => 'URL', 'path' => '/admin/pagos/pendientescomprobantes', 'color' => 'normal', 'icon' => 'fa fa-file-text-o', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 2, 'created_at' => '2025-04-29 17:00:38', 'updated_at' => null],
    ['id' => 95, 'name' => 'Contrato', 'type' => 'URL', 'path' => '/admin/estudiante_contratos', 'color' => 'green', 'icon' => 'fa fa-legal', 'parent_id' => 61, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 1, 'created_at' => '2025-04-30 10:41:48', 'updated_at' => null],
    ['id' => 96, 'name' => 'Tareas Asignadas', 'type' => 'URL', 'path' => '/admin/tareas/familiaalumnosguia', 'color' => 'normal', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 61, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 2, 'created_at' => '2025-04-30 11:22:29', 'updated_at' => null],
    ['id' => 97, 'name' => 'Notas Finales', 'type' => 'URL', 'path' => '/admin/cuadro-notas/notasestudiantes', 'color' => 'green', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 61, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 4, 'created_at' => '2025-04-30 14:27:21', 'updated_at' => null],
    ['id' => 98, 'name' => 'Boletas por Estudiante', 'type' => 'URL', 'path' => '/admin/cuadro-notas/buscar', 'color' => 'green', 'icon' => 'fa fa-hacker-news', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 1, 'created_at' => '2025-04-30 14:28:32', 'updated_at' => null],
    ['id' => 99, 'name' => 'Tareas y Actividades Calificadas', 'type' => 'URL', 'path' => '/admin/calificaciones/reportesdocentes', 'color' => 'green', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 59, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 1, 'created_at' => '2025-05-01 01:11:43', 'updated_at' => '2025-05-01 01:15:15'],
    ['id' => 100, 'name' => 'Tareas Calificadas', 'type' => 'URL', 'path' => '/admin/calificaciones/reportes', 'color' => 'green', 'icon' => 'fa fa-newspaper-o', 'parent_id' => 60, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 4, 'created_at' => '2025-05-01 01:12:58', 'updated_at' => '2025-10-15 22:27:05'],
    ['id' => 101, 'name' => 'Cronograma de Tareas', 'type' => 'URL', 'path' => '/admin/calendario-tareas', 'color' => 'aqua', 'icon' => 'fa fa-calendar', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => null, 'created_at' => '2025-06-06 19:46:04', 'updated_at' => '2025-10-18 23:01:06'],
    ['id' => 102, 'name' => 'Gestion de Cursos', 'type' => 'Route', 'path' => 'AdminPvCgshges39ControllerGetIndex', 'color' => null, 'icon' => 'fa fa-university', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => 19, 'created_at' => '2025-11-07 22:00:41', 'updated_at' => null],
    ['id' => 103, 'name' => 'Cierre Escolar', 'type' => 'URL', 'path' => '/admin/cierre_academico', 'color' => 'green', 'icon' => 'fa fa-cog', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => null, 'created_at' => '2025-11-09 00:57:33', 'updated_at' => null],
     ['id' => 104, 'name' => 'Insolventes', 'type' => 'URL', 'path' => '/admin/pagos/insolventes', 'color' => 'green', 'icon' => 'fa fa-money', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => null, 'created_at' => '2025-11-16 06:21:52', 'updated_at' => null],
      ['id' => 105, 'name' => 'Cronograma de Tareas', 'type' => 'URL', 'path' => '/admin/calendario-tareaspares', 'color' => 'green', 'icon' => 'fa fa-calendar', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 1, 'id_cms_privileges' => 1, 'sorting' => null, 'created_at' => '2025-11-18 05:31:18', 'updated_at' => null],
       ['id' => 106, 'name' => 'Notificaciones Reset', 'type' => 'URL', 'path' => '/admin/notifications/clear-view', 'color' => 'red', 'icon' => 'fa fa-remove', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => null, 'created_at' => '2025-11-18 05:31:18', 'updated_at' => null],
        ['id' => 107, 'name' => 'Exonerados', 'type' => 'URL', 'path' => '/admin/pagos/exonerados', 'color' => 'normal', 'icon' => 'fa fa-money', 'parent_id' => 0, 'is_active' => 1, 'is_dashboard' => 0, 'id_cms_privileges' => 1, 'sorting' => null, 'created_at' => '2025-11-18 05:31:18', 'updated_at' => null],

    ];

        foreach ($menus as $menu) {
            DB::table('cms_menus')->updateOrInsert(
                ['id' => $menu['id']], // evita duplicados por ID
                $menu
            );
        }
    }
}
