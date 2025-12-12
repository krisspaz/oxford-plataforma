<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CmsPrivilegesRolesSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['id' => 1, 'is_visible' => 1, 'is_create' => 0, 'is_read' => 0, 'is_edit' => 0, 'is_delete' => 0, 'id_cms_privileges' => 1, 'id_cms_moduls' => 1, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null],
            ['id' => 2, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 2, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null],
            ['id' => 3, 'is_visible' => 0, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 3, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null],
            ['id' => 4, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 4, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null],
            ['id' => 5, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 5, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null],
            ['id' => 6, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 6, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null],
            ['id' => 7, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 7, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null],
            ['id' => 8, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 8, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null],
            ['id' => 9, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 9, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null],
            ['id' => 10, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 10, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null],
            ['id' => 11, 'is_visible' => 1, 'is_create' => 0, 'is_read' => 1, 'is_edit' => 0, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 11, 'created_at' => '2024-11-14 07:03:16', 'updated_at' => null],
            ['id' => 12, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 12, 'created_at' => null, 'updated_at' => null],
            ['id' => 13, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 13, 'created_at' => null, 'updated_at' => null],
            ['id' => 14, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 14, 'created_at' => null, 'updated_at' => null],
            ['id' => 15, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 16, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 16, 'created_at' => null, 'updated_at' => null],
            ['id' => 17, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 17, 'created_at' => null, 'updated_at' => null],
            ['id' => 18, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 18, 'created_at' => null, 'updated_at' => null],
            ['id' => 19, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 19, 'created_at' => null, 'updated_at' => null],
            ['id' => 20, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 20, 'created_at' => null, 'updated_at' => null],
            ['id' => 21, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 21, 'created_at' => null, 'updated_at' => null],
            ['id' => 22, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 22, 'created_at' => null, 'updated_at' => null],
            ['id' => 23, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 23, 'created_at' => null, 'updated_at' => null],
            ['id' => 24, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 24, 'created_at' => null, 'updated_at' => null],
            ['id' => 25, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 25, 'created_at' => null, 'updated_at' => null],
            ['id' => 26, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 26, 'created_at' => null, 'updated_at' => null],
            ['id' => 27, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 27, 'created_at' => null, 'updated_at' => null],
            ['id' => 28, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 28, 'created_at' => null, 'updated_at' => null],
            ['id' => 29, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 29, 'created_at' => null, 'updated_at' => null],
            ['id' => 30, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 30, 'created_at' => null, 'updated_at' => null],
            ['id' => 31, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 7, 'id_cms_moduls' => 24, 'created_at' => null, 'updated_at' => null],
            ['id' => 32, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 7, 'id_cms_moduls' => 16, 'created_at' => null, 'updated_at' => null],
            ['id' => 33, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 7, 'id_cms_moduls' => 23, 'created_at' => null, 'updated_at' => null],
            ['id' => 34, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 7, 'id_cms_moduls' => 13, 'created_at' => null, 'updated_at' => null],
            ['id' => 35, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 7, 'id_cms_moduls' => 12, 'created_at' => null, 'updated_at' => null],
            ['id' => 36, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 7, 'id_cms_moduls' => 21, 'created_at' => null, 'updated_at' => null],
            ['id' => 37, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 7, 'id_cms_moduls' => 4, 'created_at' => null, 'updated_at' => null],
            ['id' => 38, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 7, 'id_cms_moduls' => 17, 'created_at' => null, 'updated_at' => null],
            ['id' => 39, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 7, 'id_cms_moduls' => 20, 'created_at' => null, 'updated_at' => null],
            ['id' => 40, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 7, 'id_cms_moduls' => 26, 'created_at' => null, 'updated_at' => null],
            ['id' => 41, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 7, 'id_cms_moduls' => 15, 'created_at' => null, 'updated_at' => null],
            ['id' => 42, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 7, 'id_cms_moduls' => 19, 'created_at' => null, 'updated_at' => null],
            ['id' => 43, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 7, 'id_cms_moduls' => 25, 'created_at' => null, 'updated_at' => null],
            ['id' => 44, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 7, 'id_cms_moduls' => 18, 'created_at' => null, 'updated_at' => null],
            ['id' => 45, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 31, 'created_at' => null, 'updated_at' => null],
            ['id' => 47, 'is_visible' => 1, 'is_create' => 0, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 6, 'id_cms_moduls' => 4, 'created_at' => null, 'updated_at' => null],
            ['id' => 49, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 32, 'created_at' => null, 'updated_at' => null],
            ['id' => 50, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 33, 'created_at' => null, 'updated_at' => null],
            ['id' => 51, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 34, 'created_at' => null, 'updated_at' => null],
            ['id' => 52, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 35, 'created_at' => null, 'updated_at' => null],
            ['id' => 53, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 36, 'created_at' => null, 'updated_at' => null],
            ['id' => 54, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 37, 'created_at' => null, 'updated_at' => null],
            ['id' => 55, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 38, 'created_at' => null, 'updated_at' => null],
            ['id' => 56, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 39, 'created_at' => null, 'updated_at' => null],
            ['id' => 57, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 40, 'created_at' => null, 'updated_at' => null],
            ['id' => 58, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 41, 'created_at' => null, 'updated_at' => null],
            ['id' => 59, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 42, 'created_at' => null, 'updated_at' => null],
            ['id' => 60, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 43, 'created_at' => null, 'updated_at' => null],
            ['id' => 61, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 44, 'created_at' => null, 'updated_at' => null],
            ['id' => 62, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 45, 'created_at' => null, 'updated_at' => null],
            ['id' => 63, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 46, 'created_at' => null, 'updated_at' => null],
            ['id' => 64, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 47, 'created_at' => null, 'updated_at' => null],
            ['id' => 65, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 48, 'created_at' => null, 'updated_at' => null],
            ['id' => 66, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 49, 'created_at' => null, 'updated_at' => null],
            ['id' => 67, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 50, 'created_at' => null, 'updated_at' => null],
            ['id' => 68, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 51, 'created_at' => null, 'updated_at' => null],
            ['id' => 69, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 52, 'created_at' => null, 'updated_at' => null],
            ['id' => 70, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 53, 'created_at' => null, 'updated_at' => null],
            ['id' => 71, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 54, 'created_at' => null, 'updated_at' => null],
            ['id' => 72, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 55, 'created_at' => null, 'updated_at' => null],
            ['id' => 73, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 56, 'created_at' => null, 'updated_at' => null],
            ['id' => 74, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 57, 'created_at' => null, 'updated_at' => null],
            ['id' => 75, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 58, 'created_at' => null, 'updated_at' => null],
            ['id' => 76, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 59, 'created_at' => null, 'updated_at' => null],
            ['id' => 77, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 60, 'created_at' => null, 'updated_at' => null],
            ['id' => 78, 'is_visible' => 1, 'is_create' => 1, 'is_read' => 1, 'is_edit' => 1, 'is_delete' => 1, 'id_cms_privileges' => 1, 'id_cms_moduls' => 61, 'created_at' => null, 'updated_at' => null],
        ];

        foreach ($roles as $role) {
            DB::table('cms_privileges_roles')->updateOrInsert(
                ['id' => $role['id']],
                $role
            );
        }
    }
}
