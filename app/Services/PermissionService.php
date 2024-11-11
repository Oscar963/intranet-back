<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermissionService
{
    public function getAllPermissions()
    {
        return Permission::all();
    }

    public function createPermission(array $data)
    {
        $permission = new Permission();
        $permission->name = $data['name'];
        $permission->description = $data['description'] ?? null;
        $permission->save();

        return $permission;
    }

    public function getPermissionById($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            throw new ModelNotFoundException("Permission not found");
        }

        return $permission;
    }

    public function updatePermission($id, array $data)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            throw new ModelNotFoundException("Permission not found");
        }

        if (isset($data['name'])) {
            $permission->name = $data['name'];
        }

        if (isset($data['description'])) {
            $permission->description = $data['description'];
        }

        $permission->save();

        return $permission;
    }

    public function deletePermission($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            throw new ModelNotFoundException("Permission not found");
        }

        $permission->delete();
        return true;
    }
}
