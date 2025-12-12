<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CmsSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['id' => 1, 'name' => 'login_background_color', 'content' => null, 'content_input_type' => 'text', 'dataenum' => null, 'helper' => 'Input hexacode', 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Estilo de página de registro', 'label' => 'Login Background Color'],
            ['id' => 2, 'name' => 'login_font_color', 'content' => null, 'content_input_type' => 'text', 'dataenum' => null, 'helper' => 'Input hexacode', 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Estilo de página de registro', 'label' => 'Login Font Color'],
            ['id' => 3, 'name' => 'login_background_image', 'content' => 'uploads/2025-05/f6b92e962ccc51c49e5cca54a75dd66e.png', 'content_input_type' => 'upload_image', 'dataenum' => null, 'helper' => null, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Estilo de página de registro', 'label' => 'Login Background Image'],
            ['id' => 4, 'name' => 'email_sender', 'content' => 'mail.smtp2go.com', 'content_input_type' => 'text', 'dataenum' => null, 'helper' => null, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Ajustes de Correo', 'label' => 'Email Sender'],
            ['id' => 5, 'name' => 'smtp_driver', 'content' => 'smtp', 'content_input_type' => 'select', 'dataenum' => 'smtp,mail,sendmail', 'helper' => null, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Ajustes de Correo', 'label' => 'Mail Driver'],
            ['id' => 6, 'name' => 'smtp_host', 'content' => 'mail.smtp2go.com', 'content_input_type' => 'text', 'dataenum' => null, 'helper' => null, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Ajustes de Correo', 'label' => 'SMTP Host'],
            ['id' => 7, 'name' => 'smtp_port', 'content' => '2525', 'content_input_type' => 'text', 'dataenum' => null, 'helper' => 'default 25', 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Ajustes de Correo', 'label' => 'SMTP Port'],
            ['id' => 8, 'name' => 'smtp_username', 'content' => 'notificaciones@colegiooxford.edu.gt', 'content_input_type' => 'text', 'dataenum' => null, 'helper' => null, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Ajustes de Correo', 'label' => 'SMTP Username'],
            ['id' => 9, 'name' => 'smtp_password', 'content' => 'XjqWYx62OEtsnSX9', 'content_input_type' => 'text', 'dataenum' => null, 'helper' => null, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Ajustes de Correo', 'label' => 'SMTP Password'],
            ['id' => 10, 'name' => 'appname', 'content' => 'School System', 'content_input_type' => 'text', 'dataenum' => null, 'helper' => null, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => '2025-01-04 11:12:50', 'group_setting' => 'Ajustes de Aplicaciones', 'label' => 'Nombre de la Aplicacion'],
            ['id' => 11, 'name' => 'default_paper_size', 'content' => 'Legal', 'content_input_type' => 'text', 'dataenum' => null, 'helper' => 'Paper size, ex : A4, Legal, etc', 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Ajustes de Aplicaciones', 'label' => 'Default Paper Print Size'],
            ['id' => 12, 'name' => 'logo', 'content' => 'uploads/2025-10/2e230c596dd3c9e376945bb270a52319.png', 'content_input_type' => 'upload_image', 'dataenum' => null, 'helper' => null, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Ajustes de Aplicaciones', 'label' => 'Logo'],
            ['id' => 13, 'name' => 'favicon', 'content' => 'uploads/2025-05/ca0290f3ce3849fb9ecfd906bc66ed47.png', 'content_input_type' => 'upload_image', 'dataenum' => null, 'helper' => null, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Ajustes de Aplicaciones', 'label' => 'Favicon'],
            ['id' => 14, 'name' => 'api_debug_mode', 'content' => 'true', 'content_input_type' => 'select', 'dataenum' => 'true,false', 'helper' => null, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Ajustes de Aplicaciones', 'label' => 'API Debug Mode'],
            ['id' => 15, 'name' => 'google_api_key', 'content' => null, 'content_input_type' => 'text', 'dataenum' => null, 'helper' => null, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Ajustes de Aplicaciones', 'label' => 'Google API Key'],
            ['id' => 16, 'name' => 'google_fcm_key', 'content' => null, 'content_input_type' => 'text', 'dataenum' => null, 'helper' => null, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null, 'group_setting' => 'Ajustes de Aplicaciones', 'label' => 'Google FCM Key'],
            ['id' => 17, 'name' => 'background_image', 'content' => 'uploads/2025-05/6422c16c695290c8d9e23474cbddc44f.png', 'content_input_type' => 'upload_image', 'dataenum' => 'Admin', 'helper' => null, 'created_at' => '2024-12-04 05:26:51', 'updated_at' => '2024-12-04 10:09:24', 'group_setting' => 'Página de Inicio', 'label' => 'Fondo de la Pagina'],
            ['id' => 18, 'name' => 'background_size', 'content' => 'cover', 'content_input_type' => 'select', 'dataenum' => 'contain, none,cover', 'helper' => null, 'created_at' => '2024-12-04 05:31:06', 'updated_at' => '2024-12-04 10:09:47', 'group_setting' => 'Página de Inicio', 'label' => 'Tamaño o Dimension del fondo'],
            ['id' => 19, 'name' => 'color', 'content' => '12', 'content_input_type' => 'number', 'dataenum' => '1,2,3', 'helper' => 'dasd', 'created_at' => '2025-03-26 14:20:47', 'updated_at' => null, 'group_setting' => 'General Setting', 'label' => 'Color'],
            ['id' => 20, 'name' => 'logo_reporte', 'content' => 'uploads/2025-11/3c6089ddb8e37efc7736cbc8a81dfd05.jpg', 'content_input_type' => 'upload_image', 'dataenum' => null, 'helper' => null, 'created_at' => '2025-10-30 19:24:39', 'updated_at' => null, 'group_setting' => 'Reporte de Sistema', 'label' => 'Logo'],
            ['id' => 21, 'name' => 'nombre_del_establecimiento', 'content' => 'OXFORD BILINGUAL SCHOOL PRUEBAS', 'content_input_type' => 'text', 'dataenum' => null, 'helper' => null, 'created_at' => '2025-10-30 19:25:06', 'updated_at' => null, 'group_setting' => 'Reporte de Sistema', 'label' => 'Nombre del Establecimiento'],
            ['id' => 22, 'name' => 'direccion_del_establecimiento', 'content' => '2a. calle 15-84 zona 4, Cobán, Alta Verapaz', 'content_input_type' => 'text', 'dataenum' => null, 'helper' => null, 'created_at' => '2025-10-30 19:25:22', 'updated_at' => null, 'group_setting' => 'Reporte de Sistema', 'label' => 'Direccion del Establecimiento'],
            ['id' => 23, 'name' => 'numero_de_telefono', 'content' => '7951-3898', 'content_input_type' => 'text', 'dataenum' => null, 'helper' => null, 'created_at' => '2025-10-30 19:25:47', 'updated_at' => null, 'group_setting' => 'Reporte de Sistema', 'label' => 'Numero de Telefono'],
            ['id' => 24, 'name' => 'comentario', 'content' => 'Comentario', 'content_input_type' => 'textarea', 'dataenum' => null, 'helper' => null, 'created_at' => '2025-10-31 15:05:08', 'updated_at' => null, 'group_setting' => 'Reporte de Sistema', 'label' => 'Comentario'],
            ['id' => 25, 'name' => 'logo_recibointerno', 'content' => 'uploads/2025-11/950a0f1a1e180dd6e1fab4156e57bec8.png', 'content_input_type' => 'upload_image', 'dataenum' => null, 'helper' => null, 'created_at' => '2025-11-03 22:09:09', 'updated_at' => null, 'group_setting' => 'Recibo Interno', 'label' => 'logo_recibo'],
            ['id' => 26, 'name' => 'direccion_recibointerno', 'content' => 'Coban', 'content_input_type' => 'text', 'dataenum' => null, 'helper' => null, 'created_at' => '2025-11-03 22:10:31', 'updated_at' => null, 'group_setting' => 'Recibo Interno', 'label' => 'direccion_recibo'],
            ['id' => 27, 'name' => 'telefono_recibointerno', 'content' => '3434-34343', 'content_input_type' => 'text', 'dataenum' => null, 'helper' => null, 'created_at' => '2025-11-03 22:11:32', 'updated_at' => null, 'group_setting' => 'Recibo Interno', 'label' => 'telefono_recibointerno'],
            ['id' => 28, 'name' => 'nombre_corto_del_establecimiento', 'content' => 'OXFORD', 'content_input_type' => 'text', 'dataenum' => null, 'helper' => null, 'created_at' => '2025-11-06 01:39:43', 'updated_at' => null, 'group_setting' => 'Recibo Interno', 'label' => 'nombre corto del establecimiento'],
        ];

        foreach ($settings as $setting) {
            DB::table('cms_settings')->updateOrInsert(
                ['name' => $setting['name']], // condición para evitar duplicados
                $setting // datos a insertar o actualizar
            );
        }
    }
}
