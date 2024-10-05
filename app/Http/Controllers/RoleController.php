<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Traits\LogsActivity;

class RoleController extends Controller
{
    use LogsActivity;

    // Obtener todos los roles (GET /api/roles)
    public function index()
    {
        $roles = Role::with(['permissions'])->get(['id', 'nombre']);
        return response()->json(['data' => $roles], 200);
    }

    // Crear un nuevo rol (POST /api/roles)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:20|unique:roles,nombre',
            'permisos' => 'array', // Validación de permisos
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = new Role();
        $role->nombre = $request->nombre;
        $role->guard_name = str_replace('-', '_', Str::slug($request->nombre));
        $role->save();

        if ($request->has('permisos')) {
            $role->permissions()->attach($request->permisos);
        }

        // Lógica para crear un item
        $this->logActivity('create_item', 'Usuario registro un rol ID :' . $role->id);
        return response()->json(['message' => 'Se ha registrado un nuevo rol', 'data' => $role], 201);
    }

    // Obtener un rol específico (GET /api/roles/{id})
    public function show($id)
    {
        $role = Role::with(['permissions'])->find($id);

        if (!$role) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        return response()->json(['data' => $role], 200);
    }

    // Actualizar un rol (PUT/PATCH /api/roles/{id})
    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:20|unique:roles,nombre,' . $id,
            'permisos' => 'array', // Validación de permisos
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role->nombre = $request->nombre;
        $role->guard_name = str_replace('-', '_', Str::slug($request->nombre));
        $role->save();

        if ($request->has('permisos')) {
            $role->permissions()->sync($request->permisos);
        }

        // Lógica para actualizar un item
        $this->logActivity('update_item', 'Usuario actualizó rol ID :' . $role->id);
        return response()->json(['message' => 'Rol actualizado', 'data' => $role], 200);
    }

    // Eliminar un rol (DELETE /api/roles/{id})
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        $role->delete();

        // Lógica para eliminar un item
        $this->logActivity('delete_item', 'Usuario eliminó rol ID :' . $role->id);
        return response()->json(['message' => 'Rol eliminado con éxito'], 204);
    }
}
