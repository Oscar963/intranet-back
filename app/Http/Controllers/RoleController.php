<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Traits\LogsActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class RoleController extends Controller
{
    use LogsActivity;

    protected  $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of roles.
     */
    public function index(): JsonResponse
    {
        try {
            $roles = $this->roleService->getAllRoles();
            return response()->json(['data' => RoleResource::collection($roles)], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al obtener los roles.'], 500);
        }
    }

    /**
     * Store a newly created role.
     */
    public function store(RoleRequest $request): JsonResponse
    {
        try {
            $role = $this->roleService->createRole($request->validated());
            $this->logActivity('create_role', 'Usuario creó un rol con ID: ' . $role->id);
            return response()->json(['message' => 'Rol creado con éxito!', 'data' => new RoleResource($role)], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al crear el rol.'], 500);
        }
    }

    /**
     * Display the specified role.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $role = $this->roleService->getRoleById($id);
            return response()->json(['data' => new RoleResource($role)], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Rol no encontrado.'], 404);
        }
    }

    /**
     * Update the specified role.
     */
    public function update(RoleRequest $request, int $id): JsonResponse
    {
        try {
            $role = $this->roleService->updateRole($id, $request->validated());
            $this->logActivity('update_role', 'Usuario actualizó el rol con ID: ' . $role->id);
            return response()->json(['message' => 'Rol actualizado con éxito!', 'data' => new RoleResource($role)], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al actualizar el rol.'], 500);
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->roleService->deleteRole($id);
            $this->logActivity('delete_role', 'Usuario eliminó el rol con ID: ' . $id);
            return response()->json(['message' => 'Rol eliminado con éxito'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al eliminar el rol.'], 500);
        }
    }
}
