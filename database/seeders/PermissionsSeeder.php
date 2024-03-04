<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listPermissions = [
            'create-roles',
            'read-roles',
            'update-roles',
            'delete-roles',
            'assign-permissions',
            'create-permissions',
            'read-permissions',
            'update-permissions',
            'delete-permissions',
        ];

        foreach ($listPermissions as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        }
    }
}
