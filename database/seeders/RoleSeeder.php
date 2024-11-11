<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::create(['nombre' => 'Administrator', 'guard_name' => 'administrator']);
        $admin->permissions()->attach(Permission::all());

        $dimao = Role::create(['nombre' => 'Usuario DIMAO', 'guard_name' => 'dimao']);
        $dimao->permissions()->attach(Permission::all());

        $revisor = Role::create(['nombre' => 'Usuario Revisor', 'guard_name' => 'revisor']);
        $revisor->permissions()->attach(Permission::all());
    }
}
