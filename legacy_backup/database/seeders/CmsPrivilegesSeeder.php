<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CmsPrivilegesSeeder extends Seeder
{

    public function run()
    {
        $privileges = [
            ['id' => 1, 'name' => 'Super Administrator', 'is_superadmin' => 1, 'theme_color' => 'skin-red', 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null],
            ['id' => 2, 'name' => 'ESTUDIANTE', 'is_superadmin' => 0, 'theme_color' => 'skin-blue', 'created_at' => null, 'updated_at' => null],
            ['id' => 3, 'name' => 'PADRES', 'is_superadmin' => 0, 'theme_color' => 'skin-blue', 'created_at' => null, 'updated_at' => null],
            ['id' => 4, 'name' => 'ENCARGADO', 'is_superadmin' => 0, 'theme_color' => 'skin-blue', 'created_at' => null, 'updated_at' => null],
            ['id' => 5, 'name' => 'SECRETARIA', 'is_superadmin' => 0, 'theme_color' => 'skin-blue', 'created_at' => null, 'updated_at' => null],
            ['id' => 6, 'name' => 'DOCENTE', 'is_superadmin' => 0, 'theme_color' => 'skin-blue', 'created_at' => null, 'updated_at' => null],
            ['id' => 7, 'name' => 'ADMINISTRATIVO', 'is_superadmin' => 0, 'theme_color' => 'skin-blue', 'created_at' => null, 'updated_at' => null],
            ['id' => 8, 'name' => 'COORDINACION ACADEMICA', 'is_superadmin' => 0, 'theme_color' => 'skin-blue', 'created_at' => null, 'updated_at' => null],
            ['id' => 9, 'name' => 'DIRECTOR', 'is_superadmin' => 0, 'theme_color' => 'skin-blue', 'created_at' => null, 'updated_at' => null],
        ];

        foreach ($privileges as $privilege) {
            // Evita duplicar si ya existe por ID o nombre
            DB::table('cms_privileges')->updateOrInsert(
                ['id' => $privilege['id']], // criterio de coincidencia
                $privilege // valores a insertar o actualizar
            );
        }
    }
}
