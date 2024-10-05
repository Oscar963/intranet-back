<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['nombre' => 'rol.list', 'guard_name' => 'rol']);
        Permission::create(['nombre' => 'rol.create', 'guard_name' => 'rol']);
        Permission::create(['nombre' => 'rol.edit', 'guard_name' => 'rol']);
        Permission::create(['nombre' => 'rol.delete', 'guard_name' => 'rol']);
        Permission::create(['nombre' => 'rol.export', 'guard_name' => 'rol']);
        Permission::create(['nombre' => 'rol.import', 'guard_name' => 'rol']);
        Permission::create(['nombre' => 'rol.file', 'guard_name' => 'page']);

        Permission::create(['nombre' => 'logs.list', 'guard_name' => 'logs']);
        Permission::create(['nombre' => 'logs.create', 'guard_name' => 'logs']);
        Permission::create(['nombre' => 'logs.edit', 'guard_name' => 'logs']);
        Permission::create(['nombre' => 'logs.delete', 'guard_name' => 'logs']);
        Permission::create(['nombre' => 'logs.export', 'guard_name' => 'logs']);
        Permission::create(['nombre' => 'logs.import', 'guard_name' => 'logs']);
        Permission::create(['nombre' => 'logs.file', 'guard_name' => 'page']);

        Permission::create(['nombre' => 'users.list', 'guard_name' => 'user']);
        Permission::create(['nombre' => 'users.create', 'guard_name' => 'user']);
        Permission::create(['nombre' => 'users.edit', 'guard_name' => 'user']);
        Permission::create(['nombre' => 'users.delete', 'guard_name' => 'user']);
        Permission::create(['nombre' => 'users.export', 'guard_name' => 'user']);
        Permission::create(['nombre' => 'users.import', 'guard_name' => 'user']);
        Permission::create(['nombre' => 'users.file', 'guard_name' => 'page']);

        Permission::create(['nombre' => 'banner.list', 'guard_name' => 'banner']);
        Permission::create(['nombre' => 'banner.create', 'guard_name' => 'banner']);
        Permission::create(['nombre' => 'banner.edit', 'guard_name' => 'banner']);
        Permission::create(['nombre' => 'banner.delete', 'guard_name' => 'banner']);
        Permission::create(['nombre' => 'banner.export', 'guard_name' => 'banner']);
        Permission::create(['nombre' => 'banner.import', 'guard_name' => 'banner']);
        Permission::create(['nombre' => 'banner.file', 'guard_name' => 'page']);

        Permission::create(['nombre' => 'page.list', 'guard_name' => 'page']);
        Permission::create(['nombre' => 'page.create', 'guard_name' => 'page']);
        Permission::create(['nombre' => 'page.edit', 'guard_name' => 'page']);
        Permission::create(['nombre' => 'page.delete', 'guard_name' => 'page']);
        Permission::create(['nombre' => 'page.export', 'guard_name' => 'page']);
        Permission::create(['nombre' => 'page.import', 'guard_name' => 'page']);
        Permission::create(['nombre' => 'page.file', 'guard_name' => 'page']);
    }
}
