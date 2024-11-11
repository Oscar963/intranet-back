<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as PasswordRules;
use App\Rules\RutValidation;
use Illuminate\Support\Facades\Hash;

use App\Traits\LogsActivity;

class UserController extends Controller
{
    use LogsActivity;

    // Obtener todos los usuarios (GET /api/users)
    public function index()
    {
        $users = User::with(['roles'])->get();
        return response()->json(['data' => $users], 200);
    }

    // Crear un nuevo usuario (POST /api/users)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:15|string',
            'rut' => ['required', 'string', 'unique:users', new RutValidation],
            'email' => 'required|string|email|max:255|unique:users',
            'apellido_paterno' =>  'required|max:20|string',
            'apellido_materno' =>  'required|max:20|string',
            'contrasena_nueva' => [
                'required', PasswordRules::defaults() // Utilizar las políticas de contraseña configuradas
            ],
            'contrasena_confirmar' => [
                'required', 'same:contrasena_nueva'
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = new User();
        $user->rut = $request->rut;
        $user->nombre = $request->nombre;
        $user->apellido_paterno = $request->apellido_paterno;
        $user->apellido_materno = $request->apellido_materno;
        $user->estado = $request->estado;
        $user->email = $request->email;

        if ($request->has('contrasena_nueva')) {
            $user->password = Hash::make($request->contrasena_nueva);
        }
        $user->save();

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        // Lógica para crear un item
        $this->logActivity('create_item', 'Usuario registro un usuario ID :' . $user->id);
        return response()->json(['message' => 'Se ha registrado un nuevo usuario', 'data' => $user], 201);
    }

    // Obtener un usuario específico (GET /api/users/{id})
    public function show($id)
    {
        $user = User::with(['roles'])->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json(['data' => $user], 200);
    }

    // Actualizar un usuario (PUT/PATCH /api/users/{id})
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:15|string',
            'rut' => ['required', 'string', 'unique:users,rut,' . $id, new RutValidation],
            'email' => 'required|max:100|email|unique:users,email,' . $id,
            'apellido_paterno' =>  'required|max:20|string',
            'apellido_materno' =>  'required|max:20|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->rut = $request->rut;
        $user->nombre = $request->nombre;
        $user->apellido_paterno = $request->apellido_paterno;
        $user->apellido_materno = $request->apellido_materno;
        $user->estado = $request->estado;
        $user->email = $request->email;
        $user->save();

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        $userUpdate = User::with(['roles'])->find($user->id);
        $data = [
            'nombre' => $userUpdate->nombre,
            'apellido_paterno' => $userUpdate->apellido_paterno,
            'apellido_materno' => $userUpdate->apellido_materno,
            'email' => $userUpdate->email,
            'rut' => $userUpdate->rut,
            'roles' => $userUpdate->roles()->pluck('nombre')->unique()->values()->toArray(),
        ];

        // Lógica para actualizar un item
        $this->logActivity('update_item', 'Usuario actualizó usuario ID :' . $user->id);
        return response()->json(['message' => 'Usuario actualizado', 'data' => $data], 200);
    }

    // Eliminar un usuario (DELETE /api/users/{id})
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $user->delete();

        // Lógica para eliminar un item
        $this->logActivity('delete_item', 'Usuario eliminó usuario ID :' . $user->id);
        return response()->json(['message' => 'Usuario eliminado con éxito'], 204);
    }
}
