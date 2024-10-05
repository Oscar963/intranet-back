<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\RutValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRules;
use Illuminate\Support\Facades\Validator;

use App\Traits\LogsActivity;


class AuthController extends Controller
{
    use LogsActivity;


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rut' => ['required', 'string', new RutValidation()],
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $credentials = $request->only('rut', 'password');
        $remember = $request->boolean('remember', false);

        if (!Auth::attempt($credentials, $remember)) {
            return response()->json(['message' => 'Credenciales incorrectas. <br> Verifique su Rut y contraseña e intente nuevamente.'], 401);
        }

        $user = Auth::user();
        if (!$user->estado) {
            Auth::logout();
            return response()->json(['message' => 'Tu cuenta está suspendida. <br> Por favor contáctate con el administrador del sistema.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // TRAITS  Lógica de inicio de sesión
        $this->logActivity('login', 'Usuario inició sesión');

        return response()->json([
            'message' => 'Bienvenido(a) al sistema ' . $user->nombre,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'nombre' => $user->nombre,
                'apellido_paterno' => $user->apellido_paterno,
                'apellido_materno' => $user->apellido_materno,
                'email' => $user->email,
                'rut' => $user->rut
            ],
            'roles' => $user->roles()->pluck('nombre')->unique()->values()->toArray(),
            'permissions' => $user->roles()->with('permissions')->get()
                ->pluck('permissions')
                ->flatten()
                ->pluck('nombre')
                ->unique()
                ->values()
                ->toArray(),
        ], 200);
    }

    public function getPermissions(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'roles' => $user->roles()->pluck('nombre')->unique()->values()->toArray(),
            'permissions' => $user->roles()->with('permissions')->get()
                ->pluck('permissions')
                ->flatten()
                ->pluck('nombre')
                ->unique()
                ->values()
                ->toArray(),
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        // Lógica de cierre de sesión
        $this->logActivity('logout', 'Usuario cerró sesión');
        return response()->json(['message' => 'Sesión cerrada'], 200);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'No se encontró ningún usuario con esta dirección de correo electrónico.'], 404);
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Hemos enviado un enlace para restablecer tu contraseña a la dirección ' . $request->email], 200);
        } else {
            return response()->json(['message' => 'Error al enviar el enlace de restablecimiento de contraseña.'], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'confirmed',
                PasswordRules::defaults()
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }

        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Contraseña restablecida correctamente.'], 200);
        } else {
            return response()->json(['message' => 'Error al restablecer la contraseña.'], 500);
        }
    }

    public function actualizarPerfil(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:25|string',
            'apellido_paterno' => 'required|max:20|string',
            'apellido_materno' => 'required|max:20|string',
            'email' => 'required|max:100|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->nombre = $request->nombre;
        $user->apellido_paterno = $request->apellido_paterno;
        $user->apellido_materno = $request->apellido_materno;
        $user->email = $request->email;
        $user->save();

        $userUpdate = User::find($user->id);

        $data = [
            'nombre' => $userUpdate->nombre,
            'apellido_paterno' => $userUpdate->apellido_paterno,
            'apellido_materno' => $userUpdate->apellido_materno,
            'email' => $userUpdate->email,
            'rut' => $userUpdate->rut,
            'roles' => $userUpdate->roles()->pluck('nombre')->unique()->values()->toArray(),
        ];

        $this->logActivity('actualizar_perfil', 'Usuario actualizó su perfil');
        return response()->json(['message' => 'Usuario actualizado', 'data' => $data], 200);
    }

    public function cambiarContrasena(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'contrasena_actual' => 'required',
            'contrasena_nueva' => [
                'required', PasswordRules::defaults()
            ],
            'contrasena_confirmar' => [
                'required', 'same:contrasena_nueva'
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Hash::check($request->contrasena_actual, $user->password)) {
            return response()->json([
                'errors' => ['contrasena_actual' => ['La contraseña actual es incorrecta']]
            ], 422);
        }

        $user->password = Hash::make($request->contrasena_nueva);
        $user->save();

        $this->logActivity('cambiar_contrasena', 'Usuario cambió su contraseña');
        return response()->json(['message' => 'Contraseña se ha actualizado correctamente'], 200);
    }

    public function restablecerContrasena(Request $request)
    {
        $user = User::find($request->id);

        $validator = Validator::make($request->all(), [
            'password' => [
                'required', PasswordRules::defaults()
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $this->logActivity('cambiar_contrasena', 'Usuario restablecio contraseña');
        return response()->json(['message' => 'Se ha restablecido la contraseña'], 200);
    }
}
