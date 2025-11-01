<?php
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Asegúrate de que las peticiones se autentiquen con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Rutas para la gestión de Usuarios
    Route::apiResource('users', UserController::class)->only(['index', 'show', 'store', 'update']);
    Route::put('users/{user}/deactivate', [UserController::class, 'deactivate']);

    // También necesitamos las dependencias de Roles y Estados para el formulario de usuario
    use App\Http\Controllers\RoleController;
    use App\Http\Controllers\StateController;
    Route::apiResource('roles', RoleController::class)->only(['index']);
    Route::apiResource('states', StateController::class)->only(['index']);
});
