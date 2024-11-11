<?php

namespace App\Http\Controllers;

use App\Services\PermissionService;
use App\Http\Requests\PermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Traits\LogsActivity;

class PermissionController extends Controller
{
    use LogsActivity;

    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        $permissions = $this->permissionService->getAllPermissions();
        return response()->json(['data' => PermissionResource::collection($permissions)], 200);
    }

    public function store(PermissionRequest $request)
    {
        $permission = $this->permissionService->createPermission($request->validated());
        $this->logActivity('create_permission', 'Usuario creó un permiso con ID: ' . $permission->id);
        return response()->json(['data' => new PermissionResource($permission)], 201);
    }

    public function show($id)
    {
        $permission = $this->permissionService->getPermissionById($id);
        return response()->json(['data' => new PermissionResource($permission)], 200);
    }

    public function update(PermissionRequest $request, $id)
    {
        $permission = $this->permissionService->updatePermission($id, $request->validated());
        $this->logActivity('update_permission', 'Usuario actualizó permiso con ID: ' . $permission->id);
        return response()->json(['data' => new PermissionResource($permission)], 200);
    }

    public function destroy($id)
    {
        $this->permissionService->deletePermission($id);
        $this->logActivity('delete_permission', 'Usuario eliminó permiso con ID: ' . $id);
        return response()->json(['message' => 'Permission deleted successfully'], 204);
    }
}
