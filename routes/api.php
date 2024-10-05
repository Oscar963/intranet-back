<?php

use App\Http\Controllers\ArchivoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\LogActivityController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PageController;

// Rutas web, disponibles sin necesidad de autenticación previa
Route::middleware('web')->group(function () {
    Route::get('/listar-banners', [PageController::class, 'listarBanners'])->name('web.listarBanners');
    Route::get('/listar-seccion/{idPage}', [PageController::class, 'listarSeccion'])->name('web.listarSeccion');
});

// Rutas app
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('files', ArchivoController::class);
    Route::apiResource('banners', BannerController::class);
    Route::apiResource('pages', PageController::class);
    Route::post('pages/upload/{idPage}', [PageController::class, 'uploadFile'])->name('page.uploadFile');
    Route::get('pages/archivos/{idPage}', [PageController::class, 'getFiles'])->name('page.getFiles');
});


// Rutas de autenticación
Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('password/email', [AuthController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:5,1') // Permite 5 intentos por minuto
        ->name('password.email');
    Route::post('password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::put('actualizar-perfil', [AuthController::class, 'actualizarPerfil'])->name('actualizarPerfil');
        Route::post('cambiar-contrasena', [AuthController::class, 'cambiarContrasena'])->name('cambiarContrasena');
        Route::post('restablecer-contrasena', [AuthController::class, 'restablecerContrasena'])->name('restablecer.contrasena');
    });
});

// Rutas protegidas por Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Obtener roles y permisos del usuario autenticado
    Route::get('user/roles-permissions', [AuthController::class, 'getPermissions']);

    // Recursos de API RESTful
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('logs', LogActivityController::class)->only([
        'index'
    ]);
});
