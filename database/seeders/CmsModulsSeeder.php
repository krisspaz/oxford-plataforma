<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class CmsModulsSeeder extends Seeder
{

    public function run()
    {
        $data = [
            ['id' => 1, 'name' => 'Notificaciones', 'icon' => 'fa fa-cog', 'path' => 'notifications', 'table_name' => 'cms_notifications', 'controller' => 'NotificationsController', 'is_protected' => 1, 'is_active' => 1, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 2, 'name' => 'Privilegios', 'icon' => 'fa fa-cog', 'path' => 'privileges', 'table_name' => 'cms_privileges', 'controller' => 'PrivilegesController', 'is_protected' => 1, 'is_active' => 1, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 3, 'name' => 'Privilegios & Roles', 'icon' => 'fa fa-cog', 'path' => 'privileges_roles', 'table_name' => 'cms_privileges_roles', 'controller' => 'PrivilegesRolesController', 'is_protected' => 1, 'is_active' => 1, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 4, 'name' => 'Gestión de usuarios', 'icon' => 'fa fa-users', 'path' => 'users', 'table_name' => 'cms_users', 'controller' => 'AdminCmsUsersController', 'is_protected' => 0, 'is_active' => 1, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 5, 'name' => 'Ajustes', 'icon' => 'fa fa-cog', 'path' => 'settings', 'table_name' => 'cms_settings', 'controller' => 'SettingsController', 'is_protected' => 1, 'is_active' => 1, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 6, 'name' => 'Generador de Módulos', 'icon' => 'fa fa-database', 'path' => 'module_generator', 'table_name' => 'cms_moduls', 'controller' => 'ModulsController', 'is_protected' => 1, 'is_active' => 1, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 7, 'name' => 'Gestión de Menús', 'icon' => 'fa fa-bars', 'path' => 'menu_management', 'table_name' => 'cms_menus', 'controller' => 'MenusController', 'is_protected' => 1, 'is_active' => 1, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 8, 'name' => 'Plantillas de Correo', 'icon' => 'fa fa-envelope-o', 'path' => 'email_templates', 'table_name' => 'cms_email_templates', 'controller' => 'EmailTemplatesController', 'is_protected' => 1, 'is_active' => 1, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 9, 'name' => 'Generador de Estadísticas', 'icon' => 'fa fa-dashboard', 'path' => 'statistic_builder', 'table_name' => 'cms_statistics', 'controller' => 'StatisticBuilderController', 'is_protected' => 1, 'is_active' => 1, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 10, 'name' => 'Generador de API', 'icon' => 'fa fa-cloud-download', 'path' => 'api_generator', 'table_name' => '', 'controller' => 'ApiCustomController', 'is_protected' => 1, 'is_active' => 1, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 11, 'name' => 'Log de Accesos (Usuarios)', 'icon' => 'fa fa-flag-o', 'path' => 'logs', 'table_name' => 'cms_logs', 'controller' => 'LogsController', 'is_protected' => 1, 'is_active' => 1, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 12, 'name' => 'Gestion Academica', 'icon' => 'fa fa-university', 'path' => 'gestionesacademicas', 'table_name' => 'gestionesacademicas', 'controller' => 'AdminGestionesacademicasController', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2024-11-14 07:09:26', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 13, 'name' => 'Estados', 'icon' => 'fa fa-check', 'path' => 'tb_estados', 'table_name' => 'tb_estados', 'controller' => 'AdminTbEstadosController', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2024-11-14 07:12:14', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 16, 'name' => 'Cursos', 'icon' => 'fa fa-newspaper-o', 'path' => 'cursos', 'table_name' => 'cursos', 'controller' => 'AdminCursosController', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2024-11-14 07:53:01', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 17, 'name' => 'Grados', 'icon' => 'fa fa-newspaper-o', 'path' => 'tb_grados', 'table_name' => 'tb_grados', 'controller' => 'AdminTbGradosController', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2024-11-14 08:52:18', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 18, 'name' => 'Secciones', 'icon' => 'fa fa-newspaper-o', 'path' => 'secciones', 'table_name' => 'secciones', 'controller' => 'AdminSeccionesController', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2024-11-14 09:30:12', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 19, 'name' => 'Parentescos', 'icon' => 'fa fa-user-plus', 'path' => 'tb_parentescos', 'table_name' => 'tb_parentescos', 'controller' => 'AdminTbParentescosController', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2024-11-14 09:52:01', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 20, 'name' => 'Jornadas y Horarios', 'icon' => 'fa fa-times-circle', 'path' => 'pv_jornada_dia_horarios', 'table_name' => 'pv_jornada_dia_horarios', 'controller' => 'AdminPvJornadaDiaHorariosController', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2024-11-22 08:12:32', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 21, 'name' => 'Gestion de Cursos', 'icon' => 'fa fa-university', 'path' => 'pv_cgshges39', 'table_name' => 'pv_cgshges', 'controller' => 'AdminPvCgshges39Controller', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2024-11-22 08:27:41', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 23, 'name' => 'Documentos de Identificación', 'icon' => 'fa fa-user-plus', 'path' => 'tb_identificacion_documentos', 'table_name' => 'tb_identificacion_documentos', 'controller' => 'AdminTbIdentificacionDocumentosController', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2024-12-05 06:34:04', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 24, 'name' => 'Costos por Nivel', 'icon' => 'fa fa-money', 'path' => 'tb_costos_niveles', 'table_name' => 'tb_costos_niveles', 'controller' => 'AdminTbCostosNivelesController', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2024-12-10 05:35:57', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 26, 'name' => 'Materias', 'icon' => 'fa fa-newspaper-o', 'path' => 'materias', 'table_name' => 'materias', 'controller' => 'AdminMateriasController', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2024-12-16 16:51:11', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 31, 'name' => 'Cuadros', 'icon' => 'fa fa-glass', 'path' => 'tb_cuadro_notas', 'table_name' => 'tb_cuadro_notas', 'controller' => 'AdminTbCuadroNotasController', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2024-12-31 11:14:09', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 32, 'name' => 'Gestion Bimestral', 'icon' => 'fa fa-newspaper-o', 'path' => 'bimestres', 'table_name' => 'bimestres', 'controller' => 'AdminBimestresController', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2025-01-20 13:29:03', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 33, 'name' => 'Gestión de Materias', 'icon' => 'fa fa-newspaper-o', 'path' => 'gestion_materias', 'table_name' => 'gestion_materias', 'controller' => 'AdminGestionMateriasController', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2025-02-06 01:06:00', 'updated_at' => null, 'deleted_at' => null],
            ['id' => 38, 'name' => 'Cargos Administrativos', 'icon' => 'fa fa-newspaper-o', 'path' => 'cargos', 'table_name' => 'cargos', 'controller' => 'AdminCargos1Controller', 'is_protected' => 0, 'is_active' => 0, 'created_at' => '2025-04-22 18:47:21', 'updated_at' => null, 'deleted_at' => null],
        ];

        foreach ($data as $item) {
            DB::table('cms_moduls')->updateOrInsert(
                ['id' => $item['id']],
                $item
            );
        }
    }
}
