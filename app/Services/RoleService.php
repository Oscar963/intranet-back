<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleService
{
    public function getAllRoles($perPage = 10)
    {
        return Role::with('permissions')->paginate($perPage);
    }

    public function createRole(array $data)
    {
        $role = new Role();
        $role->nombre = $data['nombre'];
        $role->guard_name = str_replace('-', '_', Str::slug($data['nombre']));
        $role->save();

        if (!empty($data['permisos'])) {
            $role->permissions()->attach($data['permisos']);
        }

        return $role;
    }

    public function getRoleById($id)
    {
        $role = Role::with('permissions')->find($id);
        if (!$role) {
            throw new ModelNotFoundException("Role not found");
        }

        return $role;
    }

    public function updateRole($id, array $data)
    {
        $role = Role::find($id);
        if (!$role) {
            throw new ModelNotFoundException("Role not found");
        }

        if (isset($data['nombre'])) {
            $role->nombre = $data['nombre'];
            $role->guard_name = str_replace('-', '_', Str::slug($data['nombre']));
        }

        $role->save();

        if (isset($data['permisos'])) {
            $role->permissions()->sync($data['permisos']);
        }

        return $role;
    }

    public function deleteRole($id)
    {
        $role = Role::find($id);
        if (!$role) {
            throw new ModelNotFoundException("Role not found");
        }

        $role->delete();
        return true;
    }
}
