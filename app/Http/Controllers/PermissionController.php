<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Traits\LogsActivity;

class PermissionController extends Controller
{
    use LogsActivity;

    // Obtener todos los permisos (GET /api/permissions)
    public function index()
    {
        $permissions = Permission::all();
        return response()->json(['data' => $permissions], 200);
    }

    // Crear un nuevo permiso (POST /api/permissions)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $permission = new Permission();
        $permission->name = $request->name;
        $permission->description = $request->description;
        $permission->save();

        // Lógica para crear un item
        $this->logActivity('create_item', 'Usuario registro un permiso ID: ' . $permission->id);
        return response()->json(['data' => $permission], 201);
    }


    // Actualizar un permiso (PUT/PATCH /api/permissions/{id})
    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['message' => 'Permission no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:permissions,name,' . $id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('name')) {
            $permission->name = $request->name;
        }

        if ($request->has('description')) {
            $permission->description = $request->description;
        }

        $permission->save();

        // Lógica para actualizar un item
        $this->logActivity('update_item', 'Usuario actualizó permiso ID :' . $permission->id);
        return response()->json(['data' => $permission], 200);
    }

    // Eliminar un permiso (DELETE /api/permissions/{id})
    public function destroy($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['message' => 'Permission no encontrado'], 404);
        }

        $permission->delete();
        // Lógica para eliminar un item
        $this->logActivity('delete_item', 'Usuario eliminó permiso ID :' . $permission->id);
        return response()->json(['message' => 'Permission deleted successfully'], 204);
    }
}
